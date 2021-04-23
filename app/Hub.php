<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $table = 'hubs';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
    * An hub belongs to a zone
    */
    public function zone_genre()
    {
    	return $this->belongsTo('App\ZoneGenre', 'zone_genre_id', 'id');
    }
  
    /**
    * An hub belongs to a zone
    */
    public function zone()
    {
        return $this->belongsTo('App\Zone', 'zone_id', 'id');
    }

    /**
    * An hub belongs to a responsible user
    */
    public function responsible_user()
    {
    	return $this->belongsTo('App\User', 'responsible_user_id', 'id');
    }
}
