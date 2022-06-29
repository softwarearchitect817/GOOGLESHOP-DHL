<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Attribute;

class BlogSetting extends Model
{
    protected $fillable =[ 'user_id', 'article_per_page', 'comments'];
    // this is blogsetting model
  
}