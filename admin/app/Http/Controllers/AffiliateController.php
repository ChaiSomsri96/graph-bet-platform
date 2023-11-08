<?php
namespace App\Http\Controllers;
use App\Http\Controllers\QuickbitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Affiliate;
use App\Bitcoin;
class AffiliateController extends QuickbitController
{
    protected $week_day_name;
    protected function initialize() {
        $this->week_day_name = ['日', '月', '火', '水', '木', '金', '土'];
    }
    function index() {
        return $this->load_view('affiliate/index', 'アフィリエイト', '', array());
    }
    public function setBonus(Request $request) {
        $aff_pros = $request->input('AFF_PROS');
        $id = $request->input('ID');
        User::where('ID', $id)
        ->update(array( "AFF_PROS" => intval($aff_pros) ,
                        "UPDATE_TIME" => time()));
        return response()->json(array('status' => 'success'));
    }
    function getList(Request $request) {
        $ret = array();
        $query = User::select('users.*');
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        if ($request->input("aff_code") != "") {
            $query = $query->where('users.AFFILIATE_CODE', 'like', '%'.$request->input("aff_code").'%');
        }
        $query = $query->where('users.DEL_YN', '=', 'N');
        $query = $query->orderBy('users.UPDATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = User::select('users.*');
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
            }
            if ($request->input("aff_code") != "") {
                $temp_query = $temp_query->where('users.AFFILIATE_CODE', 'like', '%'.$request->input("aff_code").'%');
            }
            $temp_query = $temp_query->where('users.DEL_YN', '=', 'N');
            $temp_query = $temp_query->orderBy('users.CREATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            $table_item = array(
                'aff_code' => $item->AFFILIATE_CODE,
                'total_deposit' => number_format($this->getTotalInputSum('', '', $item->WALLET_ID) / pow(10, 6), 8, '.', ''),
                'total_withdraw' => number_format($this->getTotalOutputSum('', '', $item->WALLET_ID) / pow(10, 6), 8, '.', ''),
                'bonus_sum' => number_format($this->getAffiliateSum('', '', $item->ID) / pow(10, 6), 8, '.', ''),
                'bonus_withdraw' => number_format($this->getTotalOutputSum('', '', $item->WALLET_ID) / pow(10, 6), 8, '.', ''),
                'username' => $item->USERNAME,
                'id' => $item->ID,
                'aff_pros' => $item->AFF_PROS
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
    public function history(Request $request) {
        $user_id = $request->user_id;
        $detail = User::find($user_id);
        return $this->load_view('affiliate/history', '報酬支払い履歴', '', array('user_id' => $user_id, 'aff_code' => $detail->AFFILIATE_CODE));
    }
    public function statistics(Request $request) {
        $user_id = $request->user_id;
        $detail = User::find($user_id);
        $query = Bitcoin::select(DB::raw('MIN(deposit_withdraw_log.CREATE_TIME) as time'))
        ->where('TYPE', '1')
        ->get()->toArray();
        $date_list = array();
        if(isset($query[0]['time'])) {
            //var_dump($query[0]['time']);
            $m2 = intval(date("m", $query[0]['time']));
            $y2 = intval(date("Y", $query[0]['time']));
            $m1 = intval(date("m"));
            $y1 = intval(date("Y"));
            $diff_month = ($y1 - $y2) * 12 + $m1 - $m2;
            for($i = 0; $i <= $diff_month; $i ++) {
                $month = ($m2 + $i) % 12;
                if($month == 0)
                    $month = 12;
                $year = $y2 + intval(($m2 + $i - 1) / 12);
                if($month < 10)
                    $month = '0'.$month;
                $date_list[] = array(
                    'value' => $year.'-'.$month,
                    'text' => $year.'年'.$month.'月'
                );
            }
        }
        else {
            $date_list[] = array(
                'value' => date('Y-m'),
                'text' => date('Y年m月')
            );
        }
        return $this->load_view('affiliate/statistics', 'アフィリエイト月別通計', '', array('user_id' => $user_id, 'aff_code' => $detail->AFFILIATE_CODE, 'date_list' => $date_list));
    }

    public function getStatisticList(Request $request) {
        $ret = array();
        ///////////////////////////////////
        $sum_query = Affiliate::select(DB::raw('IFNULL(SUM(affiliates.BONUS),0) AS sum1'))
        ->leftJoin('users', 'users.ID', '=', 'affiliates.AFF_USER_ID');
        if($request->input("time") != "") {
            $pieces = explode("-", $request->input("time"));
            $month = $pieces[1];
            $year = $pieces[0];
            $start_timestamp = mktime(0,0,0,$month,1,$year);
            $end_timestamp = mktime(23,59,00,$month+1,0,$year);
            $sum_query = $sum_query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
            $sum_query = $sum_query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
        }
        $sum_query = $sum_query->where('affiliates.DEL_YN', '=', 'N');
        $sum_query = $sum_query->where('affiliates.USER_ID', '=', $request->input("userid"));
        $sum = $sum_query->get()->toArray();
        $sum = number_format(doubleval($sum[0]['sum1']) / pow(10, 6), 8, '.', '');
        //////////////////////////////
        $query = Affiliate::select('affiliates.*')
        ->leftJoin('users', 'users.ID', '=', 'affiliates.AFF_USER_ID');
        if($request->input("time") != "") {
            $pieces = explode("-", $request->input("time"));
            $month = $pieces[1];
            $year = $pieces[0];
            $start_timestamp = mktime(0,0,0,$month,1,$year);
            $end_timestamp = mktime(23,59,00,$month+1,0,$year);
            $query = $query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
            $query = $query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
        }
        $query = $query->where('affiliates.DEL_YN', '=', 'N');
        $query = $query->where('affiliates.USER_ID', '=', $request->input("userid"));
        $query = $query->orderBy('affiliates.UPDATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = Affiliate::select('affiliates.*')
            ->leftJoin('users', 'users.ID', '=', 'affiliates.AFF_USER_ID');
            if($request->input("time") != "") {
                $pieces = explode("-", $request->input("time"));
                $month = $pieces[1];
                $year = $pieces[0];
                $start_timestamp = mktime(0,0,0,$month,1,$year);
                $end_timestamp = mktime(23,59,00,$month+1,0,$year);
                $temp_query = $temp_query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
                $temp_query = $temp_query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
            }
            $temp_query = $temp_query->where('affiliates.DEL_YN', '=', 'N');
            $temp_query = $temp_query->where('affiliates.USER_ID', '=', $request->input("userid"));
            $temp_query = $temp_query->orderBy('affiliates.UPDATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            $table_item = array(
                'id' => $item->ID,
                'userid' => $item->AFF_USER_ID,
                'username' => $item->user->USERNAME,
                'first_deposit' => number_format(doubleval($item->FIRST_DEPOSIT) / pow(10, 6), 8, '.', ''),
                'bonus' => number_format(doubleval($item->BONUS) / pow(10, 6), 8, '.', ''),
                'pros' => $item->AFF_PROS
            );
            if(date('w', $item->CREATE_TIME) == 0 || date('w', $item->CREATE_TIME) == 6) {
                $table_item['time'] = '<span style="color:#dc3545; font-weight: bold;">'.date('d日', $item->CREATE_TIME).' ('.$this->week_day_name[date('w', $item->CREATE_TIME)].')</span>';
            }
            else
                $table_item['time'] = date('d日', $item->CREATE_TIME).' ('.$this->week_day_name[date('w', $item->CREATE_TIME)].')';
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data, 'sum'=>$sum));
    }

    public function getHistoryList(Request $request) {
        $ret = array();
        ///////////////////////////////////
        $sum_query = Affiliate::select(DB::raw('IFNULL(SUM(affiliates.BONUS),0) AS sum1'))
        ->leftJoin('users', 'users.ID', '=', 'affiliates.AFF_USER_ID');
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $sum_query = $sum_query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $sum_query = $sum_query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
        }
        if ($request->input("username") != "") {
            $sum_query = $sum_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        $sum_query = $sum_query->where('affiliates.DEL_YN', '=', 'N');
        $sum_query = $sum_query->where('affiliates.USER_ID', '=', $request->input("userid"));
        $sum = $sum_query->get()->toArray();
        $sum = number_format(doubleval($sum[0]['sum1']) / pow(10, 6), 8, '.', '');
        //////////////////////////////
        $query = Affiliate::select('affiliates.*')
        ->leftJoin('users', 'users.ID', '=', 'affiliates.AFF_USER_ID');
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $query = $query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $query = $query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
        }
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        $query = $query->where('affiliates.DEL_YN', '=', 'N');
        $query = $query->where('affiliates.USER_ID', '=', $request->input("userid"));
        $query = $query->orderBy('affiliates.UPDATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = Affiliate::select('affiliates.*')
            ->leftJoin('users', 'users.ID', '=', 'affiliates.AFF_USER_ID');
            if($request->input("startDate") != "") {
                $start_timestamp = strtotime($request->input("startDate"));
                $temp_query = $temp_query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
            }
            if($request->input("endDate") != "") {
                $end_timestamp = strtotime($request->input("endDate")) + 86399;
                $temp_query = $temp_query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
            }
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
            }
            $temp_query = $temp_query->where('affiliates.DEL_YN', '=', 'N');
            $temp_query = $temp_query->where('affiliates.USER_ID', '=', $request->input("userid"));
            $temp_query = $temp_query->orderBy('affiliates.UPDATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            $table_item = array(
                'id' => $item->ID,
                'userid' => $item->AFF_USER_ID,
                'username' => $item->user->USERNAME,
                'time' => $item->CREATE_TIME,
                'first_deposit' => number_format(doubleval($item->FIRST_DEPOSIT) / pow(10, 6), 8, '.', ''),
                'bonus' => number_format(doubleval($item->BONUS) / pow(10, 6), 8, '.', ''),
                'pros' => $item->AFF_PROS
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data, 'sum'=>$sum));
    }
}


