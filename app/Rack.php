<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
   protected $fillable = [
      'hub_id', 'zone_id', 'rack_title', 'width', 'height', 'length', 'status', 'created_by', 'updated_by'
   ];

   /**
   * An shelf belongs to a hub
   */
   public function get_hub()
   {
     return $this->belongsTo('App\Hub', 'hub_id', 'id');
   }

   /**
   * An shelf belongs to a zone
   */
   public function get_zone()
   {
     return $this->belongsTo('App\Zone', 'zone_id', 'id');
   }

}
