<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTrip extends Model
{
    protected $table = 'product_trip';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo('App\OrderProduct', 'product_id', 'id');
    }

    public function trip()
    {
        return $this->belongsTo('App\Trip', 'trip_id', 'id');
    }
}
