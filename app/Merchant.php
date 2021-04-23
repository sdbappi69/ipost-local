<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $table = 'merchants';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
     * A mechent has many store
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }

    /**
    * An merchane belongs to a zone
    */
    public function zone()
    {
        return $this->belongsTo('App\Zone', 'zone_id', 'id');
    }
}
