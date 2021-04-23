<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsQueue extends Model
{
    protected $table = 'sms_queue';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
