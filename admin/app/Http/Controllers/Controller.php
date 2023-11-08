<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Setting;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function load_view($path , $pagetitle = '', $parenttitle = '', $data = array()) {
        // get admin wallet
        $query = DB::table('admins')
        ->select('admins.WALLET_ID as WALLET_ID')
        ->where('admins.ID', 1);
        $admin_wallet_id = $query->get()->toArray();
        $query = DB::table('user_wallets')
        ->select('user_wallets.WALLET as WALLET')
        ->where('user_wallets.WALLET_ID', $admin_wallet_id[0]->WALLET_ID);
        $admin_wallet = $query->get()->toArray();
        $profit = number_format(doubleval($admin_wallet[0]->WALLET) / pow(10, 6), 8, '.', '');
        // get bets
        $query = DB::table('game_log')
        ->select(DB::raw('IFNULL(COUNT(game_log.ID),0) AS bets'))
        ->where('game_log.DEL_YN', 'N')
        ->where('game_log.BOT_YN', 'N')
        ->get()->toArray();
        $bets = $query[0]->bets;
        $is_child = true;
        if($parenttitle == '')
            $is_child = false;
        $setting = array();
        $ret = Setting::where(array('del_yn' => 'N'))->get()->toArray();
        foreach($ret as $item) {
            $setting[$item['VARIABLE']] = $item['VALUE'];
        }
        return view($path, array('bets' => $bets, 'profit' => $profit, 'is_child' => $is_child, 'page_title' => $pagetitle, 'parent_title' => $parenttitle, 'setting' => $setting, 'data'=>$data));
    }
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function __construct() {
        $this->initialize();
    }
    protected function initialize() {
    }
}
