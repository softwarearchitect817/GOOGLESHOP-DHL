<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planlog extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
		'userplan_id','domain_id'
	];
}
