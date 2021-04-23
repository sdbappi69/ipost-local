<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TatTrip extends Model
{
    protected $table = 'tat_trip';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
