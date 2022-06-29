<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
class Stock extends Model
{
   public $timestamps = false;

   public function attribute()
   {
   	return $this->hasOne('App\Attribute','id','attribute_id');
   }
   
   public function attributes()
   {
   	return $this->hasOne('App\Attribute','id','attribute_id')->where('user_id',Auth::id())->with('product');
   }

   public function term()
   {
   	return $this->belongsTo('App\Term')->with('preview');
   }
}
