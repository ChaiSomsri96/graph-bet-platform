<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    //
    protected $primaryKey = 'ID';
    protected $table = 'faqs';

    public function category(){
    	return $this->hasOne('App\FaqCategory', 'ID', 'TYPE');
    }
}
