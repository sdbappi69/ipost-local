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
use App\ExtractLog;

use Excel;

class MerchantOrderController extends Controller
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
        $this->middleware('role:merchantadmin|merchantsupport|storeadmin|salesteam|saleshead|superadministrator|systemadministrator|operationmanager|operationalhead');
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
                                    'sub_orders.id',
                                    'sub_orders.parent_sub_order_id',
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
                                    // 'merchants.name AS merchant_name',
                                    // 'zones_d.name AS delivery_zone',
                                    // 'cities_d.name AS delivery_city',
                                    // 'states_d.name AS delivery_state',
                                    'order_product.product_title',
                                    'order_product.quantity',
                                    'order_product.picking_attempts',
                                    'order_product.weight',
                                    'order_product.sub_total',
                                    'order_product.delivery_paid_amount'
                                    // 'cart_product.weight AS proposed_weight',
                                    // 'product_categories.name AS product_category',
                                    // 'pickup_locations.title AS pickup_name',
                                    // 'pickup_locations.email AS pickup_email',
                                    // 'pickup_locations.msisdn AS pickup_msisdn',
                                    // 'pickup_locations.alt_msisdn AS pickup_alt_msisdn',
                                    // 'pickup_locations.address1 AS pickup_address',
                                    // 'zones_p.name AS pickup_zone',
                                    // 'cities_p.name AS pickup_city',
                                    // 'states_p.name AS pickup_state',
                                    // 'status.code AS sub_order_status_code'
                                    // 'status.title AS sub_order_status'
                                    // 'delivery_task.updated_at AS final_delivery_attempt'
                                )
                            ->where('sub_orders.status', '!=', 0)
                            ->where('sub_orders.sub_order_status', '>', 1)
                            ->where('sub_orders.parent_sub_order_id', 0)
                            ->where('sub_orders.created_at', '>', '2017-06-05 23:59:59')
                            // ->leftJoin('delivery_task','delivery_task.unique_suborder_id','=','sub_orders.unique_suborder_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            // ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                            // ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                            // ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                            // ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id');

                            // ->leftJoin('cart_product','cart_product.order_product_id','=','order_product.id')
                            // ->leftJoin('pickup_locations','pickup_locations.id','=','order_product.pickup_location_id')
                            // ->leftJoin('product_categories','product_categories.id','=','order_product.product_category_id')
                            // ->leftJoin('zones AS zones_p','zones_p.id','=','pickup_locations.zone_id')
                            // ->leftJoin('cities AS cities_p','cities_p.id','=','pickup_locations.city_id')
                            // ->leftJoin('states AS states_p','states_p.id','=','pickup_locations.state_id')
                            // ->leftJoin('status','status.code','=','sub_orders.sub_order_status');
                            // ->leftJoin('hubs AS hubs_p','hubs_p.id','=','sub_orders.source_hub_id')
                            // ->leftJoin('hubs AS hubs_d','hubs_d.id','=','sub_orders.destination_hub_id');

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

            $query->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id');

            // $whereInStatusText = merchantWhereInStatusText($request->sub_order_status);

            // $query->WhereIn('order_logs.text',$whereInStatusText)->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

