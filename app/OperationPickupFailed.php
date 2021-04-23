<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationPickupFailed extends Model
{
    protected $table = 'operation_pickup_failed';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
