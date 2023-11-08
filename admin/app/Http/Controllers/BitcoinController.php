<?php
namespace App\Http\Controllers;
use App\Http\Controllers\QuickbitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Bitcoin;
use App\Wallet;
class BitcoinController extends QuickbitController
{
    function index() {
        return $this->load_view('bitcoin/deposits', '入金履歴', '入出金管理', array());
    }
    function withdraws() {
        return $this->load_view('bitcoin/withdraws', '出金申請', '入出金管理', array());
    }
    function agreeWithdraw(Request $request) {
        $id = $request->input('id');
        $ret = Bitcoin::select('deposit_withdraw_log.*')
        ->where('deposit_withdraw_log.ID', '=', $id)
        ->get()->toArray();
        $amount = 0; $wallet_id = '';
        if(count($ret) > 0) {
            $amount = intval($ret[0]['AMOUNT_BITS']);
            $wallet_id = $ret[0]['WALLET_ID'];
        }
        Bitcoin::where('ID', $id)
        ->update(array("STATUS" => 1,
                       "UPDATE_TIME" => time()));
        if($amount > 0) {
            Wallet::where('WALLET_ID', $wallet_id)
            ->decrement('WALLET_TEMP', $amount);
        }
        return response()->json(array('status' => 'success'));
    }
    function disagreeWithdraw(Request $request) {
        $id = $request->input('id');
        $ret = Bitcoin::select('deposit_withdraw_log.*')
        ->where('deposit_withdraw_log.ID', '=', $id)
        ->get()->toArray();
        $amount = 0; $wallet_id = '';
        if(count($ret) > 0) {
            $amount = intval($ret[0]['AMOUNT_BITS']);
            $wallet_id = $ret[0]['WALLET_ID'];
        }
        Bitcoin::where('ID', $id)
        ->update(array("STATUS" => 2,
                       "UPDATE_TIME" => time()));
        if($amount > 0) {
            Wallet::where('WALLET_ID', $wallet_id)
            ->increment('WALLET', $amount);
            Wallet::where('WALLET_ID', $wallet_id)
            ->decrement('WALLET_TEMP', $amount);
        }
        return response()->json(array('status' => 'success'));
    }
    function getWithdrawsList(Request $request) {
        $ret = array();
        $query = Bitcoin::select('deposit_withdraw_log.*')
        ->leftJoin('users', 'users.WALLET_ID', '=', 'deposit_withdraw_log.WALLET_ID');
        $query = $query->where('deposit_withdraw_log.TYPE', '=', 2);
        if ($request->input("email") != "") {
            $query = $query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
        }
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        if ($request->input("extra") != "") {
            $query = $query->where(function($sub_query) use($request) {
                $sub_query->where('deposit_withdraw_log.DETAIL', 'like', '%'.$request->input("extra").'%')
                        ->orWhere('deposit_withdraw_log.TXHASH', 'like', '%'.$request->input("extra").'%');
            });
        }
        $query = $query->orderBy('deposit_withdraw_log.STATUS', 'asc');
        $query = $query->orderBy('deposit_withdraw_log.CREATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = Bitcoin::select('deposit_withdraw_log.*')
            ->leftJoin('users', 'users.WALLET_ID', '=', 'deposit_withdraw_log.WALLET_ID');
            $temp_query = $temp_query->where('deposit_withdraw_log.TYPE', '=', 2);
            if ($request->input("email") != "") {
                $temp_query = $temp_query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
            }
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
            }
            if ($request->input("extra") != "") {
                $temp_query = $temp_query->where(function($sub_query) use($request) {
                    $sub_query->where('deposit_withdraw_log.DETAIL', 'like', '%'.$request->input("extra").'%')
                            ->orWhere('deposit_withdraw_log.TXHASH', 'like', '%'.$request->input("extra").'%');
                });
            }
            $temp_query = $temp_query->orderBy('deposit_withdraw_log.STATUS', 'asc');
            $temp_query = $temp_query->orderBy('deposit_withdraw_log.CREATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach($result as $item) {
            if($item->STATUS == 0) {
                $action_html = '
                    <button type="button" class="btn btn-primary text-xs" onclick="agree('.$item->ID.')">承認</button>
                    <button type="button" class="btn btn-danger text-xs" onclick="disagree('.$item->ID.')">非承認</button>
                ';
            }
            else if($item->STATUS == 1) {
                $action_html = '
                    <span class="text-sm badge badge-warning">出金済</span>
                ';
            }
            else if($item->STATUS == 2) {
                $action_html = '
                <span class="text-xs badge badge-danger">申請取消し</span>
                ';
            }
            $table_item = array(
                'user_id' => $item->user->ID,
                'user_name' => (isset($item->user->USERNAME)?$item->user->USERNAME:''),
                'total_deposit' => number_format($this->getTotalInputSum('', '', $item->user->WALLET_ID) / pow(10, 6), 8, '.', '').' btc',
                'total_trans' => number_format($this->getUserTransactionSum('', '', $item->user->ID) / pow(10, 6), 8, '.', '').' btc',
                'withdraw_request' => $item->AMOUNT_BTC.' btc',
                'wallet' => number_format(doubleval($item->user->wallet->WALLET / pow(10, 6)), 8, '.', '').' btc',
                'kyc' => ($item->user->KYC_YN == 'Y' ? '<span onclick="kyc_change('.$item->user->ID.');" class="kyc-span badge badge-success">承認</span>' : '<span onclick="kyc_change('.$item->user->ID.');" class="kyc-span badge badge-danger">未承</span>'),
                'action' => $action_html,
                'detail' => $item->DETAIL
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
    function getDepositsList(Request $request) {
        $ret = array();
        $query = Bitcoin::select('deposit_withdraw_log.*')
        ->leftJoin('users', 'users.WALLET_ID', '=', 'deposit_withdraw_log.WALLET_ID');

        $query = $query->where('deposit_withdraw_log.TYPE', '=', 1);

        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '<=', $end_timestamp);
        }
        if ($request->input("email") != "") {
            $query = $query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
        }
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        if ($request->input("extra") != "") {
            $query = $query->where(function($sub_query) use($request) {
                $sub_query->where('deposit_withdraw_log.DETAIL', 'like', '%'.$request->input("extra").'%')
                        ->orWhere('deposit_withdraw_log.TXHASH', 'like', '%'.$request->input("extra").'%');
            });
        }
        $query = $query->orderBy('deposit_withdraw_log.CREATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = Bitcoin::select('deposit_withdraw_log.*')
            ->leftJoin('users', 'users.WALLET_ID', '=', 'deposit_withdraw_log.WALLET_ID');
            $temp_query = $temp_query->where('deposit_withdraw_log.TYPE', '=', 1);
            if($request->input("startDate") != "") {
                $start_timestamp = strtotime($request->input("startDate"));
                $temp_query = $temp_query->where('deposit_withdraw_log.CREATE_TIME', '>=', $start_timestamp);
            }
            if($request->input("endDate") != "") {
                $end_timestamp = strtotime($request->input("endDate")) + 86399;
                $temp_query = $temp_query->where('deposit_withdraw_log.CREATE_TIME', '<=', $end_timestamp);
            }
            if ($request->input("email") != "") {
                $temp_query = $temp_query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
            }
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
            }
            if ($request->input("extra") != "") {
                $temp_query = $temp_query->where(function($sub_query) use($request) {
                    $sub_query->where('deposit_withdraw_log.DETAIL', 'like', '%'.$request->input("extra").'%')
                            ->orWhere('deposit_withdraw_log.TXHASH', 'like', '%'.$request->input("extra").'%');
                });
            }
            $temp_query = $temp_query->orderBy('deposit_withdraw_log.CREATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach($result as $item) {
            $table_item = array(
                'time' => $item->CREATE_TIME,
                'username' => (isset($item->user->USERNAME)?$item->user->USERNAME:''),
                'useremail' => (isset($item->user->EMAIL)?$item->user->EMAIL:''),
                'wallet_id' => $item->WALLET_ID,
                'wallet' => number_format(doubleval($item->PRE_W / pow(10, 6)), 8, '.' , ''),
                'input_amount' => number_format($item->AMOUNT_BTC, 8, '.', ''),
                'detail' => $item->DETAIL,
                'transaction' => $item->TXHASH
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
}


