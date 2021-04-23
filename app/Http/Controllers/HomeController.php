<?php

namespace App\Http\Controllers;

use App\Http\Traits\HomeTrait;
use App\Http\Traits\MerchantHomeTrait;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\PickingTask;
use App\DeliveryTask;
use App\SubOrder;
use App\OrderProduct;
use App\Merchant;
use App\Hub;
use App\ExtractLog;

use DB;
use Auth;

use Excel;

class HomeController extends Controller
{

    use HomeTrait;
    use MerchantHomeTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        if($request->from_date){
            $from_date = date("Y-m-d", strtotime($request->from_date));
        }else{
            $from_date = date('Y-m-d');
        }
        
        if($request->to_date){
            $to_date = date("Y-m-d", strtotime($request->to_date));
        }else {
            $to_date = date('Y-m-d');
        }
        
        $count_merchant = DB::table('merchants')->where('status',true)->count();
        $count_hub = DB::table('hubs')->where('status',true)->count();
        $count_store = DB::table('stores')->where('status',true)->count();

        if(Auth::user()->hasRole('salesteam')){
            $count_orders = DB::table('orders AS B')
                            ->join('stores AS C','C.id','=','B.store_id')
                            ->join('merchants AS E','E.id','=','C.merchant_id')
                            ->where('E.responsible_user_id', auth()->user()->id)
                            ->where('B.status',true)
                            ->count();
        }
        else {
            $count_orders = DB::table('orders')->where('status',true)->count();
        }

        $recent_orders = \App\Order::where('status',true)->orderBy('id', 'desc')->take(5)->get();

