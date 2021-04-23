<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantBankAccounts extends Model
{
	protected $table = 'merchant_bank_accounts';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function account()
	{
		return $this->belongsTo('App\BankAccounts', 'account_id', 'id');
	}
	
	public function merchant()
	{
		return $this->belongsTo('App\Merchant', 'merchant_id', 'id');
	}

	public function store()
	{
		return $this->belongsTo('App\Store', 'store_id', 'id');
	}
}
