<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Traits\LogsTrait;
use App\Http\Traits\CreateOrderId;
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
use App\DiscountLog;

use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Entrust;

use App\Merchant;
use App\User;

use App\Status;

use Excel;

class BulkOrderController extends Controller
{

    use LogsTrait;
    use CreateOrderId;

    public function bulk()
    {

        return view('merchant-orders.bulk');

    }

    public function bulk_submit(Request $request)
    {
        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'bulk_products' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        Excel::load($request->file('bulk_products'), function ($reader) {

            session_start();
            $_SESSION['bulk_msg'] = array();

            $sheets = $reader->toArray();
            $orders = $sheets[0];
            $input_products = $sheets[1];
//            dd($sheets);
            // $products_count = count($sheets) - 1;
            $order_count = count($orders);
            // print_r($orders); exit;
            // return $data = $sheets[0];

            // if($products_count != $order_count){

            //     session_start();
            //     $bulk_msg[] = "Failed!! Sheet mismatch.";
            //     $_SESSION['bulk_msg'] = $bulk_msg;
            //     // dd($_SESSION['bulk_msg']);
            //     header('Location: '.env('APP_URL').'merchant-order-bulk');
            //     exit;

            // }else{
            // $bulk_msg = array();
            $i = 1;
            foreach ($orders as $order) {

                $this->order_operation($order, $input_products, $i);

                $i++;
            }

            // }
            // $_SESSION['bulk_msg'] = $bulk_msg;
            header('Location: ' . env('APP_URL') . 'merchant-order-bulk');
            exit;

        });

    }

    public function order_operation($order, $input_products, $i)
    {

        $store_user = $order['store'];
        $merchant_order_id = $order['merchant_order_id'];
        $delivery_name = $order['delivery_name'];
        $delivery_email = $order['delivery_email'];
        $delivery_msisdn = $order['delivery_mobile'];
        $delivery_zone_lat = $order['delivery_zone_lat'];
        $delivery_zone_lng = $order['delivery_zone_lng'];
        if (isset($order['remarks'])) {
            $order_remarks = $order['remarks'];
        }

        if ($store_user == null) {
            $bulk_msg = "Order $i: No store defined";
            array_push($_SESSION['bulk_msg'], $bulk_msg);
        } else {

            $store = Store::whereStatus(true)->where('store_id', $store_user)->first();
            if (count($store) == 0) {
                $bulk_msg = "Order $i: Store not found";
                array_push($_SESSION['bulk_msg'], $bulk_msg);
            } else {

                $store_id = $store->id;
                $store_user_id = $store->store_id;

                $merchant = Merchant::whereStatus(true)->where('id', $store->merchant_id)->first();
                $merchant_id = $merchant->id;

                if ($delivery_name == null) {
                    $bulk_msg = "Order $i: No delivery name defined";
                    array_push($_SESSION['bulk_msg'], $bulk_msg);
                } else {

                    if ($delivery_email == null) {
                        $bulk_msg = "Order $i: No delivery email defined";
                        array_push($_SESSION['bulk_msg'], $bulk_msg);
                    } else {

                        if ($delivery_msisdn == null) {
                            $bulk_msg = "Order $i: No delivery mobile defined";
                            array_push($_SESSION['bulk_msg'], $bulk_msg);
                        } else {

                            if ($delivery_zone_lat == null or $delivery_zone_lng == null) {
                                $bulk_msg = "Order $i: delivery lat & lng required";
                                array_push($_SESSION['bulk_msg'], $bulk_msg);
                            } else {

                                $zone = getZoneBound($delivery_zone_lat, $delivery_zone_lng);
                                if (count($zone) == 0) {
                                    $bulk_msg = "Order $i: Zone invalid or not found";
                                    array_push($_SESSION['bulk_msg'], $bulk_msg);
                                } else {

                                    $delivery_zone_id = $zone->id;

                                    $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$delivery_zone_lat,$delivery_zone_lng&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM");
                                    $output = json_decode($geocode);
                                    $delivery_address = $output->results[0]->formatted_address;
//dd($delivery_address,$zone);
                                    $order = new Order();

                                    $order->merchant_order_id = $merchant_order_id;
                                    $order->store_id = $store_id;
                                    $order->delivery_name = $delivery_name;
                                    $order->delivery_email = $delivery_email;
                                    $order->delivery_msisdn = $delivery_msisdn;
                                    $order->delivery_country_id = $zone->city->state->country->id;
                                    $order->delivery_state_id = $zone->city->state->id;
                                    $order->delivery_city_id = $zone->city->id;
                                    $order->delivery_zone_id = $delivery_zone_id;
                                    $order->delivery_address1 = $delivery_address;
                                    $order->delivery_latitude = $delivery_zone_lat;
                                    $order->delivery_longitude = $delivery_zone_lng;

                                    if (isset($order_remarks)) {
                                        $order->order_remarks = $order_remarks;
                                    }

                                    $order->created_by = auth()->user()->id;
                                    $order->updated_by = auth()->user()->id;
                                    $order->unique_order_id = $this->newOrderId();
                                    $order->order_status = '1';
                                    $order->save();

                                    $bulk_msg = "Order $i: New Order Created - $order->unique_order_id";
                                    array_push($_SESSION['bulk_msg'], $bulk_msg);

                                    $j = 1;

                                    foreach ($input_products as $ip) {

                                        $this->product_operation($ip, $merchant_order_id, $merchant_id, $store_user_id, $delivery_zone_id, $order, $i, $j);

                                        $j++;

                                    }

                                }

                            }


                        }

                    }

                }

            }

        }

    }

