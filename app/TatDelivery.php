<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TatDelivery extends Model
{
    protected $table = 'tat_delivery';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}