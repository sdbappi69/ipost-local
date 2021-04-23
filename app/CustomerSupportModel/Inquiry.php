<?php

namespace App\CustomerSupportModel;

use Illuminate\Database\Eloquent\Model;
use App\CustomerSupportModel\SourceOfInformation;
use App\CustomerSupportModel\Query;
use App\CustomerSupportModel\InquiryStatus;
use App\User;
class Inquiry extends Model
{

    //
	public function sourceOfInformation(){
		return $this->belongsTo(SourceOfInformation::class,'source_of_information','id');
	}
	public function queryDetails(){
		return $this->belongsTo(Query::class,'query_id','id');
	}
	public function inquiryStatus(){
		return $this->belongsTo(InquiryStatus::class,'status','id');
	}
	public function createdBy(){
		return $this->belongsTo(User::class,'created_by','id');
	}
	public function getCreatedAtAttribute($value)
	{
		return (!is_null($value) ? date("F jS, Y h:i a", strtotime($value)) : null);
	}

}
