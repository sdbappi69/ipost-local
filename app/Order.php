<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $guarded = ['id', 'created_at', 'updated_at'];

     
    public function products()
    {
        return $this->hasMany('App\OrderProduct');
    }

     public function cart_products()
    {
        return $this->hasMany('App\CartProduct');
    }

    public function suborders()
    {
        return $this->hasMany('App\SubOrder');
    }

    
    public function verified_user()
    {
        return $this->belongsTo('App\User', 'verified_by', 'id');
    }

    
    public function picker_assign()
    {
        return $this->belongsTo('App\User', 'picker_assign_by', 'id');
    }


    public function picker()
    {
        return $this->belongsTo('App\User', 'picker_id', 'id');
    }

    
    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id', 'id');
    }

    
    public function hub()
    {
        return $this->belongsTo('App\Hub', 'hub_id', 'id');
    }

    
    public function billing_zone()
    {
    	return $this->belongsTo('App\Zone', 'billing_zone_id', 'id');
    }

    
    public function delivery_zone()
    {
    	return $this->belongsTo('App\Zone', 'delivery_zone_id', 'id');
    }

    
    public function billing_city()
    {
    	return $this->belongsTo('App\City', 'billing_city_id', 'id');
    }

    
    public function delivery_city()
    {
    	return $this->belongsTo('App\City', 'delivery_city_id', 'id');
    }

    
    public function billing_state()
    {
    	return $this->belongsTo('App\State', 'billing_state_id', 'id');
    }

    
    public function delivery_state()
    {
    	return $this->belongsTo('App\State', 'delivery_state_id', 'id');
    }

    
    public function billing_country()
    {
    	return $this->belongsTo('App\Country', 'billing_country_id', 'id');
    }

    
    public function delivery_country()
    {
    	return $this->belongsTo('App\Country', 'delivery_country_id', 'id');
    }

    public function order_log()
    {
        return $this->hasMany('App\OrderLog')->orderBy('id','desc');
    }

    public function order_history()
    {
        return $this->hasMany('App\OrderHistory');
    }

    public function paymentMethod(){
        return $this->belongsTo('App\PaymentType', 'payment_type_id', 'id');
    }

}