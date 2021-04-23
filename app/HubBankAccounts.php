<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubBankAccounts extends Model
{
	protected $table = 'hub_bank_accounts';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function account()
	{
		return $this->belongsTo('App\BankAccounts', 'account_id', 'id');
	}
	public function hub()
	{
		return $this->belongsTo('App\Hub', 'hub_id', 'id');
	}
}
