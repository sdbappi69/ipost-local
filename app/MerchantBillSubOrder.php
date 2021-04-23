<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantBillSubOrder extends Model
{
	protected $table = 'merchant_bill_suborder';

	protected $guarded = ['id'];
	
	public $timestamps = false;
}