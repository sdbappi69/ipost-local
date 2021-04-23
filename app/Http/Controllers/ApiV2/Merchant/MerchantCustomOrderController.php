<?php

namespace App\Http\Controllers\ApiV2\Merchant;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\CreateOrderId;
use App\Http\Traits\ChargeCalculetorTrait;
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

class MerchantCustomOrderController extends Controller {

    use LogsTrait;
    use CreateOrderId;
    use ChargeCalculetorTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_order(Request $request) {
 
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

        if(count($orders) > 0){

            $store = Store::whereStatus(true)->where('store_id', $request->store_id)->first();

            $i = 0;

            foreach ($orders as $order) {

                $i++;

                // Validation Start

                if(!isset($order['delivery_name'])){
                    $message[$i] = 'delivery_name not found';
                    continue;
                }else{
                    $delivery_name = $order['delivery_name'];
                }

                if(!isset($order['delivery_email'])){
                    $message[$i] = 'delivery_email not found';
                    continue;
                }else{
                    $delivery_email = $order['delivery_email'];
                }

                if(!isset($order['delivery_msisdn'])){
                    $message[$i] = 'delivery_msisdn not found';
                    continue;
                }else{
                    $delivery_msisdn = $order['delivery_msisdn'];
                }

                if(!isset($order['delivery_address'])){
                    $message[$i] = 'delivery_address not found';
                    continue;
                }else{
                    $delivery_address = $order['delivery_address'];
                }

                if(!isset($order['merchant_order_id'])){
                    $message[$i] = 'merchant_order_id not found';
                    continue;
                }else{
                    $merchant_order_id = $order['merchant_order_id'];
                }

                if(!isset($order['remarks'])){
                    $remarks = '';
                }else{
                    $remarks = $order['remarks'];
                }

                if(isset($order['delivery_pay_by_cus']) && $order['delivery_pay_by_cus']==1){
                    $delivery_pay_by_cus = 1;
                }else{
                    $delivery_pay_by_cus = 0;
                }

                if(!isset($order['products'])){
                    $message[$i] = 'products not found';
                    continue;
                }else{
                    $products = $order['products'];
                    if(count($products) == 0){
                        $message[$i] = 'No products found';
                        continue;
                    }else{

                        $j = 0;

                        $total_width = 0;
                        $total_height = 0;
                        $total_length = 0;
                        $total_weight = 0;
                        $total_unit_price = 0;

                        foreach ($products as $product) {
                            
                            $j++;

                            if(!isset($product['product_title'])){
                                $message[$i][$j] = 'product_title not found';
                                continue;
                            }else{
                                $product_title[$i][$j] = $product['product_title'];
                            }

                            if(!isset($product['product_category'])){
                                $message[$i][$j] = 'product_category not found';
                                continue;
                            }else{
                                $product_category[$i][$j] = $product['product_category'];
                            }

                            if(!isset($product['quantity'])){
                                $message[$i][$j] = 'quantity not found';
                                continue;
                            }else{
                                $quantity[$i][$j] = $product['quantity'];
                            }

                            if(!isset($product['unit_price'])){
                                $message[$i][$j] = 'unit_price not found';
                                continue;
                            }else{
                                $unit_price[$i][$j] = $product['unit_price'];
                            }

                            if(!isset($product['pickup_location'])){
                                $message[$i][$j] = 'pickup_location not found';
                                continue;
                            }else{
                                $pickup_location = PickingLocations::whereStatus(true)
                                                ->where('title', $product['pickup_location'])
                                                ->where('merchant_id', Auth::guard('api')->user()->reference_id)
                                                ->first();
                                if(count($pickup_location) == 0){
                                    $message[$i][$j] = 'pickup_location not valid';
                                    continue;
                                }else{
                                   $pickup_location_id[$i][$j] = $pickup_location->id;
                                   $pickup_location_zone_id[$i][$j] = $pickup_location->zone_id;
                                   $pickup_location_hub_id[$i][$j] = $pickup_location->zone->hub_id;
                                }
                            }

                            if(isset($product['url'])){
                                $url[$i][$j] = $product['url'];
                            }else{
                                $url[$i][$j] = '';
                            }

                            if(isset($product['weight'])){
                                $weight[$i][$j] = $product['weight'];
                            }else{
                                $weight[$i][$j] = 0.1;
                            }

                            if(isset($product['width'])){
                                $width[$i][$j] = $product['width'];
                            }else{
                                $width[$i][$j] = 0;
                            }

                            if(isset($product['height'])){
                                $height[$i][$j] = $product['height'];
                            }else{
                                $height[$i][$j] = 0;
                            }

                            if(isset($product['length'])){
                                $length[$i][$j] = $product['length'];
                            }else{
                                $length[$i][$j] = 0;
                            }

                            if(isset($product['picking_date'])){
                                $picking_date[$i][$j] = $product['picking_date'];
                            }else{
                                $picking_date[$i][$j] = '';
                            }

                            $total_weight = $total_weight + $weight[$i][$j];
                            $total_width = $total_width + $width[$i][$j];
                            $total_height = $total_height + $height[$i][$j];
                            $total_length = $total_length + $length[$i][$j];
                            $total_unit_price = $total_unit_price + $unit_price[$i][$j];

                        }

                    }
                }

                // Validation End

                // Order Creation
                try {
                    
                    DB::beginTransaction();

                        // Create Order
                        $new_order = new Order();
                        $new_order->created_by = Auth::guard('api')->user()->id;
                        $new_order->updated_by = Auth::guard('api')->user()->id;
                        $new_order->merchant_order_id = $merchant_order_id;
                        $new_order->delivery_address1 = $delivery_address;
                        $new_order->delivery_name = $delivery_name;
                        $new_order->delivery_email = $delivery_email;
                        $new_order->delivery_msisdn = $delivery_msisdn;
                        $new_order->order_remarks = $remarks;
                        $new_order->unique_order_id = $this->newOrderId();
                        $new_order->store_id = $store->id;
                        $new_order->order_status = '1';
                        $new_order->delivery_pay_by_cus = $delivery_pay_by_cus;
                        $new_order->save();

                        $this->orderLog(Auth::guard('api')->user()->id, $new_order->id, '', '', $new_order->id, 'orders', 'Created a new order: ' . $new_order->unique_order_id);

                        $message[$i]['order_id'] = $new_order->unique_order_id;
                        $message[$i]['merchant_order_id'] = $new_order->merchant_order_id;

                        // Create Sub-Order
                        $k = 0;

                        $total_payable_product_price = 0;
                        $total_delivery_charge = 0;
                        $total_payable_amount = 0;

                        foreach ($products as $product) {
                            
                            $k++;

                            if(!isset($message[$i][$k])){

                                $category = ProductCategory::whereStatus(true)->where('name', $product_category[$i][$k])->first();

                                // Create Sub-Order
                                $sub_order = new SubOrder();
                                $sub_order->unique_suborder_id = 'D'.$new_order->unique_order_id.sprintf("%02d", $k);
                                $sub_order->order_id = $new_order->id;
                                $sub_order->source_hub_id = $pickup_location_hub_id[$i][$k];
                                $sub_order->save();
                                // Update Sub-Order Status
                                $this->suborderStatus($sub_order->id, '1');

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

                                $order_product->delivery_pay_by_cus = $delivery_pay_by_cus;

                                $order_product->delivery_paid_amount = 0;
                                $order_product->width = $width[$i][$k];
                                $order_product->height = $height[$i][$k];
                                $order_product->length = $length[$i][$k];
                                $order_product->weight = $weight[$i][$k];
                                $order_product->status = 1;
                                $order_product->save();

                                $cart_product = new CartProduct();
                                $cart_product->product_unique_id = $order_product->product_unique_id;
                                $cart_product->product_category_id = $order_product->product_category_id;
                                $cart_product->order_id = $order_product->order_id;
                                $cart_product->sub_order_id = $order_product->sub_order_id;
                                $cart_product->order_product_id = $order_product->id;
                                $cart_product->pickup_location_id = $order_product->pickup_location_id;
                                $cart_product->picking_date = $order_product->picking_date;
                                $cart_product->product_title = $order_product->product_title;
                                $cart_product->unit_price = $order_product->unit_price;
                                $cart_product->quantity = $order_product->quantity;
                                $cart_product->width = $order_product->width;
                                $cart_product->height = $order_product->height;
                                $cart_product->length = $order_product->length;
                                $cart_product->weight = $order_product->weight;
                                $cart_product->status = $order_product->status;
                                $cart_product->save();

                                $this->orderLog(Auth::guard('api')->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Individual item added: ' . $order_product->product_unique_id);
                                $message[$i][$k]['product_id'] = $order_product->product_unique_id;
                                $message[$i][$k]['product_title'] = $order_product->product_title;
                                $message[$i][$k]['delivery_charge'] = $order_product->total_delivery_charge;

                                $total_payable_product_price = $total_payable_product_price + $order_product->payable_product_price;
                                $total_delivery_charge = $total_delivery_charge + $order_product->total_delivery_charge;

                            }

                        }

                        $old_order = Order::where('id', $new_order->id)->first();
                        $old_order->total_product_price = $total_payable_product_price;
                        $old_order->collectable_product_price = $total_payable_product_price;
                        $old_order->delivery_payment_amount = $total_delivery_charge;
                        $old_order->save();

                    DB::commit();

                } catch (Exception $e) {
                    
                    DB::rollback();

                }

            }

            $feedback['status_code'] = 200;
            $msg[] = "Operation done successfully";
            $feedback['message'] = $msg;
            $feedback['response'] = $message;

        }else{
            $feedback['status_code'] = 401;
            $message[] = "Order not found.";
            $feedback['message'] = $message;
        }

        return response($feedback, 200);

    }

    private function set_unauthorized($status_code, $message, $response) {
        $feedback = [];
        //$feedback['status']        =  $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
    }

}
