<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */

    /**
     * A country has many states
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->hasMany('App\State');
    }
}
