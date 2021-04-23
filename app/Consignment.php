<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
{
    protected $table = 'consignments';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function rider()
	{
		return $this->belongsTo('App\User','rider_id','id');
	}

	public function picking()
    {
        return $this->hasMany('App\PickingTask', 'consignment_id', 'id');
    }

    public function delivery()
    {
        return $this->hasMany('App\DeliveryTask', 'consignment_id', 'id');
    }

    public function route()
    {
        return $this->hasMany('App\RiderLocation', 'consignment_unique_id', 'consignment_unique_id');
    }

}
