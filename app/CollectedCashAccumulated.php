<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectedCashAccumulated extends Model
{
    protected $table = 'collected_cash_accumulated';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
