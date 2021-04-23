<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TripMap extends Model
{
    protected $table = 'trip_map';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function start_hub()
    {
        return $this->belongsTo('App\Hub', 'start_hub_id', 'id');
    }

    public function end_hub()
    {
        return $this->belongsTo('App\Hub', 'end_hub_id', 'id');
    }

    public function hub()
    {
        return $this->belongsTo('App\Hub', 'hub_id', 'id');
    }
}
