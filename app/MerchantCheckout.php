<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantCheckout extends Model
{
	protected $table = 'merchant_checkouts';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function merchant()
	{
		return $this->belongsTo('App\Merchant', 'merchant_id', 'id');
	}
	public function account()
	{
		return $this->belongsTo('App\MerchantBankAccounts', 'merchant_bank_account_id', 'id');
	}
	public function transactionDoc()
	{
		return $this->belongsTo('App\BankTransactionDoc', 'bank_transection_doc_id', 'id');
	}
	public function cheque_account(){
		return $this->belongsTo('App\HubBankAccounts', 'bank_id', 'id');
	}
}
