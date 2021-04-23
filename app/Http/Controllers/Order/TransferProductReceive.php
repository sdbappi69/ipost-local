<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Order;
use App\Hub;
use App\OrderProduct;
use App\SubOrder;
use App\ProductCategory;
use App\PickingLocations;
use App\PickingTimeSlot;
use App\User;
use App\Rack;
use App\RackProduct;
use App\Shelf;
use App\ShelfProduct;
use App\ProductTrip;
use DB;
use Session;
use Redirect;
use Validator;

class TransferProductReceive extends Controller
{

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|inboundmanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = OrderProduct::select(array(
                                            'order_product.id AS id',
                                            'order_product.product_unique_id',
                                            'order_product.product_title',
                                            'order_product.quantity',
                                            'order_product.picking_date',
                                            'pl.title',
                                            'pl.msisdn',
                                            'pl.alt_msisdn',
                                            'pl.address1',
                                            'pt.start_time',
                                            'pt.end_time',
                                            'pc.name AS product_category',
                                            'z.name AS zone_name',
                                            'c.name AS city_name',
                                            's.name AS state_name',
                                            't.unique_trip_id',
                                            'h.title AS hub_title',
                                        ))
                ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->join('cities AS c', 'c.id', '=', 'z.city_id')
                ->join('states AS s', 's.id', '=', 'c.state_id')
                ->join('product_trip AS ptr', 'ptr.product_id', '=', 'order_product.id')
                ->join('trips AS t', 'ptr.trip_id', '=', 't.id')
                ->join('hubs AS h', 'z.hub_id', '=', 'h.id')
                // ->where('order_product.status', '=', '4')
                ->where('ptr.receive_hub_id', '=', auth()->user()->reference_id)
                ->where('ptr.status', '=','1')
                ->orderBy('ptr.id', 'desc')
                ->get();

        return view('receive-transferd.index', compact('products'));
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
        //
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
        $product = OrderProduct::where('id', '=', $id)->first();
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

        $suborders = SubOrder::whereStatus(true)->where("order_id", $product->order_id)->get();

