<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bot;
use App\User;
class BotController extends Controller
{
    function index() {
        return $this->load_view('bots/index', 'Bot管理', '', array());
    }
    function change(Request $request) {
        $type = $request->input('type');
        $id = $request->input('id');
        if($type == 1) {
            Bot::where("id", $id)
            ->update(array("DEL_YN" => 'Y'));
        }
        else if($type == 2) {
            Bot::where("id", $id)
            ->update(array("STATUS" => 1));
        }
        else {
            Bot::where("id", $id)
            ->update(array("STATUS" => 2));
        }
        return response()->json(array('status' => 'success'));
    }
    function getList(Request $request) {
        $ret = array();
        $query = Bot::select('game_bots.*');
        $query = $query->where('game_bots.DEL_YN', '=', 'N');
        if ($request->input("username") != "") {
            $query = $query->where('game_bots.NAME', 'like', '%'.$request->input("username").'%');
        }
        $query = $query->orderBy('game_bots.UPDATE_TIME', 'desc');
        $is_tail_data = false;
        if (null === $request->input('type')) {
            $query = $query->offset($request->input("id"))
                ->limit(10);
            $temp_query = Bot::select('game_bots.*');
            $temp_query = $temp_query->where('game_bots.DEL_YN', '=', 'N');
            if ($request->input("username") != "") {
                $temp_query = $temp_query->where('game_bots.NAME', 'like', '%'.$request->input("username").'%');
            }
            $temp_query = $temp_query->orderBy('game_bots.UPDATE_TIME', 'desc');
            $temp_query = $temp_query->offset( intval($request->input("id")) + 10 )->limit(10);
            $temp_result = $temp_query->get();
            if(count($temp_result) < 1)
                $is_tail_data = false;
            else
                $is_tail_data = true;
        }
        $result = $query->get();
        foreach ($result as $item) {
            $status_html = '';
            if($item->STATUS == 1) {
                $status_html = '<span class="badge badge-success">活性化</span>';
            }
            else if($item->STATUS == 2) {
                $status_html = '<span class="badge badge-default">無効化</span>';
            }
            $table_item = array(
                'id' => $item->ID,
                'name' => $item->NAME,
                'win_rate' => $item->WIN_RATE,
                'take_rate' => $item->TAKE_RATE,
                'max_bet' => ($item->MAX_BET / pow(10, 6)),
                'min_bet' => ($item->MIN_BET / pow(10, 6)),
                'status_html' => $status_html,
                'status' => $item->STATUS,
                'regdate' => $item->CREATE_TIME
            );
            $ret[] = $table_item;
        }
        return response()->json(array('data'=> $ret, 'status' => 'success', 'is_tail_data' => $is_tail_data));
    }
    function create(Request $request) {
        // game_bots-----
        $users = User::where(array('DEL_YN' => 'N' , 'USERNAME' => $request->input('NAME')))->get()->toArray();
        if(count($users) > 0) {
            return response()->json(array('status' => 'fail', 'error_msg' => '贈服された名前です。 ボット名を変えてください。'));
        }
        $id = $request->input("ID");
        if (!($id == "" || !isset($id))) {
            //$bots = Bot::where(array('DEL_YN' => 'N' , 'NAME' => $request->input('NAME')))->get()->toArray();
            $bots = Bot::where('DEL_YN', '=', 'N')
                    ->where('NAME', '=', $request->input('NAME'))
                    ->where('ID', '<>', $id)
                    ->get()->toArray();
            if(count($bots) > 0) {
                return response()->json(array('status' => 'fail', 'error_msg' => '贈服された名前です。 ボット名を変えてください。'));
            }
            $bot = Bot::where('id', $id)->update(array(
                'NAME' => $request->input('NAME'),
                'MIN_BET' => intval($request->input('MIN_BET') * pow(10, 6)),
                'MAX_BET' => intval($request->input('MAX_BET') * pow(10, 6)),
                'WIN_RATE' => $request->input('WIN_RATE'),
                'TAKE_RATE' => $request->input('TAKE_RATE'),
                'UPDATE_TIME' => time()
            ));
        }
        else {
            $bots = Bot::where(array('DEL_YN' => 'N' , 'NAME' => $request->input('NAME')))->get()->toArray();
            if(count($bots) > 0) {
                return response()->json(array('status' => 'fail', 'error_msg' => '贈服された名前です。 ボット名を変えてください。'));
            }
            $bot = new Bot();
            $bot->UPDATE_TIME = time();
            $bot->CREATE_TIME = time();
            $bot->NAME = $request->input('NAME');
            $bot->WIN_RATE = $request->input('WIN_RATE');
            $bot->TAKE_RATE = $request->input('TAKE_RATE');
            $bot->MAX_BET = intval($request->input('MAX_BET') * pow(10, 6));
            $bot->MIN_BET = intval($request->input('MIN_BET') * pow(10, 6));
            $bot->STATUS = 1;
            $bot->save();
        }
        return response()->json(array('status' => 'success'));
    }
}


