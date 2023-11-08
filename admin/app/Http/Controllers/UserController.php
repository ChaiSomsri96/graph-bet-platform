<?php
namespace App\Http\Controllers;
use App\Http\Controllers\QuickbitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\User;
use App\GameLog;
class UserController extends QuickbitController
{
    function index() {
        return $this->load_view('user/index', 'ユーザー管理', '', array());
    }
    function detail(Request $request) {
        $user_id = $request->user_id;
        $detail = User::find($user_id);
        return $this->load_view('user/detail', 'ユーザー取引履歴', '', array('user_id' => $user_id, 'user_name' => $detail->USERNAME));
    }
    function getHistoryList(Request $request) {
        $ret = array();
        //////////////////////////////////
        $sum_query = GameLog::select(DB::raw('IFNULL(SUM(game_log.BET),0) AS trans_sum'));
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $sum_query = $sum_query->where('game_log.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $sum_query = $sum_query->where('game_log.CREATE_TIME', '<=', $end_timestamp);
        }
        if($request->input("gameid") != "") {
            $sum_query = $sum_query->where('game_log.GAME_ID', '=', $request->input("gameid"));
        }
        $sum_query = $sum_query->where('game_log.USER_ID', '=', $request->input("userid"));
        $sum_query = $sum_query->where('game_log.BOT_YN', '=', 'N');
        $sum = $sum_query->get()->toArray();
        $sum = number_format(doubleval($sum[0]['trans_sum']) / pow(10, 6), 8, '.', '');
        //////////////////////////////////
        $query = GameLog::select('game_log.*');
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $query = $query->where('game_log.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $query = $query->where('game_log.CREATE_TIME', '<=', $end_timestamp);
        }
        if($request->input("gameid") != "") {
            $query = $query->where('game_log.GAME_ID', '=', $request->input("gameid"));
        }
        $query = $query->where('game_log.USER_ID', '=', $request->input("userid"));
        $query = $query->where('game_log.BOT_YN', '=', 'N');
        $query = $query->orderBy('game_log.CREATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = GameLog::select('game_log.*');
            if($request->input("startDate") != "") {
                $start_timestamp = strtotime($request->input("startDate"));
                $temp_query = $temp_query->where('game_log.CREATE_TIME', '>=', $start_timestamp);
            }
            if($request->input("endDate") != "") {
                $end_timestamp = strtotime($request->input("endDate")) + 86399;
                $temp_query = $temp_query->where('game_log.CREATE_TIME', '<=', $end_timestamp);
            }
            if($request->input("gameid") != "") {
                $temp_query = $temp_query->where('game_log.GAME_ID', '=', $request->input("gameid"));
            }
            $temp_query = $temp_query->where('game_log.USER_ID', '=', $request->input("userid"));
            $temp_query = $temp_query->where('game_log.BOT_YN', '=', 'N');
            $temp_query = $temp_query->orderBy('game_log.CREATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            if($item->CRASHED_YN == 'Y') {
                $bust_html = '<span style="color: #dc3545;">'.number_format(doubleval($item->game->BUST)/100 , 2 , '.' , '').'x</span>';
                $profit_html = '<span style="color: #dc3545; font-weight: bold;">'.number_format(doubleval($item->PROFIT) / pow(10, 6) , 8 , '.' , '').' btc</span>';
            }
            else {
                $bust_html = '<span style="color: green;">'.number_format(doubleval($item->CASHOUT)/100 , 2 , '.' , '').'x</span>';
                $profit_html = '<span style="color: green; font-weight: bold;">+'.number_format((doubleval($item->PROFIT) + doubleval($item->BET)) / pow(10, 6), 8, '.', '').' btc</span>';
            }
            $table_item = array(
                'time' => $item->CREATE_TIME,
                'game_round' => 'GAME #'.$item->GAME_ID,
                'bust' => $bust_html,
                'bet' => number_format(doubleval($item->BET) / pow(10, 6) , 8 , '.' , '').' btc',
                'profit' => $profit_html
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data, 'sum'=>$sum));
    }
    function getList(Request $request) {
        $ret = array();
        $query = User::select('users.*');
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $query = $query->where('users.CREATE_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $query = $query->where('users.CREATE_TIME', '<=', $end_timestamp);
        }
        if ($request->input("email") != "") {
            $query = $query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
        }
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        $query = $query->where('users.DEL_YN', '=', 'N');
        $query = $query->orderBy('users.UPDATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = User::select('users.*');
            if($request->input("startDate") != "") {
                $start_timestamp = strtotime($request->input("startDate"));
                $temp_query = $temp_query->where('users.CREATE_TIME', '>=', $start_timestamp);
            }
            if($request->input("endDate") != "") {
                $end_timestamp = strtotime($request->input("endDate")) + 86399;
                $temp_query = $temp_query->where('users.CREATE_TIME', '<=', $end_timestamp);
            }
            if ($request->input("email") != "") {
                $temp_query = $temp_query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
            }
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
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
        $t_trans = array(
            '1.2305', '0.00', '0.00', '0.00', '0.00',
            '0.00', '0.00', '0.00', '0.00', '0.00',
            '0.00', '0.00', '0.00', '0.00', '0.00'
        );
        foreach($result as $item) {
            $aff_html = '';
            //affiliate html code for user list
            if($item->AFF_YN == 'N') {
                $aff_html = '<div class="flex items-center justify-end"><div>'.$item->AFFILIATE_CODE.'</div><span class="ml-2 text-xs badge badge-danger" style="font-size: 12px;">OFF</span></div>';
                $aff_button = '<button type="button" onclick="affiliate_on('.$item->ID.')" class="ml-2 text-xs btn btn-warning">権限付与</button>';
            }
            else {
                $aff_html = '<div class="flex items-center justify-end"><div>'.$item->AFFILIATE_CODE.'</div><span class="ml-2 text-xs badge badge-warning">ON</span></div>';
                $aff_button = '<button type="button" onclick="affiliate_off('.$item->ID.')" class="ml-2 text-xs btn btn-danger">権限解除</button>';
            }
            //getFirstDeposit
            $table_item = array(
                'id' => $item->ID,
                'username' =>  $item->USERNAME,
                'email' => $item->EMAIL,
                'regdate' => $item->CREATE_TIME,
                'affiliate_code' => $aff_html,
                'first_deposit' => number_format($this->getFirstDeposit('', '', $item->WALLET_ID) / pow(10, 6), 8, '.', ''),
                'total_deposit' => number_format($this->getTotalInputSum('', '', $item->WALLET_ID) / pow(10, 6), 8, '.', ''),
                'total_withdraw' => number_format($this->getTotalOutputSum('', '', $item->WALLET_ID) / pow(10, 6), 8, '.', ''),
                'total_affiliate' => number_format($this->getAffiliateSum('', '', $item->ID) / pow(10, 6), 8, '.', ''),
                'total_trans_sum' => number_format($this->getUserTransactionSum('', '', $item->ID) / pow(10, 6), 8, '.', ''),
                'wallet' => isset($item->wallet->WALLET)?( number_format($item->wallet->WALLET / pow(10, 6), 8, '.', '') ):'0.00000000',
                'kyc' => ($item->KYC_YN == 'Y' ? '<span onclick="kyc_change('.$item->ID.');" class="kyc-span badge badge-success">承認</span>' : '<span onclick="kyc_change('.$item->ID.');" class="kyc-span badge badge-danger">未承</span>'),
                'block_yn' => $item->BLOCK_YN,
                'aff_button' => $aff_button
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
    function change(Request $request) {
        $type = $request->input('type');
        $id = $request->input('id');
        if($type == 1) {
            User::where("id", $id)
            ->update(array("DEL_YN" => 'Y', "UPDATE_TIME" => time()));
        }
        else if($type == 2) {
            User::where("id", $id)
            ->update(array("BLOCK_YN" => 'Y', "UPDATE_TIME" => time()));
        }
        else if($type == 3) {
            User::where("id", $id)
            ->update(array("BLOCK_YN" => 'N', "UPDATE_TIME" => time()));
        }
        else if($type == 4) {
            User::where("id", $id)
            ->update(array("AFF_YN" => 'Y', "UPDATE_TIME" => time()));
        }
        else if($type == 5) {
            User::where("id", $id)
            ->update(array("AFF_YN" => 'N', "UPDATE_TIME" => time()));
        }
        else if($type == 6) {
            $kyc_user = User::where("id", $id)->get()->toArray();
            if(count($kyc_user) < 1) {
                User::where("id", $id)
                ->update(array("KYC_YN" => $request->input('kyc'), "UPDATE_TIME" => time()));
            }
            else {
                if($kyc_user[0]['KYC_YN'] != $request->input('kyc'))
                    User::where("id", $id)
                    ->update(array("KYC_YN" => $request->input('kyc'), "UPDATE_TIME" => time()));
            }
        }
        return response()->json(array('status' => 'success'));
    }
    function reset(Request $request) {
        $NEW_PASSWORD = $request->input('NEW_PASSWORD');
        $id = $request->input("ID");
        $user = User::where('id', $id)->update(array(
            'PASSWORD' => md5($NEW_PASSWORD)
        ));
        return response()->json(array('status' => 'success'));
    }
    function getKYCImages(Request $request) {
        $user_id = $request->input("id");
        $detail = User::find($user_id);
        $images = [];
        if(isset($detail->PASSPORT) && $detail->PASSPORT != '') {
            $images[] = Config::get('master.front_host').$detail->PASSPORT;
        }
        if(isset($detail->ID_CARD) && $detail->ID_CARD != '') {
            $images[] = Config::get('master.front_host').$detail->ID_CARD;
        }
        return response()->json(array('status' => 'success', 'data' => $images));
    }
}