        if(Auth::user()->hasRole('superadministrator') or Auth::user()->hasRole('superadministrator') or Auth::user()->hasRole('systemadministrator') or Auth::user()->hasRole('systemmoderator'))
        {

            $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
            $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);
            return view('home.admindashboard',compact('home_info', 'count_merchant', 'count_hub', 'count_store', 'count_orders', 'from_date', 'to_date', 'merchants', 'hubs'));
        }
        else if(Auth::user()->hasRole('salesteam'))
        {
            $merchants = Merchant::where('responsible_user_id', auth()->user()->id)->whereStatus(true)->lists('name', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);

            return view('home.home',compact('home_info', 'count_merchant','count_hub','count_store','count_orders','recent_orders', 'from_date', 'to_date', 'merchants'));
        }
        elseif(Auth::user()->hasRole('hubmanager') or Auth::user()->hasRole('inboundmanager') or Auth::user()->hasRole('vehiclemanager') or Auth::user()->hasRole('inventoryoperator') ){

            $hub_home_info = $this->HubManagerInfo($from_date, $to_date);
//            dd($from_date, $to_date,$hub_home_info);

            return view('home.hub-manager', compact('from_date', 'to_date'), $hub_home_info);

     }
     elseif(Auth::user()->hasRole('merchantadmin') or Auth::user()->hasRole('merchantsupport')){

        $merchant_home_info = $this->MerchantInfo($from_date, $to_date);

        return view('home.merchantadmin', compact('from_date', 'to_date'), $merchant_home_info);

        // return view('home.merchantadmin',compact('recent_orders'));
    }
    elseif(Auth::user()->hasRole('storeadmin'))
    {
       $number_of_pickup_request =   \App\OrderProduct::join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
        ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
        ->join('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
        ->whereIn('so.sub_order_status', [2])
        ->where('order_product.status', '=', '1')
        ->where('s.id', '=', auth()->user()->reference_id)
        ->count();

        $Pending_orders_with_number_of_pickup_attempt = \App\OrderProduct::join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
        ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
        ->join('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
        ->where('s.id', '=', auth()->user()->reference_id)
        ->where('order_product.picking_attempts','>',0)
        ->where('so.sub_order_status' ,'>',2)
        ->where('so.sub_order_status' ,'<',7)
        ->count();

        $avg_number_of_attempt = \App\OrderProduct::join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
        ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
        ->where('s.id', '=', auth()->user()->reference_id)
        ->where('order_product.picking_attempts','>',0)
        ->avg('picking_attempts');
    //dd(auth()->user()->reference_id);
        $success_failure_partial_rate=DB::table('picking_task')
        ->join('order_product AS op', 'op.product_unique_id', '=', 'picking_task.product_unique_id')
        ->join('orders AS o', 'o.id', '=', 'op.order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->select(
            DB::raw("
                SUM(CASE WHEN picking_task.status = 2 AND s.id =".auth()->user()->reference_id." THEN 1 ELSE 0 END) as success_rate,
                SUM(CASE WHEN picking_task.status = 4 AND s.id =".auth()->user()->reference_id." THEN 1 ELSE 0 END) as failure_rate,
                SUM(CASE WHEN picking_task.status = 3 AND s.id =".auth()->user()->reference_id." THEN 1 ELSE 0 END) as partial_rate

                "
                )
            )
        ->first();

        $total_rate_pickup = $success_failure_partial_rate->success_rate + $success_failure_partial_rate->failure_rate + $success_failure_partial_rate->partial_rate;

        $pending_shipping_status = \App\SubOrder::where('sub_orders.status', '1')
        ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->where('o.order_status', '=', '5')
        ->where('s.id', '=', auth()->user()->reference_id)
        ->count();

        $number_of_pending_delivery_product  = \App\SubOrder::where('sub_orders.status', '1')
        ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->where('sub_orders.sub_order_status', '>', '27')
        ->where('sub_orders.sub_order_status', '<', '31')
        ->where('s.id', '=', auth()->user()->reference_id)
        ->count();

        $avg_number_of_attempt_delivery= \App\SubOrder::where('sub_orders.status', '1')
        ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->where('s.id', '=', auth()->user()->reference_id)
        ->where('sub_orders.no_of_delivery_attempts','>',0)
        ->avg('no_of_delivery_attempts');

        $success_failure_partial_rate_delivery=
        DB::table('delivery_task')
        ->join('sub_orders AS so', 'so.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
        ->join('orders AS o', 'o.id', '=', 'so.order_id')
        ->join('stores AS s', 's.id', '=', 'o.store_id')
        ->select(
            DB::raw("
                SUM(CASE WHEN delivery_task.status = 2 AND s.id =".auth()->user()->reference_id." THEN 1 ELSE 0 END) as success_rate,
                SUM(CASE WHEN delivery_task.status = 4 AND s.id =".auth()->user()->reference_id." THEN 1 ELSE 0 END) as failure_rate,
                SUM(CASE WHEN delivery_task.status = 3 AND s.id =".auth()->user()->reference_id." THEN 1 ELSE 0 END) as partial_rate

                "
                )
            )
        ->first();

        $total_rate_delivery = $success_failure_partial_rate_delivery->success_rate + $success_failure_partial_rate_delivery->failure_rate + $success_failure_partial_rate_delivery->partial_rate;

            // $query = \App\Order::whereStatus(true)->where('order_status', '>', '1');
            // if (Auth::user()->hasRole('storeadmin')) {
            //     $query->where('store_id', '=', auth()->user()->reference_id);
            // }else{
            //     $query->whereHas('store',function($q){
            //         $q->where('merchant_id', '=', auth()->user()->reference_id);
            //     });
            // }
            // $recent_orders = $query->orderBy('id', 'desc')->take(5)->get();

            return view('home.storeadmin',
                        compact('number_of_pickup_request','Pending_orders_with_number_of_pickup_attempt','avg_number_of_attempt','success_failure_partial_rate','pending_shipping_status','number_of_pending_delivery_product','avg_number_of_attempt_delivery','success_failure_partial_rate_delivery','total_rate_pickup','total_rate_delivery'));
        }
        elseif(Auth::user()->hasRole('head_of_accounts'))
        {
           return view('home');
        }
        elseif(Auth::user()->hasRole('coo'))
        {
            $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
            $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);
            return view('home.admindashboard',compact('home_info', 'count_merchant', 'count_hub', 'count_store', 'count_orders', 'from_date', 'to_date', 'merchants', 'hubs'));

        }
        elseif(Auth::user()->hasRole('saleshead'))
        {
            $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
            $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);
            return view('home.admindashboard',compact('home_info', 'count_merchant', 'count_hub', 'count_store', 'count_orders', 'from_date', 'to_date', 'merchants', 'hubs'));

        }
        elseif(Auth::user()->hasRole('operationmanager'))
        {
            $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
            $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);
            return view('home.admindashboard',compact('home_info', 'count_merchant', 'count_hub', 'count_store', 'count_orders', 'from_date', 'to_date', 'merchants', 'hubs'));

        }
        elseif(Auth::user()->hasRole('operationalhead'))
        {
            $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
            $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);
            return view('home.admindashboard',compact('home_info', 'count_merchant', 'count_hub', 'count_store', 'count_orders', 'from_date', 'to_date', 'merchants', 'hubs'));
        }
        elseif(Auth::user()->hasRole('kam'))
        {
            $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
            $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
            $home_info = $this->AdministratorInfo($request, $from_date, $to_date);
            return view('home.admindashboard',compact('home_info', 'count_merchant', 'count_hub', 'count_store', 'count_orders', 'from_date', 'to_date', 'merchants', 'hubs'));
        }
        elseif (auth()->user()->can(['manage_complain','manage_feedback','head_of_customer_support'])) {
            $data['count_query'] = \App\CustomerSupportModel\Query::whereStatus(1)->count();
            $data['count_mail_group'] = \App\CustomerSupportModel\MailGroup::whereStatus(1)->count();
            $data['count_source_of_information'] = \App\CustomerSupportModel\SourceOfInformation::whereStatus(1)->count();
            $data['count_unique_head'] = \App\CustomerSupportModel\UniqueHead::whereStatus(1)->count();
            $data['count_reaction'] = \App\CustomerSupportModel\Reaction::whereStatus(1)->count();
            $data['count_unsolved_complain'] = \App\CustomerSupportModel\Complain::whereStatus(0)->count();
            $data['count_in_process_complain'] = \App\CustomerSupportModel\Complain::whereStatus(1)->count();
            $data['count_solved_complain'] = \App\CustomerSupportModel\Complain::whereStatus(2)->count();
            $data['count_non_collected_feedback'] = \App\CustomerSupportModel\FeedBack::whereStatus(0)->count();
            $data['count_collected_feedback'] = \App\CustomerSupportModel\FeedBack::whereStatus(1)->count();
            return view('home.customerSupport',$data);
         }
        else {
            return view('home');
        }
    }

    public function dashboardexport($type){
        $inputs = \Request::all();

        if(isset($inputs['from_date'])){
            $from_date = $inputs['from_date'];
        }else{
            $from_date = date('Y-m-d');
        }

        if(isset($inputs['to_date'])){
            $to_date = $inputs['to_date'];
        }else {
            $to_date = date('Y-m-d');
        }

        if(in_array($type, ['pickup_all', 'pickup_pending', 'pickup_success', 'pickup_partial', 'pickup_failed', 'return_all', 'return_pending', 'return_success', 'return_failed'])) {
            // PickUp & Return
            $pickup_req = PickingTask::select(
                                    'D.unique_order_id',
                                    'D.merchant_order_id',
                                    'G.name AS merchant_name',
                                    'E.store_id AS store_name',
                                    'D.delivery_name',
                                    'D.created_at',
                                    'I.title AS hub',
                                    'H.name AS zone',
                                    'picking_task.updated_at AS latest_attempt',
                                    'C.sub_order_status'
                                )
                            ->join('order_product AS B','B.product_unique_id','=','picking_task.product_unique_id')
                            ->join('sub_orders AS C','C.id','=','B.sub_order_id')
                            ->join('orders AS D','D.id','=','B.order_id')
                            ->join('stores AS E','E.id','=','D.store_id')
                            ->join('consignments AS F','F.id','=','picking_task.consignment_id')
                            ->join('merchants AS G','G.id','=','E.merchant_id')
                            ->join('zones AS H','H.id','=','D.delivery_zone_id')
                            ->join('hubs AS I','I.id','=','H.hub_id')
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ;
            if(isset($inputs['hub_id'])){
                $pickup_req = $pickup_req->whereIn('F.hub_id', explode(',', $inputs['hub_id']));
            }
            if(isset($inputs['merchant_id'])){
                $pickup_req = $pickup_req->whereIn('E.merchant_id', explode(',', $inputs['merchant_id']));
            }
        } else {
            // Delivery
            $delivery_req = DeliveryTask::select(
                                'D.unique_order_id',
                                'D.merchant_order_id',
                                'F.name AS merchant_name',
                                'E.store_id AS store_name',
                                'D.delivery_name',
                                'D.created_at',
                                'H.title AS hub',
                                'G.name AS zone',
                                'delivery_task.updated_at AS latest_attempt',
                                'C.sub_order_status'
                            )
                            ->join('consignments AS B','B.id','=','delivery_task.consignment_id')
                            ->join('sub_orders AS C','C.unique_suborder_id','=','delivery_task.unique_suborder_id')
                            ->join('orders AS D','D.id','=','C.order_id')
                            ->join('stores AS E','E.id','=','D.store_id')
                            ->join('merchants AS F','F.id','=','E.merchant_id')
                            ->join('zones AS G','G.id','=','D.delivery_zone_id')
                            ->join('hubs AS H','H.id','=','G.hub_id')
                            ->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ;

            if(isset($inputs['hub_id'])){
                $delivery_req = $delivery_req->whereIn('B.hub_id', explode(',', $inputs['hub_id']));
            }
            if(isset($inputs['merchant_id'])){
                $delivery_req = $delivery_req->whereIn('E.merchant_id', explode(',', $inputs['merchant_id']));
            }
        }

        if($type == 'pickup_all') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [1, 2, 3, 4])
                            ->where('picking_task.type', 'Picking');
        }
        else if($type == 'pickup_pending') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [1])
                            ->where('picking_task.type', 'Picking');
        }
        else if($type == 'pickup_success') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [2])
                            ->where('picking_task.type', 'Picking');
        }
        else if($type == 'pickup_partial') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [3])
                            ->where('picking_task.type', 'Picking');
        }
        else if($type == 'pickup_failed') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [4])
                            ->where('picking_task.type', 'Picking');
        }
        else if($type == 'delivery_all') {
            $delivery_req = $delivery_req->whereIn('delivery_task.status', [1, 2, 3, 4]);
        }
        else if($type == 'delivery_pending') {
            $delivery_req = $delivery_req->whereIn('delivery_task.status', [1]);
        }
        else if($type == 'delivery_success') {
            $delivery_req = $delivery_req->whereIn('delivery_task.status', [2]);
        }
        else if($type == 'delivery_partial') {
            $delivery_req = $delivery_req->whereIn('delivery_task.status', [3]);
        }
        else if($type == 'delivery_failed') {
            $delivery_req = $delivery_req->whereIn('delivery_task.status', [4]);
        }
        else if($type == 'return_all') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [1, 2, 4])
                            ->where('picking_task.type', 'Return');
        }
        else if($type == 'return_pending') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [1])
                            ->where('picking_task.type', 'Return');
        }
        else if($type == 'return_success') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [2])
                            ->where('picking_task.type', 'Return');
        }
        else if($type == 'return_failed') {
            $pickup_req = $pickup_req->whereIn('picking_task.status', [4])
                            ->where('picking_task.type', 'Return');
        }

        if(in_array($type, ['pickup_all', 'pickup_pending', 'pickup_success', 'pickup_partial', 'pickup_failed', 'return_all', 'return_pending', 'return_success', 'return_failed'])) {
            $request_result = $pickup_req->get();
        } else {
            $request_result = $delivery_req->get();
        }

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order '.$type;
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('order_'.$type.'_'.time(), function($excel) use ($request_result, $type) {
            $excel->sheet('orders', function($sheet) use ($request_result, $type)
            {

                $datasheet = array();

                $datasheet[0]  =   array(
                    'Order Id',
                    'Merchant Order Id',
                    'Merchant Name',
                    'Store Name',
                    'Customer Name',
                    'Order Creation Date',
                    'Hub',
                    'Zone',
                    'Latest Attempt Date',
                    'Current Status'
                );

                $i=1;
                foreach($request_result as $datanew){
                    $sub_order_status = '';

                    if($datanew['sub_order_status'] != '')
                        $sub_order_status = hubGetStatus($datanew['sub_order_status']);

                    $datasheet[$i] = array(
                        (string)$datanew['unique_order_id'],
                        (string)$datanew['merchant_order_id'],
                        (string)$datanew['merchant_name'],
                        (string)$datanew['store_name'],
                        (string)$datanew['delivery_name'],
                        (string)$datanew['created_at'],
                        (string)$datanew['hub'],
                        (string)$datanew['zone'],
                        (string)$datanew['latest_attempt'],
                        (string)$sub_order_status
                    );

                    $i++;
                }

                $sheet->setOrientation('landscape');

                // Freeze first row
                $sheet->freezeFirstRow();

                $sheet->fromArray($datasheet);
            });
        })->download('xls');
    }
}
