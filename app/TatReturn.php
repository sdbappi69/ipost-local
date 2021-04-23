<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TatReturn extends Model
{
    protected $table = 'tat_return';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
