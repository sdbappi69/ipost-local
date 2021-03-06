<?php

namespace App\Http\Controllers\Api\Merchant;

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
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Entrust;
use App\Merchant;
use App\User;
use App\OrderLog;

class MerchantOrderController extends Controller {

    use LogsTrait;
    use CreateOrderId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('role:merchantadmin|merchantsupport|storeadmin');
//    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_order(Request $request) {
        //die();
        $query = Order::whereStatus(true)->where('order_status', '>', '1');
        if (Auth::guard('api')->user()->user_type_id == 12) {
            $query->where('store_id', '=', Auth::guard('api')->user()->reference_id);
        } else {
            $query->whereHas('store', function($q) {
                $q->where('merchant_id', '=', Auth::guard('api')->user()->reference_id);
            });
        }

        if ($request->has('sub_order_id')) {
            $query->where('id', '=', function($q) use ($request) {
                $q->from('sub_orders')
                        ->selectRaw('order_id')
                        ->where('unique_suborder_id', $request->sub_order_id);
            });
        }

        if ($request->has('pickup_man_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->from('order_product')
                        ->selectRaw('order_id')
                        ->where('picker_id', $request->pickup_man_id)->lists('order_id');
            });
        }

        if ($request->has('delivary_man_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->from('sub_orders')
                        ->selectRaw('order_id')
                        ->where('deliveryman_id', $request->delivary_man_id)->lists('order_id');
            });
        }

        if ($request->has('order_id')) {
            $query->where('orders.unique_order_id', trim($request->order_id));
        }

        ( $request->has('order_status') ) ? $query->where('orders.order_status', trim($request->order_status)) : null;
        ( $request->has('customer_mobile_no') ) ? $query->where('orders.delivery_msisdn', 'like', '%' . $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', 'like', '%' . $request->customer_mobile_no) : null;
        ( $request->has('store_id') ) ? $query->where('orders.store_id', $request->store_id) : null;
        ( $request->has('merchant_order_id') ) ? $query->where('orders.merchant_order_id', 'like', '%' . $request->merchant_order_id) : null;

        ( $request->has('search_date') ) ? $query->whereDate('orders.created_at', '=', $request->search_date) : null;

        $orders = $query->orderBy('id', 'desc')->get();
        /////
        // $stores = Store::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->lists('store_id', 'id')->toArray();

        // $order_status = ['1' => 'Verified', '5' => 'Picked', '8' => 'In Transit', '9' => 'Delivered'];
        //$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();


        // return view('merchant-orders.index', compact('stores', 'orders', 'order_status'));

        if ($orders->count() > 0) {
            $feedback['status_code'] = 200;
            $message[] = "Order found.";
            $feedback['message'] = $message;
            $feedback['response']['orders'] = $orders;
        } else {
            $status_code = 404;
            $message[] = "Order not found.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }
        return response($feedback, 200);
    }

    public function draft_order(Request $request) {
        //die();
        $query = Order::whereStatus(true)->where('order_status', '=', '1');
        if (Auth::guard('api')->user()->user_type_id == 12) {
            $query->where('store_id', '=', Auth::guard('api')->user()->reference_id);
        } else {
            $query->whereHas('store', function($q) {
                $q->where('merchant_id', '=', Auth::guard('api')->user()->reference_id);
            });
        }

        if ($request->has('sub_order_id')) {
            $query->where('id', '=', function($q) use ($request) {
                $q->from('sub_orders')
                        ->selectRaw('order_id')
                        ->where('unique_suborder_id', $request->sub_order_id);
            });
        }

        if ($request->has('pickup_man_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->from('order_product')
                        ->selectRaw('order_id')
                        ->where('picker_id', $request->pickup_man_id)->lists('order_id');
            });
        }

        if ($request->has('delivary_man_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->from('sub_orders')
                        ->selectRaw('order_id')
                        ->where('deliveryman_id', $request->delivary_man_id)->lists('order_id');
            });
        }

        if ($request->has('order_id')) {
            $query->where('orders.unique_order_id', trim($request->order_id));
        }

        ( $request->has('order_status') ) ? $query->where('orders.order_status', trim($request->order_status)) : null;
        ( $request->has('customer_mobile_no') ) ? $query->where('orders.delivery_msisdn', 'like', '%' . $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', 'like', '%' . $request->customer_mobile_no) : null;
        ( $request->has('store_id') ) ? $query->where('orders.store_id', $request->store_id) : null;
        ( $request->has('merchant_order_id') ) ? $query->where('orders.merchant_order_id', 'like', '%' . $request->merchant_order_id) : null;

        ( $request->has('search_date') ) ? $query->whereDate('orders.created_at', '=', $request->search_date) : null;

        $orders = $query->orderBy('id', 'desc')->get();
        /////
        // $stores = Store::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->lists('store_id', 'id')->toArray();

        // $order_status = ['1' => 'Verified', '5' => 'Picked', '8' => 'In Transit', '9' => 'Delivered'];
        //$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();


        // return view('merchant-orders.index', compact('stores', 'orders', 'order_status'));

        if ($orders->count() > 0) {
            $feedback['status_code'] = 200;
            $message[] = "Order found.";
            $feedback['message'] = $message;
            $feedback['response']['orders'] = $orders;
        } else {
            $status_code = 404;
            $message[] = "Order not found.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }
        return response($feedback, 200);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        if (Auth::user()->hasRole('storeadmin')) {
            $stores = Store::whereStatus(true)
                    ->where('id', '=', Auth::guard('api')->user()->reference_id)
                    ->lists('store_id', 'id')
                    ->toArray();
        } else {
            $stores = Store::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->lists('store_id', 'id')->toArray();
        }

        return view('merchant-orders.insert', compact('prefix', 'countries', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_order(Request $request) {
        $message = array();
        $validation = Validator::make($request->all(), [
                    'store_id' => 'required',
                    'delivery_name' => 'required',
                    'delivery_email' => 'sometimes|email',
                    'delivery_msisdn' => 'required|between:10,25',
                    'delivery_alt_msisdn' => 'sometimes|between:10,25',
                    'delivery_zone_id' => 'required',
                    'delivery_address1' => 'required',
                    'delivery_latitude' => 'sometimes',
                    'delivery_longitude' => 'sometimes',
                    'merchant_order_id' => 'required'
        ]);

        if ($validation->fails()) {
            $status_code = 404;
            $message = $validation->errors()->all();
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        $order = new Order();
        $order->fill($request->except('api_token', 'msisdn_country', 'alt_msisdn_country'));
        
        // Zone->Other
        $zone = Zone::findOrFail($request->delivery_zone_id);
        $order->delivery_city_id = $zone->city_id;
        $order->delivery_state_id = $zone->city->state_id;
        $order->delivery_country_id = $zone->city->state->country_id;

        $order->created_by = Auth::guard('api')->user()->id;
        $order->updated_by = Auth::guard('api')->user()->id;
        $order->unique_order_id = $this->newOrderId();

        $order->order_status = '1';

        if ($order->save()) {
            $feedback['status_code'] = 200;
            $message[] = 'Order added successfully.';
            $feedback['message'] = $message;
            $feedback['response'] = ['order_id' => $order->id];
        } else {
            $status_code = 500;
            $message[] = "Order added failed.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(Auth::guard('api')->user()->id, $order->id, '', '', $order->id, 'orders', 'Created a new order: ' . $order->unique_order_id);

        return response($feedback, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $order = Order::whereStatus(true)->findOrFail($id);

        if (Auth::user()->hasRole('storeadmin')) {
            if ($order->store_id != Auth::guard('api')->user()->reference_id) {
                abort(403);
            }
        } else {
            if ($order->store->merchant_id != Auth::guard('api')->user()->reference_id) {
                abort(403);
            }
        }
        return view('merchant-orders.view', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit_order(Request $request, $id) {
        // return $id;
        $order = Order::select(array(
                    'orders.merchant_order_id',
                    'orders.id',
                    'orders.unique_order_id',
                    'orders.store_id',
                    'orders.delivery_name',
                    'orders.delivery_email',
                    'orders.delivery_msisdn',
                    'orders.delivery_address1',
                    'orders.delivery_zone_id',
                    'orders.delivery_city_id',
                    'orders.delivery_state_id',
                    'orders.delivery_country_id',
                    'orders.delivery_latitude',
                    'orders.delivery_longitude',
                    'orders.as_package',
                    'orders.delivery_pay_by_cus',
                    'orders.status',
                    'stores.store_id AS store_name',
                    'stores.merchant_id',
                ))
                ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
                ->where('orders.id', '=', $id)
                ->where('orders.status', '<', 2)
                ->firstOrFail();
        DB::enableQueryLog();

        Auth::user();

        if (Auth::guard('api')->user()->user_type_id == 12) {
            if ($order->store_id != Auth::guard('api')->user()->reference_id) {
                abort(403);
            }
        }else{
            if ($order->merchant_id != Auth::guard('api')->user()->reference_id) {
                abort(403);
            }
        }
        $products = CartProduct::select('id','product_unique_id','product_category_id','order_id','order_product_id','pickup_location_id','picking_date','picking_time_slot_id','picking_attempts','picking_status','product_title','unit_price','unit_deivery_charge','quantity','sub_total','payable_product_price','total_delivery_charge','total_payable_amount','width','height','length','weight','url','created_at')->where('order_id', '=', $id)->orderBy('id', 'desc')->get();

        if ($order->count() > 0) {
            $feedback['status_code'] = 200;
            $message[] = "Order found.";
            $feedback['message'] = $message;
            $feedback['response']['products'] = $products;
            $feedback['response']['order'] = $order;
        } else {
            $status_code = 404;
            $message[] = "Order not found.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }
        return response($feedback, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_order(Request $request, $id) {
        
        $validation = Validator::make($request->all(), [
                    'store_id' => 'sometimes',
                    'delivery_name' => 'sometimes',
                    'delivery_email' => 'sometimes|email',
                    'delivery_msisdn' => 'sometimes|between:10,25',
                    'delivery_zone_id' => 'sometimes',
                    'delivery_address1' => 'sometimes',
                    'payable_product_price' => 'sometimes',
                    'delivery_payment_amount' => 'sometimes',
                    'merchant_order_id' => 'sometimes',
                    'delivery_pay_by_cus' => 'sometimes',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $order_update = Order::findOrFail($id);
        $order_update->fill($request->except('msisdn_country', 'alt_msisdn_country', 'step', 'include_delivery', 'amount_hidden', 'amount', 'pickup_location_id', 'weight', 'width', 'height', 'length', 'picking_date', 'picking_time_slot_id', 'api_token', 'unit_price', 'payable_product_price'));

        if ($request->delivery_pay_by_cus && $request->delivery_pay_by_cus == '1') {
            // return 1;
            $order_update->delivery_pay_by_cus = $request->delivery_pay_by_cus;
        }

        $total_product_price = 0;
        $delivery_payment_amount = 0;
        foreach ($order_update->cart_products as $p) {
            $total_product_price = $total_product_price + $p->sub_total;
            $delivery_payment_amount = $delivery_payment_amount + $p->total_delivery_charge;
        }
        // return $total_product_price;
        if($request->payable_product_price && $total_product_price != 0){
            $collectable_product_price = $request->payable_product_price;
            $percent_of_collection = ($collectable_product_price / $total_product_price) * 100;

            $order_update->total_product_price = $total_product_price;
            $order_update->collectable_product_price = $collectable_product_price;
            $order_update->percent_of_collection = $percent_of_collection;
            $order_update->delivery_payment_amount = $delivery_payment_amount;
        }
        
        $order_update->order_status = '1';

        // Zone->Other
        if($request->has('delivery_zone_id')){
            $zone = Zone::findOrFail($request->delivery_zone_id);
            $order_update->delivery_city_id = $zone->city_id;
            $order_update->delivery_state_id = $zone->city->state_id;
            $order_update->delivery_country_id = $zone->city->state->country_id;
        }

        $order_update->save();

        // Update Products
        $order = Order::where('orders.id', '=', $id)->first();
        if(count($order->cart_products) > 0){
            foreach ($order->cart_products as $row) {
                if($request->payable_product_price && $total_product_price != 0){
                    $payable_product_price = ($percent_of_collection / 100) * $row->sub_total;
                }else{
                    $payable_product_price = $row->sub_total;
                }
                
                $product = CartProduct::findOrFail($row->id);
                $product->payable_product_price = $payable_product_price;
                $product->total_payable_amount = $payable_product_price + $row->total_delivery_charge;
                $product->save();

                // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                $this->orderLog(Auth::guard('api')->user()->id, $row->order_id, $row->sub_order_id, $row->id, $row->id, 'cart_product', 'Updated payable product price & total payable amount');
            }
        }

        $order_view = Order::select(array(
            'orders.merchant_order_id',
            'orders.id',
            'orders.unique_order_id',
            'orders.store_id',
            'orders.delivery_name',
            'orders.delivery_email',
            'orders.delivery_msisdn',
            'orders.delivery_address1',
            'orders.delivery_zone_id',
            'orders.delivery_city_id',
            'orders.delivery_state_id',
            'orders.delivery_country_id',
            'orders.delivery_latitude',
            'orders.delivery_longitude',
            'orders.as_package',
            'orders.delivery_pay_by_cus',
            'orders.status',
            'stores.store_id AS store_name',
            'stores.merchant_id',
        ))
        ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
        ->where('orders.id', '=', $id)
        ->where('orders.status', '<', 2)
        ->firstOrFail();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(Auth::guard('api')->user()->id, $order_update->id, '', '', $order_update->id, 'orders', 'Updated Order Information');
        if ($request->step) {
            if ($request->step == 'complete') {
                $as_package = 0;
                if($request->as_package){
                    $as_package = $request->as_package;
                }
                if ($as_package == 0) {

                    $products = CartProduct::whereStatus(true)->where('order_id', '=', $id)->get();
                    if (count($products) != 0) {
                        // return 1;
                        OrderProduct::where('order_id', '=', $id)->delete();

                        SubOrder::where('order_id', '=', $id)->delete();

                        $i = 1;
                        foreach ($products as $product) {

                            // Create Sub-Order
                            $sub_order = new SubOrder();
                            $sub_order->unique_suborder_id = 'D'.$order->unique_order_id.sprintf("%02d", $i);
                            $sub_order->order_id = $order->id;
                            $sub_order->save();

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            $this->orderLog(Auth::guard('api')->user()->id, $order->id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: ' . $sub_order->unique_suborder_id);

                            $order_product = new OrderProduct();
                            $order_product->product_unique_id = $product->product_unique_id;
                            $order_product->product_category_id = $product->product_category_id;
                            $order_product->order_id = $product->order_id;
                            // $order_product->sub_order_id = $product->sub_order_id;
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

                            if ($request->delivery_pay_by_cus && $request->delivery_pay_by_cus == '1') {
                                // return 1;
                                $delivery_pay_by_cus = 1;
                                $order_product->delivery_pay_by_cus = $request->delivery_pay_by_cus;
                                $order_product->total_payable_amount = $product->payable_product_price + $product->total_delivery_charge;
                            } else {
                                // return 0;
                                $delivery_pay_by_cus = 0;
                                $order_product->delivery_pay_by_cus = '0';
                                $order_product->total_payable_amount = $product->payable_product_price;
                            }

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

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            $this->orderLog(Auth::guard('api')->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Individual item added: ' . $order_product->product_unique_id);

                            $i++;
                        }

                        $orderUpdate = Order::whereStatus(true)->findOrFail($order_view->id);
                        $orderUpdate->delivery_pay_by_cus = $delivery_pay_by_cus;
                        $orderUpdate->save();

                        $products = OrderProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();

                        if ($order_update) {
                            $feedback['status_code'] = 200;
                            $message[] = "Order updated.";
                            $feedback['message'] = $message;
                            $feedback['response']['products'] = $products;
                            $feedback['response']['order'] = $order_view;
                        } else {
                            $status_code = 503;
                            $message[] = "Order added failed.";
                            return $this->set_unauthorized($status_code, $message, $response = '');
                        }

                        return response($feedback, 200);
                    }
                }else if ($as_package == 1) {
                    // return 'ok';
                    OrderProduct::where('order_id', '=', $id)->delete();

                    SubOrder::where('order_id', '=', $id)->delete();

                    // $sub_order = SubOrder::where('order_id', '=', $id)->first();
                    // Create Sub-Order
                    $sub_order = new SubOrder();
                    $sub_order->unique_suborder_id = $order->unique_order_id . "-D1";
                    $sub_order->order_id = $order->id;
                    $sub_order->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(Auth::guard('api')->user()->id, $order->id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: ' . $sub_order->unique_suborder_id);

                    $order_product = new OrderProduct();
                    $order_product->product_unique_id = $order->unique_order_id . "-P1";
                    $order_product->product_category_id = 5;
                    $order_product->order_id = $order->id;
                    $order_product->sub_order_id = $sub_order->id;
                    $order_product->pickup_location_id = $request->pickup_location_id;
                    $order_product->picking_date = $request->picking_date;
                    $order_product->picking_time_slot_id = $request->picking_time_slot_id;
                    $order_product->product_title = 'Bulk Products';
                    $order_product->unit_price = $request->unit_price;
                    $order_product->unit_deivery_charge = $request->delivery_payment_amount;
                    $order_product->quantity = 1;
                    $order_product->sub_total = $request->unit_price;
                    $order_product->payable_product_price = $request->payable_product_price;
                    $order_product->total_delivery_charge = $request->delivery_payment_amount;

                    if ($request->delivery_pay_by_cus && $request->delivery_pay_by_cus == '1') {
                        $delivery_pay_by_cus = 1;
                        $order_product->delivery_pay_by_cus = $request->delivery_pay_by_cus;
                        $order_product->total_payable_amount = $request->amount + $request->delivery_payment_amount;
                    } else {
                        $delivery_pay_by_cus = 0;
                        $order_product->delivery_pay_by_cus = '0';
                        $order_product->total_payable_amount = $request->amount;
                    }

                    $order_product->width = $request->width;
                    $order_product->height = $request->height;
                    $order_product->length = $request->length;
                    $order_product->weight = $request->weight;
                    $order_product->status = 1;
                    $order_product->save();

                    $products = CartProduct::whereStatus(true)->where('order_id', '=', $id)->get();
                    if (count($products) != 0) {
                        foreach ($products as $product) {
                            $cart_product = CartProduct::findOrFail($product->id);
                            $cart_product->order_product_id = $order_product->id;
                            $cart_product->save();
                        }
                    }

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(Auth::guard('api')->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Updated as bulk product');

                    // Hub Id
                    $pickup_location_data = PickingLocations::whereStatus(true)->findOrFail($order_product->pickup_location_id);
                    $orderUpdate = Order::whereStatus(true)->findOrFail($order_product->order_id);
                    $orderUpdate->hub_id = $pickup_location_data->zone->hub_id;
                    $orderUpdate->delivery_pay_by_cus = $delivery_pay_by_cus;
                    $orderUpdate->save();

                    $products = OrderProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();

                    if ($order_update) {
                        $feedback['status_code'] = 200;
                        $message[] = "Order updated.";
                        $feedback['message'] = $message;
                        $feedback['response']['products'] = $products;
                        $feedback['response']['order'] = $order_view;
                    } else {
                        $status_code = 503;
                        $message[] = "Order added failed.";
                        return $this->set_unauthorized($status_code, $message, $response = '');
                    }

                    return response($feedback, 200);
                }
            } else {
                $step = $request->step;

                $products = CartProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();
            }
        } else {
            $step = 1;

            $products = CartProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();
        }

        if ($order_update) {
            $feedback['status_code'] = 200;
            $message[] = "Order updated.";
            $feedback['message'] = $message;
            $feedback['response']['products'] = $products;
            $feedback['response']['order'] = $order_view;
        } else {
            $status_code = 503;
            $message[] = "Order added failed.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }
        return response($feedback, 200);
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

    public function draft(Request $request) {
        // DB::connection()->enableQueryLog();
        $query = Order::where('order_status', '=', '1');
        // dd($queries = DB::getQueryLog());

        if (Auth::user()->hasRole('storeadmin')) {
            $query->where('store_id', '=', Auth::guard('api')->user()->reference_id);
        } else {
            $query->whereHas('store', function($q) {
                $q->where('merchant_id', '=', Auth::guard('api')->user()->reference_id);
            });
        }

        if ($request->has('sub_order_id')) {
            $query->where('id', '=', function($q) use ($request) {
                $q->from('sub_orders')
                        ->selectRaw('order_id')
                        ->where('unique_suborder_id', $request->sub_order_id);
            });
        }

        if ($request->has('pickup_man_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->from('order_product')
                        ->selectRaw('order_id')
                        ->where('picker_id', $request->pickup_man_id)->lists('order_id');
            });
        }
        if ($request->has('delivary_man_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->from('sub_orders')
                        ->selectRaw('order_id')
                        ->where('deliveryman_id', $request->delivary_man_id)->lists('order_id');
            });
        }

        if ($request->has('order_id')) {
            $query->where('orders.unique_order_id', trim($request->order_id));
        }

        ( $request->has('customer_mobile_no') ) ? $query->where('orders.delivery_msisdn', 'like', '%' . $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', 'like', '%' . $request->customer_mobile_no) : null;
        ( $request->has('store_id') ) ? $query->where('orders.store_id', $request->store_id) : null;
        ( $request->has('merchant_order_id') ) ? $query->where('orders.merchant_order_id', 'like', '%' . $request->merchant_order_id) : null;

        ( $request->has('search_date') ) ? $query->whereDate('orders.created_at', '=', $request->search_date) : null;

        $orders = $query->orderBy('id', 'desc')->get();


        $stores = Store::whereStatus(true)->where('merchant_id', '=', Auth::guard('api')->user()->reference_id)->lists('store_id', 'id')->toArray();

        return view('merchant-orders.draft', compact('orders', 'stores'));
    }

    public function draft_submit(Request $request) {

        $orders = json_decode($request->order_id);
        // return count($orders);

        $due = 0;

        if(count($orders) > 0){

            foreach ($orders as $id) {
                $order = Order::whereStatus(true)->findOrFail($id);

                $total_delivery_charge = 0;
                foreach ($order->products as $product) {
                    $total_delivery_charge = $total_delivery_charge + $product->total_delivery_charge;
                }
                // return $total_delivery_charge;
                if($total_delivery_charge > 0){

                    $order->order_status = 2;
                    $order->verified_by =Auth::guard('api')->user()->id;
                    $order->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(Auth::guard('api')->user()->id, $order->id, '', '', '', 'orders', 'Order Approved: '.$order->unique_order_id);

                    foreach ($order->suborders as $sub_order){
                        // Update Sub-Order Status
                        $this->suborderStatus($sub_order->id, '2');
                    }

                    // $feedback[$id]['status_code'] = 200;
                    // $message[$id][] = "Selected Orders Approved.";
                    // $feedback[$id]['message'] = $message;

                }else{

                    $due = $due + 1;
                    // $feedback[$id]['status_code'] = 500;
                    // $message = "Order can't approve without delivery charge.";

                }
            }

            $feedback['status_code'] = 200;
            if($due == 0){
                $message = "Selected Orders Approved.";
            }else{
                $message = $due." Order[s] failed to approve due to '0' delivery charge.";
            }
            $feedback['message'][] = $message;
            return response($feedback, 200);

        }else{

            // $status_code = 401;
            // $message = "Invalid Request";
            $feedback['status_code'] = 401;
            $feedback['message'] = 'Invalid Request';
            return response($feedback, 200);

        }
    }

    //private function set_unauthorized( $status, $status_code, $message, $response )
    private function set_unauthorized($status_code, $message, $response) {
        $feedback = [];
        //$feedback['status']        =  $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
        ;
    }

    public function view_order( Request $request, $order_id )
    {
      $order_id   =  $order_id ?: 0;

        $stores = Store::where('status', 1)->where('merchant_id', Auth::guard('api')->user()->reference_id)->get();
        $stotre_ids = array();
        foreach ($stores as $store) {
            $stotre_ids[] = $store->id;
        }

        $order = Order::whereStatus(true)
                            ->whereIn('store_id', $stotre_ids)
                            ->where('merchant_order_id', $order_id)
                            ->where('order_status', '>', 1)
                            ->first();

        if(count($order) == 0){

            if(is_numeric($order_id)) {
            $order = Order::whereStatus(true)
                            ->whereIn('store_id', $stotre_ids)
                            ->where('id', $order_id)
                            ->where('order_status', '>', 1)
                            ->first();
            }else{
                $order = Order::whereStatus(true)
                                ->whereIn('store_id', $stotre_ids)
                                ->where('unique_order_id', $order_id)
                                ->where('order_status', '>', 1)
                                ->first();
            }

        }

      if( $order )
      {

        // Sub Orders
        $sub_orders = array();
        foreach ($order->suborders as $suborder) {
            // Sub-Order History
            $suborderlogs =  OrderLog::where('sub_order_id', $suborder->id)->get();
            $sub_order_log = array();
            foreach ($suborderlogs as $suborderlog) {
                $date = (string)$suborderlog->created_at;
                $sub_order_log[] = array(
                                       'text' => $suborderlog->text,
                                       'date' => $date,
                                   );
            }

            // Delivery Man
            if(!empty($suborder->deliveryman_id)){
                $sub_order_rider_name = $suborder->deliveryman->name;
                $sub_order_rider_photo = $suborder->deliveryman->photo;
            }else{
                $sub_order_rider_name = '';
                $sub_order_rider_photo = '';
            }

            if($suborder->return > 1){
              $sub_order_type = 'Return';
            }else{
              $sub_order_type = 'Delivery';
            }

            // Sub-Order Status
            // if($suborder->sub_order_status > 8){
            //     $sub_order_status = 'Complete';
            // }elseif($suborder->sub_order_status > 5){
            //     $sub_order_status = 'In Transit';
            // }elseif($suborder->sub_order_status > 3){
            //     $sub_order_status = 'Picked';
            // }elseif($suborder->sub_order_status > 1){
            //     $sub_order_status = 'Verified';
            // }else{
            //     $sub_order_status = '';
            // }
            $sub_order_status = $suborder->suborder_status->title;

            // Products
            $products = array();
            foreach ($suborder->products as $product) {
                // Product History
                // $productlogs =  OrderLog::where('order_id', $order->id)->where('sub_order_id', $suborder->id)->where('product_id', $product->id)->get();
                // $product_log = array();
                // foreach ($productlogs as $productlog) {
                //     $date = (string)$productlog->created_at;
                //     $product_log[] = array(
                //                            'text' => $productlog->text,
                //                            'date' => $date,
                //                        );
                // }

                // Pickup Man
                if(!empty($product->deliveryman_id)){
                    $product_rider_name = $product->picker->name;
                    $product_rider_photo = $product->picker->photo;
                }else{
                    $product_rider_name = '';
                    $product_rider_photo = '';
                }

                // Sub-Order Status
                // if($product->status > 8){
                //     $product_status = 'Complete';
                // }elseif($product->status > 5){
                //     $product_status = 'In Transit';
                // }elseif($product->status > 3){
                //     $product_status = 'Picked';
                // }elseif($product->status > 1){
                //     $product_status = 'Verified';
                // }else{
                //     $product_status = '';
                // }

                if($product->delivery_paid_amount != null){
                    $delivery_paid_amount = $product->delivery_paid_amount;
                }else{
                    $delivery_paid_amount = 0;
                }
                $product_data = array(
                                    'product_id' => $product->product_unique_id,
                                    'product_title' => $product->product_title,
                                    'product_category' => $product->product_category->name,
                                    'pickup_location' => $product->pickup_location->title.', '.$product->pickup_location->address1.', '.$product->pickup_location->zone->name.', '.$product->pickup_location->zone->city->name.', '.$product->pickup_location->zone->city->state->name,
                                    'product_unit_price' => $product->unit_price,
                                    'product_unit_delivery_charge' => $product->unit_deivery_charge,
                                    'product_total_price' => $product->sub_total,
                                    'product_total_delivery_charge' => $product->total_delivery_charge,
                                    'product_paid_amount' => $delivery_paid_amount,
                                    'product_quantity' => $product->quantity,
                                    'product_payable_amount' => $product->total_payable_amount,
                                    'product_width' => $product->width,
                                    'product_height' => $product->height,
                                    'product_length' => $product->length,
                                    // 'product_log' => $product_log,
                                    // 'product_status' => $product_status,
                                );
                $products[] = $product_data;

            }

            $sub_order_data = array(
                                    'sub_order_id' => $suborder->unique_suborder_id,
                                    'sub_order_pickup_attempts' => $product->picking_attempts,
                                    'sub_order_pickup_rider_name' => $product_rider_name,
                                    'sub_order_pickup_rider_photo' => $product_rider_photo,
                                    'sub_order_delivery_attempts' => $suborder->no_of_delivery_attempts,
                                    'sub_order_delivery_rider_name' => $sub_order_rider_name,
                                    'sub_order_delivery_rider_photo' => $sub_order_rider_photo,
                                    'sub_order_status' => $sub_order_status,
                                    'sub_order_type'  => $sub_order_type,
                                    'sub_order_product' => $products,
                                    'sub_order_log' => $sub_order_log,
                                );
            $sub_orders[] = $sub_order_data;
        }

        // Order Status
        if($order->order_status > 8){
            $order_status = 'Complete';
        }elseif($order->order_status > 5){
            $order_status = 'In Transit';
        }elseif($order->order_status > 3){
            $order_status = 'Picked';
        }elseif($order->order_status > 1){
            $order_status = 'Verified';
        }else{
            $order_status = '';
        }

        $shipping_address = array(
                                'name' => $order->delivery_name,
                                'email' => $order->delivery_email,
                                'msisdn' => $order->delivery_msisdn,
                                'alt_msisdn' => $order->delivery_alt_msisdn,
                                'state' => $order->delivery_zone->city->state->name,
                                'city' => $order->delivery_zone->city->name,
                                'zone' => $order->delivery_zone->name,
                                'address' => $order->delivery_address1,
                            );

        $order_data = array(
                                'order_id' => $order->unique_order_id,
                                'order_merchant' => $order->store->merchant->name,
                                'merchant_order_id' => $order->merchant_order_id,
                                // 'order_log' => $order_log,
                                'order_status' => $order_status,
                                'shipping_address' => $shipping_address,
                                'sub_orders' => $sub_orders,
                            );

         /**
         * Sending history
         * @return history
         */
         $status        =  'success';
         $status_code   =  200;
         $message       =  'Order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         // $feedback['response'][0]['order']     =  $order_data;
         $feedback['response']     =  $order_data;

         return response($feedback, $status_code);
      }
      else
      {
         /**
         * Sending response
         * @return ErrorException
         */
         $status        =  'failed';
         $status_code   =  401;
         $message       =  'No order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         // $feedback['response']      =  "";

         return response($feedback, 200);
      }

    }

}
