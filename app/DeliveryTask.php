<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryTask extends Model
{
   protected $table = 'delivery_task';

   protected $fillable  =  [
      'deliveryman_id',
      'unique_suborder_id',
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

   public function suborder()
   {
      return $this->belongsTo('App\SubOrder', 'unique_suborder_id', 'unique_suborder_id');
   }

   public function reason(){
      return $this->belongsTo('App\Reason', 'reason_id', 'id');
   }

   public function consignment()
   {
      return $this->belongsTo('App\Consignment', 'consignment_id', 'id');
   }

   public function deliveryman()
    {
      return $this->belongsTo('App\User', 'deliveryman_id', 'id');
    }

}
