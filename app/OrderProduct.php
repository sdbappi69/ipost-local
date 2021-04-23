<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';

    protected $guarded = ['id', 'created_at', 'updated_at'];

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

    /**
    * An product belongs to a pickup_location
    */
    public function pickup_location()
    {
    	return $this->belongsTo('App\PickingLocations', 'pickup_location_id', 'id');
    }

    /**
    * An product belongs to a picking_time_slot
    */
    public function picking_time_slot()
    {
    	return $this->belongsTo('App\PickingTimeSlot', 'picking_time_slot_id', 'id');
    }

    public function picker()
    {
        return $this->belongsTo('App\User', 'picker_id', 'id');
    }

    // new 
    public function pTask(){
       return $this->belongsTo('App\PickingTask','product_unique_id','product_unique_id');
    }

    public function discount_log()
    {
        return $this->belongsTo('App\DiscountLog', 'product_unique_id', 'product_unique_id');
    }
     
}
