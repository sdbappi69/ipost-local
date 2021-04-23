<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgingDelivery extends Model
{
    protected $table = 'aging_delivery';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
