<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requestdomain extends Model
{
    use HasFactory;

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function parentdomain()
    {
    	return $this->belongsTo('App\Domain','domain_id');
    }
}
