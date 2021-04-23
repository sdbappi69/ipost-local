<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait; // add this trait to your user model

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /* Relation(s) */
    public function type()
    {
        return $this->belongsTo('App\Role');
    }

    /**
     * An hub users to a role
     */
    public function role()
    {
        return $this->belongsTo('App\Role', 'user_type_id', 'id');
    }

    public function userReference()
    {
        return $this->hasMany(RiderReference::class, 'user_id')->select('reference_id');
    }
}
