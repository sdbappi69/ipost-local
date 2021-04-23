<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsignmentCommon extends Model
{
    protected $table = 'consignments_common';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function rider()
    {
        return $this->belongsTo('App\User','rider_id','id');
    }

    public function hub()
    {
        return $this->belongsTo('App\Hub','hub_id','id');
    }

    public function task()
    {
        return $this->hasMany(ConsignmentTask::class, 'consignment_id', 'id');
    }

    public function route()
    {
        return $this->hasMany('App\RiderLocation', 'consignment_unique_id', 'consignment_unique_id');
    }

}
