<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgingPickup extends Model
{
    protected $table = 'aging_pickup';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
