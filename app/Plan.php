<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    
    public function users()
    {
    	return $this->hasMany('App\Subscriber');
    }

    public function active_users()
    {
    	return $this->hasMany('App\Models\Userplan')->where('status',1);
    }
   
}
