<?php

namespace App\Http\Controllers\ApiV2\Merchant;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\CreateOrderId;
use App\Http\Traits\ChargeCalculetorTrait;
use Illuminate\Support\Facades\Log;
use App\Country;
use App\Order;
use App\SubOrder;
use App\Store;
use App\State;
use App\City;
use App\Zone;
use App\ProductCategory;
use App\PickingLocations;
use App\OrderProduct;
use App\PickingTimeSlot;
use App\CartProduct;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Entrust;
use App\Merchant;
use App\User;
use App\OrderLog;

class MerchantOrderController extends Controller
{

    use LogsTrait;
    use CreateOrderId;
    use ChargeCalculetorTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_order(Request $request)
    {

        Log::info($request->all());

        // return $request->all();

        $validation = Validator::make($request->all(), [
            'store_id' => 'required',
            'orders' => 'required'
        ]);

        if ($validation->fails()) {
            $status_code = 404;
            $message = $validation->errors()->all();
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        $message = array();

        $orders = json_decode($request->orders, true);

        if ($orders != null) {

            $store = Store::whereStatus(true)->where('store_id', $request->store_id)->first();

            $i = 0;

            foreach ($orders as $order) {

                $i++;

                // Validation Start

                if (!isset($order['delivery_name'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'delivery_name not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $delivery_name = $order['delivery_name'];
                }

                if (!isset($order['delivery_email'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'delivery_email not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $delivery_email = $order['delivery_email'];
                }

                if (!isset($order['delivery_msisdn'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'delivery_msisdn not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $delivery_msisdn = $order['delivery_msisdn'];
                }

                if (!isset($order['delivery_zone_lat'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'delivery_zone_lat not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $delivery_zone_lat = $order['delivery_zone_lat'];
                }

                if (!isset($order['delivery_zone_lng'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'delivery_zone_lng not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $delivery_zone_lng = $order['delivery_zone_lng'];
                }
                
                if(!isset($order['payment_type_id']) || !\App\PaymentType::find($order['payment_type_id'])){
//                    $message[$i] = [
//                        'status' => 422,
//                        'message' => 'Invalid Payment Type',
//                        'data' => (object)[],
//                    ];
//                    continue;
                    $paymentType = 1;
                }else{
                    $paymentType = $order['payment_type_id'];
                }

//                if(!isset($order['delivery_address'])){
//                    $message[$i] = 'delivery_address not found';
//                    continue;
//                }else{
//                    $delivery_address = $order['delivery_address'];
//                }
                $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$delivery_zone_lat,$delivery_zone_lng&key=AIzaSyCBWhNYtf2cofZBppq9lfBqzGpJDjLBc4g");

                $output = json_decode($geocode);

                $delivery_address = $output->results[0]->formatted_address;

                if (!isset($order['merchant_order_id'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'merchant_order_id not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $merchant_order_id = $order['merchant_order_id'];
                }

                if (!isset($order['remarks'])) {
                    $remarks = '';
                } else {
                    $remarks = $order['remarks'];
                }

                if (isset($order['as_package']) && $order['as_package'] == 1) {
                    $as_package = 1;

                    if (!isset($order['pickup_location'])) {
                        $message[$i] = [
                            'status' => 422,
                            'message' => 'pickup_location not found',
                            'data' => (object)[],
                        ];
                        continue;
                    } else {
                        $default_pickup_location = PickingLocations::whereStatus(true)
                            ->where('title', $order['pickup_location'])
                            ->where('merchant_id', Auth::guard('api')->user()->reference_id)
                            ->first();
                        if (!$default_pickup_location) {
                            $message[$i] = [
                                'status' => 422,
                                'message' => 'pickup_location not valid',
                                'data' => (object)[],
                            ];
                            continue;
                        } else {
                            $default_pickup_location_id = $default_pickup_location->id;
                            $default_pickup_location_zone_id = $default_pickup_location->zone_id;
                            $default_pickup_location_hub_id = $default_pickup_location->zone->hub_id;
                        }

                        if (!isset($order['picking_date'])) {
                            $default_picking_date = $order['picking_date'];
                        } else {
                            $default_picking_date = "";
                        }
                    }

                } else {
                    $as_package = 0;
                }

                if (isset($order['delivery_pay_by_cus']) && $order['delivery_pay_by_cus'] == 1) {
                    $delivery_pay_by_cus = 1;
                } else {
                    $delivery_pay_by_cus = 0;
                }

                if (isset($order['verified']) && $order['verified'] == 1) {
                    $verified = 1;
                } else {
                    $verified = 0;
                }

                if (!isset($order['products'])) {
                    $message[$i] = [
                        'status' => 422,
                        'message' => 'products not found',
                        'data' => (object)[],
                    ];
                    continue;
                } else {
                    $products = $order['products'];
                    if (count($products) == 0) {
                        $message[$i] = [
                            'status' => 422,
                            'message' => 'No products found',
                            'data' => (object)[],
                        ];
                        continue;
                    } else {

                        $j = 0;

                        $total_width = 0;
                        $total_height = 0;
                        $total_length = 0;
                        $total_weight = 0;
                        $total_unit_price = 0;
                        $total_sub_total = 0;

                        foreach ($products as $product) {

                            $j++;

                            if (!isset($product['product_title'])) {
                                $message[$i][$j]['status'] = 422;
                                $message[$i][$j]['message'] = 'product_title not found';
                                $message[$i][$j]['data'] = (object)[];
                                continue;
                            } else {
                                $product_title[$i][$j] = $product['product_title'];
                            }

                            if (!isset($product['product_category'])) {
                                $message[$i][$j]['status'] = 422;
                                $message[$i][$j]['message'] = 'product_category not found';
                                $message[$i][$j]['data'] = (object)[];
                                continue;
                            } else {
                                $product_category[$i][$j] = $product['product_category'];
                            }

                            if (!isset($product['quantity'])) {
                                $message[$i][$j]['status'] = 422;
                                $message[$i][$j]['message'] = 'quantity not found';
                                $message[$i][$j]['data'] = (object)[];
                                continue;
                            } else {
                                $quantity[$i][$j] = $product['quantity'];
                            }

                            if (!isset($product['unit_price'])) {
                                $message[$i][$j]['status'] = 422;
                                $message[$i][$j]['message'] = 'unit_price not found';
                                $message[$i][$j]['data'] = (object)[];
                                continue;
                            } else {
                                $unit_price[$i][$j] = $product['unit_price'];
                            }

                            if (!isset($product['pickup_location'])) {
                                $message[$i][$j]['status'] = 422;
                                $message[$i][$j]['message'] = 'pickup_location not found';
                                $message[$i][$j]['data'] = (object)[];
                                continue;
                            } else {
                                $pickup_location = PickingLocations::whereStatus(true)
                                    ->where('title', $product['pickup_location'])
                                    ->where('merchant_id', Auth::guard('api')->user()->reference_id)
                                    ->first();
                                if ($pickup_location == null) {
                                    $message[$i][$j]['status'] = 422;
                                    $message[$i][$j]['message'] = 'pickup_location not valid';
                                    $message[$i][$j]['data'] = (object)[];
                                    continue;
                                } else {
                                    $pickup_location_id[$i][$j] = $pickup_location->id;
                                    $pickup_location_zone_id[$i][$j] = $pickup_location->zone_id;
                                    $pickup_location_hub_id[$i][$j] = $pickup_location->zone->hub_id;
                                }
                            }

                            if (isset($product['url'])) {
                                $url[$i][$j] = $product['url'];
                            } else {
                                $url[$i][$j] = '';
                            }

                            if (isset($product['weight'])) {
                                $weight[$i][$j] = $product['weight'];

                                if ($product['weight'] > 99.99) {
                                    $weight[$i][$j] = 99.99;
                                } else if ($product['weight'] < 0.1) {
                                    $weight[$i][$j] = 0.1;
                                }
                            } else {
                                $weight[$i][$j] = 0.1;
                            }

                            if (isset($product['width'])) {
                                $width[$i][$j] = $product['width'];
                            } else {
                                $width[$i][$j] = 0;
                            }

                            if (isset($product['height'])) {
                                $height[$i][$j] = $product['height'];
                            } else {
                                $height[$i][$j] = 0;
                            }

                            if (isset($product['length'])) {
                                $length[$i][$j] = $product['length'];
                            } else {
                                $length[$i][$j] = 0;
                            }

                            if (isset($product['picking_date'])) {
                                $picking_date[$i][$j] = $product['picking_date'];
                            } else {
                                $picking_date[$i][$j] = '';
                            }

                            $total_weight = $total_weight + $weight[$i][$j];
                            $total_width = $total_width + $width[$i][$j];
                            $total_height = $total_height + $height[$i][$j];
                            $total_length = $total_length + $length[$i][$j];
                            $total_unit_price = $total_unit_price + $unit_price[$i][$j];
                            $tmp_sub_total = $unit_price[$i][$j] * $quantity[$i][$j];
                            $total_sub_total = $total_sub_total + $tmp_sub_total;

                        }

                    }
                }

                if (isset($order['return']) && $order['return'] == 1) {
                    $return = 1;
                    $postDeliveryReturn = 1;
                } else {
                    $return = 0;
                    $postDeliveryReturn = 0;
                }

                // Validation End

                // Order Creation
                try {

                    DB::beginTransaction();

                    // return $delivery_zone_lat.",".$delivery_zone_lng;
                    $delivery_zone = getZoneBound($delivery_zone_lat, $delivery_zone_lng);

                    if ($delivery_zone == null) {
                        $message[$i] = [
                            'status' => 422,
                            'message' => 'Invalid delivery zone',
                            'data' => (object)[],
                        ];
                        continue;
                    } else {
                        $delivery_zone_id = $delivery_zone->id;
                        $delivery_city_id = $delivery_zone->city->id;
                        $delivery_state_id = $delivery_zone->city->state->id;
                        $delivery_country_id = $delivery_zone->city->state->country->id;
                        $delivery_hub_id = $delivery_zone->hub_id;
                    }

                    // Create Order
                    $new_order = new Order();
                    $new_order->created_by = Auth::guard('api')->user()->id;
                    $new_order->updated_by = Auth::guard('api')->user()->id;
                    $new_order->merchant_order_id = $merchant_order_id;
                    $new_order->delivery_address1 = $delivery_address;
                    $new_order->delivery_zone_id = $delivery_zone_id;
                    $new_order->delivery_city_id = $delivery_city_id;
                    $new_order->delivery_state_id = $delivery_state_id;
                    $new_order->delivery_country_id = $delivery_country_id;
                    $new_order->delivery_latitude = $delivery_zone_lat;
                    $new_order->delivery_longitude = $delivery_zone_lng;
                    $new_order->delivery_name = $delivery_name;
                    $new_order->delivery_email = $delivery_email;
                    $new_order->delivery_msisdn = $delivery_msisdn;
                    $new_order->order_remarks = $remarks;
                    $new_order->as_package = $as_package;
                    $new_order->unique_order_id = $this->newOrderId();
                    $new_order->store_id = $store->id;
                    $new_order->order_status = '1';
                    $new_order->delivery_pay_by_cus = $delivery_pay_by_cus;
                    $new_order->payment_type_id = $paymentType;
                    $new_order->save();

                    $this->orderLog(Auth::guard('api')->user()->id, $new_order->id, '', '', $new_order->id, 'orders', 'Created a new order: ' . $new_order->unique_order_id);

                    $message[$i]['status'] = 200;
                    $message[$i]['message'] = 'Success';
                    $message[$i]['data']['order_id'] = $new_order->unique_order_id;
                    $message[$i]['data']['merchant_order_id'] = $new_order->merchant_order_id;

                    // Create Sub-Order
                    $k = 0;

                    $total_payable_product_price = 0;
                    $total_delivery_charge = 0;
                    $total_payable_amount = 0;

                    if ($as_package == 0) {

                        foreach ($products as $product) {

                            $k++;

                            if (!isset($message[$i][$k])) {

                                // Call Charge Calculation API
                                $post = [
                                    'store_id' => $store->store_id,
                                    'width' => $width[$i][$k],
                                    'height' => $height[$i][$k],
                                    'length' => $length[$i][$k],
                                    'weight' => $weight[$i][$k],
                                    'product_category' => $product_category[$i][$k],
                                    'pickup_zone_id' => $pickup_location_zone_id[$i][$k],
                                    'delivery_zone_id' => $delivery_zone_id,
                                    'quantity' => $quantity[$i][$k],
                                    'unit_price' => $unit_price[$i][$k],
                                ];
//dd($post);
                                // return config("app.url").'api/charge-calculator';
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, config("app.url").'api/charge-calculator');
//                                curl_setopt($ch, CURLOPT_URL, 'http://localhost/ipost/public/api/charge-calculator');
//                                    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8484/api/v2/charge-calculator');
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                                $response = curl_exec($ch);
                                $charges = json_decode($response);
//                                dd($charges);
                                $charges = $charges[0];
//                                dd($charges,$charges['chargeDetails'], json_encode($charges['chargeDetails']));
                                if (isset($charges) && $charges->status == 'Failed') {
                                    $message[$i][$k] = [
                                        'status' => 422,
                                        'message' => "Charge calculation failed",
                                        'data' => (object)[],
                                    ];
                                    continue;
                                } else {

                                    $category = ProductCategory::whereStatus(true)->where('name', $product_category[$i][$k])->first();

                                    // Create Sub-Order
                                    $sub_order = new SubOrder();
                                    $sub_order->order_id = $new_order->id;

                                    if($return == 1){

                                        $sub_order->unique_suborder_id = 'R' . $new_order->unique_order_id . sprintf("%02d", $k);
                                        $sub_order->source_hub_id = $delivery_hub_id;
                                        $sub_order->next_hub_id = $pickup_location_hub_id[$i][$k];
                                        $sub_order->destination_hub_id = $pickup_location_hub_id[$i][$k];
                                        $sub_order->return = 1;
                                        $sub_order->post_delivery_return = $postDeliveryReturn;

                                        $sub_total = 0;
                                        $payable_product_price = 0;
                                        if($delivery_pay_by_cus == 1){
                                            $total_payable_amount = $payable_product_price + $charges->product_delivery_charge;
                                        }else{
                                            $total_payable_amount = $payable_product_price;
                                        }

                                    }else{

                                        $sub_order->unique_suborder_id = 'D' . $new_order->unique_order_id . sprintf("%02d", $k);
                                        $sub_order->source_hub_id = $pickup_location_hub_id[$i][$k];
                                        $sub_order->next_hub_id = $delivery_hub_id;
                                        $sub_order->destination_hub_id = $delivery_hub_id;

                                        $sub_total = $charges->product_total_price;
                                        $payable_product_price = $charges->product_total_price;
                                        if($delivery_pay_by_cus == 1){
                                            $total_payable_amount = $payable_product_price + $charges->product_delivery_charge;
                                        }else{
                                            $total_payable_amount = $payable_product_price;
                                        }

                                    }

                                    $sub_order->save();
                                    // Update Sub-Order Status
                                    $this->suborderStatus($sub_order->id, '2');

                                    // Create Product
                                    $order_product = new OrderProduct();
                                    $order_product->product_unique_id = $sub_order->unique_suborder_id;
                                    $order_product->product_category_id = $category->id;
                                    $order_product->order_id = $new_order->id;
                                    $order_product->sub_order_id = $sub_order->id;
                                    $order_product->pickup_location_id = $pickup_location_id[$i][$k];
                                    $order_product->picking_date = $picking_date[$i][$k];
                                    $order_product->product_title = $product_title[$i][$k];
                                    $order_product->url = $url[$i][$k];
                                    $order_product->unit_price = $unit_price[$i][$k];
                                    $order_product->quantity = $quantity[$i][$k];
                                    $order_product->sub_total = $sub_total;

                                    $order_product->unit_deivery_charge = $charges->product_unit_delivery_charge;
                                    $order_product->payable_product_price = $payable_product_price;
                                    $order_product->total_delivery_charge = $charges->product_delivery_charge;
                                    $order_product->delivery_pay_by_cus = $delivery_pay_by_cus;
                                    $order_product->total_payable_amount = $total_payable_amount;

                                    $order_product->delivery_paid_amount = 0;
                                    $order_product->width = $width[$i][$k];
                                    $order_product->height = $height[$i][$k];
                                    $order_product->length = $length[$i][$k];
                                    $order_product->weight = $weight[$i][$k];
                                    $order_product->status = 1;
                                    $order_product->charge_details = json_encode($charges['chargeDetails']);
                                    $order_product->save();

                                    $this->orderLog(Auth::guard('api')->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Individual item added: ' . $order_product->product_unique_id);
                                    $message[$i]['data'][$k]['status'] = 200;
                                    $message[$i]['data'][$k]['message'] = 'success';
                                    $message[$i]['data'][$k]['data']['product_id'] = $order_product->product_unique_id;
                                    $message[$i]['data'][$k]['data']['product_title'] = $order_product->product_title;
                                    $message[$i]['data'][$k]['data']['delivery_charge'] = $order_product->total_delivery_charge;

                                    $total_payable_product_price = $total_payable_product_price + $order_product->payable_product_price;
                                    $total_delivery_charge = $total_delivery_charge + $order_product->total_delivery_charge;

                                }

                            }

                        }

                    } else {

                        $k++;

                        // Call Charge Calculation API
                        $post = [
                            'store_id' => $store->store_id,
                            'width' => $total_width,
                            'height' => $total_height,
                            'length' => $total_length,
                            'weight' => $total_weight,
                            'product_category' => 'Bulk Product',
                            'pickup_zone_id' => $default_pickup_location_zone_id,
                            'delivery_zone_id' => $delivery_zone_id,
                            'quantity' => 1,
                            // 'unit_price' => $total_unit_price,
                            'unit_price' => $total_sub_total,
                        ];
                        // return config("app.url").'/api/charge-calculator';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, config("app.url") . 'api/charge-calculator');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                        $response = curl_exec($ch);
                        $charges = json_decode($response, true);
                        $charges = $charges[0];
                        if ($charges['status'] == 'Failed') {
                            $message[$i][$k] = [
                                'status' => 422,
                                'message' => "Charge calculation failed",
                                'data' => (object)[],
                            ];
                            continue;
                        } else {

                            // Create Sub-Order
                            $sub_order = new SubOrder();
                            $sub_order->order_id = $new_order->id;

                            if($return == 1){

                                $sub_order->unique_suborder_id = 'R' . $new_order->unique_order_id . sprintf("%02d", $k);
                                $sub_order->source_hub_id = $delivery_hub_id;
                                $sub_order->next_hub_id = $default_pickup_location_hub_id;
                                $sub_order->destination_hub_id = $default_pickup_location_hub_id;
                                $sub_order->return = 1;
                                $sub_order->post_delivery_return = $postDeliveryReturn;

                                $sub_total = 0;
                                $payable_product_price = 0;
                                if($delivery_pay_by_cus == 1){
                                    $total_payable_amount = $payable_product_price + $charges['product_delivery_charge'];
                                }else{
                                    $total_payable_amount = $payable_product_price;
                                }

                            }else{

                                $sub_order->unique_suborder_id = 'D' . $new_order->unique_order_id . sprintf("%02d", $k);
                                $sub_order->source_hub_id = $default_pickup_location_hub_id;
                                $sub_order->next_hub_id = $delivery_hub_id;
                                $sub_order->destination_hub_id = $delivery_hub_id;

                                $sub_total = $total_sub_total;
                                $payable_product_price = $charges['product_total_price'];
                                if($delivery_pay_by_cus == 1){
                                    $total_payable_amount = $payable_product_price + $charges['product_delivery_charge'];
                                }else{
                                    $total_payable_amount = $payable_product_price;
                                }

                            }

                            $sub_order->save();
                            // Update Sub-Order Status
                            $this->suborderStatus($sub_order->id, '2');

                            // Create Product
                            $order_product = new OrderProduct();
                            $order_product->product_unique_id = $sub_order->unique_suborder_id;
                            $order_product->product_category_id = 5;
                            $order_product->order_id = $new_order->id;
                            $order_product->sub_order_id = $sub_order->id;
                            $order_product->pickup_location_id = $default_pickup_location_id;
                            $order_product->picking_date = $default_picking_date;
                            $order_product->product_title = 'Package';
                            $order_product->unit_price = $total_sub_total;
                            $order_product->quantity = 1;
                            $order_product->sub_total = $total_sub_total;

                            $order_product->unit_deivery_charge = $charges['product_unit_delivery_charge'];
                            $order_product->payable_product_price = $payable_product_price;
                            $order_product->total_delivery_charge = $charges['product_delivery_charge'];
                            $order_product->delivery_pay_by_cus = $delivery_pay_by_cus;
                            $order_product->total_payable_amount = $total_payable_amount;

                            $order_product->delivery_paid_amount = 0;
                            $order_product->width = $total_width;
                            $order_product->height = $total_height;
                            $order_product->length = $total_length;
                            $order_product->weight = $total_weight;
                            $order_product->status = 1;
                            $order_product->charge_details = json_encode($charges['chargeDetails']);
                            $order_product->save();

                            // Cart Product
                            foreach ($products as $product) {

                                $cart_product_category = ProductCategory::where('name', $product['product_category'])->first();
                                if($cart_product_category){
                                    $cart_product_category_id = $cart_product_category->id;
                                }else{
                                    $new_cart_product_category = new ProductCategory;
                                    $new_cart_product_category->name = $product['product_category'];
                                    $new_cart_product_category->category_type = "parent";
                                    $new_cart_product_category->status = 1;
                                    $new_cart_product_category->created_by = Auth::guard('api')->user()->id;
                                    $new_cart_product_category->updated_by = Auth::guard('api')->user()->id;
                                    $new_cart_product_category->created_at = date('Y-m-d H:i:s');
                                    $new_cart_product_category->save();
                                    $cart_product_category_id = $new_cart_product_category->id;

                                    // DB::commit();
                                }

                                $cart_product = new CartProduct();
                                $cart_product->product_unique_id = $sub_order->unique_suborder_id;
                                $cart_product->product_category_id = $cart_product_category_id;
                                $cart_product->order_id = $new_order->id;
                                $cart_product->sub_order_id = $sub_order->id;
                                $cart_product->order_product_id = $order_product->id;
                                $cart_product->pickup_location_id = $default_pickup_location_id;
                                $cart_product->picking_date = $product['picking_date'];
                                $cart_product->product_title = $product['product_title'];
                                $cart_product->unit_price = $product['unit_price'];
                                $cart_product->quantity = $product['quantity'];
                                $cart_product->sub_total = $product['quantity'] * $product['unit_price'];
                                $cart_product->width = $product['width'];
                                $cart_product->height = $product['height'];
                                $cart_product->length = $product['length'];
                                $cart_product->weight = $product['weight'];
                                $cart_product->status = 1;
                                $cart_product->save();
                            }

                            $this->orderLog(Auth::guard('api')->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Individual item added: ' . $order_product->product_unique_id);
                            $message[$i]['data'][$k]['status'] = 200;
                            $message[$i]['data'][$k]['message'] = 'success';
                            $message[$i]['data'][$k]['data']['product_id'] = $order_product->product_unique_id;
                            $message[$i]['data'][$k]['data']['product_title'] = $order_product->product_title;
                            $message[$i]['data'][$k]['data']['delivery_charge'] = $order_product->total_delivery_charge;

                            $total_payable_product_price = $total_payable_product_price;
                            $total_delivery_charge = $total_delivery_charge;

                        }

                    }

                    $old_order = Order::where('id', $new_order->id)->first();
                    $old_order->total_product_price = $total_payable_product_price;
                    $old_order->collectable_product_price = $total_payable_product_price;
                    $old_order->delivery_payment_amount = $total_delivery_charge;
                    if ($total_delivery_charge != 0 && $verified == 1) {
                        $old_order->order_status = 2;
                        $old_order->verified_by = Auth::guard('api')->user()->id;

                        foreach ($old_order->suborders as $sub_order) {
                            // Update Sub-Order Status
                            $this->suborderStatus($sub_order->id, '2');
                        }
                    }
                    $old_order->save();

                    DB::commit();

                    // Call the microservice
                    $fcm_task = $this->fcm_task_req($sub_order->id);
                    $fcm_task = json_decode($fcm_task, true);

                    if(isset($fcm_task["status_code"]) && $fcm_task["status_code"] == 200){
                        Log::info("TmTask created for SubOrder: ".$sub_order->id);
                    }else{
                        Log::info("Failed to create TmTask for SubOrder: ".$sub_order->id);
                    }

                } catch (Exception $e) {

                    DB::rollback();

                }

            }

            $feedback['status_code'] = 200;
            $msg[] = "Operation done successfully";
            $feedback['message'] = $msg;
            $feedback['response'] = $message;

        } else {
            $feedback['status_code'] = 401;
            $message[] = "Order not found.";
            $feedback['message'] = $message;
        }

        return response($feedback, 200);

    }

    public function view_order(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'store_id' => 'required',
            'orders' => 'required'
        ]);

        if ($validation->fails()) {
            $status_code = 404;
            $message = $validation->errors()->all();
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        $message = array();

        $orders = json_decode($request->orders, true);

        if (count($orders) > 0) {

            $store = Store::whereStatus(true)->where('store_id', $request->store_id)->first();

            $tracked_orders = array();

            $i = 0;

            foreach ($orders as $o) {

                $i++;

                $order = Order::where('unique_order_id', $o)->where('store_id', $store->id)->first();
                if (count($order) > 0) {

                    if ($order->order_status == 1) {
                        $order_status = 'Not Approved';
                    } else if ($order->order_status >= 9) {
                        $order_status = 'Complete';
                    } else if ($order->order_status >= 8) {
                        $order_status = 'In Transit';
                    } else if ($order->order_status >= 5) {
                        $order_status = 'Picked';
                    } else if ($order->order_status > 1) {
                        $order_status = 'Verified';
                    }

                    $products = array();
                    if (count($order->suborders) > 0) {

                        foreach ($order->suborders as $sub_order) {

                            if ($sub_order->parent_sub_order_id == 0) {

                                if (isset($sub_order->suborder_last_status)) {
                                    $sub_order_status = $sub_order->suborder_last_status->title;
                                } else {
                                    $sub_order_status = $sub_order->suborder_status->title;
                                }

                                $product_title = $sub_order->product->product_title;
                                $product_quantity = $sub_order->product->quantity;
                                $product_delivery_charge = $sub_order->product->total_delivery_charge;
                                $product_total_payable_amount = $sub_order->product->total_payable_amount;

                                $history = array();
                                if (count($sub_order->history) > 0) {
                                    foreach ($sub_order->history as $h) {
                                        if ($h->type == "reference" || $h->type == "parent") {
                                            $history[] = $h->created_at . " : " . $h->text;
                                        }
                                    }
                                }

                                if ($sub_order->return == 0) {
                                    $type = "Delivery";
                                } else {
                                    $type = "Return";
                                }

                                $products[] = array(
                                    'product_id' => $sub_order->unique_suborder_id,
                                    'type' => $type,
                                    'status' => $sub_order_status,
                                    'product' => $product_title,
                                    'quantity' => $product_quantity,
                                    'delivery_charge' => $product_delivery_charge,
                                    'customer_payable_amount' => $product_total_payable_amount,
                                    'history' => array_reverse($history),
                                );
                            }

                        }

                    }

                    $tracked_orders[] = array(
                        'order_id' => $order->unique_order_id,
                        'merchant_order_id' => $order->merchant_order_id,
                        'customer_name' => $order->delivery_name,
                        'customer_msisdn' => $order->delivery_msisdn,
                        'customer_address' => $order->delivery_address1,
                        'status' => $order_status,
                        'products' => $products,
                    );

                }

            }

            $feedback['status_code'] = 200;
            $message[] = "Operation done successfully";
            $feedback['message'] = $message;
            $feedback['response'] = $tracked_orders;

        } else {
            $feedback['status_code'] = 401;
            $message[] = "No order found.";
            $feedback['message'] = $message;
        }

        return response($feedback, 200);

    }

    private function set_unauthorized($status_code, $message, $response)
    {
        $feedback = [];
        //$feedback['status']        =  $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
    }

}
