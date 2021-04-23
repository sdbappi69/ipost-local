<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $table = 'charges';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /* Relation(s) */

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id', 'id');
    }

    public function product_category()
    {
        return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }

    public function charge_model()
    {
        return $this->belongsTo('App\ChargeModel', 'charge_model_id', 'id');
    }

    public function zone_genre()
    {
        return $this->belongsTo('App\ZoneGenre', 'zone_genre_id', 'id');
    }

}
