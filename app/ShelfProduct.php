<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShelfProduct extends Model
{
    protected $table = 'shelf_products';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function shelf()
    {
    	return $this->belongsTo('App\Shelf', 'shelf_id', 'id');
    }

    public function product()
    {
    	return $this->belongsTo('App\OrderProduct', 'product_id', 'id');
    }
}
