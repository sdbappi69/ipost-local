<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';

    protected $guarded = ['id', 'created_at', 'updated_at'];


}
