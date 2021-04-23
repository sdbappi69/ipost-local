<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $table = 'order_histories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }

        
    public function delivery_zone()
    {
    	return $this->belongsTo('App\Zone', 'delivery_zone_id', 'id');
    }

    
        
    public function delivery_city()
    {
    	return $this->belongsTo('App\City', 'delivery_city_id', 'id');
    }

    
        
    public function delivery_state()
    {
    	return $this->belongsTo('App\State', 'delivery_state_id', 'id');
    }

    
        
    public function delivery_country()
    {
    	return $this->belongsTo('App\Country', 'delivery_country_id', 'id');
    }

}