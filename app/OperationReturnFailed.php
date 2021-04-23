<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationReturnFailed extends Model
{
    protected $table = 'operation_return_failed';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
