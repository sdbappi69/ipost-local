<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantAccumulatedCash extends Model
{
    protected $table = 'merchant_accumulated_cash';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
