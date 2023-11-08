<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WalletHistory;
use App\Admin;
use App\Bitcoin;
/*
use App\GameLog;
use App\User;
*/
use Illuminate\Support\Facades\DB;
class WalletController extends QuickbitController
{
    function index(Request $request) {

        $yearmonth = $request->input('yearmonth');

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
        $start_timestamp = $end_timestamp = '';
        if(isset($yearmonth) && $yearmonth != '')
        {
            $pieces = explode("-", $yearmonth);
            $month = $pieces[1];
            $year = $pieces[0];
            $start_timestamp = mktime(0, 0, 0, $month, 1, $year);
            $end_timestamp = mktime(23, 59, 00, $month + 1, 0, $year);
        }
        // total_input_sum
        $total_input_sum = number_format($this->getTotalInputSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        // total_output_sum
        $total_output_sum = number_format($this->getTotalOutputSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        // total_referral_sum
        $total_bonus_sum = number_format($this->getAffiliateSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        // total_wallet_sum
        $total_wallet_sum = number_format($this->getWalletSum() / pow(10, 6), 8, '.', '');
        // total_profit_sum
        $total_profit_sum = number_format($this->getUserProfitSum($start_timestamp, $end_timestamp) / pow(10, 6), 8, '.', '');
        if(!isset($yearmonth))
            $yearmonth = '';
        return $this->load_view('wallets/index', '月次統計', 'ウォレット管理', array('total_input_sum' => $total_input_sum, 'total_output_sum' => $total_output_sum, 'total_bonus_sum' => $total_bonus_sum,
                                                                            'total_wallet_sum' => $total_wallet_sum,
                                                                            'total_profit_sum' => $total_profit_sum,
                                                                            'date_list' => $date_list,
                                                                            'yearmonth' => $yearmonth));
    }
    function history() {
        return $this->load_view('wallets/history', '詳細履歴', 'ウォレット管理', array());
    }
    function getList(Request $request) {
        $ret = array();
        //get admin wallet id
        $admin = Admin::where(array('ID' => 1))->get()->toArray();
        $admin_wallet_id = '';
        if(count($admin) > 0) {
            $admin_wallet_id = $admin[0]['WALLET_ID'];
        }
        //
        $query = WalletHistory::select('wallet_history.*')
        ->leftJoin('users', 'users.WALLET_ID', '=', 'wallet_history.TO_WID');
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $query = $query->where('wallet_history.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $query = $query->where('wallet_history.CREATE_TIME', '<=', $end_timestamp);
        }
        if ($request->input("gameid") != "") {
            $query = $query->where('wallet_history.GAME_ID', '=', $request->input("gameid"));
        }
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        $query = $query->where(function($sub_query) use($admin_wallet_id) {
            $sub_query->where('FROM_WID', '=', $admin_wallet_id)
                      ->orWhere('TO_WID', '=', $admin_wallet_id);
        });
        $query = $query->orderBy('wallet_history.CREATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = WalletHistory::select('wallet_history.*')
                ->leftJoin('users', 'users.WALLET_ID', '=', 'wallet_history.TO_WID');
            if($request->input("startDate") != "") {
                $start_timestamp = strtotime($request->input("startDate"));
                $temp_query = $temp_query->where('wallet_history.CREATE_TIME', '>=', $start_timestamp);
            }
            if($request->input("endDate") != "") {
                $end_timestamp = strtotime($request->input("endDate")) + 86399;
                $temp_query = $temp_query->where('wallet_history.CREATE_TIME', '<=', $end_timestamp);
            }
            if ($request->input("gameid") != "") {
                $temp_query = $temp_query->where('wallet_history.GAME_ID', '=', $request->input("gameid"));
            }
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
            }
            $temp_query = $temp_query->where(function($sub_query) use($admin_wallet_id) {
                $sub_query->where('FROM_WID', '=', $admin_wallet_id)
                          ->orWhere('TO_WID', '=', $admin_wallet_id);
            });
            $temp_query = $temp_query->orderBy('wallet_history.CREATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach($result as $item) {
            if($item->FROM_WID == $admin_wallet_id) {
                //cashout
                $table_item = array(
                    'time' => $item->CREATE_TIME, //method_time
                    'prev_wallet' => number_format(doubleval($item->FROM_PRE_W / pow(10, 6)), 8, '.', ''),
                    'amount' => -number_format(doubleval($item->AMOUNT / pow(10, 6)), 8, '.', ''),
                    'after_wallet' => number_format(doubleval( ($item->FROM_PRE_W - $item->AMOUNT) / pow(10, 6)), 8, '.', ''),
                    'note' => 'Pay for cashout user',
                    'object' => (isset($item->tuser->USERNAME) ? $item->tuser->USERNAME : ''),
                    'gameid' => 'GAME #'.$item->GAME_ID
                );
            }
            else if($item->TO_WID == $admin_wallet_id) {
                //bust
                $table_item = array(
                    'time' => $item->CREATE_TIME, //method_time
                    'prev_wallet' => number_format(doubleval($item->TO_PRE_W / pow(10, 6)), 8, '.', ''),
                    'amount' => number_format(doubleval($item->AMOUNT / pow(10, 6)), 8, '.', ''),
                    'after_wallet' => number_format(doubleval( ($item->TO_PRE_W + $item->AMOUNT) / pow(10, 6)), 8, '.', ''),
                    'note' => 'Get bet amount after game is bust',
                    'object' => 'アドミン仮ワレット',
                    'gameid' => 'GAME #'.$item->GAME_ID
                );
            }
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
}


