<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    public function category(){
        return $this->belongsTo(BlogCategory::class, 'category_id', 'id');
    }
    
    public function subcategory(){
        return $this->belongsTo(BlogCategory::class, 'subcategory_id', 'id');
    }
}
