<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */

    /**
     * A city has many zones
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zones()
    {
        return $this->hasMany('App\Zone');
    }

    /**
     * A city belongs to a state
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('App\State', 'state_id', 'id');
    }

}
