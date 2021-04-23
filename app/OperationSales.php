<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationSales extends Model
{
    protected $table = 'operation_sales';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
