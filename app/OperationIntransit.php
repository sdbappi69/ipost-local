<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationIntransit extends Model
{
    protected $table = 'operation_intransit';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
