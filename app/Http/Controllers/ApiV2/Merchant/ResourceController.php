<?php

namespace App\Http\Controllers\ApiV2\Merchant;

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
use App\Lp;
use App\Charge;

class ResourceController extends Controller {

    public function resource(Request $request) {

        $token = $request->input("api_token");
        $user_info = User::where('api_token', $token)
                ->where('status', 1)
                ->first();
        if (!$user_info) {
            $status_code = 401;
            $message[] = 'User unauthorized';
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        $pickup_locations = $this->pickup_location();

        $product_categories = $this->product_categories();

        $paymentTypes = \App\PaymentType::select('id', 'name')->whereStatus(1)->get()->toArray();

        if (!$pickup_locations) {
            $pickup_locations = [];
        }

        $message = array();

        $location = Zone::
                select(
                        // 'zones.id as zone_id',
                        DB::raw('CONCAT(zones.name, ",", cities.name) AS delivery_zone')
                )
                ->join('cities', 'cities.id', '=', 'zones.city_id')
                ->where('zones.status', 1)
                ->orderBy('delivery_zone', 'asc')
                ->get();

        if (count($location) > 0) {
            $feedback['status_code'] = 200;
            $message[] = "Data Found";
            $feedback['message'] = $message;
            $feedback['response']['delivery_zones'] = $location;
            $feedback['response']['pickup_locations'] = $pickup_locations;
            $feedback['response']['product_categories'] = $product_categories;
            $feedback['response']['payment_types'] = $paymentTypes;
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

        $categories = ProductCategory::select('name AS product_category')
                ->where('parent_category_id', '!=', null)
                ->where('status', '=', '1')
                ->get();

        if (!count($categories) > 0) {
            return FALSE;
        }
        return $categories;
    }

    public function pickup_location() {
        $pl = PickingLocations::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->addSelect('title AS pickup_location')->get();
        if (!count($pl) > 0) {
            return FALSE;
        }
        return $pl;
    }

    public function pickup_time($logistic_partner_id) {
        $pt = PickingTimeSlot::whereStatus(true)->where('logistic_partner_id', $logistic_partner_id)->addSelect('id AS pickup_time_id', DB::raw('CONCAT(day, " (", start_time, " - ", end_time, ")") AS pickup_time_title'))->get();
        if (!count($pt) > 0) {
            return FALSE;
        }
        return $pt;
    }

    public function storePickupLocation(Request $request) {
        $validator = Validator::make($request->all(), [
                    'title' => 'required|max:200',
                    'email' => 'required|email',
                    'msisdn' => 'required|max:25',
                    'alt_msisdn' => 'sometimes|max:25',
                    'address1' => 'required|max:200',
                    'address2' => 'sometimes|max:200',
                    'zone_title' => 'sometimes|exists:zones,name',
                    'city_title' => 'sometimes|exists:cities,name',
                    'latitude' => 'required',
                    'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => "Validation Failed",
                'status_code' => 422,
                'message' => $validator->errors(),
                    ], 200);
        }
        try {
            if ($request->has("zone_title")) {
                $zone = Zone::with('city.state.country')->whereName($request->zone_title)->first();
            } else {
//                $zone = Zone::with('city.state.country')->where('id', Auth::guard('api')->user()->zone_id)->first();
                $zone = getZoneBound($request->latitude, $request->longitude);
            }
            if (!$zone) {
                return response([
                    'status' => "Failed",
                    'status_code' => 500,
                    'message' => ['Out of zone coverage!'],
                        ], 200);
            }
            $pickup_location = PickingLocations::updateOrCreate(
                            ['title' => $request->title, 'merchant_id' => Auth::guard('api')->user()->reference_id], [
                        'email' => $request->email,
                        'msisdn' => $request->msisdn,
                        'alt_msisdn' => $request->alt_msisdn,
                        'address1' => $request->address1,
                        'address2' => $request->address2,
                        'zone_id' => $zone->id,
                        'city_id' => $zone->city_id,
                        'state_id' => $zone->city->state_id,
                        'country_id' => $zone->city->state->country_id,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'created_by' => Auth::guard('api')->user()->id,
                            ]
            );
            return response([
                'status' => "Success",
                'status_code' => 200,
                'message' => ['Pickup location created successfully.'],
                'response' => $pickup_location,
                    ], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
            return response([
                'status' => "Internal Server Error",
                'status_code' => 500,
                'message' => ['Pickup location could not created.'],
                    ], 200);
        }
    }

    public function findNearestPickupLocation(Request $request) {
        $validation = Validator::make($request->all(), [
                    'delivery_lat' => 'required|min:0',
                    'delivery_lng' => 'required|min:0'
        ]);

        if ($validation->fails()) {
            $status_code = 404;
            $message = $validation->errors()->all();
            return $this->set_unauthorized($status_code, $message, $response = '');
        }
        $pickupLocations = PickingLocations::select('id', 'title', 'address1', 'latitude', 'longitude', \DB::raw("6371 * acos(cos(radians(" . $request->delivery_lat . "))
                                     * cos(radians(latitude))
                                     * cos(radians(longitude) - radians(" . $request->delivery_lng . "))
                                     + sin(radians(" . $request->delivery_lat . "))
                                     * sin(radians(latitude))) AS distance"))
                        ->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)
                        ->where('status', 1)
                        ->orderBy('distance', 'asc')
                        ->take(3)->get()->toArray();
        if (count($pickupLocations) > 0) {
            $feedback['status_code'] = 200;
            $feedback['message'] = ["Data Found"];
            $feedback['response']['pickup_locations'] = $pickupLocations;
        } else {
            $status_code = 404;
            $message[] = 'No data found';
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        return response($feedback, 200);
    }

}
