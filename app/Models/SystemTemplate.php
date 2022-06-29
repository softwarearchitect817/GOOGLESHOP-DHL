<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemTemplate extends Model
{
    protected $fillable =[ 'user_id', 'description', 'title', 'template_for'];
    // this is systemtemplate model
  
}