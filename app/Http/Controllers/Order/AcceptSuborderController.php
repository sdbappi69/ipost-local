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
use App\SuborderTrip;
use DB;
use Session;
use Redirect;
use Validator;

use App\Store;
use App\State;
use App\City;
use App\Zone;

use App\Merchant;

use App\Status;

class AcceptSuborderController extends Controller
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
        $this->middleware('role:hubmanager|vehiclemanager|inventoryoperator');
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
                            ->where('sub_orders.current_hub_id', '!=', auth()->user()->reference_id)
                            // ->where('sub_orders.source_hub_id', '!=', auth()->user()->reference_id)
                            ->where('sub_orders.next_hub_id', '=', auth()->user()->reference_id)
                            ->whereIn('sub_orders.sub_order_status', [20, 21])
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
                            ->leftJoin('hubs AS hubs_c','hubs_c.id','=','sub_orders.current_hub_id')
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

        return view('accept-suborder.index', compact('sub_orders', 'stores', 'merchants', 'pickupman', 'zones'));
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
            'unique_suborder_id' => 'required',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Update Sub-Order
        $suborder = SubOrder::where('unique_suborder_id', $request->unique_suborder_id)
                    ->whereIn('sub_order_status', [20, 21])
                    // ->where('source_hub_id', '!=', auth()->user()->reference_id)
                    ->where('current_hub_id', '!=', auth()->user()->reference_id)
                    ->where('next_hub_id', '=', auth()->user()->reference_id)
                    ->first();

        if(count($suborder) == 1){
            // Update Sub-Order Status
            $this->suborderStatus($suborder->id, '22');

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            // $this->orderLog(auth()->user()->id, $suborder->order_id, $suborder->id, '', $suborder->deliveryman_id, 'users', 'Assigned a delivery man for the Sub-Order');
            $this->orderLog(auth()->user()->id, $suborder->order_id, $suborder->id, '', auth()->user()->reference_id, 'hubs', 'Received Sub-Order at destination');

            // Update Order
            // $order_due = SubOrder::where('sub_order_status', '!=', '7')->where('order_id', '=', $request->order_id)->count();

            // if($order_due == 0){
            //     $order = Order::findOrFail($suborder->order_id);
            //     $order->order_status = '7';
            //     $order->save();

            //     // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            //     $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) received');
            // }

            // Release Trip
            $trip = SuborderTrip::where('sub_order_id', '=', $suborder->id)->where('status', '=', '1')->firstOrFail();
            $trip->status = '2';
            $trip->save();
            // return 1;
            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $suborder->order_id, $suborder->id, '', $trip->id, 'trips', 'Sub-Order released from the trip');

            // Select Hubs
            $picking_hub_id = $suborder->source_hub_id;
            $delivery_hub_id = $suborder->destination_hub_id;

            // Decide Hub or Rack
            $sub_order_size = OrderProduct::select(array(
                                                        DB::raw("SUM(`width`) AS width"),
                                                        DB::raw("SUM(`height`) AS height"),
                                                        DB::raw("SUM(`length`) AS length"),
                                                    ))
                                                ->where('sub_order_id', '=', $suborder->id)
                                                ->where('status', '!=', '0')
                                                ->first();

            $rackData = Rack::whereStatus(true)->where('hub_id', '=', $delivery_hub_id)->get();
            if($rackData->count() != 0){
                foreach ($rackData as $rack) {
                    $rackUsed = RackProduct::select(array(
                                                        DB::raw("SUM(`width`) AS total_width"),
                                                        DB::raw("SUM(`height`) AS total_height"),
                                                        DB::raw("SUM(`length`) AS total_length"),
                                                    ))
                                                ->join('order_product AS op', 'op.id', '=', 'rack_products.product_id')
                                                ->where('rack_products.status', '=', '1')
                                                ->where('rack_products.rack_id', '=', $rack->id)
                                                ->first();
                    $available_width = $rack->width - $rackUsed->width;
                    $available_height = $rack->height - $rackUsed->height;
                    $available_length = $rack->length - $rackUsed->length;

                    if($available_width >= $sub_order_size->width && $available_height >= $sub_order_size->height && $available_length >= $sub_order_size->length){
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

            // Insert product on rack
            $sub_order_products = OrderProduct::where('sub_order_id', '=', $suborder->id)
                                                ->where('status', '!=', '0')
                                                ->get();

            if($suborder->destination_hub_id == auth()->user()->reference_id){
                // Update Sub-Order Status
                $this->suborderStatus($suborder->id, '26');
            }else{

                $next_hub_id = $this->getNextHubId($suborder->id, auth()->user()->reference_id);
                $suborder->next_hub_id = $next_hub_id;
                $suborder->current_hub_id = auth()->user()->reference_id;
                $suborder->save();

            }

            foreach ($sub_order_products as $product) {
                $rack_suborder = new RackProduct();
                $rack_suborder->rack_id = $rack_id;
                $rack_suborder->product_id = $product->id;
                $rack_suborder->status = '1';
                $rack_suborder->created_by = auth()->user()->id;
                $rack_suborder->updated_by = auth()->user()->id;
                $rack_suborder->save();

                // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                $this->orderLog(auth()->user()->id, $suborder->order_id, $suborder->id, $rack_suborder->product_id, $rack_suborder->id, 'rack_products', 'Product racked');
            }
        }else{
            $message = 'Invalid Request';
        }
        // $suborder->sub_order_status = '7';
        // $suborder->delivery_assigned_by = auth()->user()->id;
        // $suborder->deliveryman_id = $request->deliveryman_id;
        // $suborder->delivery_status = '0';
        // $suborder->save();

        

        Session::flash('message', $message);
        return redirect('/accept-suborder');
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
}
