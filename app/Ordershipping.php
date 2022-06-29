<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ordershipping extends Model
{
     public $timestamps = false;

     public function city()
     {
     	return $this->hasOne('App\Category','id','location_id');
     }

     public function shipping_method()
     {
     	return $this->hasOne('App\Category','id','shipping_id');
     }
}
