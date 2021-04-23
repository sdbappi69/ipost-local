<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickingLocations extends Model
{
    protected $table = 'pickup_locations';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
    * An pickup PickingLocation to a zone
    */
    public function zone()
    {
        return $this->belongsTo('App\Zone', 'zone_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo('App\Merchant', 'merchant_id', 'id');
    }

    public function getAltMsisdnAttribute($value)
    {
        if($value == null) {
            return "";
        }
    }

    public function getAddress2Attribute($value)
    {
        if($value == null) {
            return "";
        }
    }
}
