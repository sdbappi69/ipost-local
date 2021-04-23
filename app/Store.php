<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
    * An store belongs to a merchant
    */
    public function merchant()
    {
        return $this->belongsTo('App\Merchant', 'merchant_id', 'id');
    }

    /**
    * An store belongs to a store_type
    */
    public function store_type()
    {
        return $this->belongsTo('App\Storetype', 'store_type_id', 'id');
    }
}