    public function product_operation($ip, $merchant_order_id, $merchant_id, $store_user_id, $delivery_zone_id, $order, $i, $j)
    {

        if ($ip['merchant_order_id'] == $merchant_order_id) {

            $product_title = $ip['product_title'];
            $url = $ip['url'];
            $product_category_title = $ip['product_category'];
            $unit_price = $ip['unit_price'];
            $quantity = $ip['quantity'];
            $width = $ip['width'];
            $height = $ip['height'];
            $length = $ip['length'];
            $weight = $ip['weight'];
            $picking_location_title = $ip['picking_location'];
            $picking_date = $ip['picking_date'];
            $picking_time_slot_full = $ip['picking_time_slot'];

            if ($product_title == null) {
                $bulk_msg = "Product $i.$j: No product title defined";
                array_push($_SESSION['bulk_msg'], $bulk_msg);
            } else {

                if ($product_category_title == null) {
                    $bulk_msg = "Product $i.$j: No product category defined";
                    array_push($_SESSION['bulk_msg'], $bulk_msg);
                } else {

                    $product_category = ProductCategory::whereStatus(true)->where('name', $product_category_title)->first();

                    if (count($product_category) == 0) {
                        $bulk_msg = "Product $i.$j: Zone product category  not found";
                        array_push($_SESSION['bulk_msg'], $bulk_msg);
                    } else {

                        $product_category_id = $product_category->id;

                        // if($unit_price == null){
                        //     $bulk_msg[] = "Product $i.$j: No unit price defined";
                        // }else{

                        if ($quantity == null) {
                            $bulk_msg = "Product $i.$j: No quantity defined";
                            array_push($_SESSION['bulk_msg'], $bulk_msg);
                        } else {

                            if ($weight == null) {
                                $bulk_msg = "Product $i.$j: No weight defined";
                                array_push($_SESSION['bulk_msg'], $bulk_msg);
                            } else {

                                if ($picking_location_title == null) {
                                    $bulk_msg = "Product $i.$j: No picking location defined";
                                    array_push($_SESSION['bulk_msg'], $bulk_msg);
                                } else {

                                    $pickup_location = PickingLocations::whereStatus(true)->where('title', '=', $picking_location_title)->where('merchant_id', $merchant_id)->first();
                                    if (count($pickup_location) == 0) {
                                        $bulk_msg = "Product $i.$j: Picking location invalid or not found";
                                        array_push($_SESSION['bulk_msg'], $bulk_msg);
                                    } else {

                                        $pickup_location_id = $pickup_location->id;
                                        $hub_id = $pickup_location->zone->hub_id;

                                        if ($picking_date == null) {
                                            $bulk_msg = "Product $i.$j: No picking date defined";
                                            array_push($_SESSION['bulk_msg'], $bulk_msg);
                                        } else {

                                            $day = date('D', strtotime($picking_date));
                                            if ($picking_time_slot_full == null) {
                                                $bulk_msg = "Product $i.$j: No picking time slot defined";
                                                array_push($_SESSION['bulk_msg'], $bulk_msg);
                                            } else {

                                                $split_picking_time_slot = explode(' - ', $picking_time_slot_full);

                                                $picking_time_slot = PickingTimeSlot::where('day', $day)->where('start_time', $split_picking_time_slot[0])->where('end_time', $split_picking_time_slot[1])->first();

                                                if (count($picking_time_slot) == 0) {
                                                    $bulk_msg = "Product $i.$j: Picking time slot invalid or not found";
                                                    array_push($_SESSION['bulk_msg'], $bulk_msg);
                                                } else {
                                                    $picking_time_slot_id = $picking_time_slot->id;
                                                    // Call Charge Calculation API
                                                    $post = [
                                                        'store_id' => $store_user_id,
                                                        'width' => $width,
                                                        'height' => $height,
                                                        'length' => $length,
                                                        'weight' => $weight,
                                                        'product_category' => $product_category->name,
                                                        'pickup_zone_id' => $pickup_location->zone_id,
                                                        'delivery_zone_id' => $delivery_zone_id,
                                                        'quantity' => $quantity,
                                                        'unit_price' => $unit_price,
                                                    ];
                                                    // dd($post);
                                                    // return env('APP_URL').'/api/charge-calculator';
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, config("app.url") . 'api/charge-calculator');
                                                    // $ch = curl_init(env('APP_URL').'api/charge-calculator');
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                                                    $response = curl_exec($ch);
                                                    $charges = json_decode($response);
                                                    // if($order->merchant_order_id == '675978'){
                                                    //     print_r($charges);
                                                    // }
//                                                    $charges = $charges[0];
                                                    // dd($charges);
                                                    // echo $order->merchant_order_id.'-'.$charges->status;
                                                    if ($charges) {

                                                        if ($charges->status == 'Failed') {
                                                            $bulk_msg = "Product $i.$j: Product failed to save";
                                                            array_push($_SESSION['bulk_msg'], $bulk_msg);
                                                        } else {

                                                            // Product Unique Id
                                                            $product_info = CartProduct::where('order_id', $order->id)->orderBy('id', 'desc')->first();
                                                            if (count($product_info) == 0) {
                                                                $product_unique_id = 'D' . $order->unique_order_id . '01';
                                                                $unique_suborder_id = 'D' . $order->unique_order_id . '01';
                                                            } else {
                                                                // $split_last_product_unique_id = explode('-P', $product_info->product_unique_id);
                                                                // $product_unique_id = $split_last_product_unique_id[0]."-P".($split_last_product_unique_id[1]+1);
                                                                // $unique_suborder_id = $split_last_product_unique_id[0]."-D".($split_last_product_unique_id[1]+1);

                                                                $last_sub_order_number = substr($product_info->product_unique_id, -2);
                                                                $product_unique_id = substr($product_info->product_unique_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);
                                                                $unique_suborder_id = substr($product_info->product_unique_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);
                                                            }

                                                            $product = new CartProduct();
                                                            $product->picking_attempts = '0';
                                                            $product->product_category_id = $product_category_id;
                                                            $product->order_id = $order->id;
                                                            $product->pickup_location_id = $pickup_location_id;
                                                            $product->picking_date = $picking_date;
                                                            $product->picking_time_slot_id = $picking_time_slot_id;
                                                            $product->product_title = $product_title;
                                                            $product->unit_price = $unit_price;
                                                            $product->quantity = $quantity;
                                                            $product->width = $width;
                                                            $product->height = $height;
                                                            $product->length = $length;
                                                            $product->weight = $weight;
                                                            $product->url = $url;
                                                            $product->status = '1';
                                                            $product->sub_total = $unit_price * $quantity;
                                                            // $product->product_unique_id = "P".time().rand(10,99);
                                                            $product->product_unique_id = $product_unique_id;
                                                            $product->created_by = auth()->user()->id;
                                                            $product->updated_by = auth()->user()->id;
                                                            // $product->sub_order_id = $sub_order->id;

                                                            $product->unit_deivery_charge = $charges->product_unit_delivery_charge;
                                                            $product->payable_product_price = $charges->product_total_price;
                                                            $product->total_delivery_charge = $charges->product_delivery_charge;
                                                            $product->delivery_pay_by_cus = '1';
                                                            $product->total_payable_amount = $charges->product_total_price + $charges->product_delivery_charge;
                                                            $product->delivery_paid_amount = '0';

                                                            if ($product->save()) {

                                                                $bulk_msg = "Product $i.$j: Product saved successfully";
                                                                array_push($_SESSION['bulk_msg'], $bulk_msg);

                                                                // Discount Log
                                                                if ($charges->product_discount != 0) {
                                                                    $discount_log = new DiscountLog();
                                                                    $discount_log->product_unique_id = $product->product_unique_id;
                                                                    $discount_log->discount_id = $charges->delivery_discount_id;
                                                                    $discount_log->order_id = $product->order_id;
                                                                    $discount_log->unit_actual_charge = $charges->product_actual_unit_delivery_charge;
                                                                    $discount_log->unit_discount = $charges->product_unit_discount;
                                                                    $discount_log->unit_payable_charge = $charges->product_unit_delivery_charge;
                                                                    $discount_log->quantity = $product->quantity;
                                                                    $discount_log->total_actual_charge = $charges->product_actual_delivery_charge;
                                                                    $discount_log->total_discount = $charges->product_discount;
                                                                    $discount_log->total_payable_charge = $charges->product_delivery_charge;
                                                                    $discount_log->created_by = auth()->user()->id;
                                                                    $discount_log->updated_by = auth()->user()->id;
                                                                    $discount_log->save();
                                                                }

                                                                //Order Update
                                                                $order_update = Order::where('id', $order->id)->first();
                                                                $order_update->hub_id = $hub_id;
                                                                $order_update->delivery_pay_by_cus = '0';
                                                                $total_product_price = 0;
                                                                $delivery_payment_amount = 0;
                                                                foreach ($order_update->cart_products as $p) {
                                                                    $total_product_price = $total_product_price + $p->sub_total;
                                                                    $delivery_payment_amount = $delivery_payment_amount + $p->total_delivery_charge;
                                                                }
                                                                if ($total_product_price != 0) {
                                                                    $collectable_product_price = $total_product_price;
                                                                    $percent_of_collection = ($collectable_product_price / $total_product_price) * 100;

                                                                    $order_update->total_product_price = $total_product_price;
                                                                    $order_update->collectable_product_price = $collectable_product_price;
                                                                    $order_update->percent_of_collection = $percent_of_collection;
                                                                }
                                                                $order_update->delivery_payment_amount = $delivery_payment_amount;
                                                                $order_update->order_status = '1';
                                                                $order_update->save();

                                                                // Order Product
                                                                // $products = CartProduct::whereStatus(true)->where('order_id', '=', $order->id)->get();
                                                                // if (count($products) != 0) {
                                                                // return 1;
                                                                // OrderProduct::where('order_id', '=', $order->id)->delete();

                                                                // SubOrder::where('order_id', '=', $order->id)->delete();

                                                                // $k = 1;
                                                                // foreach ($products as $product) {

                                                                // Create Sub-Order
                                                                $sub_order = new SubOrder();
                                                                // $sub_order->unique_suborder_id = $order->unique_order_id . "-D" . $k;
                                                                $sub_order->unique_suborder_id = $unique_suborder_id;
                                                                $sub_order->order_id = $order->id;
                                                                $sub_order->save();

                                                                $order_product = new OrderProduct();
                                                                $order_product->product_unique_id = $product->product_unique_id;
                                                                $order_product->product_category_id = $product->product_category_id;
                                                                $order_product->order_id = $product->order_id;
                                                                $order_product->sub_order_id = $sub_order->id;
                                                                $order_product->pickup_location_id = $product->pickup_location_id;
                                                                $order_product->picking_date = $product->picking_date;
                                                                $order_product->picking_time_slot_id = $product->picking_time_slot_id;
                                                                $order_product->product_title = $product->product_title;
                                                                $order_product->image = $product->image;
                                                                $order_product->unit_price = $product->unit_price;
                                                                $order_product->unit_deivery_charge = $product->unit_deivery_charge;
                                                                $order_product->quantity = $product->quantity;
                                                                $order_product->sub_total = $product->sub_total;
                                                                $order_product->payable_product_price = $product->payable_product_price;
                                                                $order_product->total_delivery_charge = $product->total_delivery_charge;

                                                                $order_product->delivery_pay_by_cus = '0';
                                                                $order_product->total_payable_amount = $product->payable_product_price;

                                                                $order_product->delivery_paid_amount = $product->delivery_paid_amount;
                                                                $order_product->width = $product->width;
                                                                $order_product->height = $product->height;
                                                                $order_product->length = $product->length;
                                                                $order_product->weight = $product->weight;
                                                                $order_product->url = $product->url;
                                                                $order_product->status = $product->status;
                                                                $order_product->save();

                                                                // return $order_product->id;

                                                                $cart_product = CartProduct::findOrFail($product->id);
                                                                $cart_product->order_product_id = $order_product->id;
                                                                $cart_product->save();

                                                                // $k++;
                                                                // }

                                                                // $products = OrderProduct::where('order_id', '=', $order->id)->orderBy('id', 'desc')->get();

                                                                $bulk_msg = "Product $i.$j: Product pricing completed";
                                                                array_push($_SESSION['bulk_msg'], $bulk_msg);

                                                                // }

                                                            } else {

                                                                $bulk_msg = "Product $i.$j: Product failed to save";
                                                                array_push($_SESSION['bulk_msg'], $bulk_msg);

                                                            }

                                                        }

                                                    } else {

                                                        $bulk_msg = "Product $i.$j: Product failed to save";
                                                        array_push($_SESSION['bulk_msg'], $bulk_msg);

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                        // }

                    }

                }

            }

        }

    }

}
