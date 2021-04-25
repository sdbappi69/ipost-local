<?php

namespace App\Http\Controllers\LpApi\Merchant;

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
use DB;
use Validator;
use Session;
use Redirect;
use Auth;

class ProductController extends Controller
{

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('role:superadministrator|systemadministrator|systemmoderator|merchantadmin|merchantsupport|storeadmin|hubmanager');
    // }

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
    public function store_product(Request $request)
    {
        // return 1;
        $validation = Validator::make($request->all(), [
            'order_id' => 'required|numeric',
            'product_title' => 'required',
            'url' => 'sometimes',
            // 'product_category_id' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'length' => 'required|numeric',
            'weight' => 'required|numeric',
            'pickup_location_id' => 'required|numeric',
            'picking_date' => 'required|date',
            'picking_time_slot_id' => 'required|numeric',
            // 'status' => 'required|numeric',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // $sub_order = SubOrder::whereStatus(true)->where('order_id', '=', $request->order_id)->first();
        $order = Order::whereStatus(true)->where('id', '=', $request->order_id)->first();
        $product_category = ProductCategory::whereStatus(true)->where('id', '=', 5)->first();
        $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $request->pickup_location_id)->first();
        $hub_id = $pickup_location->zone->hub_id;

        // Call Charge Calculation API
        $post = [
            'logistic_partner_id' => (string)$order->logistic_partner_id,
            'width' => $request->width,
            'height'   => $request->height,
            'length' => $request->length,
            'weight' => $request->weight,
            'product_category' => $product_category->name,
            'pickup_zone_id'   => (string)$pickup_location->zone_id,
            'delivery_zone_id' => (string)$order->delivery_zone_id,
            'quantity' => $request->quantity,
            'unit_price'   => $request->unit_price,
        ];
        // return url('/').'/api/charge-calculator';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url('/').'/api/charge-calculator');
        // $ch = curl_init(env('APP_URL').'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $charges = json_decode($response);
//        $charges = $charges[0];
        if($charges->status == 'Failed'){
            abort(403);
        }

        // Product Unique Id
        $product_info = CartProduct::where('order_id', $request->order_id)->orderBy('id', 'desc')->first();
        if(count($product_info) == 0){
            $product_unique_id = 'D'.$order->unique_order_id.'01';
        }else{
            $last_sub_order_number = substr($product_info->product_unique_id, -2);
            $product_unique_id = substr($product_info->product_unique_id, 0, 8).sprintf("%02d", $last_sub_order_number+1);
        }

        $product = new CartProduct();
        $product->fill($request->except('api_token'));
        $product->picking_attempts = '0';
        $product->sub_total = $request->unit_price * $request->quantity;
        // $product->product_unique_id = "P".time().rand(10,99);
        $product->product_unique_id = $product_unique_id;
        $product->created_by = Auth::guard('api')->user()->id;
        $product->updated_by = Auth::guard('api')->user()->id;
        // $product->sub_order_id = $sub_order->id;

        $product->unit_deivery_charge = $charges->product_unit_delivery_charge;
        $product->payable_product_price = $charges->product_total_price;
        $product->total_delivery_charge = $charges->product_delivery_charge;
        $product->delivery_pay_by_cus = '0';
        // $product->total_payable_amount = $charges->product_total_price + $charges->product_delivery_charge;
        $product->total_payable_amount = $charges->product_total_price;
        $product->delivery_paid_amount = '0';

        // $product->save();

        if ($product->save()) {
            $feedback['status_code'] = 200;
            $message[] = 'Product added successfully.';
            $feedback['message'] = $message;
            $feedback['response'] = ['product_id' => $product->id];
        } else {
            $status_code = 500;
            $message[] = "Product added failed.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(Auth::guard('api')->user()->id, $product->order_id, '', $product->id, $product->id, 'cart_product', 'Created a new product: '.$product->product_unique_id);

        // Update Hub_id
        $order = Order::findOrFail($request->order_id);
        $order->hub_id = $hub_id;
        //$order->hub_id = 16; // For BPO
        $order->save();


        return response($feedback, 200);

        // Session::flash('message', "Product added successfully");
        // return Redirect::back();
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
    public function edit_product($id)
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
     public function update_product(Request $request, $id)
    {
        // return $request->all();
        $validation = Validator::make($request->all(), [
            'product_title' => 'required',
            'url' => 'sometimes',
            // 'product_category_id' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'length' => 'required|numeric',
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
        $product_category = ProductCategory::whereStatus(true)->where('id', '=', 5)->first();
        $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $request->pickup_location_id)->first();
        $hub_id = $pickup_location->zone->hub_id;
        $post = [
        'logistic_partner_id' => (string)$order->logistic_partner_id,
        'width' => $request->width,
        'height'   => $request->height,
        'length' => $request->length,
        'weight' => $request->weight,
        'product_category' => $product_category->name,
        'pickup_zone_id'   => $pickup_location->zone_id,
        'delivery_zone_id' => $order->delivery_zone_id,
        'quantity' => $request->quantity,
        'unit_price'   => $request->unit_price,
        ];
        //dd($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url('/').'/api/charge-calculator');
        // $ch = curl_init(env('APP_URL').'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $charges = json_decode($response);
//        $charges = $charges[0];
        //dd($charges);
        if($charges->status == 'Failed'){
            abort(403);
        }
        $product = CartProduct::findOrFail($id);
        $product->fill($request->except('responsible_user_id', 'api_token'));
        //$product->fill($request->all());
        ///$product->picking_attempts = '0';
        $product->sub_total = $request->unit_price * $request->quantity;
        // $product->product_unique_id = "P".time().rand(10,99);
        //$product->product_unique_id = $product_unique_id;
        //$product->created_by = auth()->user()->id;
        $product->updated_by = Auth::guard('api')->user()->id;
        //$product->sub_order_id = $sub_order->id;
        $product->unit_deivery_charge = $charges->product_unit_delivery_charge;
        $product->payable_product_price = $charges->product_total_price;
        $product->total_delivery_charge = $charges->product_delivery_charge;
        $product->delivery_pay_by_cus = '1';
        $product->total_payable_amount = $charges->product_total_price + $charges->product_delivery_charge;
        $product->delivery_paid_amount = '0';
        //dd($product->toArray());
        
        if ($product->save()) {
            $feedback['status_code'] = 200;
            $message[] = 'Product updated successfully.';
            $feedback['message'] = $message;
            $feedback['response']['product'] = $product;
        } else {
            $status_code = 500;
            $message[] = "Product update failed.";
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        //
      //  $product = CartProduct::findOrFail($id);
       // $product->fill($request->except('responsible_user_id'));
       // $product->sub_total = $request->unit_price * $request->quantity;
      //  $product->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(Auth::guard('api')->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product->id, 'cart_product', "Updated product information");

        if($request->responsible_user_id){
            $sub_order = SubOrder::findOrFail($product->sub_order_id);
            $sub_order->responsible_user_id = $request->responsible_user_id;
            $sub_order->source_hub_id = Auth::guard('api')->user()->reference_id;
            $sub_order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(Auth::guard('api')->user()->id, $sub_order->order_id, $sub_order->id, '', $request->responsible_user_id, 'users', "Created new resposible for Sub-Order");
        }

        return response($feedback, 200);

        // Session::flash('message', "Product information updated successfully");
        // return Redirect::back();
    }
//    public function update(Request $request, $id)
//    {
//        $validation = Validator::make($request->all(), [
//            'product_title' => 'required',
//            'url' => 'sometimes',
//            'product_category_id' => 'required|numeric',
//            'unit_price' => 'required|numeric',
//            'quantity' => 'required|numeric',
//            'width' => 'required|numeric',
//            'height' => 'required|numeric',
//            'length' => 'required|numeric',
//            'pickup_location_id' => 'required|numeric',
//            'picking_date' => 'required|date',
//            'picking_time_slot_id' => 'required|numeric',
//            'status' => 'sometimes|numeric',
//            'receive_remarks' => 'sometimes',
//            'responsible_user_id' => 'sometimes|numeric',
//        ]);
//
//        if($validation->fails()) {
//            return Redirect::back()->withErrors($validation)->withInput();
//        }
//
//        $product = CartProduct::findOrFail($id);
//        $product->fill($request->except('responsible_user_id'));
//        $product->sub_total = $request->unit_price * $request->quantity;
//        $product->save();
//
//        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
//        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product->id, 'cart_product', "Updated product information");
//
//        if($request->responsible_user_id){
//            $sub_order = SubOrder::findOrFail($product->sub_order_id);
//            $sub_order->responsible_user_id = $request->responsible_user_id;
//            $sub_order->source_hub_id = auth()->user()->reference_id;
//            $sub_order->save();
//
//            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
//            $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $request->responsible_user_id, 'users', "Created new resposible for Sub-Order");
//        }
//
//        Session::flash('message', "Product information updated successfully");
//        return Redirect::back();
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_product($id)
    {
        CartProduct::where('id', '=', $id)->delete();

        $feedback['status_code'] = 200;
        $message[] = 'Product delete successfully.';
        $feedback['message'] = $message;
        // $feedback['response']['product'] = $product;

        return response($feedback, 200);
        // Session::flash('message', "Product removed successfully");
        // return Redirect::back();
    }

    public function pick_up_slot($day){

        $data = PickingTimeSlot::whereStatus(true)->where('day', '=', $day)->addSelect("id", DB::raw("CONCAT(start_time,' - ',end_time) AS title"))->get();

        return $_GET['callback']."(".json_encode($data).")";
    }
}
