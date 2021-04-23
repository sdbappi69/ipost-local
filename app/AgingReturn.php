<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgingReturn extends Model
{
    protected $table = 'aging_return';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
