<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orderitem extends Model
{
     public $timestamps = false;

     public function term()
     {
     	return $this->hasOne('App\Term','id','term_id');
     }

     public function attribute()
     {
     	return $this->hasOne('App\Attribute','id','attribute_id')->with('attribute','variation');
     }

     public function file()
     {
     	return $this->hasMany('App\File','term_id','term_id');
     }

     public function stock(){
          return $this->hasOne('App\Stock','term_id','term_id')->where('stock_manage',1);
     }
}
