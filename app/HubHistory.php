<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubHistory extends Model
{
    protected $table = 'hub_volt_history';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function vault()
	{
		return $this->belongsTo('App\HubVaultAccounts', 'hub_volt_account_id', 'id');
	}
	public function manager_id()
	{
		return $this->belongsTo('App\User', 'hub_manager_id', 'id');
	}
	public function hub()
	{
		return $this->belongsTo('App\Hub', 'hub_id', 'id');
	}
	

}
