<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeModel extends Model
{
    protected $table = 'charge_models';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
