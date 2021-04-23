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

use App\ProductCategory;
use App\PickingLocations;
use App\PickingTimeSlot;

class ResourceController extends Controller {

    public function resource(Request $request) {
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

        $product_categories = $this->product_categories();

        if (!$stores) {
            $product_categories = [];
        }

        $pickup_locations = $this->pickup_location();

        if (!$pickup_locations) {
            $pickup_locations = [];
        }

        $pickup_times = $this->pickup_time();

        $order_status_list = $this->order_status_list();

        if (!$pickup_times) {
            $pickup_times = [];
        }

        $message = array();

        $location = Zone::
                select(
                        array(DB::raw('CONCAT(zones.name, ", ", cities.name) AS zone_name'),
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
            $feedback['response']['product_categories'] = $product_categories;
            $feedback['response']['pickup_locations'] = $pickup_locations;
            $feedback['response']['pickup_times'] = $pickup_times;
            $feedback['response']['order_status_list'] = $order_status_list;
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

    public function product_categories() {
        // $stores = Store::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->addSelect('store_id as store_name', 'id')->get();

        $categories = ProductCategory::select(array(
            'product_categories.id AS id',
            DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
            ))
        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        // ->where('product_categories.category_type', '=', 'child')
        ->where('product_categories.parent_category_id', '!=', null)
        ->where('product_categories.status', '=', '1')
        ->where('pc.status', '=', '1')
        // ->lists('cat_name', 'id')
        ->get();

        if (!count($categories) > 0) {
            return FALSE;
        }
        return $categories;
    }

    public function pickup_location() {
        $pl = PickingLocations::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->addSelect('title', 'id')->get();
        if (!count($pl) > 0) {
            return FALSE;
        }
        return $pl;
    }

    public function pickup_time() {
        $pt = PickingTimeSlot::whereStatus(true)->addSelect('id', 'day', 'start_time', 'end_time')->get();
        if (!count($pt) > 0) {
            return FALSE;
        }
        return $pt;
    }

    public function order_status_list(){
        $order_status_list = [
                                array('id' => 1, 'name' => 'Verified'),
                                array('id' => 2, 'name' => 'Picked'),
                                array('id' => 3, 'name' => 'In Transit'),
                                array('id' => 4, 'name' => 'Complete')
                            ];

        return $order_status_list;
    }

}
