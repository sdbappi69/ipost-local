<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubCheckout extends Model
{
	protected $table = 'hub_checkouts';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function hub_ban_account()
	{
		return $this->belongsTo('App\HubBankAccounts', 'hub_bank_account_id', 'id');
	}
	
	public function manager_id()
	{
		return $this->belongsTo('App\User', 'hub_manger_id', 'id');
	}
	public function depositor()
	{
		return $this->belongsTo('App\User', 'depositor_id', 'id');
	}
	public function transactionDoc()
	{
		return $this->belongsTo('App\BankTransactionDoc', 'bank_transection_doc_id', 'id');
	}
	public function hub(){
		return $this->belongsTo('App\Hub', 'hub_id', 'id');
	}
}
