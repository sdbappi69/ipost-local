<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickingTimeSlot extends Model
{
    protected $table = 'picking_time_slots';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
