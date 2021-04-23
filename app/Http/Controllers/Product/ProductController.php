<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\PickingTimeSlot;
use App\OrderProduct;
use App\CartProduct;
use App\SubOrder;
use App\Order;
use App\ProductCategory;
use App\PickingLocations;
use App\DiscountLog;
use DB;
use Validator;
use Session;
use Redirect;
use Excel;

class ProductController extends Controller
{

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|merchantadmin|merchantsupport|storeadmin|hubmanager|salesteam|operationmanager|operationalhead');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'order_id' => 'required|numeric',
            'product_title' => 'required',
            'url' => 'sometimes',
            'product_category_id' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'width' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'weight' => 'required|numeric',
            'pickup_location_id' => 'required|numeric',
            'picking_date' => 'required|date',
            'picking_time_slot_id' => 'required|numeric',
            'status' => 'sometimes|numeric',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // $sub_order = SubOrder::whereStatus(true)->where('order_id', '=', $request->order_id)->first();
        $order = Order::whereStatus(true)->where('id', '=', $request->order_id)->first();
        $product_category = ProductCategory::whereStatus(true)->where('id', '=', $request->product_category_id)->first();
        $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $request->pickup_location_id)->first();
        $hub_id = $pickup_location->zone->hub_id;

        // Call Charge Calculation API
        $post = [
            'store_id' => $order->store->store_id,
            'width' => 0,
            'height'   => 0,
            'length' => 0,
            'weight' => $request->weight,
            'product_category' => $product_category->name,
            'pickup_zone_id'   => $pickup_location->zone_id,
            'delivery_zone_id' => $order->delivery_zone_id,
            'quantity' => $request->quantity,
            'unit_price'   => $request->unit_price,
        ];
