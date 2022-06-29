<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorymedia extends Model
{
    public $timestamps = false;
    public function media()
    {
    	return $this->belongsTo('App\Media')->select('id','url');
    }
}
