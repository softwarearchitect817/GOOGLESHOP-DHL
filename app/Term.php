<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Term extends Model
{
    use HasTranslations;
    public $translatable = ['title'];
  public function meta()
	{
		return 	$this->hasOne('App\Meta','term_id','id');
	}



	public function categories()
    {
        return $this->belongsToMany('App\Category','postcategories','term_id','category_id')->where('type','category');
    }
    public function brands()
    {
        return $this->belongsToMany('App\Category','postcategories','term_id','category_id')->where('type','brand');
    }

    public function medias()
    {
        return $this->belongsToMany('App\Media','postmedia','term_id','media_id');
    }

    public function product_categories()
    {
         return $this->hasMany('App\Postcategory','term_id')->where('type','product_category')->with('category')->select('id','term_id','category_id','type');
    }

    public function post_categories()
    {
        return $this->hasMany('App\Postcategory');
    }

    public function product_brand()
    {
         return $this->hasOne('App\Postcategory','term_id')->where('type','brand')->with('category')->select('id','term_id','category_id','type');
    }

    public function postcategory()
    {
        return $this->hasMany('App\Postcategory','term_id');
    }
     
    
    public function Productcategory()
    {
        return $this->hasMany('App\Postcategory','term_id')->where('type','product_category');
    }

    public function category()
    {
        return $this->hasOne('App\Postcategory')->whereHas('category')->with('category');
    }

    public function Brand()
    {
        return $this->hasOne('App\Postcategory','term_id')->where('type','brand');
    }

  	

	public function user()
	{
		return $this->belongsTo('App\User')->select('name','id');
	}

	
	
	public function seo()
	{
		return $this->hasOne('App\Meta','term_id')->where('key','seo');
	}

    public function affiliate()
    {
        return $this->hasOne('App\Meta','term_id')->where('key','affiliate');
    }



	public function content()
	{
		return $this->hasOne('App\Meta','term_id')->where('key','content');
	}
    
    public function excerpt()
    {
        return $this->hasOne('App\Meta','term_id')->where('key','excerpt');
    }

	
	

	public function attributes()
	{
		return $this->hasMany('App\Attribute','term_id')->with('attribute','variation');
	}
	public function attribute()
	{
		return $this->hasOne('App\Attribute','term_id')->with('attribute','variation');
	}

    public function attributes_relation()
    {
        return $this->hasMany('App\Attribute','term_id');
    }

    public function files()
    {
        return $this->hasMany('App\File','term_id');
    }


    public function preview()
    {
        return $this->hasOne('App\Postmedia')->with('media');
    }

    public function order()
    {
        return $this->hasMany('App\Orderitem');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }
    
    public function price()
    {
        return $this->hasOne('App\Models\Price');
    }
    public function stock()
    {
        return $this->hasOne('App\Stock');
    }
    
    public function options()
    {
        return $this->hasMany('App\Models\Termoption')->where('type',1)->with('childrenCategories');
    }
    public function termoption()
    {
        return $this->hasMany('App\Models\Termoption')->where('type',0);
    }
	

}