//            $codes = array();
//            foreach ($request->sub_order_status as $sos) {
//                $ocs = merchantWhereInStatus($sos);
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

        if (Auth::user()->hasRole('storeadmin')) {
            $query->where('stores.id', '=', auth()->user()->reference_id);
        }else{
            if($request->has('store_id')){
                $query->where('orders.store_id', $request->store_id);
            }else{
                $query->where('stores.merchant_id', auth()->user()->reference_id);
            }
        }

        // $sub_orders = $query->orderBy('sub_orders.id', 'desc')->paginate(10);

        // if($request->all()){
        $sub_orders = $query->orderBy('sub_orders.id', 'desc')->groupBy('sub_orders.id')->paginate(10);
        // }else{
        //     $sub_orders = null;
        // }

        // $sub_order_status = ['1' => 'Verified','2' => 'Picked','3' => 'In Transit','4' => 'Product Returned','5' => 'Delivery Completed','6' => 'Delivery Partial Completed'];
        $sub_order_status = merchantAllStatus();

        $stores = Store::whereStatus(true)->where('merchant_id', '=', auth()->user()->reference_id)->lists('store_id', 'id')->toArray();  

        return view('merchant-orders.index', compact('stores','sub_order_status','sub_orders', 'zones'));
    }

    public function orderexport(Request $request, $type){
        $query = SubOrder::select(
                                    'sub_orders.id AS suborder_id',
                                    'sub_orders.id',
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
                                    'order_product.sub_total',
                                    'order_product.delivery_paid_amount',
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
                                    'states_p.name AS pickup_state'
                                    // 'status.code AS sub_order_status',
                                    // 'parent_status.code AS sub_order_last_status'
                                    // 'delivery_task.updated_at AS final_delivery_attempt',
                                    // 'dtask_reasons.reason AS dtask_reason',
                                    // 'picking_task.updated_at AS final_picking_attempt',
                                    // 'ptask_reasons.reason AS ptask_reason'
                                )
                            ->where('sub_orders.status', '!=', 0)
                            ->where('sub_orders.sub_order_status', '>', 1)
                            ->where('sub_orders.parent_sub_order_id', 0)
                            ->where('sub_orders.created_at', '>', '2017-06-05 23:59:59')
                            // ->leftJoin('delivery_task','delivery_task.unique_suborder_id','=','sub_orders.unique_suborder_id')
                            // ->leftJoin('reasons AS dtask_reasons','delivery_task.reason_id','=','dtask_reasons.id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                            ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
                            ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
                            ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            // ->leftJoin('picking_task','picking_task.product_unique_id','=','order_product.product_unique_id')
                            // ->leftJoin('reasons AS ptask_reasons','picking_task.reason_id','=','ptask_reasons.id')
                            ->leftJoin('cart_product','cart_product.order_product_id','=','order_product.id')
                            ->leftJoin('pickup_locations','pickup_locations.id','=','order_product.pickup_location_id')
                            ->leftJoin('product_categories','product_categories.id','=','order_product.product_category_id')
                            ->leftJoin('zones AS zones_p','zones_p.id','=','pickup_locations.zone_id')
                            ->leftJoin('cities AS cities_p','cities_p.id','=','pickup_locations.city_id')
                            ->leftJoin('states AS states_p','states_p.id','=','pickup_locations.state_id')
                            // ->leftJoin('status','status.code','=','sub_orders.sub_order_status')
                            // ->leftJoin('status AS parent_status','status.code','=','sub_orders.sub_order_last_status')
                            ->leftJoin('hubs AS hubs_p','hubs_p.id','=','sub_orders.source_hub_id')
                            ->leftJoin('hubs AS hubs_d','hubs_d.id','=','sub_orders.destination_hub_id');

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

            $query->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id');

            // $whereInStatusText = merchantWhereInStatusText($request->sub_order_status);

            // $query->WhereIn('order_logs.text',$whereInStatusText)->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

//            $codes = array();
//            foreach ($request->sub_order_status as $sos) {
//                $ocs = merchantWhereInStatus($sos);
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

        if (Auth::user()->hasRole('storeadmin')) {
            $query->where('stores.id', '=', auth()->user()->reference_id);
        }else{
            if($request->has('store_id')){
                $query->where('orders.store_id', $request->store_id);
            }else{
                $query->where('stores.merchant_id', auth()->user()->reference_id);
            }
        }

        $sub_orders = $query->orderBy('sub_orders.id', 'desc')->groupBy('sub_orders.id')->get()->toArray();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Merchant Orders';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('order_'.time(), function($excel) use ($sub_orders) {
            $excel->sheet('orders', function($sheet) use ($sub_orders)
            {

                $datasheet = array();
                $datasheet[0]  =   array('Order Id','Sub-Order Id','Type','Merchant Order Id','Merchant','Store','Created','Product','Quantity','Proposed Weight','Verified Weight','Category','Amount to be collected','Amount collected','Pickup Name','Pickup Email','Pickup Mobile','Pickup Alt. mobile','Pickup Address','Pickup Zone','Pickup City','Pickup State','Picking Hub','Picking Attempts','Delivery Name','Delivery Email','Delivery Mobile','Delivery Alt. Mobile','Delivery Address','Delivery Zone','Delivery City','Delivery State','Delivery Hub','Delivery Attempts','Current Status','Latest picking attempt','Latest picking reason','Latest delivery attempt','Latest delivery reason');
                $i=1;
                foreach($sub_orders as $datanew){

                    if($datanew['sub_order_last_status'] === NULL){
                        $sub_order_status = merchantGetStatus($datanew['sub_order_status']);
                    }else{
                        $sub_order_status = merchantGetStatus($datanew['sub_order_last_status']);
                    }

                    if($datanew['return'] == 1){
                        $type = 'Return';
                    }else{
                        $type = 'Delivery';
                    }

                    if (!preg_match("/^[a-zA-Z0-9._\-\s\(\)]+$/", $datanew['delivery_name'])){
                        $delivery_name = '';
                    } else {
                        $delivery_name = $datanew['delivery_name'];
                    }
                     // $delivery_name = $datanew['delivery_name'];

                    $sub_order_note = json_decode($datanew['sub_order_note'],true);

                    if(isset($sub_order_note['tat'])){ $tat = $sub_order_note['tat']; }else{ $tat = ""; }
                    if(isset($sub_order_note['pickup_aging'])){ $pickup_aging = $sub_order_note['pickup_aging']; }else{ $pickup_aging = ""; }
                    if(isset($sub_order_note['delivery_aging'])){ $delivery_aging = $sub_order_note['delivery_aging']; }else{ $delivery_aging = ""; }
                    if(isset($sub_order_note['delivery_attempt_aging'])){ $delivery_attempt_aging = $sub_order_note['delivery_attempt_aging']; }else{ $delivery_attempt_aging = ""; }
                    if(isset($sub_order_note['latest_picking_attempt'])){ $latest_picking_attempt = $sub_order_note['latest_picking_attempt']; }else{ $latest_picking_attempt = ""; }
                    if(isset($sub_order_note['latest_picking_reason'])){ $latest_picking_reason = $sub_order_note['latest_picking_reason']; }else{ $latest_picking_reason = ""; }
                    // if(isset($sub_order_note['latest_delivery_attempt'])){ $latest_delivery_attempt = $sub_order_note['latest_delivery_attempt']; }else{ $latest_delivery_attempt = ""; }
                    // if(isset($sub_order_note['latest_delivery_reason'])){ $latest_delivery_reason = $sub_order_note['latest_delivery_reason']; }else{ $latest_delivery_reason = ""; }

                    $last_delivery_attempt = lastDeliveryTask($datanew['id'], $datanew['unique_suborder_id']);

                    $datasheet[$i] = array(  
                        $datanew['unique_order_id'],
                        $datanew['unique_suborder_id'],
                        $type,
                        $datanew['merchant_order_id'],
                        $datanew['merchant_name'],
                        $datanew['store_name'],
                        $datanew['created_at'],                        
                        $datanew['product_title'],
                        $datanew['quantity'],
                        $datanew['proposed_weight'],
                        $datanew['weight'],
                        $datanew['product_category'],
                        $datanew['sub_total'],
                        $datanew['delivery_paid_amount'],
                        $datanew['pickup_name'],
                        $datanew['pickup_email'],
                        $datanew['pickup_msisdn'],
                        $datanew['pickup_alt_msisdn'],
                        $datanew['pickup_address'],
                        $datanew['pickup_zone'],
                        $datanew['pickup_city'],
                        $datanew['pickup_state'],
                        $datanew['pickup_hub'],
                        $datanew['picking_attempts'],
                        $delivery_name,
                        $datanew['delivery_email'],
                        $datanew['delivery_msisdn'],
                        $datanew['delivery_alt_msisdn'],
                        $datanew['delivery_address'],
                        $datanew['delivery_zone'],
                        $datanew['delivery_city'],
                        $datanew['delivery_state'],
                        $datanew['delivery_hub'],
                        // $datanew['final_delivery_attempt'],
                        $datanew['no_of_delivery_attempts'],
                        $sub_order_status,
                        // $sub_order_note['tat'],
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

        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        if (Auth::user()->hasRole('storeadmin')) {
            $stores = Store::whereStatus(true)
            ->where('id', '=', auth()->user()->reference_id)
            ->lists('store_id', 'id')
            ->toArray();
        }else{
            $stores = Store::whereStatus(true)->where('merchant_id', '=', auth()->user()->reference_id)->lists('store_id', 'id')->toArray();
        }

        return view('merchant-orders.insert', compact('prefix', 'countries', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validation = Validator::make($request->all(), [
            'store_id' => 'required',
            'delivery_name' => 'required',
            'delivery_email' => 'required|email',
            'delivery_msisdn' => 'required|between:10,25',
            'delivery_alt_msisdn' => 'sometimes|between:10,25',
            'delivery_country_id' => 'required',
            'delivery_state_id' => 'required',
            'delivery_city_id' => 'required',
            'delivery_zone_id' => 'required',
            'delivery_address1' => 'required',
            // 'delivery_latitude' => 'required',
            // 'delivery_longitude' => 'required',
            'merchant_order_id' => 'required',
            // 'latitude' => 'required',
            // 'longitude' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $order = new Order();
        $order->fill($request->except('msisdn_country', 'alt_msisdn_country'));
        // $order->delivery_latitude = $request->latitude;
        // $order->delivery_longitude = $request->longitude;
        $order->created_by = auth()->user()->id;
        $order->updated_by = auth()->user()->id;
        // $order->unique_order_id = "B".substr(time(), -3).rand(01,99);
        $order->unique_order_id = $this->newOrderId();
        // $time = (string)time();
        // $order->unique_order_id = "B".substr($time, -3).rand(01,99);

        $order->order_status = '1';
        // $order->verified_by = auth()->user()->id;

        $order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'Created a new order: '.$order->unique_order_id);

        // Create Sub-Order
        // $sub_order = new SubOrder();
        // $sub_order->unique_suborder_id = $order->unique_order_id."-D1";
        // $sub_order->order_id = $order->id;
        // $sub_order->save();

        // // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        // $this->orderLog(auth()->user()->id, $order->id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created defult Sub-Order: '.$sub_order->unique_suborder_id);

        Session::flash('message', "Shipping information saved successfully");
        return redirect('/merchant-order/'.$order->id.'/edit?step=2');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::whereStatus(true)->findOrFail($id);

        if(Auth::user()->hasRole('storeadmin')) {
            if($order->store_id != auth()->user()->reference_id){
                abort(403);
            }
        }else{
            if($order->store->merchant_id != auth()->user()->reference_id){
                abort(403);
            }
        }

        // $productStatus = $order->products[0]->status;
        // switch ($productStatus) {
        //     case "0":
        //         $pickStatus = 'error';
        //         foreach ($order->products as $row) {
        //             if($row->status != $productStatus){
        //                 $pickStatus = 'active';
        //             }
        //         }
        //         break;
        //     case "1":
        //         $pickStatus = '';
        //         foreach ($order->products as $row) {
        //             if($row->status != $productStatus){
        //                 $pickStatus = 'active';
        //             }
        //         }
        //         break;
        //     case "2":
        //         $pickStatus = 'done';
        //         foreach ($order->products as $row) {
        //             if($row->status != $productStatus){
        //                 $pickStatus = 'active';
        //             }
        //         }
        //         break;
        //     default:
        // }
        // dd($order->toArray());
        // return view('merchant-orders.view', compact('order', 'pickStatus'));
        return view('merchant-orders.view', compact('order'));
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
                    // ->where('orders.order_status', '=', '')
        ->where('orders.status', '<', 2)
        ->firstOrFail();

        if(Auth::user()->hasRole('storeadmin')) {
            if($order->store_id != auth()->user()->reference_id){
                abort(403);
            }
        }else{
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
            $stores = Store::whereStatus(true)->where('merchant_id', '=', auth()->user()->reference_id)->lists('store_id', 'id')->toArray();
        }

        // For Page One
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $order->delivery_country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $order->delivery_state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $order->delivery_city_id)->lists('name', 'id')->toArray();

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
            $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', auth()->user()->reference_id)->lists('title', 'id')->toArray();
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
            // return env('APP_URL').'api/charge-calculator?store_id='.$order->store_name.'&order_id='.$order->unique_order_id;
            // $chargesJson = file_get_contents(env('APP_URL').'api/charge-calculator?store_id='.$order->store_name.'&order_id='.$order->unique_order_id);
            // $charges = json_decode($chargesJson);
            // if($charges->status == 'Failed'){
            //     abort(403);
            // }
            $order = Order::where('orders.id', '=', $id)->first();
        }

        if($request->step){
            $step = $request->step;
        }else{
            $step = 1;
        }

        // return view('merchant-orders.edit', compact('prefix', 'countries', 'step', 'id', 'order', 'states', 'cities', 'zones', 'stores', 'categories', 'warehouse', 'products', 'picking_time_slot', 'shipping_loc', 'charges'));
        return view('merchant-orders.edit', compact('prefix', 'countries', 'step', 'id', 'order', 'states', 'cities', 'zones', 'stores', 'categories', 'warehouse', 'products', 'picking_time_slot', 'shipping_loc', 'order', 'pickup_location_id', 'picking_date', 'picking_time_slot_id'));
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
            'delivery_country_id' => 'sometimes',
            'delivery_state_id' => 'sometimes',
            'delivery_city_id' => 'sometimes',
            'delivery_zone_id' => 'sometimes',
            'delivery_address1' => 'sometimes',
            'amount' => 'sometimes',
            'delivery_payment_amount' => 'sometimes',
            'merchant_order_id' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

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
        $order = Order::where('orders.id', '=', $id)->first();
        foreach($order->cart_products as $row){
            // echo ($percent_of_collection/100)*$row->sub_total;
            // echo '<br>';
            $payable_product_price = ($percent_of_collection/100)*$row->sub_total;
            $product = CartProduct::findOrFail($row->id);
            $product->payable_product_price = $payable_product_price;
            $product->total_payable_amount = $payable_product_price + $row->total_delivery_charge;
            $product->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $row->order_id, $row->sub_order_id, $row->id, $row->id, 'cart_product', 'Updated payable product price & total payable amount');
        }

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
        $order_update->order_status = '1';
        $order_update->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $order_update->id, '', '', $order_update->id, 'orders', 'Updated Order Information');
        if($request->step){
            if($request->step == 'complete'){

                if($request->as_package == 0){

                    $products = CartProduct::whereStatus(true)->where('order_id', '=', $id)->get();
                    if(count($products) != 0){

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
                            $this->orderLog(auth()->user()->id, $order->id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: '.$sub_order->unique_suborder_id);

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

                        // return $request->step;
                        Session::flash('message', "Order information updated successfully");
                        // return redirect('/merchant-order');
                        return redirect('/merchant-order-draftv2');

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
                    // $pickup_location_data = PickingLocations::whereStatus(true)->findOrFail($order_product->pickup_location_id);
                    // $orderUpdate = Order::whereStatus(true)->findOrFail($order_product->order_id);
                    // $orderUpdate->hub_id = $pickup_location_data->zone->hub_id;
                    // $orderUpdate->save();

                    // return $request->step;
                    Session::flash('message', "Order information updated successfully");
                    // return redirect('/merchant-order');
                    return redirect('/merchant-order-draftv2');

                }

            }else{
                $step = $request->step;
            }
        }else{
            $step = 1;
        }
        Session::flash('message', "Order information saved successfully");
        return redirect('/merchant-order/'.$id.'/edit?step='.$step);
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

        return view('merchant-orders.draft',compact('orders', 'stores'));
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
                    $sub_order_int->source_hub_id = $sub_order->product->pickup_location->zone->hub_id;
                    $sub_order_int->destination_hub_id = $order->delivery_zone->hub_id;
                    $sub_order_int->next_hub_id = $order->delivery_zone->hub_id;
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
        return redirect('/merchant-order-draftv2');

    }
}
