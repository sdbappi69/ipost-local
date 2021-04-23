<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_types';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
