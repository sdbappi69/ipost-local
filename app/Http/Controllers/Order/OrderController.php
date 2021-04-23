<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Country;
use App\Order;
use App\Store;
use App\State;
use App\City;
use App\Zone;
use App\ProductCategory;
use App\PickingLocations;
use App\OrderProduct;
use App\PickingTimeSlot;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Entrust;

use App\SubOrder;

use App\Merchant;
use App\User;

use App\Status;
use App\Hub;
use App\ExtractLog;

use Excel;

class OrderController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|head_of_accounts|customerservice|salesteam|coo|saleshead|operationmanager|operationalhead|kam|hocs|customer_care_agent');
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
                                    'sub_orders.return',
                                    'sub_orders.sub_order_status',
                                    'sub_orders.sub_order_last_status',
                                    'sub_orders.sub_order_note',
                                    'orders.unique_order_id',
                                    'orders.delivery_name',
                                    'orders.delivery_email',
                                    'orders.delivery_msisdn',
                                    'orders.delivery_alt_msisdn',
                                    'orders.delivery_address1 AS delivery_address',
                                    'orders.created_at',
                                    'orders.merchant_order_id',
                                    'stores.store_id AS store_name',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'order_product.picking_attempts',
                                    'order_product.weight',
                                    'order_product.sub_total',
                                    'order_product.delivery_paid_amount'
                                )
                            ->where('sub_orders.status', '!=', 0)
                            ->where('sub_orders.sub_order_status', '>', 1)
                            ->where('sub_orders.parent_sub_order_id', 0)
                            ->where('sub_orders.created_at', '>', '2017-06-05 23:59:59')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            ->leftJoin('status','status.code','=','sub_orders.sub_order_status')
                            ->leftJoin('pickup_locations','pickup_locations.id','=','order_product.pickup_location_id')
                            ->leftJoin('zones','zones.id','=','orders.delivery_zone_id');

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

        // if(Auth::user()->hasRole('salesteam')){
        //     $query->leftJoin('merchants','merchants.id','=','stores.merchant_id');
        //     $query->where('merchants.responsible_user_id', auth()->user()->id);
        // }

        if($request->has('sub_order_status')){

            // $query->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id');

            // $statusInfo = Status::whereIn('code', $request->sub_order_status)->get();
            // $whereStatusText = array();
            // foreach ($statusInfo as $si) {
            //     $whereStatusText[] = $si->title;
            // }

            // $query->whereIn('order_logs.text',$whereStatusText)
            //     ->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')
            //     ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

            $query->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id');

//            $codes = array();
//            foreach ($request->sub_order_status as $sos) {
//                $ocs = hubWhereInStatus($sos);
//                foreach ($ocs as $sos_codes) {
//                    $codes[] = $sos_codes;
//                }
//            }
//
//            $statusInfo = Status::whereIn('code', $codes)->get();
//            $whereStatusText = array();
//            foreach ($statusInfo as $si) {
//                $whereStatusText[] = $si->title;
//            }

            $query->whereIn('order_logs.sub_order_status',$request->sub_order_status)
                ->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')
                ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        }else{
            $query->where('sub_orders.updated_at', '!=', '0000-00-00 00:00:00')->WhereBetween('sub_orders.updated_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
        }

        if($request->has('order_id')){
            $query->where('orders.unique_order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('orders.merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_orders.unique_suborder_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('orders.delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('orders.store_id', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('stores.merchant_id', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('order_product.picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('sub_orders.deliveryman_id', $request->delivary_man_id);
        }

        // if($request->has('sub_order_status')){
        //     $query->where('sub_orders.sub_order_status', $request->sub_order_status);
        // }

        if($request->has('pickup_zone_id')){
            $query->whereIn('pickup_locations.zone_id', $request->pickup_zone_id);
        }

        if($request->has('delivery_zone_id')){
            $query->whereIn('orders.delivery_zone_id', $request->delivery_zone_id);
        }

        if($request->has('current_sub_order_status')){
            //$query->where('sub_orders.sub_order_last_status', $request->current_sub_order_status);

            $whereCurrentStatus = array();
            foreach ($request->current_sub_order_status as $si) {
                $currentStatusInfo = hubWhereInStatus($si);
                foreach ($currentStatusInfo as $currentStatus) {
                    $whereCurrentStatus[] = $currentStatus;
                }
            }

            $query->where(function($q) use ($whereCurrentStatus){

                $q->whereIn('sub_orders.sub_order_last_status',$whereCurrentStatus);
                $q->orWhereIn('sub_orders.sub_order_status',$whereCurrentStatus);

            });
        }

        if($request->has('hub_id')){
            $query->whereIn('zones.hub_id', $request->hub_id);
        }

        $sub_orders = $query->groupBy('sub_orders.id')->orderBy('sub_orders.id', 'desc')->paginate(10);

        // DB::enableQueryLog();
        // $sub_orders = $query->groupBy('sub_orders.id')->orderBy('sub_orders.id', 'desc')->get();
        // dd(DB::getQueryLog());

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
        // $sub_order_status = Status::where('id', '>', 1)->lists('title', 'id')->toArray();
        $sub_order_status = hubAllStatus();
        // pick man

        $pickupman = User::
        select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
        ->leftJoin('hubs','hubs.id','=','users.reference_id')->
        where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'),'zones.id')
        ->leftJoin('cities','cities.id','=','zones.city_id')->
        where('zones.status',true)->lists('name','zones.id')->toArray();

        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

        return view('orders.index', compact('pickupman','merchants','stores','sub_order_status','sub_orders', 'zones', 'hubs'));
    }


    public function orderexport(Request $request, $type){
        set_time_limit(1200);
        $query = SubOrder::select(
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.unique_suborder_id',
                                    'sub_orders.order_id',
                                    'sub_orders.no_of_delivery_attempts',
                                    'sub_orders.sub_order_status',
                                    'sub_orders.sub_order_last_status',
                                    'sub_orders.return',
                                    'sub_orders.sub_order_note',
                                    'orders.unique_order_id',
                                    'orders.delivery_name',
                                    'orders.delivery_email',
                                    'orders.delivery_msisdn',
                                    // 'orders.delivery_alt_msisdn',
                                    'orders.delivery_address1 AS delivery_address',
                                    'orders.created_at',
                                    'orders.merchant_order_id',
                                    'hubs_d.title AS delivery_hub',
                                    'stores.store_id AS store_name',
                                    'merchants.name AS merchant_name',
                                    'zones_d.name AS delivery_zone',
                                    'cities_d.name AS delivery_city',
                                    // 'states_d.name AS delivery_state',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'order_product.picking_attempts',
                                    'order_product.weight',
                                    'order_product.sub_total',
                                    'order_product.delivery_paid_amount',
                                    // 'cart_product.weight AS proposed_weight',
                                    // 'cart_product.weight AS proposed_weight',
                                    // 'product_categories.name AS product_category',
                                    'pickup_locations.title AS pickup_name',
                                    'pickup_locations.email AS pickup_email',
                                    'pickup_locations.msisdn AS pickup_msisdn',
                                    // 'pickup_locations.alt_msisdn AS pickup_alt_msisdn',
                                    'pickup_locations.address1 AS pickup_address',
                                    'hubs_p.title AS pickup_hub',
                                    'zones_p.name AS pickup_zone',
                                    // 'cities_p.name AS pickup_city',
                                    // 'states_p.name AS pickup_state',
                                    // 'status.title AS sub_order_status',
                                    'delivery_task.updated_at AS final_delivery_attempt'
                                )
                            ->where('sub_orders.status', '!=', 0)
                            ->where('sub_orders.sub_order_status', '>', 1)
                            ->where('sub_orders.parent_sub_order_id', 0)
                            ->where('sub_orders.created_at', '>', '2017-06-05 23:59:59')
                            ->leftJoin('delivery_task','delivery_task.unique_suborder_id','=','sub_orders.unique_suborder_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                            ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                            ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                            // ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            // ->leftJoin('cart_product','cart_product.order_product_id','=','order_product.id')
                            ->leftJoin('pickup_locations','pickup_locations.id','=','order_product.pickup_location_id')
                            ->leftJoin('zones AS zones_pl','zones_pl.id','=','pickup_locations.zone_id')
                            // ->leftJoin('product_categories','product_categories.id','=','order_product.product_category_id')
                            ->leftJoin('zones AS zones_p','zones_p.id','=','pickup_locations.zone_id')
                            // ->leftJoin('cities AS cities_p','cities_p.id','=','pickup_locations.city_id')
                            // ->leftJoin('states AS states_p','states_p.id','=','pickup_locations.state_id')
                            ->leftJoin('status','status.code','=','sub_orders.sub_order_status')
                            ->leftJoin('hubs AS hubs_p','hubs_p.id','=','zones_pl.hub_id')
                            ->leftJoin('hubs AS hubs_d','hubs_d.id','=','zones_d.hub_id');

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

        if($request->has('sub_order_status')){

            // $query->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id');

            // $statusInfo = Status::whereIn('code', $request->sub_order_status)->get();
            // $whereStatusText = array();
            // foreach ($statusInfo as $si) {
            //     $whereStatusText[] = $si->title;
            // }

            // $query->whereIn('order_logs.text',$whereStatusText)
            //     ->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')
            //     ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

            $query->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id');

//            $codes = array();
//            foreach ($request->sub_order_status as $sos) {
//                $ocs = hubWhereInStatus($sos);
//                foreach ($ocs as $sos_codes) {
//                    $codes[] = $sos_codes;
//                }
//            }
//
//            $statusInfo = Status::whereIn('code', $codes)->get();
//            $whereStatusText = array();
//            foreach ($statusInfo as $si) {
//                $whereStatusText[] = $si->title;
//            }

            $query->whereIn('order_logs.sub_order_status',$request->sub_order_status)
                ->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')
                ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        }else{
            $query->where('sub_orders.updated_at', '!=', '0000-00-00 00:00:00')->WhereBetween('sub_orders.updated_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
        }

        if($request->has('order_id')){
            $query->where('orders.unique_order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('orders.merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_orders.unique_suborder_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('orders.delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('orders.store_id', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('stores.merchant_id', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('order_product.picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('sub_orders.deliveryman_id', $request->delivary_man_id);
        }

        // if($request->has('sub_order_status')){
        //     $query->where('sub_orders.sub_order_status', $request->sub_order_status);
        // }

        if($request->has('pickup_zone_id')){
            $query->whereIn('pickup_locations.zone_id', $request->pickup_zone_id);
        }

        if($request->has('delivery_zone_id')){
            $query->whereIn('orders.delivery_zone_id', $request->delivery_zone_id);
        }

        // if($request->has('current_sub_order_status')){
        //     $query->where(function($q) use ($request, $start_date, $end_date){
        //             $q->whereIn('sub_orders.sub_order_last_status',$request->current_sub_order_status)
        //             ->whereNotNull('sub_orders.sub_order_last_status')
        //             ->where('sub_orders.updated_at', '!=', '0000-00-00 00:00:00')->WhereBetween('sub_orders.updated_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
        //     })->orWhere(function($q) use ($request, $start_date, $end_date){
        //         $q->whereIn('sub_orders.sub_order_status',$request->current_sub_order_status)
        //         ->where('sub_orders.sub_order_last_status',null)
        //         ->where('sub_orders.updated_at', '!=', '0000-00-00 00:00:00')->WhereBetween('sub_orders.updated_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
        //     });
        // }
        if($request->has('current_sub_order_status')){
            //$query->where('sub_orders.sub_order_last_status', $request->current_sub_order_status);

            $whereCurrentStatus = array();
            foreach ($request->current_sub_order_status as $si) {
                $currentStatusInfo = hubWhereInStatus($si);
                foreach ($currentStatusInfo as $currentStatus) {
                    $whereCurrentStatus[] = $currentStatus;
                }
            }

            $query->where(function($q) use ($whereCurrentStatus){

                $q->whereIn('sub_orders.sub_order_last_status',$whereCurrentStatus);
                $q->orWhereIn('sub_orders.sub_order_status',$whereCurrentStatus);

            });

            // $query->where(function($q) use ($request){

            //     $q->whereIn('sub_orders.sub_order_last_status',$whereCurrentStatus);
            //     $q->orWhereIn('sub_orders.sub_order_status',$whereCurrentStatus);

            //     // $q->whereIn('sub_orders.sub_order_last_status',$request->current_sub_order_status);
            //     // $q->orWhereIn('sub_orders.sub_order_status',$request->current_sub_order_status);
            // });
        }

        $sub_orders = $query->groupBy('sub_orders.id')->orderBy('sub_orders.id', 'desc')->get()->toArray();

        // echo "<pre>";
        // print_r($datasheet); exit;

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order List';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('order_'.time(), function($excel) use ($sub_orders) {
            $excel->sheet('orders', function($sheet) use ($sub_orders)
            {

                $datasheet = array();
                $datasheet[0]  =   array(
                    'Order Id',
                    'Sub-Order Id',
                    'Type',
                    'Merchant Order Id',
                    'Merchant',
                    'Store',
                    'Created',
                    'Product',
                    'Quantity',
                    'Verified Weight',
                    'Amount to be collected',
                    'Amount collected',
                    'Pickup Name',
                    'Pickup Email',
                    'Pickup Mobile',
                    'Pickup Address',
                    'Pickup Zone',
                    'Picking Hub',
                    'Picking Attempts',
                    'Delivery Name',
                    'Delivery Email',
                    'Delivery Mobile',
                    'Delivery Address',
                    'Delivery Zone',
                    'Delivery City',
                    'Delivery Hub',
                    'Delivery Attempts',
                    'Current Status',
                    'Latest picking attempt',
                    'Latest picking reason',
                    'Latest delivery attempt',
                    'Latest delivery reason'
                );
                $i=1;
                foreach($sub_orders as $datanew){

                    if($datanew['return'] == 1){
                        $type = 'Return';
                    }else{
                        $type = 'Delivery';
                    }

                    if($datanew['sub_order_last_status'] === NULL){
                        $sub_order_status = hubGetStatus($datanew['sub_order_status']);
                    }else{
                        $sub_order_status = hubGetStatus($datanew['sub_order_last_status']);
                    }

                    if (!preg_match("/^[a-zA-Z0-9._\-\s\(\)]+$/", $datanew['delivery_name'])){
                        $delivery_name = '';
                    } else {
                        $delivery_name = $datanew['delivery_name'];
                    }

                    $sub_order_note = json_decode($datanew['sub_order_note'],true);

                    if(isset($sub_order_note['tat'])){ $tat = $sub_order_note['tat']; }else{ $tat = ""; }
                    if(isset($sub_order_note['pickup_aging'])){ $pickup_aging = $sub_order_note['pickup_aging']; }else{ $pickup_aging = ""; }
                    if(isset($sub_order_note['delivery_aging'])){ $delivery_aging = $sub_order_note['delivery_aging']; }else{ $delivery_aging = ""; }
                    if(isset($sub_order_note['delivery_attempt_aging'])){ $delivery_attempt_aging = $sub_order_note['delivery_attempt_aging']; }else{ $delivery_attempt_aging = ""; }
                    if(isset($sub_order_note['latest_picking_attempt'])){ $latest_picking_attempt = $sub_order_note['latest_picking_attempt']; }else{ $latest_picking_attempt = ""; }
                    if(isset($sub_order_note['latest_picking_reason'])){ $latest_picking_reason = $sub_order_note['latest_picking_reason']; }else{ $latest_picking_reason = ""; }
                    // if(isset($sub_order_note['latest_delivery_attempt'])){ $latest_delivery_attempt = $sub_order_note['latest_delivery_attempt']; }else{ $latest_delivery_attempt = ""; }
                    // if(isset($sub_order_note['latest_delivery_reason'])){ $latest_delivery_reason = $sub_order_note['latest_delivery_reason']; }else{ $latest_delivery_reason = ""; }

                    $last_delivery_attempt = lastDeliveryTask($datanew['suborder_id'], $datanew['unique_suborder_id']);

                    $datasheet[$i] = array(
                        (string)$datanew['unique_order_id'],
                        (string)$datanew['unique_suborder_id'],
                        (string)$type,
                        (string)$datanew['merchant_order_id'],
                        (string)$datanew['merchant_name'],
                        (string)$datanew['store_name'],
                        (string)$datanew['created_at'],
                        (string)$datanew['product_title'],
                        (string)$datanew['quantity'],
                        (string)$datanew['weight'],
                        (string)$datanew['sub_total'],
                        (string)$datanew['delivery_paid_amount'],
                        (string)$datanew['pickup_name'],
                        (string)$datanew['pickup_email'],
                        (string)$datanew['pickup_msisdn'],
                        (string)$datanew['pickup_address'],
                        (string)$datanew['pickup_zone'],
                        (string)$datanew['pickup_hub'],
                        (string)$datanew['picking_attempts'],
                        $delivery_name,
                        (string)$datanew['delivery_email'],
                        (string)$datanew['delivery_msisdn'],
                        (string)$datanew['delivery_address'],
                        (string)$datanew['delivery_zone'],
                        (string)$datanew['delivery_city'],
                        (string)$datanew['delivery_hub'],
                        (string)$datanew['no_of_delivery_attempts'],
                        (string)$sub_order_status,
                        (string)$latest_picking_attempt,
                        (string)$latest_picking_reason,
                        (string)$last_delivery_attempt['updated_at'],
                        (string)$last_delivery_attempt['reason']
                    );

                    $i++;
                }

                $sheet->setOrientation('landscape');

                // Freeze first row
                $sheet->freezeFirstRow();

                $sheet->fromArray($datasheet);
            });
        })->download($type);
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
     //   dd($id);
        $order = Order::whereStatus(true)->findOrFail($id);
        return view('orders.view', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // For Page One
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $order->delivery_country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $order->delivery_state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $order->delivery_city_id)->lists('name', 'id')->toArray();

        $stores = Store::whereStatus(true)->where('merchant_id', '=', $order->store->merchant->id)->lists('store_id', 'id')->toArray();

        // // For Page Two
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
        $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', $order->store->merchant->id)->lists('title', 'id')->toArray();
        $products = OrderProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();
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

        // Call Charge Calculation API
        // return env('APP_URL').'api/charge-calculator?store_id='.$order->store->store_id.'&order_id='.$order->unique_order_id;
        $chargesJson = file_get_contents(env('APP_URL').'api/charge-calculator?store_id='.$order->store->store_id.'&order_id='.$order->unique_order_id);
        $charges = json_decode($chargesJson);
        if($charges->status == 'Failed'){
            abort(403);
        }

        if($request->step){
            $step = $request->step;
        }else{
            $step = 1;
        }

        // return view('orders.edit', compact('prefix', 'countries', 'step', 'id', 'order', 'states', 'cities', 'zones', 'stores', 'categories', 'warehouse', 'products', 'picking_time_slot', 'shipping_loc', 'charges'));

        return view('orders.edit', compact('prefix', 'countries', 'step', 'id', 'order', 'states', 'cities', 'zones', 'stores', 'categories', 'warehouse', 'products', 'picking_time_slot', 'shipping_loc', 'charges'));
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
            'store_id' => 'sometimes',
            'delivery_name' => 'sometimes',
            'delivery_email' => 'sometimes|email',
            'delivery_msisdn' => 'sometimes|between:10,25',
            'delivery_alt_msisdn' => 'sometimes|between:10,25',
            'delivery_country_id' => 'sometimes',
            'delivery_state_id' => 'sometimes',
            'delivery_city_id' => 'sometimes',
            'delivery_zone_id' => 'sometimes',
            'delivery_address1' => 'sometimes',
            'amount' => 'sometimes',
            'delivery_payment_amount' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $order = Order::findOrFail($id);

        $order->fill($request->except('msisdn_country','alt_msisdn_country','step','include_delivery','amount_hidden'));
        if($request->include_delivery){
            if($request->include_delivery == '1'){
                $order->total_amount = $request->amount + $request->delivery_payment_amount;
            }
        }
        $order->save();

        if($request->step){
            if($request->step == 'complete'){
                // return $request->step;
                Session::flash('message', "Order information updated successfully");
                return redirect('/order');
            }else{
                $step = $request->step;
            }
        }else{
            $step = 1;
        }
        Session::flash('message', "Order information saved successfully");
        return redirect('/order/'.$id.'/edit?step='.$step);
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

    public function draft(Request $request)
    {
        $query = Order::where('order_status', '=', '1');

        ($request->has('customer_mobile_no'))      ? $query->where('orders.delivery_msisdn', 'like', '%' . $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', 'like', '%' . $request->customer_mobile_no) : null;
        ($request->has('store_id'))  ? $query->where('orders.store_id',$request->store_id) : null;
        ($request->has('merchant_order_id'))  ? $query->where('orders.merchant_order_id','like','%'.$request->merchant_order_id) : null;

        ($request->has('search_date') )  ? $query->whereDate('orders.created_at','=',$request->search_date) : null;

        $orders = $query->orderBy('id', 'desc')->get();

        $stores = Store::select(DB::raw('CONCAT(stores.store_id, " - ", merchants.name) AS name'),'stores.id')
                        ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                        ->where('stores.status', 1)
                        ->where('merchants.status', 1)
                        ->lists('name', 'id')
                        ->toArray();

        return view('orders.draft',compact('orders', 'stores'));
    }
}
