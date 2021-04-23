<?php

namespace App\Http\Controllers\Api\Merchant;

use App\RiderLocation;
use App\User;
use App\State;
use App\City;
use App\Zone;
// masud
use App\Country;
use App\Store;
use Auth;
// end masud
use Illuminate\Http\Request;
use DB;
use Log;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LocationController extends Controller {

    public function location(Request $request) {
        // return $reference_id    =  Auth::guard('api')->user()->reference_id;
        // $token = $request->input("api_token");
        // $user_info = User::where('api_token', $token)
        //         ->where('status', 1)
        //         ->first();
        // if (!count($user_info) > 0) {
        //     $status_code = 401;
        //     $message[] = 'User unauthorized';
        //     return $this->set_unauthorized($status_code, $message, $response = '');
        // }
        // get store list for this merchant
        $stores = $this->store_list();

        if (!$stores) {
            $stores = [];
        }

        $message = array();

        $location = Zone::
                select(
                        array('zones.name as zone_name',
                            'cities.name as city_name',
                            'states.name as states_name',
                            'countries.name as country_name',
                            'zones.id as zone_id',
                            'cities.id as city_id',
                            'states.id as states_id',
                            'countries.id as country_id')
                )
                ->join('cities', 'cities.id', '=', 'zones.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->join('countries', 'countries.id', '=', 'states.country_id')
                ->where('zones.status', 1)
                ->get();
        //return $zone;
        //$country = Country::whereStatus(true)->addSelect('id', 'name')->get();
        if (count($location) > 0) {
            $feedback['status_code'] = 200;
            $message[] = "Data Found";
            $feedback['message'] = $message;
            $feedback['response']['location'] = $location;
            $feedback['response']['store_list'] = $stores;
        } else {
            $status_code = 404;
            $message[] = 'No data found';
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        return response($feedback, 200);
    }

    //private function set_unauthorized( $status, $status_code, $message, $response )
    private function set_unauthorized($status_code, $message, $response) {
        $feedback = [];
        //$feedback['status']        =  $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
    }

    public function store_list() {
        $stores = Store::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->addSelect('store_id as store_name', 'id')->get();
        if (!count($stores) > 0) {
            return FALSE;
        }
        return $stores;
    }

}
