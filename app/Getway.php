<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Getway extends Model
{
    public function method()
    {
    	return $this->belongsTo('App\Category','category_id');
    }
}
