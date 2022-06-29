<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function customer()
    {
    	return $this->hasOne('App\Models\Customer','id','customer_id');
    }

    public function order_items()
    {
    	return $this->hasMany('App\Orderitem');
    }
    public function getway()
    {
    	return $this->belongsTo('App\Category','category_id','id');
    }

    public function order_item()
    {
    	return $this->hasMany('App\Orderitem')->with('term');
    }
    public function order_item_with_file()
    {
    	return $this->hasMany('App\Orderitem')->with('term','file');
    }

    public function order_item_with_stock()
    {
        return $this->hasMany('App\Orderitem')->with('stock');
    }

    public function files()
    {
        return $this->hasMany('App\Orderitem')->with('file');
    }

    public function order_content()
    {
    	return $this->hasOne('App\Ordermeta')->where('key','content');
    }

    public function shipping_info()
    {
    	return $this->hasOne('App\Ordershipping')->with('city','shipping_method');
    }

    public function payment_method()
    {
        return $this->belongsTo('App\Trasection','trasection_id')->with('method');
    }




}
