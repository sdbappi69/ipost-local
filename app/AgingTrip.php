<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgingTrip extends Model
{
    protected $table = 'aging_trip';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
