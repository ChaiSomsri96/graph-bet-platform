<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GameHistory;
use App\GameLog;
class GameHistoryController extends Controller
{
    function index() {
        return $this->load_view('game_history/index', 'ゲーム履歴', '', array());
    }
    function getList(Request $request) {
        $ret = array();
        $query = GameHistory::select('game_total.*');
        if($request->input("startDate") != "") {
            $start_timestamp = strtotime($request->input("startDate"));
            $query = $query->where('game_total.BUST_TIME', '>=', $start_timestamp);
        }
        if($request->input("endDate") != "") {
            $end_timestamp = strtotime($request->input("endDate")) + 86399;
            $query = $query->where('game_total.BUST_TIME', '<=', $end_timestamp);
        }
        if($request->input("gameid") != "") {
            $query = $query->where('game_total.GAME_ID', '=', $request->input("gameid"));
        }
        $query = $query->where('game_total.DEL_YN', '=', 'N');
        $query = $query->where('game_total.STATE', '=', 'BUSTED');
        $query = $query->orderBy('game_total.GAME_ID', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = GameHistory::select('game_total.*');
            /* ***** */
            if($request->input("startDate") != "") {
                $start_timestamp = strtotime($request->input("startDate"));
                $temp_query = $temp_query->where('game_total.BUST_TIME', '>=', $start_timestamp);
            }
            if($request->input("endDate") != "") {
                $end_timestamp = strtotime($request->input("endDate")) + 86399;
                $temp_query = $temp_query->where('game_total.BUST_TIME', '<=', $end_timestamp);
            }
            if($request->input("gameid") != "") {
                $temp_query = $temp_query->where('game_total.GAME_ID', '=', $request->input("gameid"));
            }
            /* ***** */
            $temp_query = $temp_query->where('game_total.DEL_YN', '=', 'N');
            $temp_query = $temp_query->orderBy('game_total.GAME_ID', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            if(doubleval($item->BUST) / 100 < 2)
                $html_bust = '<span style="color: #dc3545;">'.number_format(doubleval($item->BUST) / 100 , 2 , '.' , '').'x</span>';
            else {
                $html_bust = '<span style="color: green;">'.number_format(doubleval($item->BUST) / 100 , 2 , '.' , '').'x</span>';
            }
            $table_item = array(
                'id' => $item->ID,
                'game_id' => $item->GAME_ID,
                'play_time' => $item->BUST_TIME,
                'bust' => $html_bust,
                'wagered' => doubleval($item->TOTAL_REAL / pow(10, 6)),
                'profit' => doubleval($item->PROFIT / pow(10, 6)),
                'users' => $item->USERS,
                'bots' => $item->BOTS
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
    public function postDetail(Request $request) {
        $gameId = $request->input('game_id');
        $ret = array();
        $query = GameLog::select('game_log.*')
        ->leftJoin('users', 'users.ID', '=', 'game_log.USER_ID');
        $query = $query->where('game_log.GAME_ID', '=', $gameId);
        $query = $query->where('game_log.BOT_YN', '=', 'N');
        $query = $query->orderBy('game_log.CRASHED_YN', 'asc');
        $query = $query->orderBy('game_log.CASHOUT', 'asc');
        $query = $query->orderBy('game_log.BET', 'asc');
        $result = $query->get();
        foreach($result as $item) {
            $profit_html = number_format(doubleval($item->PROFIT / pow(10, 8)), 8, '.', '').' btc';
            $cashout_html = doubleval($item->CASHOUT / pow(10, 2)).'x';
            if($item->CRASHED_YN == 'Y') {
                $profit_html = '<span style="color: #dc3545;">'.$profit_html.'</span>';
                $cashout_html = '<span style="color: #dc3545;">Lose</span>';
            }
            else{
                $profit_html = '<span style="color: green;">'.$profit_html.'</span>';
                $cashout_html = '<span style="color: green;">'.$cashout_html.'</span>';
            }
            $table_item = array(
                'username' => $item->user->USERNAME,
                'time' => $item->CREATE_TIME,
                'wagered' => number_format( doubleval($item->BET / pow(10, 8)) , 8 , '.' , ''),
                'cashout' => $cashout_html,
                'profit' => $profit_html
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success'));
    }
}


