<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubOrderTripMap extends Model
{
    protected $table = 'sub_order_trip_map';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function trip_map()
    {
        return $this->belongsTo('App\TripMap', 'trip_map_id', 'id');
    }

    public function sub_order()
    {
        return $this->belongsTo('App\SubOrder', 'sub_order_id', 'id');
    }
}
