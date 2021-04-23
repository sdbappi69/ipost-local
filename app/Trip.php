<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'trips';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function vehicle_type()
    {
        return $this->belongsTo('App\VehicleType', 'vehicle_type_id', 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle', 'vehicle_id', 'id');
    }

    public function source_hub()
    {
    	return $this->belongsTo('App\Hub', 'source_hub_id', 'id');
    }

    public function destination_hub()
    {
    	return $this->belongsTo('App\Hub', 'destination_hub_id', 'id');
    }

    public function responsible_user()
    {
        return $this->belongsTo('App\User', 'responsible_user_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo('App\User', 'driver_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\ProductTrip');
    }

    public function suborders()
    {
        return $this->hasMany('App\SuborderTrip', 'trip_id', 'id');
    }
}
