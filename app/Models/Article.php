<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
      protected $fillable = ['category_id', 'subcategory_id', 'title', 'slug', 'description', 'tags', 'is_comment', 'user_id', 'views', 'image'];
      use HasFactory;
      public function category()
      {
            return $this->belongsTo(BlogCategory::class, 'category_id', 'id');
      }

      public function subcategory()
      {
            return $this->belongsTo(BlogCategory::class, 'subcategory_id', 'id');
      }

      public function comments()
      {
            return $this->hasMany(ArticleComment::class, 'article_id', 'id');
      }
      public function user()
      {
            return $this->belongsTo(User::class);
      }
}
