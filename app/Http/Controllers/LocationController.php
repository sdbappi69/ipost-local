<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\State;
use App\City;
use App\Zone;

class LocationController extends Controller
{
    public function states($country_id){

    	$states = State::whereStatus(true)->where('country_id', '=', $country_id)->addSelect('id','name')->get();

    	return $_GET['callback']."(".json_encode($states).")";
    }

    public function cities($state_id){

    	$cities = City::whereStatus(true)->where('state_id', '=', $state_id)->addSelect('id','name')->get();

    	return $_GET['callback']."(".json_encode($cities).")";
    }

    public function zones($city_id){

    	$zones = Zone::whereStatus(true)->where('city_id', '=', $city_id)->addSelect('id','name')->get();

    	return $_GET['callback']."(".json_encode($zones).")";
    }

    /**
     * [zonebound description]
     * @param  [type] $lat [description]
     * @param  [type] $lng [description]
     * @return [type]      [description]
     * @author Risul Islam <risul.islam@sslwireless.com><risul321@gmail.com>
     */
    public function zonebound($lat, $lng){
        $zone = getZoneBound($lat, $lng);
        return $_GET['callback']."(".json_encode($zone).")";
    }
}
