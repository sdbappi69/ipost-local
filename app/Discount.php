<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
	protected $table = 'discounts';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function product_category()
	{
		return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
	}

	public function store()
	{
		return $this->belongsTo('App\Store', 'store_id', 'id');
	}
}
