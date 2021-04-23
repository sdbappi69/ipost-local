<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoneGenre extends Model
{
    protected $table = 'zone_genres';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
