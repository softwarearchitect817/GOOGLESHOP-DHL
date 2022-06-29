<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $table = 'abandoned_carts';
    
    public function customer()
    {
    	return $this->belongsTo('App\Models\Customer','customer_id','id');
    }
}