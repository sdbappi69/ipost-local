<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class RiderReference extends Model
{
    protected $table = 'rider_references';
    protected $fillable = ['user_id', 'reference_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reference()
    {
        return $this->belongsTo(Hub::class, 'reference_id');
    }
}