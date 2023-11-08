<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuickbitController extends Controller
{
    function getDiffDays($start_date, $end_date) {
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        $datediff = $end_timestamp - $start_timestamp;
        return round($datediff / (60 * 60 * 24));
    }
    function getTotalInputSum($start_timestamp = '', $end_timestamp = '', $wallet_id = '') {
        $query = DB::table('deposit_withdraw_log')
            ->select(DB::raw('IFNULL(SUM(deposit_withdraw_log.AMOUNT_BITS),0) AS input_sum'))
            ->where('deposit_withdraw_log.TYPE', 1);
        if($start_timestamp != '')
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '>=', $start_timestamp);
        if($end_timestamp != '')
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '<=', $end_timestamp);
        if($wallet_id != '')
            $query = $query->where('deposit_withdraw_log.WALLET_ID', '=', $wallet_id);
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->input_sum);
    }
    function getTotalOutputSum($start_timestamp = '', $end_timestamp = '', $wallet_id = '') {
        $query = DB::table('deposit_withdraw_log')
                ->select(DB::raw('IFNULL(SUM(deposit_withdraw_log.AMOUNT_BITS),0) AS output_sum'))
                ->where('deposit_withdraw_log.TYPE', 2)
                ->where('deposit_withdraw_log.STATUS', 1);
        if($start_timestamp != '')
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '>=', $start_timestamp);
        if($end_timestamp != '')
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '<=', $end_timestamp);
        if($wallet_id != '')
            $query = $query->where('deposit_withdraw_log.WALLET_ID', '=', $wallet_id);
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->output_sum);
    }
    function getAffiliateSum($start_timestamp = '', $end_timestamp = '', $user_id = '') {
        $query = DB::table('affiliates')
                ->select(DB::raw('IFNULL(SUM(affiliates.BONUS),0) AS bonus_sum'))
                ->where('affiliates.DEL_YN', 'N');
        if($start_timestamp != '')
            $query = $query->where('affiliates.CREATE_TIME', '>=', $start_timestamp);
        if($end_timestamp != '')
            $query = $query->where('affiliates.CREATE_TIME', '<=', $end_timestamp);
        if($user_id != '')
            $query = $query->where('affiliates.USER_ID', '=', $user_id);
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->bonus_sum);
    }
    function getWalletSum() {
        $query = DB::table('users')
                ->select(DB::raw('IFNULL(SUM(user_wallets.WALLET),0) AS wallet_sum'))
                ->leftJoin('user_wallets', 'users.WALLET_ID', '=', 'user_wallets.WALLET_ID')
                ->where('users.DEL_YN', 'N');
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->wallet_sum);
    }
    function getUserProfitSum($start_timestamp = '', $end_timestamp = '') {
        $query = DB::table('game_log')
                ->select(DB::raw('IFNULL(SUM(game_log.PROFIT),0) AS profit_sum'))
                ->where('game_log.BOT_YN', 'N')
                ->where('game_log.DEL_YN', 'N');
        if($end_timestamp != '')
            $query = $query->where('game_log.CREATE_TIME', '<=', $end_timestamp);
        if($start_timestamp != '')
            $query = $query->where('game_log.CREATE_TIME', '>=', $start_timestamp);
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->profit_sum);
    }
    function getTotalProfitSum($start_timestamp = '', $end_timestamp = '') {
        $query = DB::table('game_total')
                ->select(DB::raw('IFNULL(SUM(game_total.PROFIT),0) AS profit_sum'))
                ->where('game_total.DEL_YN', 'N');
        if($start_timestamp != '')
            $query = $query->where('game_total.CREATE_TIME', '>=', $start_timestamp);
        if($end_timestamp != '')
            $query = $query->where('game_total.CREATE_TIME', '<=', $end_timestamp);
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->profit_sum);
    }
    function getUserTransactionSum($start_timestamp = '', $end_timestamp = '', $user_id = '') {
        $query = DB::table('game_log')
                ->select(DB::raw('IFNULL(SUM(game_log.BET),0) AS bet_sum'))
                ->where('game_log.BOT_YN', 'N')
                ->where('game_log.DEL_YN', 'N');
        if($end_timestamp != '')
            $query = $query->where('game_log.CREATE_TIME', '<=', $end_timestamp);
        if($start_timestamp != '')
            $query = $query->where('game_log.CREATE_TIME', '>=', $start_timestamp);
        if($user_id != '')
            $query = $query->where('game_log.USER_ID', '=', $user_id);
        $sum = $query->get()->toArray();
        return doubleval($sum[0]->bet_sum);
    }
    function getFirstDeposit($start_timestamp = '', $end_timestamp = '', $wallet_id = '') {
        $query = DB::table('deposit_withdraw_log')
                ->select(DB::raw('deposit_withdraw_log.AMOUNT_BTC'))
                ->where('TYPE', 1);
        if($start_timestamp != '')
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '>=', $start_timestamp);
        if($end_timestamp != '')
            $query = $query->where('deposit_withdraw_log.CREATE_TIME', '<=', $end_timestamp);
        if($wallet_id != '')
            $query = $query->where('deposit_withdraw_log.WALLET_ID', '=', $wallet_id);
        $query = $query->orderBy('deposit_withdraw_log.CREATE_TIME', 'asc');
        $query = $query->limit(1);
        $ret = $query->get()->toArray();
        if(count($ret) < 1)
            return 0;
        else
            return doubleval($ret[0]->AMOUNT_BTC);
    }
    function getNewUserCount($start_timestamp = '', $end_timestamp = '', $new_user = true) {
        $query = DB::table('users')
                ->select(DB::raw('count(users.ID) as cnt'))
                ->where('DEL_YN', 'N');
        if( !$new_user )
            $query = $query->where('BLOCK_YN', 'N');
        if($start_timestamp != '')
            $query = $query->where('users.CREATE_TIME', '>=', $start_timestamp);
        if($end_timestamp != '')
            $query = $query->where('users.CREATE_TIME', '<=', $end_timestamp);
        $ret = $query->get()->toArray();
        if(count($ret) < 1)
            return 0;
        else
            return intval($ret[0]->cnt);
    }
    function getTotalOutputRequestCount() {
        $query = DB::table('deposit_withdraw_log')
                ->select(DB::raw('count(deposit_withdraw_log.ID) as cnt'))
                ->where('TYPE', 2)
                ->where('STATUS', 0);
        $ret = $query->get()->toArray();
        if(count($ret) < 1)
            return 0;
        else
            return intval($ret[0]->cnt);
    }
}
