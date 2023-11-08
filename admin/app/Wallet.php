<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    //
    protected $primaryKey = 'ID';
    protected $table = 'user_wallets';
}
