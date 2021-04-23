<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
	protected $table = 'order_logs';

	protected $guarded = ['id', 'created_at', 'updated_at'];


	public function sub_order()
	{
		return $this->belongsTo('App\SubOrder','sub_order_id');
	}
	public function product()
	{
		return $this->belongsTo('App\OrderProduct','product_id');
	}
    public function user()
	{
		return $this->belongsTo('App\User','user_id','id');
	}
	
}


