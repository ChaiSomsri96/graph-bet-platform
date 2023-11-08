<?php
namespace App\Http\Controllers;
use App\Http\Controllers\QuickbitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DashboardController extends QuickbitController
{
    function index() {
        return $this->load_view('dashboard/index', 'ダッシュボード', '');
    }
    function getAffiliateInfo($user_id) {
        $query = DB::table('affiliates')
        ->select(DB::raw('IFNULL(SUM(BONUS), 0) AS bonus_sum, IFNULL(COUNT(ID), 0) AS bonus_count'))
        ->where('USER_ID', '=', $user_id)
        ->where('DEL_YN', '=', 'N')
        ->get()
        ->toArray();
        return $query[0];
    }
    function getAffiliateUserRanking() {
        $query = DB::table('users')
        ->select(DB::raw('IFNULL(COUNT(users.`ID`),0) AS cnt, users.`ID`, users.`AFFILIATE_CODE`'))
        ->leftJoin(DB::raw('users AS A'), 'A.AFFILIATE_CODE_P', '=', 'users.AFFILIATE_CODE')
        ->whereNotNull('A.ID')
        ->groupBy('users.ID')
        ->groupBy('users.AFFILIATE_CODE')
        ->orderBy(DB::raw('COUNT(users.ID)'), 'desc')
        ->limit(5)
        ->get()
        ->toArray();
        return $query;
    }
    function getDashboardData(Request $request) {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date) + 86399;
        $data = array();
        $data['diff_days'] = $this->getDiffDays($start_date, $end_date);
        $data['new_user_cnt'] = $this->getNewUserCount($start_timestamp, $end_timestamp);
        $data['total_input_sum'] = number_format($this->getTotalInputSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        $data['total_output_sum'] = number_format($this->getTotalOutputSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        $data['total_profit_sum'] = number_format($this->getTotalProfitSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        $data['output_request_count'] = $this->getTotalOutputRequestCount();
        //today profit sum
        $today = date('Y-m-d');
        $start_timestamp = strtotime($today);
        $end_timestamp = strtotime($today) + 86399;
        $data['total_today_profit_sum'] = number_format($this->getTotalProfitSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        //this week profit sum
        $strtotime = date("o-\WW");
        $start_timestamp = strtotime($strtotime);
        $end_timestamp = strtotime("+6 days 23:59:59", $start_timestamp);
        $data['total_this_week_profit_sum'] = number_format($this->getTotalProfitSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        //this month profit sum
        $start_timestamp = mktime(0, 0, 0, date("n"), 1);
        $end_timestamp = mktime(23, 59, 59, date("n"), date("t"));
        $data['total_this_month_profit_sum'] = number_format($this->getTotalProfitSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        //this year profit sum
        $year = date('Y');
        $start_timestamp = strtotime($year.'-01-01');
        $end_timestamp = strtotime($year.'-12-31') + 86399;
        $data['total_this_year_profit_sum'] = number_format($this->getTotalProfitSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        //playing user count
        $data['total_playing_count'] = $this->getNewUserCount('', '', false);
        //affiliate list
        $ret = $this->getAffiliateUserRanking();
        $index  = 1;
        $table_data = array();
        foreach($ret as $item) {
            $a_info = $this->getAffiliateInfo($item->ID);
            $table_item = array(
                'no' => $index,
                'affiliate_code' => $item->AFFILIATE_CODE,
                'cnt' => $item->cnt,
                'a_cnt' => $a_info->bonus_count,
                'a_sum' => number_format(doubleval($a_info->bonus_sum) / pow(10, 6), 8, '.', '')
            );
            $index ++;
            $table_data[] = $table_item;
        }
        $data['table_data'] = $table_data;
        if($data['diff_days'] <= 30) {
            $results = DB::select(DB::raw("
                SELECT B.DATE_FIELD , SUM(sum1) AS sum1 , SUM(sum2) AS sum2, SUM(sum3) AS sum3
                FROM (
                SELECT DATE_FIELD, IFNULL(A.sum , 0) AS sum1, 0 AS sum2, 0 AS sum3
                FROM calendar
                LEFT JOIN (
                SELECT DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') AS 'date_formatted', SUM(AMOUNT_BITS) AS `sum`
                FROM deposit_withdraw_log
                WHERE `TYPE` = 1
                AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') >= '".$start_date."'
                AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') <= '".$end_date."'
                GROUP BY DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d')
                ) A
                ON A.date_formatted = calendar.`DATE_FIELD`
                WHERE DATE_FIELD >= '".$start_date."'
                AND DATE_FIELD <= '".$end_date."'

                UNION

                SELECT DATE_FIELD, 0 AS sum1, IFNULL(A.sum , 0) AS sum2, 0 AS sum3
                FROM calendar
                LEFT JOIN (
                SELECT DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') AS 'date_formatted', SUM(AMOUNT_BITS) AS `sum`
                FROM deposit_withdraw_log
                WHERE `TYPE` = 2
                AND `STATUS` = 1
                AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') >= '".$start_date."'
                AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') <= '".$end_date."'
                GROUP BY DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d')
                ) A
                ON A.date_formatted = calendar.`DATE_FIELD`
                WHERE DATE_FIELD >= '".$start_date."'
                AND DATE_FIELD <= '".$end_date."'

                UNION

                SELECT DATE_FIELD, 0 AS sum1, 0 AS sum2 , IFNULL(A.sum , 0) AS sum3
                FROM calendar
                LEFT JOIN (
                SELECT DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') AS 'date_formatted', SUM(PROFIT) AS `sum`
                FROM game_total
                WHERE DEL_YN = 'N'
                AND STATE='BUSTED'
                AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') >= '".$start_date."'
                AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') <= '".$end_date."'
                GROUP BY DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d')
                ) A
                ON A.date_formatted = calendar.`DATE_FIELD`
                WHERE DATE_FIELD >= '".$start_date."'
                AND DATE_FIELD <= '".$end_date."' ) AS B
                GROUP BY B.DATE_FIELD
                ORDER BY B.DATE_FIELD
            "));
            $data['graph_data'] = $results;
        }
        else {
            $start_date = date('Y-m-01', strtotime($start_date));
            $end_date = date('Y-m-t', strtotime($end_date));

            $results = DB::select(DB::raw("
                SELECT CONCAT(YEAR(F_TB.DATE_FIELD), '-', MONTH(F_TB.DATE_FIELD)) AS DATE_FIELD , SUM(sum1) as sum1, SUM(sum2) as sum2, SUM(sum3) as sum3
                FROM (
                    SELECT B.DATE_FIELD , SUM(sum1) AS sum1 , SUM(sum2) AS sum2, SUM(sum3) AS sum3
                    FROM (
                    SELECT DATE_FIELD, IFNULL(A.sum , 0) AS sum1, 0 AS sum2, 0 AS sum3
                    FROM calendar
                    LEFT JOIN (
                    SELECT DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') AS 'date_formatted', SUM(AMOUNT_BITS) AS `sum`
                    FROM deposit_withdraw_log
                    WHERE `TYPE` = 1
                    AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') >= '".$start_date."'
                    AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') <= '".$end_date."'
                    GROUP BY DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d')
                    ) A
                    ON A.date_formatted = calendar.`DATE_FIELD`
                    WHERE DATE_FIELD >= '".$start_date."'
                    AND DATE_FIELD <= '".$end_date."'

                    UNION

                    SELECT DATE_FIELD, 0 AS sum1, IFNULL(A.sum , 0) AS sum2, 0 AS sum3
                    FROM calendar
                    LEFT JOIN (
                    SELECT DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') AS 'date_formatted', SUM(AMOUNT_BITS) AS `sum`
                    FROM deposit_withdraw_log
                    WHERE `TYPE` = 2
                    AND `STATUS` = 1
                    AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') >= '".$start_date."'
                    AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') <= '".$end_date."'
                    GROUP BY DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d')
                    ) A
                    ON A.date_formatted = calendar.`DATE_FIELD`
                    WHERE DATE_FIELD >= '".$start_date."'
                    AND DATE_FIELD <= '".$end_date."'

                    UNION

                    SELECT DATE_FIELD, 0 AS sum1, 0 AS sum2 , IFNULL(A.sum , 0) AS sum3
                    FROM calendar
                    LEFT JOIN (
                    SELECT DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') AS 'date_formatted', SUM(PROFIT) AS `sum`
                    FROM game_total
                    WHERE DEL_YN = 'N'
                    AND STATE='BUSTED'
                    AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') >= '".$start_date."'
                    AND DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d') <= '".$end_date."'
                    GROUP BY DATE_FORMAT(FROM_UNIXTIME(CREATE_TIME), '%Y-%m-%d')
                    ) A
                    ON A.date_formatted = calendar.`DATE_FIELD`
                    WHERE DATE_FIELD >= '".$start_date."'
                    AND DATE_FIELD <= '".$end_date."' ) AS B
                    GROUP BY B.DATE_FIELD
                    ORDER BY B.DATE_FIELD
                ) F_TB
                GROUP BY YEAR(F_TB.DATE_FIELD),
                MONTH(F_TB.DATE_FIELD)
            "));
            $data['graph_data'] = $results;
        }
        return response()->json(array('status' => 'success', 'data' => $data));
    }
}


