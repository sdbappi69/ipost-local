<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoneMap extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * [zone description]
     * @return [type] [description]
     * @author Risul Islam <risul.islam@sslwireless.com><risul321@gmail.com>
     */
    public function zone()
    {
    	return $this->belongsTo('App\Zone');
    }
}
