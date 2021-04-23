<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	protected $table = 'roles';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
