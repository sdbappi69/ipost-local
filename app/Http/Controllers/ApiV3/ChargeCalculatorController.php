<?php

namespace App\Http\Controllers\ApiV3;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\ChargeCalculetorTrait;
use App\Order;
use App\ProductCategory;
use App\OrderProduct;
use App\Charge;
use App\ChargeModel;
use App\Zone;
use App\Store;
use App\ZoneGenre;
use App\PickingLocations;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Log;

class ChargeCalculatorController extends Controller {

    use ChargeCalculetorTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $validation = Validator::make($request->all(), [
                    'width' => 'required',
                    'height' => 'required',
                    'length' => 'required',
                    'weight' => 'required',
                    'product_category' => 'required',
                    'store_id' => 'required',
                    'pickup_location' => 'required',
                    'delivery_zone' => 'required',
                    'quantity' => 'required|numeric',
                    'unit_price' => 'sometimes|numeric',
        ]);
        if ($validation->fails()) {
            return response()->json([
                        'status' => 'Failed',
                        'status_code' => 404,
                        'message' => $validation->errors()->all(),
                        'data' => [],
                            ], 200);
        }
        // Get unit_price
        if ($request->has('unit_price')) {
            $unit_price = $request->unit_price;
        } else {
            $unit_price = 0;
        }

        // Get weight
        if ($request->has('weight')) {
            $weight = $request->weight;
        } else {
            $weight = ($request->width * $request->height * $request->length) / 5000;
        }

        // Get Pickup Zone & Delivery Zone
        $pickup_location = PickingLocations::where('title', $request->pickup_location)->first();
        if (!$pickup_location) {
            return response()->json([
                        'status' => 'Failed',
                        'status_code' => 404,
                        'message' => [trans('api.picking_location_not_found')],
                        'data' => [],
                            ], 200);
        }
        $delivery_zone_city = explode(",", $request->delivery_zone);
        $delivery_zone_title = $delivery_zone_city[0];
        $delivery_city_title = $delivery_zone_city[1];
        $delivery_zone = Zone::select('zones.id')
                ->join('cities', 'cities.id', '=', 'zones.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->where('zones.status', '1')
                ->where('cities.status', '1')
                ->where('zones.name', $delivery_zone_title)
                ->where('cities.name', $delivery_city_title)
                ->first();
        if (!$delivery_zone) {
            return response()->json([
                        'status' => 'Failed',
                        'status_code' => 404,
                        'message' => [trans('api.delivery_location_not_found')],
                        'data' => [],
                            ], 200);
        }
        $pickup_zone_id = $pickup_location->zone_id;
        $delivery_zone_id = $delivery_zone->id;

        // Get product_category_id
        $product_category_data = ProductCategory::where('name', $request->product_category)->orderBy('id', 'desc')->first();
        if (!$product_category_data) {
            return response()->json([
                        'status' => 'Failed',
                        'status_code' => 404,
                        'message' => [trans('api.product_category_not_found')],
                        'data' => [],
                            ], 200);
        }
        $product_category_id = $product_category_data->id;

        // Get store_id
        $store_data = Store::where('store_id', $request->store_id)->orderBy('id', 'desc')->first();
        if (!$store_data) {
            return response()->json([
                        'status' => 'Failed',
                        'status_code' => 404,
                        'message' => [trans('api.store_not_found')],
                        'data' => [],
                            ], 200);
        }
        $store_id = $store_data->id;
        $charge = $this->store_charge($product_category_id, $store_id, $pickup_zone_id, $delivery_zone_id, $weight);
        if ($charge['status_code'] != 200) {
            return response()->json($charge, 200);
        }
        $hub_number = $this->hub_number($pickup_zone_id, $delivery_zone_id);
        // Get charge
        $delivery_charge = $charge['data']['initial_charge'] + ($charge['data']['hub_transfer_charge'] * $hub_number);
        
        // Get Discount
        $discount = $this->get_discount($delivery_charge, $store_id, $product_category_id);

        if ($discount) {
            $discount_data = $this->get_discount_data($discount, $delivery_charge, $weight);
            $delivery_discount = $discount_data['delivery_discount'];
            $delivery_discount_charge = $delivery_charge - $delivery_discount;
            $delivery_discount_id = $discount_data['delivery_discount_id'];
            $discount_title = $discount_data['discount_title'];
        } else {
            $delivery_discount = 0;
            $delivery_discount_charge = $delivery_charge;
            $delivery_discount_id = 0;
            $discount_title = '';
        }

        $productDetails[] = array(
            'status' => 'Success',
            'message' => trans('api.request_successful'),
            'product_category' => $request->product_category,
            'product_quantity' => $request->quantity,
            'product_unit_price' => $unit_price,
            'product_total_price' => $unit_price * $request->quantity,
            'product_actual_unit_delivery_charge' => $delivery_charge,
            'product_actual_delivery_charge' => $delivery_charge * $request->quantity,
            'product_unit_discount' => $delivery_discount,
            'product_discount' => $delivery_discount * $request->quantity,
            'delivery_discount_id' => $delivery_discount_id,
            'delivery_discount_title' => $discount_title,
            'product_unit_delivery_charge' => $delivery_discount_charge,
            'product_delivery_charge' => $delivery_discount_charge * $request->quantity,
        );

        return response()->json([
                    'status' => 'Success',
                    'status_code' => 200,
                    'message' => [],
                    'data' => $productDetails,
                        ], 200);
    }

    public function open($product_category_id, $pickup_zone_id, $delivery_zone_id, $quantity, $width, $height, $length) {
        $weight = ($width * $height * $length) / 5000;

        // Get charge_model_id
        $weight_charge_model_id = $this->get_weight_charge_model_id($weight);

        // Get zone_genre_id
        $zone_genre_id = $this->get_zone_genre_id($pickup_zone_id, $delivery_zone_id);

        // Get product_category_id
        $product_category_data = ProductCategory::where('id', $product_category_id)->orderBy('id', 'desc')->first();
        $product_category_id = $product_category_data->id;

        // Get charge
        $charge = $this->get_open_charge($product_category_id, $zone_genre_id, $weight_charge_model_id);

        // Get Delivery Charge
        if (count($charge) == 0) {
            $delivery_charge = 0;
        } else {
            $delivery_charge = $this->get_delivery_charge($charge, $weight);
        }

        $productDetails[] = array(
            'status' => 'Success',
            'message' => 'Request Successful',
            'product_category' => $product_category_data->name,
            'product_quantity' => $quantity,
            'product_unit_delivery_charge' => $delivery_charge,
            'product_total_delivery_charge' => $delivery_charge * $quantity,
        );

        return json_encode($productDetails);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
