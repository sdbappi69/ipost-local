<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * A settings belongs to a country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    /**
     * A settings belongs to a state
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('App\State');
    }

    /**
     * A settings belongs to a city
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    /**
     * A settings belongs to a state
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo('App\Zone');
    }

}
