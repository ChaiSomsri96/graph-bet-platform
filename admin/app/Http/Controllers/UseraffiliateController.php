<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Affiliate;
use App\Bitcoin;
use App\Bot;
use App\Wallet;
class UseraffiliateController extends Controller
{
    protected $week_day_name;
    protected function initialize() {
        $this->week_day_name = ['日', '月', '火', '水', '木', '金', '土'];
    }
    function affLink(Request $request) {
        $user_id = $request->session()->get('id');
        $detail = User::find($user_id);
        return $this->load_view('user_affiliate/aff_link', '紹介URL', 'アフィリエイト', array('aff_link' => Config::get('master.aff_link_prefix').'aff_code='.$detail->AFFILIATE_CODE));
    }
    function user(Request $request) {
        $user_id = $request->session()->get('id');
        $detail = User::find($user_id);
        return $this->load_view('user_affiliate/user', 'アフィリエイトユーザー', 'アフィリエイト', array('aff_code' => $detail->AFFILIATE_CODE));
    }

    function index(Request $request) {
        //$request->session()->put("id", $admin->ID);
        $user_id = $request->session()->get('id');
        $detail = User::find($user_id);
        return $this->load_view('user_affiliate/history', '報酬支払い履歴', 'アフィリエイト', array('aff_code' => $detail->AFFILIATE_CODE));
    }
    public function userCreate(Request $request) {
        $aff_code = $request->session()->get('aff_code');

        // check username duplicate
        $users = User::where(array('DEL_YN' => 'N' , 'USERNAME' => $request->input('USERNAME')))->get()->toArray();
        if(count($users) > 0) {
            return response()->json(array('status' => 'fail', 'error_msg' => 'これは重複ユーザー名です。他のユーザー名に変更してください。'));
        }
        $bots = Bot::where(array('DEL_YN' => 'N' , 'NAME' => $request->input('USERNAME')))->get()->toArray();
        if(count($bots) > 0) {
            return response()->json(array('status' => 'fail', 'error_msg' => 'これは重複ユーザー名です。他のユーザー名に変更してください。'));
        }
        // check duplicate affiliate code
        $affiliate = User::where(array('DEL_YN' => 'N' , 'AFFILIATE_CODE' => $request->input('AFF_CODE')))->get()->toArray();
        if (count($affiliate) > 0) {
            return response()->json(array('status' => 'fail', 'error_msg' => 'アフィリエイトコードが重複しています。'));
        }
        while(1) {
            //
            $wallet_id = $this->generateRandomString(10);
            //check wallet id is duplicate
            $wallet = User::where(array('DEL_YN' => 'N' , 'WALLET_ID' => $wallet_id))->get()->toArray();
            if(count($wallet) < 1) {
                //
                $user = new User();
                $user->UPDATE_TIME = time();
                $user->CREATE_TIME = time();
                $user->WALLET_ID = $wallet_id;
                $user->USERNAME = $request->input('USERNAME');
                $user->PASSWORD = md5($request->input('PASSWORD'));
                $user->EMAIL = $request->input('EMAIL');
                $user->AFFILIATE_CODE = $request->input('AFF_CODE');
                $user->AFFILIATE_CODE_P = $aff_code;
                $user->save();

                $wallet = new Wallet();
                $wallet->CREATE_TIME = time();
                $wallet->UPDATE_TIME = time();
                $wallet->WALLET_ID = $wallet_id;
                $wallet->WALLET = 0;
                $wallet->save();
                break;
            }
        }
        return response()->json(array('status' => 'success'));
    }
    public function monthlyList(Request $request) {
        $user_id = $request->session()->get('id');
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
        return $this->load_view('user_affiliate/statistics', 'アフィリエイト月別通計', 'アフィリエイト', array('aff_code' => $detail->AFFILIATE_CODE, 'date_list' => $date_list));
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
        $sum_query = $sum_query->where('affiliates.USER_ID', '=', $request->session()->get('id'));
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
        $query = $query->where('affiliates.USER_ID', '=', $request->session()->get('id'));
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
            $temp_query = $temp_query->where('affiliates.USER_ID', '=', $request->session()->get('id'));
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
        $sum_query = $sum_query->where('affiliates.USER_ID', '=', $request->session()->get('id'));
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
        $query = $query->where('affiliates.USER_ID', '=', $request->session()->get('id'));
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
            $temp_query = $temp_query->where('affiliates.USER_ID', '=', $request->session()->get('id'));
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
    function getUserList(Request $request) {
        $ret = array();
        $query = User::select('users.*');
        if ($request->input("email") != "") {
            $query = $query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
        }
        if ($request->input("username") != "") {
            $query = $query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
        }
        if ($request->input("aff_code") != "") {
            $query = $query->where('users.AFFILIATE_CODE', 'like', '%'.$request->input("aff_code").'%');
        }

        $query = $query->where('users.AFFILIATE_CODE_P', '=', $request->session()->get('aff_code'));
        $query = $query->where('users.DEL_YN', '=', 'N');
        $query = $query->orderBy('users.UPDATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = User::select('users.*');

            if ($request->input("email") != "") {
                $temp_query = $temp_query->where('users.EMAIL', 'like', '%'.$request->input("email").'%');
            }
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('users.USERNAME', 'like', '%'.$request->input("username").'%');
            }
            if ($request->input("aff_code") != "") {
                $temp_query = $temp_query->where('users.AFFILIATE_CODE', 'like', '%'.$request->input("aff_code").'%');
            }
            $temp_query = $temp_query->where('users.AFFILIATE_CODE_P', '=', $request->session()->get('aff_code'));
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
        $aff_val = array(
            '1.32', '0.00', '3.19', '0.972', '0.302', '1.28', '2.15', '1.089', '0.54', '0.29', '1.18',
            '1.22', '0.00', '0.68', '0.95', '1.23'
        );
        $first_val = array(
            '0.024', '0.032', '0.0098', '0.204', '0.0053', '0.105', '0.0091', '0.017', '0.28', '1.002',
            '0.0072', '0.045', '0.0207', '0.019', '0.054'
        );
        $t_deposit = array(
            '0.2', '1.56', '1.09', '2.305', '2.24', '1.23', '1.92', '0.042', '1.08', '1.2',
            '0.59', '0.36', '0.102', '1.33', '0.532'
        );
        $t_withdraw = array(
            '0.00', '0.32', '0.09', '1.125', '0.00', '0.57', '0.00', '0.013', '0.36', '0.00',
            '0.00', '0.12', '0.00', '0.48', '0.127'
        );
        /*
        $t_trans = array(
            '1.29', '0.0578', '0.048', '0.238', '0.0032', '1.086', '0.45', '0.021', '0.03', '0.8',
            '0.38', '0.184', '0.204', '0.95', '0.202'
        );
        */
        $t_trans = array(
            '1.2305', '0.00', '0.00', '0.00', '0.00',
            '0.00', '0.00', '0.00', '0.00', '0.00',
            '0.00', '0.00', '0.00', '0.00', '0.00'
        );
        foreach($result as $item) {
            $aff_html = '';
            if($item->AFF_YN == 'N') {
                $aff_html = $item->AFFILIATE_CODE;
            }
            else {
                $aff_html = $item->AFFILIATE_CODE;
            }
            $table_item = array(
                'id' => $item->ID,
                'username' =>  $item->USERNAME,
                'email' => $item->EMAIL,
                'regdate' => $item->CREATE_TIME,
                'affiliate_code' => $aff_html,
                'total_trans_sum' => $t_trans[$item->ID % 15]
            );
            if($table_item['username'] == 'test')
                $table_item['total_trans_sum'] = '0.0824';
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
}


