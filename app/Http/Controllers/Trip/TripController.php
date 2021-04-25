<?php

namespace App\Http\Controllers\Trip;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Trip;
use App\Vehicle;
use App\VehicleType;
use App\Hub;
use App\ProductTrip;
use App\SuborderTrip;
use App\OrderProduct;
use App\SubOrder;
use App\ShelfProduct;
use App\Order;

use Validator;
use Session;
use Redirect;
use Image;
use DB;
use Auth;
use Entrust;

use App\Store;
use App\Merchant;
use App\Zone;
use App\User;
use App\Consignment;

use PDF;

class TripController extends Controller
{

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:vehiclemanager|hubmanager|inventoryoperator|inboundmanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return 1;
        $vehicles = Vehicle::select(array(
            'vehicles.id AS id',
            DB::raw("CONCAT(vehicles.name,' - ',vt.title) AS title")
            ))
            ->leftJoin('vehicle_types AS vt', 'vt.id', '=', 'vehicles.vehicle_type_id')
            ->where('vehicles.status', '=', '1')
            ->where('vt.status', '=', '1')
            ->lists('title', 'id')
            ->toArray();

        $hubs = Hub::select(array('id', 'title'))->where('status', '=', '1')->lists('title', 'id')->toArray();

        $drivers = User::select(DB::raw('CONCAT(users.name, " - ", vehicle_types.title) AS name'),'users.id')
            ->join('vehicle_types','vehicle_types.id','=','users.transparent_mode')
            ->join('rider_references','rider_references.user_id','=','users.id')
            ->where('users.status',1)
            ->where('user_type_id', '=', '8')
            ->where('rider_references.reference_id', '=', auth()->user()->reference_id)
            ->lists('name', 'id')->toArray();

        $status = array(
                            '1' => 'Waiting',
                            '2' => 'In Transit',
                            '3' => 'Reched'
                        );

        // return auth()->user()->reference_id;
        $trips = Trip::whereStatus(true);

        if($request->has('start_date')){
            $start_date = $request->start_date;
        }else{
            $start_date = '2017-03-21';
        }

        if($request->has('end_date')){
            $end_date = $request->end_date;
        }else{
            $end_date = date('Y-m-d');
        }

        $trips->WhereBetween('created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('unique_trip_id')){
            $trips->where('unique_trip_id', $request->unique_trip_id);
        }

        if($request->has('vehicle_id')){
            $trips->where('vehicle_id', $request->vehicle_id);
        }

        if($request->has('source_hub_id')&&$request->has('destination_hub_id')){
            $trips->where('source_hub_id', $request->source_hub_id);
            $trips->where('destination_hub_id', $request->destination_hub_id);
        }else if($request->has('source_hub_id')){
            $trips->where('source_hub_id', $request->source_hub_id);
        }else if($request->has('destination_hub_id')){
            $trips->where('destination_hub_id', $request->destination_hub_id);
        }else{
            $trips->whereRaw("(source_hub_id = ".auth()->user()->reference_id." OR destination_hub_id = ".auth()->user()->reference_id.")");
        }

        if($request->has('trip_status')){
            $trips->where('trip_status', $request->trip_status);
        }

        if($request->has('driver_id')){
            $trips->where('driver_id', $request->driver_id);
        }

        $trips = $trips->orderBy('id', 'desc')->paginate(10);

