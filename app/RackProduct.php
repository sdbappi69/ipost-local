<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RackProduct extends Model
{
    protected $table = 'rack_products';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function rack()
    {
    	return $this->belongsTo('App\Rack', 'rack_id', 'id');
    }

    public function product()
    {
    	return $this->belongsTo('App\OrderProduct', 'product_id', 'id');
    }
}
