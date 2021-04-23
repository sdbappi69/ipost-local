<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashCollection extends Model
{
    protected $table = 'cash_collections';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sub_order()
    {
        return $this->belongsTo('App\SubOrder', 'sub_order_id', 'id');
    }
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }
    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id', 'id');
    }
    public function hub()
    {
        return $this->belongsTo('App\Hub', 'hub_id', 'id');
    }
    public function merchant()
    {
        return $this->belongsTo('App\Merchant', 'merchant_id', 'id');
    }
    public function productDetails(){
        return $this->belongsTo('App\OrderProduct','sub_order_id','sub_order_id');
    }

}