        return view('trips.index', compact('trips', 'vehicles', 'hubs', 'status', 'drivers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $query = SubOrder::select(
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.unique_suborder_id',
                                    'orders.delivery_address1 AS delivery_address',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    'states_d.name AS delivery_state',
                                    'hubs.title AS delivery_hub',
                                    'next_hub.title AS next_hub_title',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'pc.name AS product_category'
                                )
                                ->where('sub_orders.status', 1)
                                ->whereIn('sub_orders.sub_order_status', [15, 16, 17, 47])
                                ->where('sub_orders.current_hub_id', '=', auth()->user()->reference_id)
                                ->where('sub_orders.next_hub_id', '!=', auth()->user()->reference_id)
                                ->leftJoin('hubs','hubs.id','=','sub_orders.destination_hub_id')
                                ->leftJoin('hubs as next_hub','next_hub.id','=','sub_orders.next_hub_id')
                                ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                                ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                                ->leftJoin('stores','stores.id','=','orders.store_id')
                                ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                                ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                                ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                                ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id');

        if($request->has('order_id')){
            $query->where('orders.unique_order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('orders.merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_orders.unique_suborder_id', $request->sub_order_id);
        }

        if($request->has('sub_order_status')){
            $query->where('sub_orders.sub_order_status', $request->sub_order_status);
        }

        if($request->has('customer_mobile_no')){
            $query->where('orders.delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->where('orders.store_id', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->where('stores.merchant_id', $request->merchant_id);
        }

        if($request->has('delivery_hub_id')){
            $query->where('sub_orders.destination_hub_id', $request->delivery_hub_id);
        }

        if($request->has('start_date')){
            $start_date = $request->start_date;
        }else{
            $start_date = '2017-03-21';
        }

        if($request->has('end_date')){
            $end_date = $request->end_date;
        }else{
            $end_date = date('Y-m-d');
        }

        $query->WhereBetween('sub_orders.updated_at',array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        $sub_orders = $query->orderBy('sub_orders.id', 'desc')->get();

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'),'zones.id')
        ->leftJoin('cities','cities.id','=','zones.city_id')->
        where('zones.status',true)->lists('name','zones.id')->toArray();

        $hubs = Hub::whereStatus(true)->where('id', '!=', auth()->user()->reference_id)->lists('title', 'id')->toArray();

        $tripconsignments = Trip::select(DB::raw('CONCAT(trips.unique_trip_id, " - ", vehicles.name) AS name'),'trips.id')
                        ->leftJoin('vehicles','vehicles.id','=','trips.vehicle_id')
                        ->where('trips.status', 1)
                        ->where('trips.trip_status', 1)
                        ->where('trips.source_hub_id', auth()->user()->reference_id)
                        ->lists('name', 'id')
                        ->toArray();

        $drivers = User::select(DB::raw('CONCAT(users.name, " - ", vehicle_types.title) AS name'),'users.id')
                ->join('vehicle_types','vehicle_types.id','=','users.transparent_mode')
                ->join('rider_references','rider_references.user_id','=','users.id')
                ->where('users.status',1)
                ->where('user_type_id', '=', '8')
                ->where('rider_references.reference_id', '=', auth()->user()->reference_id)
                ->lists('name', 'id')->toArray();

        return view('trips.insert', compact('hubs', 'sub_orders','merchants','stores', 'zones', 'tripconsignments', 'drivers'));
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
            'driver_id' => 'required|exists:users,id',
            'destination_hub_id' => 'required',
//            'status' => 'required',
//            'remarks' => 'sometimes',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $driver = User::find($request->driver_id);
//        $vehicle_on_the_way = Trip::whereIn('trip_status', [1,2])->where('vehicle_id', $request->vehicle_id)->count();
//        if($vehicle_on_the_way > 0){
//            Session::flash('message', "vehicle is busy with another trip.");
//            return Redirect::back();
//        }

        if(Session::has('trip_cart') && count(Session::get('trip_cart'))>0){

            // $rider_on_the_way = \App\ConsignmentCommon::where('status', '>', 0)->where('status', '<', 3)->where('rider_id', $request->driver_id)->count();
            // if($rider_on_the_way > 0){
            //     Session::flash('message', "Driver is busy with a consignment.");
            //     return Redirect::back();
            // }

            $driver_on_the_way = Trip::where('status', '>', 0)->where('trip_status', '<', 3)->where('driver_id', $request->driver_id)->count();
            if($driver_on_the_way > 0){
                Session::flash('message', "Driver is busy with another trip.");
                return Redirect::back();
            }

            $trip = new Trip();
            // $trip->fill($request->all());
            $trip->unique_trip_id = "BT".time().rand(10,99);
            $trip->vehicle_type_id = $driver->transparent_mode;
//            $trip->vehicle_id = $request->vehicle_id;
            $trip->driver_id = $request->driver_id;
            $trip->destination_hub_id = $request->destination_hub_id;
            $trip->status = 1;
//            $trip->remarks = $request->remarks;
            $trip->source_hub_id = auth()->user()->reference_id;
            $trip->responsible_user_id = auth()->user()->id;
            $trip->created_by = auth()->user()->id;
            $trip->updated_by = auth()->user()->id;
            $trip->trip_status = 1;
            $trip->save();

            // activityLog('user_id', 'ref_id', 'ref_table', 'text')
            $this->activityLog(auth()->user()->id, $trip->id, 'trips', 'Created a new trip: '.$trip->unique_trip_id);
            
            if (count(Session::get('trip_cart')) != 0) {
                $message = array();
                foreach (Session::get('trip_cart') as $unique_suborder_id) {
                    if($trip->trip_status == 1){

                        $sub_order = SubOrder::where('unique_suborder_id', $unique_suborder_id)
                                                ->where('status', '1')
                                                ->whereIn('sub_order_status', [15, 16, 17, 47])
                                                ->where('current_hub_id', '=', auth()->user()->reference_id)
                                                ->where('destination_hub_id', '!=', auth()->user()->reference_id)
                                                ->first();

                        if($sub_order){

                            $count = SuborderTrip::where('sub_order_id', $sub_order->id)->where('trip_id', $trip->id)->count();

                            if($count == 0){

                                $shelf = SuborderTrip::where('sub_order_id', '=', $sub_order->id)->where('status', '=', '1')->first();
                                if($shelf){
                                    $shelf->status = '0';
                                    $shelf->save();
                                }

                                $suborder_on_trip = new SuborderTrip();
                                $suborder_on_trip->sub_order_id = $sub_order->id;
                                $suborder_on_trip->trip_id = $trip->id;
                                $suborder_on_trip->save();

                                // Update Sub-Order Status
                                $this->suborderStatus($sub_order->id, '19');

                            }

                            $message[] = "$unique_suborder_id: Sub-Order uploaded successfully";

                        }else{ $message[] = "$unique_suborder_id: Invalid Sub-Order"; }

                    }else{ $message[] = "$unique_suborder_id: You can't add anything on this trip"; }

                }

                Session::forget('trip_cart');

                $msg_txt = '';
                foreach ($message as $text) {
                    $msg_txt = $msg_txt.$text."\n";
                }

                Session::flash('message', $msg_txt);
                return redirect('/trip/'.$trip->id.'/edit');
            }

        }else{
            Session::flash('message', "Cart is empty");
            return Redirect::back();
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
        $query = SubOrder::select(
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.unique_suborder_id',
                                    'orders.delivery_address1 AS delivery_address',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    'states_d.name AS delivery_state',
                                    'hubs.title AS delivery_hub',
                                    'order_product.product_title',
                                    DB::Raw("SUM(cart_product.quantity) as quantity"),
                                    'pc.name AS product_category',
                                    'suborder_trip.status AS sub_order_trip_status'
                                )
                                ->where('sub_orders.status', 1)
                                ->where('suborder_trip.trip_id', '=', $id)
                                ->leftJoin('suborder_trip','suborder_trip.sub_order_id','=','sub_orders.id')
                                ->leftJoin('hubs','hubs.id','=','sub_orders.destination_hub_id')
                                ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                                ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                                ->leftJoin('cart_product','cart_product.sub_order_id','=','sub_orders.id')
                                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                                ->leftJoin('stores','stores.id','=','orders.store_id')
                                ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                                ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                                ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                                ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id');

        $sub_orders = $query->groupBy('sub_orders.id')->orderBy('sub_orders.id', 'desc')->get();

        return view('trips.view', compact('sub_orders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $trip = Trip::findorfail($id);

        if(auth()->user()->reference_id != $trip->source_hub_id && $trip->trip_status != 1){
            return redirect('/trip');
        }

        $query = SubOrder::select(
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.unique_suborder_id',
                                    'orders.delivery_address1 AS delivery_address',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    'states_d.name AS delivery_state',
                                    'hubs.title AS delivery_hub',
                                    'next_hub.title AS next_hub_title',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'pc.name AS product_category'
                                )
                                ->where('sub_orders.status', 1)
                                ->whereIn('sub_order_status', [15, 16, 17, 47])
                                ->where('current_hub_id', '=', auth()->user()->reference_id)
                                ->where('sub_orders.destination_hub_id', '!=', auth()->user()->reference_id)
                                ->leftJoin('hubs','hubs.id','=','sub_orders.destination_hub_id')
                                ->leftJoin('hubs as next_hub','next_hub.id','=','sub_orders.next_hub_id')
                                ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                                ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                                ->leftJoin('stores','stores.id','=','orders.store_id')
                                ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                                ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                                ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                                ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id');

        if($request->has('order_id')){
            $query->where('orders.unique_order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('orders.merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_orders.unique_suborder_id', $request->sub_order_id);
        }

        if($request->has('sub_order_status')){
            $query->where('sub_orders.sub_order_status', $request->sub_order_status);
        }

        if($request->has('customer_mobile_no')){
            $query->where('orders.delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->where('orders.store_id', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->where('stores.merchant_id', $request->merchant_id);
        }

        if($request->has('delivery_hub_id')){
            $query->where('sub_orders.destination_hub_id', $request->delivery_hub_id);
        }

        if($request->has('start_date')){
            $start_date = $request->start_date;
        }else{
            $start_date = '2017-03-21';
        }

        if($request->has('end_date')){
            $end_date = $request->end_date;
        }else{
            $end_date = date('Y-m-d');
        }

        $query->WhereBetween('sub_orders.updated_at',array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        $sub_orders = $query->orderBy('sub_orders.id', 'desc')->get();

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'),'zones.id')
        ->leftJoin('cities','cities.id','=','zones.city_id')->
        where('zones.status',true)->lists('name','zones.id')->toArray();

        $vehicletypes = VehicleType::whereStatus(true)->lists('title', 'id')->toArray();
        $hubs = Hub::whereStatus(true)->where('id', '!=', auth()->user()->reference_id)->lists('title', 'id')->toArray();

        return view('trips.edit', compact('vehicletypes', 'hubs', 'sub_orders','merchants','stores', 'zones', 'trip'));
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
        //
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

    public function tripLoad(Request $request, $id){
        
        $validation = Validator::make($request->all(), [
                'unique_id' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $unique_id = $request->unique_id;
        $split_unique_id = explode('-', $request->unique_id);
        $trip = Trip::where('id', $id)->first();

        if($trip->trip_status == 1){

            $sub_order = SubOrder::where('unique_suborder_id', $unique_id)
                                    ->where('status', '1')
                                    ->whereIn('sub_order_status', [15, 16, 17, 47])
                                    ->where('current_hub_id', '=', auth()->user()->reference_id)
                                    ->where('destination_hub_id', '!=', auth()->user()->reference_id)
                                    ->first();

            if(count($sub_order)>0){

                $count = SuborderTrip::where('sub_order_id', $sub_order->id)->where('trip_id', $trip->id)->count();

                if($count == 0){

                    $shelf = SuborderTrip::where('sub_order_id', '=', $sub_order->id)->where('status', '=', '1')->first();
                    if($shelf){
                        $shelf->status = '0';
                        $shelf->save();
                    }

                    $suborder_on_trip = new SuborderTrip();
                    $suborder_on_trip->sub_order_id = $sub_order->id;
                    $suborder_on_trip->trip_id = $trip->id;
                    $suborder_on_trip->save();

                    // Update Sub-Order
                    // $suborderUp = SubOrder::findOrFail($sub_order->id);
                    // $suborderUp->sub_order_status = '6';
                    // $suborderUp->save();

                    // Update Sub-Order Status
                    $this->suborderStatus($sub_order->id, '19');

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $suborder_on_trip->id, 'suborder_trip', 'Sub-Order Loaded for trip');

                    // Update Order
                    $order_due = SubOrder::where('sub_order_status', '!=', '6')->where('order_id', '=', $sub_order->order_id)->count();
                    if($order_due == 0){
                        $orderUp = Order::findOrFail($sub_order->order_id);
                        $orderUp->order_status = '6';
                        $orderUp->save();

                        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                        $this->orderLog(auth()->user()->id, $orderUp->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) In Transit');
                    }

                }

                $message = "Sub-Order uploaded successfully";

            }else{
                $message = "Invalid Bar-Code";
            }

        }else{
            $message = "You can't add anything on this trip";
        }

        Session::flash('message', $message);
        return Redirect::back();

    }

    public function tripStart($id){
        $trip = Trip::where('id', $id)->where('trip_status', '1')->first();
        if(count($trip) > 0){
            if(auth()->user()->reference_id == $trip->source_hub_id){
                $trip->updated_by = auth()->user()->id;
                $trip->trip_status = '2';
                $trip->save();

                foreach ($trip->suborders as $suborder) {
                    // setting next receive hub
                    SubOrder::whereId($suborder->sub_order_id)->update(['next_hub_id' => $trip->destination_hub_id]);

                    // Update Sub-Order Status
                    $this->suborderStatus($suborder->sub_order_id, '20');

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(auth()->user()->id, $suborder->sub_order->order_id, $suborder->sub_order_id, '', $suborder->sub_order_id, 'sub_orders', 'Sub-Order Trip on Transit');
                }

                $message = 'Trip start from: '.$trip->source_hub->title;

                // activityLog('user_id', 'ref_id', 'ref_table', 'text')
                $this->activityLog(auth()->user()->id, $trip->id, 'trips', $message);
            }else{
                $message = "You haven't permission to start the trip";
            }
        }else{
            $message = "Invalid Trip";
        }

        Session::flash('message', $message);
        return redirect('/trip');
    }

    public function tripEnd($id){
        $trip = Trip::where('id', $id)->where('trip_status', '2')->first();
        if(count($trip) > 0){
            if(auth()->user()->reference_id == $trip->destination_hub_id){
                // need to check all suborder taken or not before ending the trip
                $leftProducts = SuborderTrip::where('trip_id', '=', $id)->where('status', '=', '1')->get();
                if(count($leftProducts) < 1){
                    $trip->updated_by = auth()->user()->id;
                    $trip->trip_status = '3';
                    $trip->save();

                    $message = 'Trip ends at: '.$trip->destination_hub->title;

                    // activityLog('user_id', 'ref_id', 'ref_table', 'text')
                    $this->activityLog(auth()->user()->id, $trip->id, 'trips', $message);
                }else{
                    $message = "All Product didn't receive yet!";
                }
            }else{
                $message = "You haven't permission to end the trip";
            }
        }else{
            $message = "Invalid Trip";
        }

        Session::flash('message', $message);
        return redirect('/trip');
    }

    public function tripCancel($id){
        $trip = Trip::where('id', $id)->where('trip_status', '1')->first();

        if(count($trip) > 0){
            $unique_trip_id = $trip->unique_trip_id;

            foreach ($trip->suborders as $suborder) {
                $sub_order_id = $suborder->sub_order_id;

                SuborderTrip::where('trip_id', '=', $trip->id)->where('sub_order_id', '=', $sub_order_id)->delete();

                // Update Sub-Order Status
                $this->suborderStatus($sub_order_id, '15');
            }

            Trip::where('id', '=', $id)->delete();

            Session::flash('message', "$unique_trip_id deleted successfully");
            return redirect('/trip');
        }else{
            Session::flash('message', 'Invalid Request');
            return redirect('/trip');
        }
    }

    public function onTrip($id)
    {

        $sub_orders = SuborderTrip::select(
                                    'suborder_trip.trip_id AS trip_id',
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.unique_suborder_id',
                                    'orders.delivery_address1 AS delivery_address',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    'states_d.name AS delivery_state',
                                    'hubs.title AS delivery_hub',
                                    'next_hub.title AS next_hub_title',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'pc.name AS product_category'
                                )
                                ->where('sub_orders.status', 1)
                                ->where('suborder_trip.trip_id', $id)
                                ->leftJoin('sub_orders', 'sub_orders.id', '=', 'suborder_trip.sub_order_id')
                                ->leftJoin('hubs','hubs.id','=','sub_orders.destination_hub_id')
                                ->leftJoin('hubs as next_hub','next_hub.id','=','sub_orders.next_hub_id')
                                ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                                ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                                ->leftJoin('stores','stores.id','=','orders.store_id')
                                ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                                ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                                ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                                ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id')->get();
        
        return $_GET['callback']."(".json_encode($sub_orders).")";
    }

    public function trip_submit_load(Request $request) {
        $validation = Validator::make($request->all(), [
            'tripconsignment_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if(Session::has('trip_cart') && count(Session::get('trip_cart'))>0){

            $trip = Trip::findOrFail($request->tripconsignment_id);
            
            if (count(Session::get('trip_cart')) != 0) {
                foreach (Session::get('trip_cart') as $unique_suborder_id) {
                    
                    if($trip->trip_status == 1){

                        $sub_order = SubOrder::where('unique_suborder_id', $unique_suborder_id)
                                                ->where('status', '1')
                                                ->whereIn('sub_order_status', [15, 16, 17, 47])
                                                ->where('current_hub_id', '=', auth()->user()->reference_id)
                                                ->where('destination_hub_id', '!=', auth()->user()->reference_id)
                                                ->first();

                        if(count($sub_order)>0){

                            $count = SuborderTrip::where('sub_order_id', $sub_order->id)->where('trip_id', $trip->id)->count();

                            if($count == 0){

                                $shelf = SuborderTrip::where('sub_order_id', '=', $sub_order->id)->where('status', '=', '1')->first();
                                if($shelf){
                                    $shelf->status = '0';
                                    $shelf->save();
                                }

                                $suborder_on_trip = new SuborderTrip();
                                $suborder_on_trip->sub_order_id = $sub_order->id;
                                $suborder_on_trip->trip_id = $trip->id;
                                $suborder_on_trip->save();

                                // Update Sub-Order Status
                                $this->suborderStatus($sub_order->id, '19');

                            }

                            $message = "Sub-Order uploaded successfully";

                        }else{ $message = "Invalid Sub-Order"; }

                    }else{ $message = "You can't add anything on this trip"; }

                }
            }

            Session::forget('trip_cart');

            Session::flash('message', "Sub-Order addedd successfully to $trip->unique_trip_id.");
            return redirect('trip/'.$trip->id.'/edit');

        }else{
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

    }

    public function remove_from_trip($trip_id, $sub_order_id){

        $sub_order = SubOrder::findOrFail($sub_order_id);
        $sub_order->sub_order_status = 15;
        $sub_order->save();

        SuborderTrip::where('trip_id', '=', $trip_id)->where('sub_order_id', '=', $sub_order_id)->delete();

        Session::flash('message', "$sub_order->unique_suborder_id Removed from the trip");
        return redirect('trip/'.$trip_id.'/edit');

    }


    public function trip_run_sheet($id){
        $trip = Trip::findorfail($id);

        $pdf = PDF::loadView('trips.pdf.run_sheet',['trip' => $trip])->setPaper('a4', 'landscape');

        return $pdf->stream($trip->unique_trip_id.'_run_sheet.pdf');
    }
}
