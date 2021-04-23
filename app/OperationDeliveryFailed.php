<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationDeliveryFailed extends Model
{
    protected $table = 'operation_delivery_failed';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
