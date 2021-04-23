<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function boot()
    {
        $class = get_called_class();
        $class::observe(new Observer());

        parent::boot();
    }

    public function type()
    {
        return $this->belongsTo('App\VehicleType', 'vehicle_type_id', 'id');
    }

    public function driver()
    {
        return $this->belongsToMany('App\Driver');
    }

}
