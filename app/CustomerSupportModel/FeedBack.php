<?php

namespace App\CustomerSupportModel;

use App\CustomerSupportModel\Reaction;
use App\CustomerSupportModel\UniqueHead;
use App\SubOrder;
use App\User;
use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    //
	protected $table = 'feedbacks';
	public function sub_order(){
		return $this->belongsTo(SubOrder::class,'suborder_id','id');
	}
	public function riderDetails(){
		return $this->belongsTo(User::class,'rider','id');
	}
	public function updatedBy(){
		return $this->belongsTo(User::class,'updated_by','id');
	}
	public function reactionDetails(){
		return $this->belongsTo(Reaction::class,'reaction','id');
	}
	public function uniqueHead(){
		return $this->belongsTo(UniqueHead::class,'unique_head','id');
	}
	public function getOrderCreatedAtAttribute($value){
		return (!is_null($value) ? date("F jS, Y h:i a", strtotime($value)) : null);
	}
	public function getDeliverdDateAttribute($value){
		return (!is_null($value) ? date("F jS, Y h:i a", strtotime($value)) : null);
	}
	public function getCreatedAtAttribute($value){
		return (!is_null($value) ? date("F jS, Y h:i a", strtotime($value)) : null);
	}
	public function getCallDateAttribute($value){
		return (!is_null($value) ? date("F jS, Y h:i a", strtotime($value)) : null);
	}
	public function getStatusAttribute($value)
	{
		$status = null;
		if($value == 0){
			$status = 'Pending';
		}
		else if($value == 1){
			$status = 'Collected';
		}
		else{
			$status = '';
		}
		return $status;
	}
}
