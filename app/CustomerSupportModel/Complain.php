<?php

namespace App\CustomerSupportModel;

use Illuminate\Database\Eloquent\Model;
use App\CustomerSupportModel\SourceOfInformation;
use App\CustomerSupportModel\Query;
use App\SubOrder;
use App\User;
class Complain extends Model
{

    //
	public function sourceOfInformation(){
		return $this->belongsTo(SourceOfInformation::class,'source_of_information','id');
	}
	public function queryDetails(){
		return $this->belongsTo(Query::class,'query_id','id');
	}
	public function sub_order(){
		return $this->belongsTo(SubOrder::class,'sub_order_id','id');
	}
	public function createdBy(){
		return $this->belongsTo(User::class,'created_by','id');
	}
	public function getCreatedAtAttribute($value)
	{
		return (!is_null($value) ? date("F jS, Y h:i a", strtotime($value)) : null);
	}
	public function getStatusAttribute($value)
	{
		$status = null;
		if($value == 0){
			$status = 'Unsolved';
		}
		else if($value == 1){
			$status = 'In process';
		}
		else if($value == 2){
			$status = 'Solved';
		}
		else{
			$status = '';
		}
		return $status;
	}
}
