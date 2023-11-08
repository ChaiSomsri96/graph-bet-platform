<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    //
    protected $primaryKey = 'ID';
    protected $table = 'wallet_history';
    public function fuser() {
    	return $this->hasOne('App\User', 'WALLET_ID', 'FROM_WID');
    }
    public function tuser() {
        return $this->hasOne('App\User', 'WALLET_ID', 'TO_WID');
    }
}
