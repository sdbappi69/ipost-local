<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\SubOrder;
use App\Trip;
use App\SuborderTrip;
use App\Order;
use App\OrderProduct;

use Session;
use Redirect;
use Validator;

use App\Store;
use App\State;
use App\City;
use App\Zone;

use App\Merchant;
use App\User;

use App\Status;
use DB;

class QueuedShippingController extends Controller
{
    
    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:vehiclemanager|hubmanager|inventoryoperator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = SubOrder::select(
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.unique_suborder_id',
                                    'sub_orders.order_id',
                                    'sub_orders.no_of_delivery_attempts',
                                    'orders.unique_order_id',
                                    'orders.delivery_name',
                                    'orders.delivery_email',
                                    'orders.delivery_msisdn',
                                    'orders.delivery_alt_msisdn',
                                    'orders.delivery_address1 AS delivery_address',
                                    'orders.created_at',
                                    'orders.merchant_order_id',
                                    'hubs_d.title AS delivery_hub',
                                    'stores.store_id AS store_name',
                                    'merchants.name AS merchant_name',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    'states_d.name AS delivery_state',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'order_product.picking_attempts',
                                    'order_product.weight',
                                    'cart_product.weight AS proposed_weight',
                                    'product_categories.name AS product_category',
                                    'pickup_locations.title AS pickup_name',
                                    'pickup_locations.email AS pickup_email',
                                    'pickup_locations.msisdn AS pickup_msisdn',
                                    'pickup_locations.alt_msisdn AS pickup_alt_msisdn',
                                    'pickup_locations.address1 AS pickup_address',
                                    'hubs_p.title AS pickup_hub',
                                    'zones_p.name AS pickup_zone',
                                    'cities_p.name AS pickup_city',
                                    'states_p.name AS pickup_state',
                                    'status.title AS sub_order_status'
                                )
                            ->where('sub_orders.status', '!=', 0)
                            ->whereIn('sub_orders.sub_order_status', [15, 16])
                            ->where('sub_orders.source_hub_id', '=', auth()->user()->reference_id)
                            ->where('sub_orders.destination_hub_id', '!=', auth()->user()->reference_id)
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                            ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                            ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                            ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            ->leftJoin('cart_product','cart_product.order_product_id','=','order_product.id')
                            ->leftJoin('pickup_locations','pickup_locations.id','=','order_product.pickup_location_id')
                            ->leftJoin('product_categories','product_categories.id','=','order_product.product_category_id')
                            ->leftJoin('zones AS zones_p','zones_p.id','=','pickup_locations.zone_id')
                            ->leftJoin('cities AS cities_p','cities_p.id','=','pickup_locations.city_id')
                            ->leftJoin('states AS states_p','states_p.id','=','pickup_locations.state_id')
                            ->leftJoin('status','status.code','=','sub_orders.sub_order_status')
                            ->leftJoin('hubs AS hubs_p','hubs_p.id','=','sub_orders.source_hub_id')
                            ->leftJoin('hubs AS hubs_d','hubs_d.id','=','sub_orders.destination_hub_id');

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

        if($request->has('pickup_zone_id')){
            $query->where('pickup_locations.zone_id', $request->pickup_zone_id);
        }

        if($request->has('delivery_zone_id')){
            $query->where('orders.delivery_zone_id', $request->delivery_zone_id);
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

        $pickupman = User::
         select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
         ->leftJoin('hubs','hubs.id','=','users.reference_id')
         ->where('reference_id', '=', auth()->user()->reference_id)
         ->where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'),'zones.id')
        ->leftJoin('cities','cities.id','=','zones.city_id')->
        where('zones.status',true)->lists('name','zones.id')->toArray();

        $trips = Trip::whereStatus(true)->where('source_hub_id', '=', auth()->user()->reference_id)->where('trip_status', '=', '1')->lists('unique_trip_id', 'id')->toArray();

        return view('queued-shipping.index', compact('sub_orders', 'stores', 'merchants', 'pickupman', 'zones'));
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
        //
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
            'order_id' => 'required',
            'sub_order_id' => 'required',
            'trip_id' => 'required',
            'remarks' => 'sometimes',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Insert Sub-Order & Trip
        $suborder_trip = new SuborderTrip();
        $suborder_trip->sub_order_id = $request->sub_order_id;
        $suborder_trip->trip_id = $request->trip_id;
        $suborder_trip->remarks = $request->remarks;
        $suborder_trip->created_by = auth()->user()->id;
        $suborder_trip->updated_by = auth()->user()->id;
        $suborder_trip->save();
        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $request->order_id, $request->sub_order_id, '', $suborder_trip->trip_id, 'trips', 'Sub-Order uploaded on a trip');

        // Update Sub-Order
        $suborderUp = SubOrder::findOrFail($request->sub_order_id);
        $suborderUp->sub_order_status = '6';
        $suborderUp->save();
        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $suborderUp->order_id, $suborderUp->id, '', auth()->user()->reference_id, 'hubs', 'Sub-Order In Transit');

        // Update Order
        $order_due = SubOrder::where('sub_order_status', '!=', '6')->where('order_id', '=', $request->order_id)->count();
        if($order_due == 0){
            $orderUp = Order::findOrFail($request->order_id);
            $orderUp->order_status = '6';
            $orderUp->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $orderUp->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) In Transit');
        }

        Session::flash('message', "Sub-Order processed successfully");
        return redirect('/transfer-suborder');

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
