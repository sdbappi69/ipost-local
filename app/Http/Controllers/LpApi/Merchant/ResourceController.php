<?php

namespace App\Http\Controllers\LpApi\Merchant;

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
        // return $reference_id    =  Auth::guard('api')->user()->reference_id;
        $token = $request->input("api_token");
        $user_info = User::where('api_token', $token)
                ->where('status', 1)
                ->first();
        if (!count($user_info) > 0) {
            $status_code = 401;
            $message[] = 'User unauthorized';
            return $this->set_unauthorized($status_code, $message, $response = '');
        }
        // get store list for this merchant
        // $stores = $this->store_list();

        // if (!$stores) {
        //     $stores = [];
        // }

        // $product_categories = $this->product_categories();

        // if (!$product_categories) {
        //     $product_categories = [];
        // }

        $pickup_locations = $this->pickup_location();

        if (!$pickup_locations) {
            $pickup_locations = [];
        }

        $message = array();

        $location = Zone::
                select(
                    'zones.id as zone_id',
                    DB::raw('CONCAT(zones.name, ", ", cities.name) AS zone_name')
                )
                ->join('cities', 'cities.id', '=', 'zones.city_id')
                ->where('zones.status', 1)
                ->orderBy('zone_name', 'asc')
                ->get();

        $partners = array();
        $logistic_partners = Lp::whereStatus(true)->get();
        foreach ($logistic_partners as $logistic_partner) {
            $chargeInfo = Charge::whereStatus(true)
                                    ->where('product_category_id', 5)
                                    ->where('logistic_partner_id', $logistic_partner->id)
                                    ->first();
            if(count($chargeInfo) == 0){
                $chargeInfo = Charge::whereStatus(true)
                                    ->where('product_category_id', 5)
                                    ->first();
            }

            if($chargeInfo->charge_model_id == 2){
                $charge_type = 'Fixed';
            }else{
                $charge_type = 'Weight Based';
            }

            if($charge_type == 'Fixed'){
                $charges = Charge::whereStatus(true)
                                        ->where('charge_model_id', 2)
                                        ->groupBy('zone_genre_id')->get();

                $same_city = 0;
                $same_division = 0;
                $diffrent_divisional_city = 0;
                $other = 0;
                $same_city_estimated_delivery_hours = 0;
                $same_division_estimated_delivery_hours = 0;
                $diffrent_divisional_city_estimated_delivery_hours = 0;
                $other_estimated_delivery_hours = 0;

                foreach ($charges as $charge) {
                    
                    if($charge->zone_genre_id == 1){
                        $same_city = $charge->fixed_charge;
                        $same_city_estimated_delivery_hours = $charge->estimated_delivery_time;
                    }else if($charge->zone_genre_id == 2){
                        $same_division = $charge->fixed_charge;
                        $same_division_estimated_delivery_hours = $charge->estimated_delivery_time;
                    }else if($charge->zone_genre_id == 3){
                        $diffrent_divisional_city = $charge->fixed_charge;
                        $diffrent_divisional_city_estimated_delivery_hours = $charge->estimated_delivery_time;
                    }else{
                        $other = $charge->fixed_charge;
                        $other_estimated_delivery_hours = $charge->estimated_delivery_time;
                    }

                }

                $fixed = array(
                                "same_city" => array('charge' => $same_city, 'estimated_delivery_hours' => $same_city_estimated_delivery_hours),
                                "same_division" => array('charge' => $same_division, 'estimated_delivery_hours' => $same_division_estimated_delivery_hours),
                                "diffrent_divisional_city" => array('charge' => $diffrent_divisional_city, 'estimated_delivery_hours' => $diffrent_divisional_city_estimated_delivery_hours),
                                "other" => array('charge' => $other, 'estimated_delivery_hours' => $other_estimated_delivery_hours)
                            );

                $all_charge = array("fixed" => $fixed);

            }else{
                $charges = Charge::whereStatus(true)
                                        ->where('charge_model_id', '!=', 2)
                                        ->groupBy('zone_genre_id')->get();

                $same_city_3 = 0;
                $same_division_3 = 0;
                $diffrent_divisional_city_3 = 0;
                $other_3 = 0;
                $same_city_3_estimated_delivery_hours = 0;
                $same_division_3_estimated_delivery_hours = 0;
                $diffrent_divisional_city_3_estimated_delivery_hours = 0;
                $other_3_estimated_delivery_hours = 0;

                $same_city_4 = 0;
                $same_division_4 = 0;
                $diffrent_divisional_city_4 = 0;
                $other_4 = 0;
                $same_city_4_estimated_delivery_hours = 0;
                $same_division_4_estimated_delivery_hours = 0;
                $diffrent_divisional_city_4_estimated_delivery_hours = 0;
                $other_4_estimated_delivery_hours = 0;

                $same_city_5 = 0;
                $same_division_5 = 0;
                $diffrent_divisional_city_5 = 0;
                $other_5 = 0;
                $same_city_5_estimated_delivery_hours = 0;
                $same_division_5_estimated_delivery_hours = 0;
                $diffrent_divisional_city_5_estimated_delivery_hours = 0;
                $other_5_estimated_delivery_hours = 0;

                $same_city_additional = 0;
                $same_division_additional = 0;
                $diffrent_divisional_city_additional = 0;
                $other_additional = 0;

                foreach ($charges as $charge) {
                    
                    foreach ($charges as $charge) {
                    
                        if($charge->charge_model_id = 3 && $charge->zone_genre_id == 1){
                            $same_city_3 = $charge->fixed_charge;
                            $same_city_3_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else if($charge->charge_model_id = 3 && $charge->zone_genre_id == 2){
                            $same_division_3 = $charge->fixed_charge;
                            $same_division_3_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else if($charge->charge_model_id = 3 && $charge->zone_genre_id == 3){
                            $diffrent_divisional_city_3 = $charge->fixed_charge;
                            $diffrent_divisional_city_3_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else{
                            $other_3 = $charge->fixed_charge;
                            $other_3_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }

                        if($charge->charge_model_id = 4 && $charge->zone_genre_id == 1){
                            $same_city_4 = $charge->fixed_charge;
                            $same_city_additional = $charge->additional_charge_per_slot;
                            $same_city_4_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else if($charge->charge_model_id = 4 && $charge->zone_genre_id == 2){
                            $same_division_4 = $charge->fixed_charge;
                            $same_division_additional = $charge->additional_charge_per_slot;
                            $same_division_4_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else if($charge->charge_model_id = 4 && $charge->zone_genre_id == 3){
                            $diffrent_divisional_city_4 = $charge->fixed_charge;
                            $diffrent_divisional_city_additional = $charge->additional_charge_per_slot;
                            $diffrent_divisional_city_4_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else{
                            $other_4 = $charge->fixed_charge;
                            $other_additional = $charge->additional_charge_per_slot;
                            $other_4_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }

                        if($charge->charge_model_id = 5 && $charge->zone_genre_id == 1){
                            $same_city_5 = $charge->fixed_charge;
                            $same_city_5_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else if($charge->charge_model_id = 5 && $charge->zone_genre_id == 2){
                            $same_division_5 = $charge->fixed_charge;
                            $same_division_5_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else if($charge->charge_model_id = 5 && $charge->zone_genre_id == 3){
                            $diffrent_divisional_city_5 = $charge->fixed_charge;
                            $diffrent_divisional_city_5_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }else{
                            $other_5 = $charge->fixed_charge;
                            $other_5_estimated_delivery_hours = $charge->estimated_delivery_time;
                        }

                        $half_to_one = array(
                                "same_city" => array('charge' => $same_city_3, 'estimated_delivery_hours' => $same_city_3_estimated_delivery_hours),
                                "same_division" => array('charge' => $same_division_3, 'estimated_delivery_hours' => $same_division_3_estimated_delivery_hours),
                                "diffrent_divisional_city" => array('charge' => $diffrent_divisional_city_3, 'estimated_delivery_hours' => $diffrent_divisional_city_3_estimated_delivery_hours),
                                "other" => array('charge' => $other_3, 'estimated_delivery_hours' => $other_3_estimated_delivery_hours)
                            );

                        $one_to_two = array(
                                "same_city" => array('charge' => $same_city_4, 'additional_per_kg' => $same_city_additional, 'estimated_delivery_hours' => $same_city_4_estimated_delivery_hours),
                                "same_division" => array('charge' => $same_division_4, 'additional_per_kg' => $same_division_additional, 'estimated_delivery_hours' => $same_division_4_estimated_delivery_hours),
                                "diffrent_divisional_city" => array('charge' => $diffrent_divisional_city_4, 'additional_per_kg' => $diffrent_divisional_city_additional, 'estimated_delivery_hours' => $diffrent_divisional_city_4_estimated_delivery_hours),
                                "other" => array('charge' => $other_4, 'additional_per_kg' => $other_additional, 'estimated_delivery_hours' => $other_4_estimated_delivery_hours)
                            );

                        $zero_to_half = array(
                                "same_city" => array('charge' => $same_city_5, 'estimated_delivery_hours' => $same_city_5_estimated_delivery_hours),
                                "same_division" => array('charge' => $same_division_5, 'estimated_delivery_hours' => $same_division_5_estimated_delivery_hours),
                                "diffrent_divisional_city" => array('charge' => $diffrent_divisional_city_5, 'estimated_delivery_hours' => $diffrent_divisional_city_5_estimated_delivery_hours),
                                "other" => array('charge' => $other_5, 'estimated_delivery_hours' => $other_5_estimated_delivery_hours)
                            );

                        $all_charge = array(
                                            "0.5 to 1 Kg" => $half_to_one,
                                            "1 to 2 Kg" => $one_to_two,
                                            "0 to 0.5 Kg" => $zero_to_half
                                        );

                    }

                }
            }

            // $pickup_times = $this->pickup_time($logistic_partner->id);

            // if (!$pickup_times) {
            //     $pickup_times = [];
            // }

            $partners[] = array(
                                    'logistic_partner_id' => $logistic_partner->id,
                                    'name' => $logistic_partner->name,
                                    'mobile' => $logistic_partner->msisdn,
                                    'alt_mobile' => $logistic_partner->alt_msisdn,
                                    'website' => $logistic_partner->website,
                                    'logo' => $logistic_partner->logo,
                                    'charge' => $all_charge,
                                    // 'pickup_times' => $pickup_times
                                );

        }

        //return $zone;
        //$country = Country::whereStatus(true)->addSelect('id', 'name')->get();
        if (count($location) > 0) {
            $feedback['status_code'] = 200;
            $message[] = "Data Found";
            $feedback['message'] = $message;
            $feedback['response']['logistic_partners'] = $partners;
            $feedback['response']['locations'] = $location;
            // $feedback['response']['store_list'] = $stores;
            // $feedback['response']['product_categories'] = $product_categories;
            $feedback['response']['pickup_locations'] = $pickup_locations;
        } else {
            $status_code = 404;
            $message[] = 'No data found';
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        return response($feedback, 200);
            // }
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
        $pl = PickingLocations::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->addSelect('title','zone_id')->get();
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

}
