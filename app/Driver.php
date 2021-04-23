<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'drivers';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function boot()
    {
        $class = get_called_class();
        $class::observe(new Observer());

        parent::boot();
    }

    public function vehicles()
    {
        return $this->belongsToMany('App\Vehicle');
    }
}
