<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Attribute;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;
    
    public $translatable = ['name'];
    
	public function posts()
	{
		return $this->hasMany('App\Postcategory');
	}

	public function categories()
	{
		return $this->hasMany(Category::class,'p_id','id');
	}

	public function parent()
	{
		return $this->hasOne(Category::class,'id','p_id');
	}

	public function childrenCategories()
	{
		return $this->hasMany(Category::class,'p_id','id')->with('categories');
	}

	public function featured_child_attribute()
	{
		return $this->hasOne(Category::class,'p_id','id')->where('featured',1);
	}

	public function featured_child_with_post_count_attribute()
	{
		return $this->hasMany(Category::class,'p_id','id')->where('featured',1)->withCount('variations');
	}

	public function variations(){
		return $this->hasMany('App\Attribute','variation_id','id');
	}

	public function parent_variation(){
		return $this->hasMany('App\Attribute','category_id','id');
	}


	public function parent_relation()
	{
		return $this->hasMany(Categoryrelation::class,'category_id','id');
	}
	public function child_relation()
	{
		 return $this->belongsToMany(Category::class,Categoryrelation::class,'relation_id');
		
	}

	public function gateway_users()
	{
		return $this->hasMany('App\Getway');
	}

	public function preview()
	{
		return $this->hasOne('App\Categorymeta')->where('type','preview')->select('category_id','type','content');
	}
	public function description()
	{
		return $this->hasOne('App\Categorymeta')->where('type','description')->select('category_id','type','content');
	}

	public function credentials()
	{
		return $this->hasOne('App\Categorymeta')->where('type','credentials')->select('category_id','type','content');
	}

	public function excerpt()
	{
		return $this->hasOne('App\Categorymeta')->where('type','excerpt')->select('category_id','type','content');
	}


	public function active_getway()
	{
		return $this->hasOne('App\Getway')->where('user_id',seller_id());
	}

	public function attributes()
	{
		return $this->hasMany('App\Attribute');
	}

	public function take_20_product()
	{
		return $this->belongsToMany('App\Term','postcategories')->with('preview','attributes')->take(15);
	}
}