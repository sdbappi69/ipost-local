<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountLog extends Model
{
    protected $table = 'discount_logs';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo('App\OrderProduct','product_unique_id');
    }

    public function discount()
    {
        return $this->belongsTo('App\Discount','discount_id');
    }

}
