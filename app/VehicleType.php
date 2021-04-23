<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $table = 'vehicle_types';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function boot()
    {
        $class = get_called_class();
        $class::observe(new Observer());

        parent::boot();
    }

}
