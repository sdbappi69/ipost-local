<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmTask extends Model
{
	protected $table = 'tm_tasks';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function sub_order()
	{
		return $this->belongsTo('App\SubOrder', 'sub_order_id', 'id');
	}

	public function hub()
	{
		return $this->belongsTo('App\Hub', 'hub_id', 'id');
	}
}
