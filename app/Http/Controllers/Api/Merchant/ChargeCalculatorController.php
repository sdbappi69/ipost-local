<?php

namespace App\Http\Controllers\Api\Merchant;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use App\ProductCategory;
use App\OrderProduct;
use App\Charge;
use App\ChargeModel;
use App\Zone;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Log;

class ChargeCalculatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function charge_calculator(Request $request)
    {
        // return 1;
        // return $request->all();
        $validation = Validator::make($request->all(), [
                'width' => 'required',
                'height' => 'required',
                'length' => 'required',
                'weight' => 'required',
                'product_category_id' => 'required',
                'store_id' => 'required',
                'pickup_zone_id' => 'required|numeric',
                'delivery_zone_id' => 'required|numeric',
                'quantity' => 'required|numeric',
                'unit_price' => 'required|numeric',
            ]);
        if($validation->fails()) {

            // $productDetails[] = array(
            //                         'status' => 'Failed',
            //                         'message' => 'Invalid Request',
            //                     );
            // return json_encode($productDetails);

            $message[] = "Invalid Request";
            $feedback['status_code'] = 401;
            $feedback['message'] = $message;
            // return $this->set_unauthorized($status_code, $message, $response = '');

            // return Redirect::back()->withErrors($validation)->withInput();
            return response($feedback, 200);
        }

        if($request->weight){
            $weight = $request->weight;
        }else{
            $weight = ($request->width * $request->height * $request->length) / 5000;
        }

        // Get Zone Genre ID
        $pickup_detail = Zone::whereStatus(true)->where('id', '=', $request->pickup_zone_id)->first();
        $delivery_detail = Zone::whereStatus(true)->where('id', '=', $request->delivery_zone_id)->first();
        if($pickup_detail->city_id == $delivery_detail->city_id){
            $zone_genre_id = '1';
        }else if($pickup_detail->city->state_id == $delivery_detail->city->state_id){
            $zone_genre_id = '2';
        }else if($pickup_detail->city->divisional_city == 1 && $delivery_detail->city->divisional_city == 1){
            $zone_genre_id = '3';
        }else{
            $zone_genre_id = '4';
        }

        // return $zone_genre_id;

        $count = Charge::select()
                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'charges.product_category_id')
                ->leftJoin('stores AS s', 's.id', '=', 'charges.store_id')
                ->where('charges.status', '1')
                ->where('pc.id', '=', $request->product_category_id)
                ->where('s.id', '=', $request->store_id)
                ->where('charges.zone_genre_id', '=', $zone_genre_id)
                ->count();

        $chargeInfo = Charge::select(array(
                                            'charges.id',
                                            'charges.charge_model_id',
                                            'charges.zone_genre_id',
                                            'charges.percentage_range_start',
                                            'charges.percentage_range_end',
                                            'charges.percentage_value',
                                            'charges.additional_range_per_slot',
                                            'charges.additional_charge_per_slot',
                                            'charges.additional_charge_type',
                                            'charges.fixed_charge',
                                            'charge_models.title AS charge_model_title',
                                            'charge_models.unit AS charge_model_unit',
                                            'zone_genres.title AS zone_genre_title',
                                        ))
                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'charges.product_category_id')
                ->leftJoin('stores AS s', 's.id', '=', 'charges.store_id')
                ->leftJoin('charge_models', 'charge_models.id', '=', 'charges.charge_model_id')
                ->leftJoin('zone_genres', 'zone_genres.id', '=', 'charges.zone_genre_id')
                ->where('charges.status', '=', '1')
                ->where('charge_models.status', '=', '1')
                ->where('zone_genres.status', '=', '1')
                ->where('pc.id', '=', $request->product_category_id)
                ->where('charges.zone_genre_id', '=', $zone_genre_id)
                ;
        if($count != 0){
            $chargeInfo->where('s.id', '=', $request->store_id);
        }
        if (in_array($request->product_category_id, array('5', '6')))
        // if($request->product_category == 'Other Products')
        {
            if($weight <= 0.5){
                $chargeInfo->where('charges.charge_model_id', '=', '5');
            }else if($weight <= 1){
                $chargeInfo->where('charges.charge_model_id', '=', '3');
            // }else if($weight <= 2){
            //     $chargeInfo->where('charges.charge_model_id', '=', '4');
            }else{
                $chargeInfo->where('charges.charge_model_id', '=', '4');
            }
        }
        $chargeInfo->orderBy('charges.id', 'desc');
        $chargeInfo = $chargeInfo->first();

        // dd($chargeInfo);

        switch ($chargeInfo->charge_model_title) {
            case "Fixed":
                $delivaryCharge = $chargeInfo->fixed_charge;
                break;

            case "1 to 2 Kg":
                if($weight <= 2){
                    $delivaryCharge = $chargeInfo->fixed_charge;
                }else{
                    $additionalWeight = $weight - 2;
                    $additionalUnits = $additionalWeight / $chargeInfo->additional_range_per_slot;
                    if($chargeInfo->additional_charge_type == 1){
                        $additionalUnits = ceil($additionalUnits);
                    }
                    $delivaryCharge = $chargeInfo->fixed_charge + ($chargeInfo->additional_charge_per_slot * $additionalUnits);
                }
                break;

            case "0.5 to 1 Kg":
                if($weight <= 1){
                    $delivaryCharge = $chargeInfo->fixed_charge;
                }else{
                    $additionalWeight = $weight - 1;
                    $additionalUnits = $additionalWeight / $chargeInfo->additional_range_per_slot;
                    if($chargeInfo->additional_charge_type == 1){
                        $additionalUnits = ceil($additionalUnits);
                    }
                    $delivaryCharge = $chargeInfo->fixed_charge + ($chargeInfo->additional_charge_per_slot * $additionalUnits);
                }
                break;

            case "0 to 0.5 Kg":
                if($weight <= 0.5){
                    $delivaryCharge = $chargeInfo->fixed_charge;
                }else{
                    $additionalWeight = $weight - 0.5;
                    $additionalUnits = $additionalWeight / $chargeInfo->additional_range_per_slot;
                    if($chargeInfo->additional_charge_type == 1){
                        $additionalUnits = ceil($additionalUnits);
                    }
                    $delivaryCharge = $chargeInfo->fixed_charge + ($chargeInfo->additional_charge_per_slot * $additionalUnits);
                }
                break;

            default:
                $delivaryCharge = 0;
        }

        // $productDetails[] = array(
                                //     'status' => 'Success',
                                //     'message' => 'Request Successful',
                                //     'product_category' => $request->product_category,
                                //     'product_quantity' => $request->quantity,
                                //     'product_unit_price' => $request->unit_price,
                                //     'product_total_price' => $request->unit_price*$request->quantity,
                                //     'product_unit_delivery_charge' => $delivaryCharge,
                                //     'product_delivery_charge' => $delivaryCharge*$request->quantity,
                                // );

        // return json_encode($productDetails);

            $feedback['status_code'] = 200;
            $message[] = "Request Successful.";
            $feedback['message'] = $message;
            $feedback['response']['product_category'] = $request->product_category_id;
            $feedback['response']['pproduct_quantity'] = $request->quantity;
            $feedback['response']['pproduct_unit_price'] = $request->unit_price;
            $feedback['response']['pproduct_total_price'] = $request->unit_price*$request->quantity;
            $feedback['response']['pproduct_unit_delivery_charge'] = $delivaryCharge;
            $feedback['response']['pproduct_delivery_charge'] = $delivaryCharge*$request->quantity;
            return response($feedback, 200);

    }
}
