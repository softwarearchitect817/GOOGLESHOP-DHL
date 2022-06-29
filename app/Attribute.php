<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;
	public $timestamps = false;
    public $translatable = ['name'];
	public function price()
	{
		return	$this->hasOne('App\Attributeprice');
	}

	public function stock()
	{
		return	$this->hasOne('App\Stock');
	}

	public function product()
	{
		return $this->belongsTo('App\Term','term_id','id')->select('id','title')->with('preview');
	}

	public function term()
	{
		return $this->belongsTo('App\Term','term_id','id')->with('preview');
	}

	public function attribute()
	{
		return $this->belongsTo('App\Category','category_id','id')->select('id','name');
	}

	public function variation()
	{
		return $this->belongsTo('App\Category','variation_id','id')->select('id','name');
	}

	public function files(){
		return $this->hasOne('App\File','attribute_id','id'); 
	}
}