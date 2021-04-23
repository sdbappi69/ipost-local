<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnedProduct extends Model
{
    protected $table = 'returned_products';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo('App\OrderProduct', 'order_product_id', 'id');
    }

    /**
    * An product belongs to a order
    */
    public function order()
    {
    	return $this->belongsTo('App\Order', 'order_id', 'id');
    }

    /**
    * An product belongs to a product_category
    */
    public function product_category()
    {
    	return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }

    /**
    * An product belongs to a sub_order
    */
    public function sub_order()
    {
    	return $this->belongsTo('App\SubOrder', 'sub_order_id', 'id');
    }

    public function source_hub()
    {
        return $this->belongsTo('App\Hub', 'source_hub_id', 'id');
    }

    public function next_hub()
    {
        return $this->belongsTo('App\Hub', 'next_hub_id', 'id');
    }

    public function delivery_hub()
    {
        return $this->belongsTo('App\Hub', 'source_hub_id', 'id');
    }
}
