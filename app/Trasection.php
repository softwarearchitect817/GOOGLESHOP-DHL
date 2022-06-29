<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trasection extends Model
{
    public function method()
    {
    	return $this->belongsTo('App\Category','category_id','id')->select('id','name');
    }
}
