<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
	protected $table = 'bank_accounts';

	protected $guarded = ['id', 'created_at', 'updated_at'];

	public function bank()
	{
		return $this->belongsTo('App\Bank', 'bank_id', 'id');
	}
}
