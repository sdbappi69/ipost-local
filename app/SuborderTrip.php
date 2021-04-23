<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuborderTrip extends Model
{
    protected $table = 'suborder_trip';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sub_order()
    {
        return $this->belongsTo('App\SubOrder', 'sub_order_id', 'id');
    }

    public function trip()
    {
        return $this->belongsTo('App\Trip', 'trip_id', 'id');
    }
}
