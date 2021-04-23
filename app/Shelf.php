<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
   protected $table = 'shelfs';

   protected $fillable = [
      'hub_id', 'shelf_title', 'assignd_hub_id', 'width', 'height', 'length', 'status', 'created_by', 'updated_by'
   ];

   /**
   * An shelf belongs to a hub
   */
   public function get_hub()
   {
     return $this->belongsTo('App\Hub', 'hub_id', 'id');
   }

   /**
   * An shelf belongs to a hub
   */
   public function assigndhub()
   {
     return $this->hasOne('App\Hub', 'id', 'assignd_hub_id');
   }

   /**
   * An hub belongs to a responsible user
   */
   public function responsible_user()
   {
     return $this->belongsTo('App\User', 'created_by', 'id');
   }
}
