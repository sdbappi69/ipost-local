<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */

    /**
     * A state has many cities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany('App\City');
    }

    /**
     * A state belongs to a country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id', 'id');
    }
}