//        dd($post);
        // return url('/').'/api/charge-calculator';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url('/').'/api/charge-calculator');
        // $ch = curl_init(config("app.url").'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $charges = json_decode($response);
        $charges = $charges[0];
        if(@$charges->status == 'Failed'){
            abort(403);
        }

        // Product Unique Id
        $product_info = CartProduct::where('order_id', $request->order_id)->orderBy('id', 'desc')->first();
        if(count($product_info) == 0){
            // $product_unique_id = $order->unique_order_id.'-P1';
            $product_unique_id = 'D'.$order->unique_order_id.'01';
        }else{
            $last_sub_order_number = substr($product_info->product_unique_id, -2);
            $product_unique_id = substr($product_info->product_unique_id, 0, 8).sprintf("%02d", $last_sub_order_number+1);
            // $split_last_product_unique_id = explode('-P', $product_info->product_unique_id);
            // $product_unique_id = $split_last_product_unique_id[0]."-P".($split_last_product_unique_id[1]+1);
        }

        $product = new CartProduct();
        $product->fill($request->all());
        $product->picking_attempts = '0';
        $product->sub_total = $request->unit_price * $request->quantity;
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
        $product->status = 1;

        $product->save();

        // Discount Log
        if($charges->product_discount != 0){
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

        // Update Hub_id
        // $order = Order::findOrFail($request->order_id);
        // $order->hub_id = $hub_id;
        //$order->hub_id = 16; // For BPO
        // $order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, '', $product->id, $product->id, 'cart_product', 'Created a new product: '.$product->product_unique_id);

        Session::flash('message', "Product added successfully");
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = CartProduct::where('id', '=', $id)->first();
        $categories = ProductCategory::select(array(
                                'product_categories.id AS id',
                                DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
                            ))
                        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        // ->where('product_categories.parent_category_id', '!=', null)
                        ->where('product_categories.status', '=', '1')
                        ->where('pc.status', '=', '1')
                        ->lists('cat_name', 'id')
                        ->toArray();
        $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', $product->order->store->merchant->id)->lists('title', 'id')->toArray();
        $picking_time_slot = PickingTimeSlot::addSelect(DB::raw("CONCAT(day,' (',start_time,' - ',end_time,')') AS title"), "id")->whereStatus(true)->lists("title", "id")->toArray();

        return view('product.edit', compact('categories', 'warehouse', 'product', 'picking_time_slot'));
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
            'product_title' => 'required',
            'url' => 'sometimes',
            'product_category_id' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'width' => 'sometimes|numeric',
            'height' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'pickup_location_id' => 'required|numeric',
            'picking_date' => 'required|date',
            'picking_time_slot_id' => 'required|numeric',
            'status' => 'sometimes|numeric',
            'receive_remarks' => 'sometimes',
            'responsible_user_id' => 'sometimes|numeric',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
       // dd($request->all());
        // calculate charge again 
        $order = Order::whereStatus(true)->where('id', '=', $request->order_id)->first();
        $product_category = ProductCategory::whereStatus(true)->where('id', '=', $request->product_category_id)->first();
        $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $request->pickup_location_id)->first();
        $hub_id = $pickup_location->zone->hub_id;
        $post = [
        'store_id' => $order->store->store_id,
        'width' => 0,
        'height'   => 0,
        'length' => 0,
        'weight' => $request->weight,
        'product_category' => $product_category->name,
        'pickup_zone_id'   => $pickup_location->zone_id,
        'delivery_zone_id' => $order->delivery_zone_id,
        'quantity' => $request->quantity,
        'unit_price'   => $request->unit_price,
        ];
        //dd($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config("app.url").'api/charge-calculator');
        // curl_setopt($ch, CURLOPT_URL, 'http://systemv2.biddyut.com/api/charge-calculator');
        // $ch = curl_init(config("app.url").'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $charges = json_decode($response);
        $charges = $charges[0];
        // dd($charges);
        if($charges->status == 'Failed'){
            abort(403);
        }

        // return $charges->product_unit_delivery_charge;
        $product = CartProduct::findOrFail($id);
        $product->fill($request->except('responsible_user_id'));
        //$product->fill($request->all());
        ///$product->picking_attempts = '0';
        $product->sub_total = $request->unit_price * $request->quantity;
        // $product->product_unique_id = "P".time().rand(10,99);
        //$product->product_unique_id = $product_unique_id;
        //$product->created_by = auth()->user()->id;
        $product->updated_by = auth()->user()->id;
        //$product->sub_order_id = $sub_order->id;
        $product->unit_deivery_charge = $charges->product_unit_delivery_charge;
        $product->payable_product_price = $charges->product_total_price;
        $product->total_delivery_charge = $charges->product_delivery_charge;
        $product->delivery_pay_by_cus = '1';
        $product->total_payable_amount = $charges->product_total_price + $charges->product_delivery_charge;
        $product->delivery_paid_amount = '0';
        //dd($product->toArray());
        $product->save();
        //
      //  $product = CartProduct::findOrFail($id);
       // $product->fill($request->except('responsible_user_id'));
       // $product->sub_total = $request->unit_price * $request->quantity;
      //  $product->save();

        // Discount Log
        if($charges->product_discount != 0){
            $discount_log = DiscountLog::where('product_unique_id', $product->product_unique_id)->first();
            if(count($discount_log) == 0){
                $discount_log = new DiscountLog();
            }
            $discount_log->product_unique_id = $product->product_unique_id;
            $discount_log->order_id = $product->order_id;
            $discount_log->discount_id = $charges->delivery_discount_id;
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

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product->id, 'cart_product', "Updated product information");

        if($request->responsible_user_id){
            $sub_order = SubOrder::findOrFail($product->sub_order_id);
            $sub_order->responsible_user_id = $request->responsible_user_id;
            $sub_order->source_hub_id = auth()->user()->reference_id;
            $sub_order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $request->responsible_user_id, 'users', "Created new resposible for Sub-Order");
        }

        Session::flash('message', "Product information updated successfully");
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CartProduct::where('id', '=', $id)->delete();
        Session::flash('message', "Product removed successfully");
        return Redirect::back();
    }

    public function pick_up_slot($day){

        $data = PickingTimeSlot::whereStatus(true)->where('day', '=', $day)->addSelect("id", DB::raw("CONCAT(start_time,' - ',end_time) AS title"))->get();

        return $_GET['callback']."(".json_encode($data).")";
    }

    // public function discount_reset($order_id){

    //     DiscountLog::where('order_id', '=', $order_id)->delete();

    //     $products = CartProduct::whereStatus(true)->where('order_id', $order_id)->get();

    //     $order = Order::whereStatus(true)->where('id', '=', $order_id)->first();

    //     foreach ($products as $product) {

    //         $product_category = ProductCategory::whereStatus(true)->where('id', '=', $product->product_category_id)->first();
    //         $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $product->pickup_location_id)->first();

    //         // Call Charge Calculation API
    //         $post = [
    //             'store_id' => $order->store->store_id,
    //             'width' => $product->width,
    //             'height'   => $product->height,
    //             'length' => $product->length,
    //             'weight' => $product->weight,
    //             'product_category' => $product_category->name,
    //             'pickup_zone_id'   => $pickup_location->zone_id,
    //             'delivery_zone_id' => $order->delivery_zone_id,
    //             'quantity' => $product->quantity,
    //             'unit_price'   => $product->unit_price,
    //         ];
    //         // return config("app.url").'/api/charge-calculator';
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, config("app.url").'api/charge-calculator');
    //         // $ch = curl_init(config("app.url").'api/charge-calculator');
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    //         $response = curl_exec($ch);
    //         $charges = json_decode($response);
    //         $charges = $charges[0];
    //         // dd($charges);
    //         if($charges->status == 'Failed'){
    //             abort(403);
    //         }

    //         // Discount Log
    //         if($charges->product_discount != 0){
    //             $discount_log = DiscountLog::where('product_unique_id', $product->product_unique_id)->first();
    //             if(count($discount_log) == 0){
    //                 $discount_log = new DiscountLog();
    //             }
    //             $discount_log->product_unique_id = $product->product_unique_id;
    //             $discount_log->discount_id = $charges->delivery_discount_id;
    //             $discount_log->order_id = $product->order_id;
    //             $discount_log->unit_actual_charge = $charges->product_actual_unit_delivery_charge;
    //             $discount_log->unit_discount = $charges->product_unit_discount;
    //             $discount_log->unit_payable_charge = $charges->product_unit_delivery_charge;
    //             $discount_log->quantity = $product->quantity;
    //             $discount_log->total_actual_charge = $charges->product_actual_delivery_charge;
    //             $discount_log->total_discount = $charges->product_discount;
    //             $discount_log->total_payable_charge = $charges->product_delivery_charge;
    //             $discount_log->created_by = auth()->user()->id;
    //             $discount_log->updated_by = auth()->user()->id;
    //             $discount_log->save();
    //         }

    //     }

    //     return 1;

    // }

    public function bulkproduct(Request $request){
        $validation = Validator::make($request->all(), [
            'bulk_products' => 'required',
            'order_id' => 'required',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $_SESSION['order_id'] = $request->order_id;

        $file = $request->bulk_products;

        Excel::load($request->file('bulk_products'), function($reader) {

            $sheets = $reader->toArray();
            $data = $sheets[0];

            $message = '';
            foreach ($data as $item) {

                if($item->product_title == null){
                    echo $message = "There is a blank title.";
                }else{
                    $product_title = $item->product_title;

                    $url = $item->url;

                    if($item->product_category == null){
                        echo $message = "There is a blank product category.";
                    }else{
                        $product_category_name = $item->product_category;
                        $product_category = ProductCategory::whereStatus(true)->where('name', '=', $product_category_name)->first();
                        if(count($product_category) == 0){
                            echo $message = "There is a invalid product category.";
                        }else{
                            $product_category_id = $product_category->id;

                            if($item->unit_price == null){
                                echo $message = "An unit price is missing.";
                            }else{
                                $unit_price = $item->unit_price;

                                if($item->quantity == null){
                                    echo $message = "A product quantity is missing.";
                                }else{
                                    $quantity = $item->quantity;
                                    // echo "**".$item->width."**"; exit;
                                    // if($item->width == 'null'){
                                    //     echo $message = "Width need at least 0";
                                    // }else{
                                        $width = $item->width;

                                        // if($item->height == 'null'){
                                        //     echo $message = "Height need at least 0";
                                        // }else{
                                            $height = $item->height;

                                            // if($item->length == null){
                                            //     echo $message = "Length need at least 0";
                                            // }else{
                                                $length = $item->length;

                                                if($item->weight == null){
                                                    echo $message = "Weight need at least 0";
                                                }else{
                                                    $weight = $item->weight;

                                                    if($item->picking_location == null){
                                                        echo $message = "A picking location is missing.";
                                                    }else{
                                                        echo $picking_location_name = $item->picking_location;
                                                        $pickup_location = PickingLocations::whereStatus(true)->where('title', '=', $picking_location_name)->where('merchant_id', auth()->user()->reference_id)->first();
                                                        if(count($pickup_location) == 0){
                                                            echo $message = "There is a invalid picking location.";
                                                        }else{
                                                            $pickup_location_id = $pickup_location->id;
                                                            $hub_id = $pickup_location->zone->hub_id;

                                                            if($item->picking_date == null){
                                                                echo $message = "A picking date is missing.";
                                                            }else{
                                                                $picking_date = $item->picking_date;
                                                                $day = date('D', strtotime($picking_date));

                                                                if($item->picking_time_slot == null){
                                                                    echo $message = "A picking time slot is missing.";
                                                                }else{
                                                                    $picking_time_slot_full = $item->picking_time_slot;
                                                                    $split_picking_time_slot = explode(' - ', $picking_time_slot_full);

                                                                    $picking_time_slot = PickingTimeSlot::where('day', $day)->where('start_time', $split_picking_time_slot[0])->where('end_time', $split_picking_time_slot[1])->first();

                                                                    if(count($picking_time_slot) == 0){
                                                                        echo $message = "There is a invalid picking time slot.";
                                                                    }else{
                                                                        $picking_time_slot_id = $picking_time_slot->id;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            // }
                                        // }
                                    // }
                                }
                            }
                        }
                    }
                }

                if($message != ''){
                    // echo $message;
                    Session::flash('message', $message);
                    // header('Location: '.config("app.url").'merchant-order/'.$_SESSION['order_id'].'/edit?step=2');
                    exit;
                }else{

                    $order = Order::whereStatus(true)->where('id', '=', $_SESSION['order_id'])->first();

                    // Call Charge Calculation API
                    $post = [
                        'store_id' => $order->store->store_id,
                        'width' => $width,
                        'height'   => $height,
                        'length' => $length,
                        'weight' => $weight,
                        'product_category' => $product_category_name,
                        'pickup_zone_id'   => $pickup_location->zone_id,
                        'delivery_zone_id' => $order->delivery_zone_id,
                        'quantity' => $quantity,
                        'unit_price'   => $unit_price,
                    ];
                    // return config("app.url").'/api/charge-calculator';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, config("app.url").'api/charge-calculator');
                    // $ch = curl_init(config("app.url").'api/charge-calculator');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                    $response = curl_exec($ch);
                    $charges = json_decode($response);
                    $charges = $charges[0];
                    if($charges->status == 'Failed'){
                        abort(403);
                    }

                    // Product Unique Id
                    $product_info = CartProduct::where('order_id', $order->id)->orderBy('id', 'desc')->first();
                    if(count($product_info) == 0){
                        $product_unique_id = $order->unique_order_id.'-P1';
                    }else{
                        $split_last_product_unique_id = explode('-P', $product_info->product_unique_id);
                        $product_unique_id = $split_last_product_unique_id[0]."-P".($split_last_product_unique_id[1]+1);
                    }

                    $product = new CartProduct();
                    // $product->fill($request->all());
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

                    if($product->save()){
                        // Update Hub_id
                        $order = Order::findOrFail($product->order_id);
                        $order->hub_id = $hub_id;
                        //$order->hub_id = 16; // For BPO
                        
                        if($order->save()){
                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            $this->orderLog(auth()->user()->id, $product->order_id, '', $product->id, $product->id, 'cart_product', 'Created a new product: '.$product->product_unique_id);
                        }else{
                            Session::flash('message', 'Fail to update order');
                            // header('Location: '.config("app.url").'merchant-order/'.$_SESSION['order_id'].'/edit?step=2');
                            exit;
                        }
                    }else{
                        Session::flash('message', 'Fail to save product');
                        // header('Location: '.config("app.url").'merchant-order/'.$_SESSION['order_id'].'/edit?step=2');
                        exit;
                    }

                    // Session::flash('message', "Product added successfully");
                    // return Redirect::back();

                }

            }

            Session::flash('message', 'Product added successfully.');
            // header('Location: '.config("app.url").'merchant-order/'.$_SESSION['order_id'].'/edit?step=2');
            exit;

        });

        // Excel::batch('public/bulk_products', function($rows, $file) {

        //     // Explain the reader how it should interpret each row,
        //     // for every file inside the batch
        //     $rows->each(function($row) {

        //         // dd($row);
        //         // Example: dump the firstname
                

        //     });

        //     // print_r($rows);

        // });

    }
}
