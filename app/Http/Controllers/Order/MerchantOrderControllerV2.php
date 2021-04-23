<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\CreateOrderId;
use App\Http\Traits\AjkerDealTrait;
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

class MerchantOrderControllerV2 extends Controller
{

    use LogsTrait;
    use CreateOrderId;
    use AjkerDealTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:merchantadmin|merchantsupport|storeadmin|customerservice|salesteam|saleshead|superadministrator|systemadministrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('merchant-ordersv2.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $zones = Zone::
                    select(DB::raw('CONCAT(zones.name, ", ", cities.name) AS delivery_zone'),
                    'zones.id'
                )
                ->join('cities', 'cities.id', '=', 'zones.city_id')
                ->where('zones.status', 1)
                ->orderBy('delivery_zone', 'asc')
                ->lists('delivery_zone','id')->toArray();

        if (Auth::user()->hasRole('storeadmin')) {
            $stores = Store::whereStatus(true)
            ->where('id', '=', auth()->user()->reference_id)
            ->lists('store_id', 'id')
            ->toArray();
        }else{
            $stores = Store::whereStatus(true)->where('merchant_id', '=', auth()->user()->reference_id)->lists('store_id', 'id')->toArray();
        }

        return view('merchant-ordersv2.insert', compact('prefix', 'zones', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'store_id' => 'required',
            'delivery_name' => 'required',
            'delivery_email' => 'sometimes|email',
            'delivery_msisdn' => 'required|between:10,25',
            'delivery_zone_id' => 'required',
            'delivery_address1' => 'required',
            'merchant_order_id' => 'required',
            'delivery_latitude' => 'sometimes',
            'delivery_longitude' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        try {
            DB::beginTransaction();

                $order = new Order();
                $order->created_by = auth()->user()->id;
                $order->updated_by = auth()->user()->id;
                $order->unique_order_id = $this->newOrderId();

                $zone = Zone::findOrFail($request->delivery_zone_id);

                $order->merchant_order_id = $request->merchant_order_id;
                $order->store_id = $request->store_id;
                $order->order_remarks = $request->order_remarks;
                $order->delivery_name = $request->delivery_name;
                $order->delivery_email = $request->delivery_email;
                $order->delivery_msisdn = $request->delivery_msisdn;
                $order->delivery_address1 = $request->delivery_address1;
                $order->delivery_latitude = $request->delivery_latitude;
                $order->delivery_longitude = $request->delivery_longitude;
                $order->delivery_zone_id = $zone->id;
                $order->delivery_city_id = $zone->city_id;
                $order->delivery_state_id = $zone->city->state_id;
                $order->delivery_country_id = $zone->city->state->country_id;

                $order->order_status = 1;
                $order->status = 1;
                $order->save();

                // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'Created a new order: '.$order->unique_order_id);

            DB::commit();

            Session::flash('message', "Order information saved successfully");
            return redirect('/merchant-orderv2/'.$order->id.'/edit?step=1');

        } catch (Exception $e) {

            DB::rollback();

            return redirect('/merchant-orderv2/create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('merchant-orders.view');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $order = Order::select(array(
            'orders.merchant_order_id',
            'orders.id',
            'orders.unique_order_id',
            'orders.store_id',
            'orders.delivery_name',
            'orders.delivery_email',
            'orders.delivery_msisdn',
            'orders.delivery_alt_msisdn',
            'orders.delivery_address1',
            'orders.delivery_zone_id',
            'orders.delivery_city_id',
            'orders.delivery_state_id',
            'orders.delivery_country_id',
            'orders.delivery_latitude',
            'orders.delivery_longitude',
            'orders.as_package',
            'orders.order_remarks',
            'orders.delivery_pay_by_cus',
            'stores.store_id AS store_name',
            'stores.merchant_id',
            ))
        ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
        ->where('orders.id', '=', $id)
        ->where('orders.status', '<', 2)
        ->firstOrFail();

        if(Auth::user()->hasRole('storeadmin')) {
            if($order->store_id != auth()->user()->reference_id){
                abort(403);
            }
        }else if(Auth::user()->hasRole('merchantadmin')) {
            if($order->merchant_id != auth()->user()->reference_id){
                abort(403);
            }
        }

        if (Auth::user()->hasRole('storeadmin')) {
            $stores = Store::whereStatus(true)
            ->where('id', '=', auth()->user()->reference_id)
            ->lists('store_id', 'id')
            ->toArray();
        }else{
            if(Auth::user()->hasRole('merchantadmin')){
                $merchant_id = auth()->user()->reference_id;
            }else{
                $merchant_id = $order->merchant_id;
            }
            $stores = Store::whereStatus(true)->where('merchant_id', '=', $merchant_id)->lists('store_id', 'id')->toArray();
        }

        // For Page One
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $order->delivery_country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $order->delivery_state_id)->lists('name', 'id')->toArray();
        $zones = Zone::
                    select(DB::raw('CONCAT(zones.name, ", ", cities.name) AS delivery_zone'),
                    'zones.id'
                )
                ->join('cities', 'cities.id', '=', 'zones.city_id')
                ->where('zones.status', 1)
                ->orderBy('delivery_zone', 'asc')
                ->lists('delivery_zone','id')->toArray();

        // For Page Two
        $categories = ProductCategory::select(array(
            'product_categories.id AS id',
            DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
            ))
        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        // ->where('product_categories.category_type', '=', 'child')
        ->where('product_categories.parent_category_id', '!=', null)
        ->where('product_categories.status', '=', '1')
        ->where('pc.status', '=', '1')
        ->lists('cat_name', 'id')
        ->toArray();
        if (Auth::user()->hasRole('storeadmin')) {
            // return auth()->user()->reference_id;
            $store_info = Store::whereStatus(true)->where('id', '=', auth()->user()->reference_id)->first();
            $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', $store_info->merchant_id)->lists('title', 'id')->toArray();
        }else{
            if(Auth::user()->hasRole('merchantadmin')){
                $merchant_id = auth()->user()->reference_id;
            }else{
                $merchant_id = $order->merchant_id;
            }
            $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', $merchant_id)->lists('title', 'id')->toArray();
        }
        $products = CartProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();
        $picking_time_slot = PickingTimeSlot::addSelect(DB::raw("CONCAT(day,' (',start_time,' - ',end_time,')') AS title"), "id")->whereStatus(true)->lists("title", "id")->toArray();

        // For Page Three
        $shipping_loc = Order::select(array(
            'countries.name AS country_title',
            'states.name AS state_title',
            'cities.name AS city_title',
            'zones.name AS zone_title'
            ))
        ->leftJoin('countries', 'countries.id', '=', 'orders.delivery_country_id')
        ->leftJoin('states', 'states.id', '=', 'orders.delivery_state_id')
        ->leftJoin('cities', 'cities.id', '=', 'orders.delivery_city_id')
        ->leftJoin('zones', 'zones.id', '=', 'orders.delivery_zone_id')
        ->where('orders.id', '=', $id)
        ->first();

        // Get Warehouse
        if($order->as_package == 1){
            $warehouse_data = OrderProduct::where('order_id', $id)->first();
            if(count($warehouse_data) > 0){
                $pickup_location_id = $warehouse_data->pickup_location_id;
                $picking_date = $warehouse_data->picking_date;
                $picking_time_slot = PickingTimeSlot::addSelect(DB::raw("CONCAT(day,' (',start_time,' - ',end_time,')') AS title"), "id")->whereStatus(true)->lists("title", "id")->toArray();
                $picking_time_slot_id = $warehouse_data->picking_time_slot_id;
            }else{
                $pickup_location_id = '';
                $picking_date = '';
                $picking_time_slot_id = '';
            }            
        }else{
            $pickup_location_id = '';
            $picking_date = '';
            $picking_time_slot_id = '';
        }

        // Call Charge Calculation API
        if($request->step == '3'){
            $order = Order::where('orders.id', '=', $id)->first();
        }

        if($request->step){
            $step = $request->step;
        }else{
            $step = 1;
        }

        return view('merchant-ordersv2.edit', compact( 'step', 'id', 'order', 'zones', 'stores', 'categories', 'warehouse', 'products', 'picking_time_slot', 'shipping_loc', 'order', 'pickup_location_id', 'picking_date', 'picking_time_slot_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
        $validation = Validator::make($request->all(), [
            'store_id' => 'sometimes',
            'delivery_name' => 'sometimes',
            'delivery_email' => 'sometimes|email',
            'delivery_msisdn' => 'sometimes|between:10,25',
            'delivery_alt_msisdn' => 'sometimes|between:10,25',
            'delivery_zone_id' => 'sometimes',
            'delivery_address1' => 'sometimes',
            'amount' => 'sometimes',
            'delivery_payment_amount' => 'sometimes',
            'merchant_order_id' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        try {
                    
            DB::beginTransaction();

                // Collection Pecentage
                if(!empty($request->amount_hidden)){
                    $total_product_price = $request->amount_hidden;
                    $collectable_product_price = $request->amount;
                    $percent_of_collection = ($collectable_product_price/$total_product_price)*100;
                }else{
                    $total_product_price = 0;
                    $collectable_product_price = 0;
                    $percent_of_collection = 0;
                }

                // Update Products
                $order = Order::where('id', '=', $id)->first();

                // Delivery Charge Re-Calculate
                $total_payable_product_price = 0;
                $total_delivery_charge = 0;

                foreach($order->cart_products as $row){
                    $payable_product_price = ($percent_of_collection/100)*$row->sub_total;
                    $product = CartProduct::findOrFail($row->id);
                    $product->payable_product_price = $payable_product_price;
                    $product->total_payable_amount = $payable_product_price + $row->total_delivery_charge;
                    $product->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(auth()->user()->id, $row->order_id, $row->sub_order_id, $row->id, $row->id, 'cart_product', 'Updated payable product price & total payable amount');
                }

                if($request->has('delivery_zone_id')){
                    $delivery_zone_id = $request->delivery_zone_id;
                }else{
                    $delivery_zone_id = $order->delivery_zone_id;
                }

                $delivery_zone = Zone::findOrFail($delivery_zone_id);
                $order_update = Order::findOrFail($id);
                $order_update->fill($request->except('msisdn_country','alt_msisdn_country','step','include_delivery', 'amount_hidden', 'amount', 'pickup_location_id', 'weight', 'width', 'height', 'length', 'picking_date', 'picking_time_slot_id', 'delivery_discount_id', 'product_actual_unit_delivery_charge', 'product_unit_discount', 'product_unit_delivery_charge', 'product_actual_delivery_charge', 'product_discount', 'product_delivery_charge'));
                if(!empty($request->amount_hidden)){
                    if($request->include_delivery && $request->include_delivery == '1'){
                        // return 1;
                        $order_update->delivery_pay_by_cus = $request->include_delivery;
                    }else{
                        // return 0;
                        $order_update->delivery_pay_by_cus = '0';
                    }
                    $order_update->total_product_price = $total_product_price;
                    $order_update->collectable_product_price = $collectable_product_price;
                    $order_update->percent_of_collection = $percent_of_collection;
                }

                $order_update->delivery_zone_id = $delivery_zone->id;
                $order_update->delivery_city_id = $delivery_zone->city_id;
                $order_update->delivery_state_id = $delivery_zone->city->state_id;
                $order_update->delivery_country_id = $delivery_zone->city->state->country_id;

                if(count($order->cart_products)){

                    foreach ($order->cart_products as $product) {

                        if(isset($product->width) && $product->width != ''){
                            $width = $product->width;
                        }else{
                            $width = 0;
                        }

                        if(isset($product->height) && $product->height != ''){
                            $height = $product->height;
                        }else{
                            $height = 0;
                        }

                        if(isset($product->length) && $product->length != ''){
                            $length = $product->length;
                        }else{
                            $length = 0;
                        }

                        if(isset($product->weight) && $product->weight != ''){
                            $weight = $product->weight;
                        }else{
                            $weight = 0;
                        }
                        
                        // Call Charge Calculation API
                        $post = [
                            'store_id' => $order->store->store_id,
                            'width' => $width,
                            'height'   => $height,
                            'length' => $length,
                            'weight' => $weight,
                            'product_category' => $product->product_category->name,
                            'pickup_zone_id'   => $product->pickup_location->zone_id,
                            'delivery_zone_id' => $delivery_zone->id,
                            'quantity' => $product->quantity,
                            'unit_price'   => $product->unit_price,
                        ];
                        // return config("app.url").'/api/charge-calculator';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, config('app.url').'/api/charge-calculator');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                        $response = curl_exec($ch);
                        $charges = json_decode($response, true);
                        $charges = $charges[0];

                        if($charges['status'] == 'Success'){
                            // $sub_order = SubOrder::where('id', $product->sub_order_id)->first();
                            // $sub_order->next_hub_id = $delivery_zone->hub_id;
                            // $sub_order->destination_hub_id = $delivery_zone->hub_id;
                            // $sub_order->save();

                            $cart_product = CartProduct::where('product_unique_id', $product->product_unique_id)->first();
                            $cart_product->sub_total = $product->unit_price * $product->quantity;
                            $cart_product->updated_by = auth()->user()->id;
                            $cart_product->unit_deivery_charge = $charges['product_unit_delivery_charge'];
                            $cart_product->payable_product_price = $charges['product_total_price'];
                            $cart_product->total_delivery_charge = $charges['product_delivery_charge'];
                            $cart_product->total_payable_amount = $charges['product_total_price'] + $charges['product_delivery_charge'];
                            $cart_product->delivery_paid_amount = '0';
                            $cart_product->save();

                            $total_payable_product_price = $total_payable_product_price + $cart_product->payable_product_price;
                            $total_delivery_charge = $total_delivery_charge + $cart_product->total_delivery_charge;

                        }

                    }

                }

                $order_update->total_product_price = $total_payable_product_price;
                $order_update->collectable_product_price = $total_payable_product_price;
                $order_update->delivery_payment_amount = $total_delivery_charge;

                $order_update->order_status = '1';
                $order_update->save();

                // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                $this->orderLog(auth()->user()->id, $order_update->id, '', '', $order_update->id, 'orders', 'Updated Order Information');
                if(isset($request->step)){
                    if($request->step == 'complete'){

                        if($request->as_package == 0){
                            // return $request->as_package;
                            $products = CartProduct::whereStatus(true)->where('order_id', '=', $id)->get();
                            if(count($products) != 0){

                                OrderProduct::where('order_id', '=', $id)->delete();

                                SubOrder::where('order_id', '=', $id)->delete();

                                $i = 1;
                                foreach ($products as $product) {

                                    // Create Sub-Order
                                    $sub_order = SubOrder::where('unique_suborder_id', 'D'.$order->unique_order_id.sprintf("%02d", $i))->first();
                                    if(count($sub_order) == 0){
                                        $sub_order = new SubOrder();
                                    }
                                    $sub_order->unique_suborder_id = 'D'.$order->unique_order_id.sprintf("%02d", $i);
                                    $sub_order->order_id = $order->id;
                                    $sub_order->next_hub_id = $delivery_zone->hub_id;
                                    $sub_order->destination_hub_id = $delivery_zone->hub_id;
                                    $sub_order->save();

                                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                                    $this->orderLog(auth()->user()->id, $order->id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: '.$sub_order->unique_suborder_id);

                                    $order_product = OrderProduct::where('product_unique_id', $product->product_unique_id)->first();
                                    if(count($order_product) == 0){
                                        $order_product = new OrderProduct();
                                    }
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

                                    if($request->include_delivery && $request->include_delivery == '1'){
                                        // return 1;
                                        $order_product->delivery_pay_by_cus = $request->include_delivery;
                                        $order_product->total_payable_amount = $product->payable_product_price + $product->total_delivery_charge;
                                    }else{
                                        // return 0;
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

                                    $cart_product = CartProduct::findOrFail($product->id);
                                    $cart_product->order_product_id = $order_product->id;
                                    $cart_product->save();

                                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                                    $this->orderLog(auth()->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Individual item added: '.$order_product->product_unique_id);

                                    $i++;
                                }

                                DB::commit();

                                // return $request->step;
                                Session::flash('message', "Order information updated successfully");
                                // return redirect('/merchant-order');
                                if(Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('storeadmin')){
                                    return redirect('/merchant-order-draftv2');
                                }else{
                                    return redirect('/order-draftv2');
                                }

                            }

                        }else if($request->as_package == 1){
                            // return 'ok';
                            OrderProduct::where('order_id', '=', $id)->delete();

                            SubOrder::where('order_id', '=', $id)->delete();

                            // DiscountLog::where('order_id', '=', $id)->delete();

                            // $sub_order = SubOrder::where('order_id', '=', $id)->first();

                            // Create Sub-Order
                            $sub_order = new SubOrder();
                            $sub_order->unique_suborder_id = 'D'.$order->unique_order_id.'01';
                            $sub_order->order_id = $order->id;
                            $sub_order->next_hub_id = $delivery_zone->hub_id;
                            $sub_order->destination_hub_id = $delivery_zone->hub_id;
                            $sub_order->save();

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            $this->orderLog(auth()->user()->id, $order->id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: '.$sub_order->unique_suborder_id);

                            $order_product = new OrderProduct();
                            $order_product->product_unique_id = 'D'.$order->unique_order_id.'01';
                            $order_product->product_category_id = 5;
                            $order_product->order_id = $order->id;
                            $order_product->sub_order_id = $sub_order->id;
                            $order_product->pickup_location_id = $request->pickup_location_id;
                            $order_product->picking_date = $request->picking_date;
                            $order_product->picking_time_slot_id = $request->picking_time_slot_id;
                            $order_product->product_title = 'Bulk Products';
                            $order_product->unit_price = $request->amount_hidden;
                            $order_product->unit_deivery_charge = $request->delivery_payment_amount;
                            $order_product->quantity = 1;
                            $order_product->sub_total = $request->amount_hidden;
                            $order_product->payable_product_price = $request->amount;
                            $order_product->total_delivery_charge = $request->delivery_payment_amount;

                            if($request->include_delivery && $request->include_delivery == '1'){
                                $order_product->delivery_pay_by_cus = $request->include_delivery;
                                $order_product->total_payable_amount = $request->amount + $request->delivery_payment_amount;
                            }else{
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
                            if(count($products) != 0){
                                foreach ($products as $product) {
                                    $cart_product = CartProduct::findOrFail($product->id);
                                    $cart_product->order_product_id = $order_product->id;
                                    $cart_product->save();
                                }
                            }

                            // Discount Log
                            if($request->product_discount != 0){
                                $discount_log = DiscountLog::where('product_unique_id', $order_product->product_unique_id)->first();
                                if(count($discount_log) == 0){
                                    $discount_log = new DiscountLog();
                                }
                                $discount_log->product_unique_id = $order_product->product_unique_id;
                                $discount_log->discount_id = $request->delivery_discount_id;
                                $discount_log->order_id = $order_product->order_id;
                                $discount_log->unit_actual_charge = $request->product_actual_unit_delivery_charge;
                                $discount_log->unit_discount = $request->product_unit_discount;
                                $discount_log->unit_payable_charge = $request->product_unit_delivery_charge;
                                $discount_log->quantity = 1;
                                $discount_log->total_actual_charge = $request->product_actual_delivery_charge;
                                $discount_log->total_discount = $request->product_discount;
                                $discount_log->total_payable_charge = $request->product_delivery_charge;
                                $discount_log->created_by = auth()->user()->id;
                                $discount_log->updated_by = auth()->user()->id;
                                $discount_log->save();
                            }

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            $this->orderLog(auth()->user()->id, $order_product->order_id, $order_product->sub_order_id, '', $order_product->id, 'order_product', 'Updated as bulk product');

                            // Hub Id
                            $pickup_location_data = PickingLocations::whereStatus(true)->findOrFail($order_product->pickup_location_id);

                            $orderUpdate = Order::whereStatus(true)->findOrFail($order_product->order_id);

                            $delivery_payment_amount = 0;
                            foreach ($orderUpdate->products as $product) {
                                $delivery_payment_amount = $delivery_payment_amount + $product->total_delivery_charge;
                            }
                            
                            $orderUpdate->hub_id = $pickup_location_data->zone->hub_id;
                            $orderUpdate->delivery_payment_amount = $delivery_payment_amount;
                            $orderUpdate->save();

                            DB::commit();

                            // return $request->step;
                            Session::flash('message', "Order information updated successfully");
                            // return redirect('/merchant-order');
                            if(Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('storeadmin')){
                                return redirect('/merchant-order-draftv2');
                            }else{
                                return redirect('/order-draftv2');
                            }

                        }

                        $step = 'complete';

                    }else{
                        $step = $request->step;
                    }
                }else{
                    $step = 1;
                }

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollback();

            // throw $e;

        }

        Session::flash('message', "Order information saved successfully");
        return redirect('/merchant-orderv2/'.$id.'/edit?step='.$step);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function draft(Request $request){
        // DB::connection()->enableQueryLog();
        $query = Order::where('order_status', '=', '1');
        // dd($queries = DB::getQueryLog());

        if (Auth::user()->hasRole('storeadmin')) {
            $query->where('store_id', '=', auth()->user()->reference_id);
        }else{
            $query->whereHas('store',function($q){
                $q->where('merchant_id', '=', auth()->user()->reference_id);
            });
        }

        if($request->has('sub_order_id')){
            $query->where('id', '=', function($q) use ($request)
            {
               $q->from('sub_orders')
               ->selectRaw('order_id')
               ->where('unique_suborder_id', $request->sub_order_id);
           });
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('id',function($q) use ($request)
            {
             $q->from('order_product')
             ->selectRaw('order_id')
             ->where('picker_id', $request->pickup_man_id)->lists('order_id');
         });
        }
        if($request->has('delivary_man_id')){
            $query->whereIn('id',function($q) use ($request)
            {
             $q->from('sub_orders')
             ->selectRaw('order_id')
             ->where('deliveryman_id', $request->delivary_man_id)->lists('order_id');
         });
        }

        if($request->has('order_id')){
         $query->where('orders.unique_order_id',trim($request->order_id));
         }

         ( $request->has('customer_mobile_no') )      ? $query->where('orders.delivery_msisdn', 'like', '%' . $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', 'like', '%' . $request->customer_mobile_no) : null;
         ( $request->has('store_id') )  ? $query->where('orders.store_id',$request->store_id) : null;
         ( $request->has('merchant_order_id') )  ? $query->where('orders.merchant_order_id','like','%'.$request->merchant_order_id) : null;

         ( $request->has('search_date') )  ? $query->whereDate('orders.created_at','=',$request->search_date) : null;

        $orders = $query->orderBy('id', 'desc')->get();


        $stores = Store::whereStatus(true)->where('merchant_id', '=', auth()->user()->reference_id)->lists('store_id', 'id')->toArray();

        return view('merchant-ordersv2.draft',compact('orders', 'stores'));
    }

    public function draft_submit(Request $request){

        $orders = $request->order_id;

        if(count($orders) > 0){

            // AjkerDeal Customization
            $ajker_deal_orders = array();

            foreach ($orders as $id) {
                $order = Order::whereStatus(true)->findOrFail($id);

                $total_delivery_charge = 0;
                foreach ($order->products as $product) {
                    $total_delivery_charge = $total_delivery_charge + $product->total_delivery_charge;
                }

                foreach ($order->suborders as $sub_order) {
                    // return $order->delivery_zone->hub_id;
                    $sub_order_int = SubOrder::whereStatus(true)->findOrFail($sub_order->id);
                    $sub_order_int->source_hub_id = $source_hub_id;
                    $sub_order_int->destination_hub_id = $order->delivery_zone->hub_id;

                    $next_hub_id = $this->createTransitMap($sub_order->id, $source_hub_id, $order->delivery_zone->hub_id);

                    $sub_order_int->next_hub_id = $next_hub_id;
                    $sub_order_int->current_hub_id = auth()->user()->reference_id;
                    $sub_order_int->save();
                }

                // return $total_delivery_charge;
                if($total_delivery_charge > 0){

                    $order->order_status = 2;
                    $order->verified_by = auth()->user()->id;
                    $order->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(auth()->user()->id, $order->id, '', '', '', 'orders', 'Order Approved: '.$order->unique_order_id);

                    foreach ($order->suborders as $sub_order){
                        // Update Sub-Order Status
                        $this->suborderStatus($sub_order->id, '2');
                    }

                    // AjkerDeal Operation
                    if($order->store_id == 83){
                        $ajker_deal_orders[] = array('unique_order_id' => $order->unique_order_id, 'merchant_order_id' => $order->merchant_order_id);
                    }

                    $message = "Selected Orders Approved";

                }else{

                    $message = "Order can't approve without delivery charge";

                }
            }

            if(count($ajker_deal_orders) > 0){
                $this->ajkerDealOrderUpdate($ajker_deal_orders);
            }

        }else{

            $message = "No orders selected";

        }

        Session::flash('message', $message);
        if(Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('storeadmin')){
            return redirect('/merchant-order-draftv2');
        }else{
            return redirect('/order-draftv2');
        }

    }

    public function draft_remove(Request $request){

        $orders = $request->order_id;

        if(count($orders) > 0){

            foreach ($orders as $id) {

                $order = Order::whereStatus(true)->findOrFail($id);
                    
                OrderProduct::where('order_id', '=', $id)->delete();
                CartProduct::where('order_id', '=', $id)->delete();
                SubOrder::where('order_id', '=', $id)->delete();

                Order::where('id', '=', $id)->delete();

            }

            $message = "Selected Orders Removed";

        }else{

            $message = "No orders selected";

        }

        Session::flash('message', $message);
        if(Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('storeadmin')){
            return redirect('/merchant-order-draftv2');
        }else{
            return redirect('/order-draftv2');
        }

    }
}
