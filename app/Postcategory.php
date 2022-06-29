<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postcategory extends Model
{
    public $timestamps = false;

    public function category()
    {
    	return $this->belongsTo('App\Category')->where('type','category');
    }
}
