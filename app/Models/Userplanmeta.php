<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userplanmeta extends Model
{
    use HasFactory;
    public $timestamps = false;

     public function activeorder(){
        return $this->hasOne('App\Models\Userplan','user_id','user_id')->where('status',1);
    }

    //

    
}
