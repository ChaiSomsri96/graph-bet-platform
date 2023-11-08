<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitcoin extends Model
{
    //
    protected $primaryKey = 'ID';
    protected $table = 'deposit_withdraw_log';
    public function user(){
    	return $this->hasOne('App\User', 'WALLET_ID', 'WALLET_ID');
    }
}
