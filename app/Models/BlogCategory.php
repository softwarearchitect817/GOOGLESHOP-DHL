<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Attribute;

class BlogCategory extends Model
{
    protected $fillable =['is_child', 'title', 'slug', 'image', 'meta_keywords', 'meta_description', 'user_id'];
    // this is blog model
    protected $table = "blog_categories";
    
    public function blog_subcategories(){
        return $this->hasMany(BlogCategory::class, 'is_child', 'id');
    }
    
    public function category_blogs(){
        return $this->hasMany(Blog::class, 'category_id', 'id');
    }
    
    public function subcategory_blogs(){
        return $this->hasMany(Blog::class, 'subcategory_id', 'id');
    }
    
}

