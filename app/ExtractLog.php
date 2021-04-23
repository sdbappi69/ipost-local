<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtractLog extends Model
{
    protected $table = 'extract_log';
    public $timestamps = false;
    
    protected $guarded = ['id'];
}
