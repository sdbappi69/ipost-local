<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantBill extends Model
{
	protected $table = 'merchant_bills';

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
}