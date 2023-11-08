<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class GameLog extends Model
{
    //
    protected $primaryKey = 'ID';
    protected $table = 'game_log';
    public function user() {
    	return $this->hasOne('App\User', 'ID', 'USER_ID');
    }
    public function game() {
        return $this->hasOne('App\GameHistory', 'GAME_ID', 'GAME_ID');
    }
}
