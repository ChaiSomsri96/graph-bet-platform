<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    //
    protected $primaryKey = 'ID';
    protected $table = 'affiliates';
    public function user(){
    	return $this->hasOne('App\User', 'ID', 'AFF_USER_ID');
    }
}
