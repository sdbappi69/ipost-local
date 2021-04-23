<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickingTask extends Model
{
   protected $table = 'picking_task';

   protected $fillable  =  [
      'picker_id',
      'product_unique_id',
      'start_time',
      'end_time',
      'start_lat',
      'start_long',
      'end_lat',
      'end_long',
      'signature',
      'image',
      'status'
   ];

    public function product()
    {
      return $this->belongsTo('App\OrderProduct', 'product_unique_id', 'product_unique_id');
    }

    public function return_product()
    {
      return $this->belongsTo('App\ReturnedProduct', 'product_unique_id', 'product_unique_id');
    }

    public function reason()
    {
      return $this->belongsTo('App\Reason', 'reason_id', 'id');
    }

    public function consignment()
    {
      return $this->belongsTo('App\Consignment', 'consignment_id', 'id');
    }

    public function picker()
    {
      return $this->belongsTo('App\User', 'picker_id', 'id');
    }
}