        $vehiclemanager = User::whereStatus(true)->where('user_type_id', '=', '7')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        return view('receive-transferd.edit', compact('categories', 'warehouse', 'product', 'picking_time_slot', 'suborders', 'vehiclemanager'));
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
            'responsible_user_id' => 'required',
            'product_title' => 'required',
            'url' => 'sometimes',
            'product_category_id' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'length' => 'required|numeric',
            'pickup_location_id' => 'required|numeric',
            'picking_date' => 'required|date',
            'picking_time_slot_id' => 'required|numeric',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $product_info = OrderProduct::where('id', '=', $id)->first();
        $product_category = ProductCategory::whereStatus(true)->where('id', '=', $request->product_category_id)->first();
        $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $request->pickup_location_id)->first();

        // Call Charge Calculation API
        $post = [
            'store_id' => $product_info->sub_order->order->store->store_id,
            'width' => $request->width,
            'height'   => $request->height,
            'length' => $request->length,
            'weight' => $product_info->weight,
            'product_category' => $product_category->name,
            'pickup_zone_id'   => $pickup_location->zone_id,
            'delivery_zone_id' => $product_info->sub_order->order->delivery_zone_id,
            'quantity' => $request->quantity,
            'unit_price'   => $request->unit_price,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('APP_URL').'api/charge-calculator');
        // $ch = curl_init(env('APP_URL').'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $charges = json_decode($response);
        $charges = $charges[0];
        if($charges->status == 'Failed'){
            abort(403);
        }

        // Select Hubs
        $picking_hub_id = $product_info->pickup_location->zone->hub_id;
        $delivery_hub_id = $product_info->order->delivery_zone->hub_id;
        $master_hub_id = $product_info->order->hub_id;

        $product = OrderProduct::findOrFail($id);
        $product->fill($request->except('responsible_user_id'));
        $product->sub_total = $request->unit_price * $request->quantity;
        $product->updated_by = auth()->user()->id;
        $product->unit_deivery_charge = $charges->product_unit_delivery_charge;
        $product->total_delivery_charge = $charges->product_delivery_charge;
        $payable_product_price = ($product_info->sub_order->order->percent_of_collection/100)*$product->sub_total;
        $product->payable_product_price = $payable_product_price;
        $product->total_payable_amount = $payable_product_price + $charges->product_delivery_charge;
        // if($master_hub_id != $picking_hub_id){
        //     $product->hub_transfer = '1';
        //     $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
        //     $delivery_hub_id = $master_hub_id;
        // }
        if($master_hub_id == auth()->user()->reference_id){
            $product->status = '5';
        }
        $product->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, auth()->user()->reference_id, 'hubs', 'Receive Product at '.$product->pickup_location->zone->hub->title);

        $order_due = OrderProduct::where('status', '!=', '5')->where('order_id', '=', $product_info->order_id)->count();
        if($order_due == 0){
            $order = Order::findOrFail($product_info->order_id);
            $order->updated_by = auth()->user()->id;
            $order->order_status = '5';
            $order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'All products picked');
        }

        $sub_order = SubOrder::findOrFail($product_info->sub_order_id);
        $sub_order->updated_by = auth()->user()->id;
        $sub_order->responsible_user_id = $request->responsible_user_id;
        if($order_due == 0){
            $sub_order->sub_order_status = '5';
            $sub_order->source_hub_id = auth()->user()->reference_id;
            $sub_order->destination_hub_id = $delivery_hub_id;
            $sub_order->next_hub_id = $delivery_hub_id;

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', auth()->user()->reference_id, 'hubs', 'Sub-Order is ready for delivery');
        }
        $sub_order->save();

        // Release Trip
        $trip = ProductTrip::where('product_id', '=', $id)->where('status', '=', '1')->firstOrFail();
        $trip->status = '2';
        $trip->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $trip->id, 'trips', 'Product released from the trip');

        // Decide Hub or Rack
        if($picking_hub_id == $delivery_hub_id){ // Go for Rack

            $rackData = Rack::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->get();
            if($rackData->count() != 0){
                foreach ($rackData as $rack) {
                    $rackUsed = RackProduct::select(array(
                                                        DB::raw("SUM(`op.width`) AS total_width"),
                                                        DB::raw("SUM(`op.height`) AS total_height"),
                                                        DB::raw("SUM(`op.length`) AS total_length"),
                                                    ))
                                                ->join('order_product AS op', 'op.id', '=', 'rack_products.product_id')
                                                ->where('rack_products.status', '=', '1')
                                                ->where('rack_products.rack_id', '=', $rack->id)
                                                ->first();
                    $available_width = $rack->width - $rackUsed->width;
                    $available_height = $rack->height - $rackUsed->height;
                    $available_length = $rack->length - $rackUsed->length;

                    if($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length){
                        $rack_id = $rack->id;
                        $message = "Please keep the product on ".$rack->rack_title;
                        break;
                    }else{
                        $rack_id = 0;
                        $message = "Dedicated rack hasn't enough space. Please use defult rack";
                    }
                }

            }else{
                $rack_id = 0;
                $message = "No Rack defined for this delivery zone.";
            }

            $sub_order = SubOrder::findOrFail($product_info->sub_order_id);
            $sub_order->responsible_user_id = $request->responsible_user_id;
            $sub_order_due = OrderProduct::where('status', '!=', '5')->where('sub_order_id', '=', $product_info->sub_order_id)->count();
            if($sub_order_due == 0){
                $sub_order->sub_order_status = '5';
            }
            $sub_order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $request->responsible_user_id, 'users', 'Responsibility assigned for the Sub-Order');

            // Insert product on rack
            $rack_product = new RackProduct();
            $rack_product->rack_id = $rack_id;
            $rack_product->product_id = $product_info->id;
            $rack_product->status = '1';
            $rack_product->created_by = auth()->user()->id;
            $rack_product->updated_by = auth()->user()->id;
            $rack_product->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $rack_product->rack_id, 'rack_products', 'Product racked');

        }else{

            $shelfData = Shelf::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->where('assignd_hub_id', '=', $delivery_hub_id)->get();

            if($shelfData->count() != 0){

                foreach ($shelfData as $shelf) {
                    $shelfUsed = ShelfProduct::select(array(
                                                        DB::raw("SUM('op.width') AS total_width"),
                                                        DB::raw("SUM('op.height') AS total_height"),
                                                        DB::raw("SUM('op.length') AS total_length"),
                                                    ))
                                                ->join('order_product AS op', 'op.id', '=', 'shelf_products.product_id')
                                                ->where('shelf_products.status', '=', '1')
                                                ->where('shelf_products.shelf_id', '=', $shelf->id)
                                                ->first();
                    $available_width = $shelf->width - $shelfUsed->width;
                    $available_height = $shelf->height - $shelfUsed->height;
                    $available_length = $shelf->length - $shelfUsed->length;

                    if($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length){
                        $shelf_id = $shelf->id;
                        $message = "Please keep the product on ".$shelf->shelf_title;
                        break;
                    }else{
                        $shelf_id = 0;
                        $message = "Dedicated rack hasn't enough space. Please use defult rack";
                    }
                }

            }else{
                $shelf_id = 0;
                $message = "No Shelf defined for this delivery hub.";
            }

            // Insert product on rack
            $shelf_product = new ShelfProduct();
            $shelf_product->shelf_id = $shelf_id;
            $shelf_product->product_id = $product_info->id;
            $shelf_product->status = '1';
            $shelf_product->created_by = auth()->user()->id;
            $shelf_product->updated_by = auth()->user()->id;
            $shelf_product->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $shelf_product->shelf_id, 'shelf_products', 'Product shelfed');

        }

        Session::flash('inventory', $message);
        return redirect('/receive-transferd');
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
}
