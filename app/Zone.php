<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */

    /**
     * A zone belongs to a city
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }

    /**
    * An product belongs to a pickup_location
    */
    public function hub()
    {
        return $this->belongsTo('App\Hub', 'hub_id', 'id');
    }

    /**
     * [Zone ZoneMap relation]
     * @return [type] [description]
     * @author Risul Islam <risul.islam@sslwireless.com><risul321@gmail.com>
     */
    public function map()
    {
        return $this->hasOne('App\ZoneMap');
    }

}
