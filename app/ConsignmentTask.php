<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsignmentTask extends Model
{
   protected $table = 'consignments_tasks';

   protected $guarded  =  ['id'];

   public function suborder()
   {
      return $this->belongsTo('App\SubOrder', 'sub_order_id', 'id');
   }

   public function reason(){
      return $this->belongsTo('App\Reason', 'reason_id', 'id');
   }

   public function consignment()
   {
      return $this->belongsTo('App\ConsignmentCommon', 'consignment_id', 'id');
   }

   public function deliveryman()
    {
      return $this->belongsTo('App\User', 'deliveryman_id', 'id');
    }

}
