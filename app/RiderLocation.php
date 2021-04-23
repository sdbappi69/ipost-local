<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiderLocation extends Model
{
    protected $table = 'rider_locations';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
