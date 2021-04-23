<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubVaultAccounts extends Model
{
    protected $table = 'hub_volt_accounts';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function hub()
	{
		return $this->belongsTo('App\Hub', 'hub_id', 'id');
	}

}
