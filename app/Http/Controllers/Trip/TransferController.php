<?php

namespace App\Http\Controllers\Trip;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use DB;

use PDF;
use App\OrderProduct;
use App\Consignment;
use App\Del;
use App\Order;
use Validator;
use App\Http\Traits\LogsTrait;

use App\SubOrder;

use App\DeliveryTask;
use Illuminate\Support\Facades\Redirect;
use Session;

use App\Store;
use App\Merchant;
use App\City;
use App\Zone;
use App\Trip;

use App\Country;
use App\State;
use App\OrderHistory;

class TransferController extends Controller
{

    use LogsTrait;
    //
    public function __construct()
    {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|inboundmanager|vehiclemanager');
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
                                    'orders.delivery_address1 AS delivery_address',
                                    'orders.id AS order_id',
                                    'orders.delivery_country_id',
                                    'orders.delivery_state_id',
                                    'orders.delivery_city_id',
                                    'orders.delivery_zone_id',
                                    'orders.delivery_name',
                                    'orders.delivery_email',
                                    'orders.delivery_msisdn',
                                    'orders.delivery_alt_msisdn',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    'states_d.name AS delivery_state'
                                )
                                ->where('sub_orders.status', 1)
                                ->whereIn('sub_orders.sub_order_status', [26, 34])
                                ->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id)
                                ->where('sub_orders.return', '=', '0')
                                ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                                ->leftJoin('order_product AS op','op.sub_order_id','=','sub_orders.id')
                                ->leftJoin('stores','stores.id','=','orders.store_id')
                                ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'op.pickup_location_id')
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

        if($request->has('pickup_man_id')){
            $query->where('order_product.picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->where('sub_orders.deliveryman_id', $request->delivary_man_id);
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

        $sub_orders = $query->orderBy('sub_orders.id', 'desc')->paginate(10);


        $deliveryman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', 20)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->whereStatus(true)->lists('name', 'id')->toArray();
        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'),'zones.id')
        ->leftJoin('cities','cities.id','=','zones.city_id')->
        where('zones.status',true)->lists('name','zones.id')->toArray();

        return view('trips.transfer.index',compact('sub_orders','deliveryman','merchants','stores', 'zones', 'countries', 'states', 'cities'));
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
        $validation = Validator::make($request->all(), [
            'delivery_name' => 'required',
            'delivery_email' => 'required',
            'delivery_msisdn' => 'required',
            'delivery_alt_msisdn' => 'sometimes',
            'delivery_country_id' => 'required',
            'delivery_state_id' => 'required',
            'delivery_city_id' => 'required',
            'delivery_zone_id' => 'required',
            'delivery_address1' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Order Old Detail
        $sub_order = SubOrder::findOrFail($id);
        $order = Order::findOrFail($sub_order->order_id);

        // Keep History
        $order_history = new OrderHistory();
        $order_history->order_id = $order->id;
        $order_history->delivery_name = $order->delivery_name;
        $order_history->delivery_email = $order->delivery_email;
        $order_history->delivery_msisdn = $order->delivery_msisdn;
        $order_history->delivery_alt_msisdn = $order->delivery_alt_msisdn;
        $order_history->delivery_country_id = $order->delivery_country_id;
        $order_history->delivery_state_id = $order->delivery_state_id;
        $order_history->delivery_city_id = $order->delivery_city_id;
        $order_history->delivery_zone_id = $order->delivery_zone_id;
        $order_history->delivery_address1 = $order->delivery_address1;
        $order_history->created_by = auth()->user()->id;
        $order_history->updated_by = auth()->user()->id;
        $order_history->save();

        // Update new
        $order->delivery_name = $request->delivery_name;
        $order->delivery_email = $request->delivery_email;
        $order->delivery_msisdn = $request->delivery_msisdn;
        $order->delivery_alt_msisdn = $request->delivery_alt_msisdn;
        $order->delivery_country_id = $request->delivery_country_id;
        $order->delivery_state_id = $request->delivery_state_id;
        $order->delivery_city_id = $request->delivery_city_id;
        $order->delivery_zone_id = $request->delivery_zone_id;
        $order->delivery_address1 = $request->delivery_address1;
        $order->updated_by = auth()->user()->id;
        $order->save();

        // Transfer Hub
        $sub_order->next_hub_id = auth()->user()->reference_id;
        $sub_order->destination_hub_id = $order->delivery_zone->hub_id;
        $sub_order->save();

        // Update Sub-Order Status
        if($sub_order->destination_hub_id != $sub_order->next_hub_id){
            $this->suborderStatus($sub_order->id, '47');
        }

        Session::flash('message', 'Delivery Information Changed');
        return redirect('/transfer');

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
