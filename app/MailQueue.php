<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    protected $table = 'mail_queue';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
