<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Storetype extends Model
{
    protected $table = 'store_types';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
