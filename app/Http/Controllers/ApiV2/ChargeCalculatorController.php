<?php

namespace App\Http\Controllers\ApiV2;

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
                    // 'pickup_zone_id' => 'required|numeric',
                    // 'delivery_zone_id' => 'required|numeric',
                    'pickup_location' => 'required',
                    'delivery_zone' => 'required',
                    'quantity' => 'required|numeric',
                    'unit_price' => 'sometimes|numeric',
        ]);
        if ($validation->fails()) {

            return $productDetails[] = $validation->errors()->all();
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

        // Get charge_model_id
        $weight_charge_model_id = $this->get_weight_charge_model_id($weight);

        // Get Pickup Zone & Delivery Zone
        $pickup_location = PickingLocations::where('title', $request->pickup_location)->first();

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

        $pickup_zone_id = $pickup_location->zone_id;
        $delivery_zone_id = $delivery_zone->id;

        // Get zone_genre_id
        $zone_genre_id = $this->get_zone_genre_id($pickup_zone_id, $delivery_zone_id);

        // Get product_category_id
        $product_category_data = ProductCategory::where('name', $request->product_category)->orderBy('id', 'desc')->first();
        $product_category_id = $product_category_data->id;

        // Get store_id
        $store_data = Store::where('store_id', $request->store_id)->orderBy('id', 'desc')->first();
        $store_id = $store_data->id;

        // Get charge
        $charge = $this->get_charge($store_id, $product_category_id, $zone_genre_id, $weight_charge_model_id);

        // Get Delivery Charge
//        if(count($charge) == 0){
        if (!$charge) {
            $delivery_charge = 0;
            $delivery_discount = 0;
            $delivery_discount_charge = $delivery_charge;
            $delivery_discount_id = 0;
            $discount_title = '';
        } else {
            $delivery_charge = $this->get_delivery_charge($charge, $weight);

            // Get Discount
            $discount = $this->get_discount($charge);
//            if(count($discount) == 0){
            if (!$discount) {
                $delivery_discount = 0;
                $delivery_discount_charge = $delivery_charge;
                $delivery_discount_id = 0;
                $discount_title = '';
            } else {
                // Count Discount
                $delivery_discount_data = $this->get_discount_data($discount, $delivery_charge, $weight);
                $delivery_discount = $delivery_discount_data['delivery_discount'];
                $delivery_discount_charge = $delivery_charge - $delivery_discount;
                $delivery_discount_id = $delivery_discount_data['delivery_discount_id'];
                $discount_title = $delivery_discount_data['discount_title'];
            }
        }

        $productDetails[] = array(
            'status' => 'Success',
            'message' => 'Request Successful',
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

        return json_encode($productDetails);
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
