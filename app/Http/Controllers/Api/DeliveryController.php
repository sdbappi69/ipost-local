<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\LogsTrait;
use App\ReturnedProduct;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\MailsTrait;
use Auth;
use DB;
use App\SubOrder;
use App\OrderProduct;
use App\PickingLocations;
use App\PickingTimeSlot;
use Carbon\Carbon;
use App\PickingTask;
use App\DeliveryTask;
use App\DeliverySurvey;
use App\Order;
use App\Consignment;

class DeliveryController extends Controller {

    use LogsTrait;
    use MailsTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        /**
         * show all delivery tasklist of specific api_token user
         * @get api_token
         * @return set of delivery task lists of current date
         */
        $user_id = Auth::guard('api')->user()->id;
        $send_lat = $request->has('lat') ? $request->lat : 0;
        $send_long = $request->has('long') ? $request->long : 0;
        /**
         * If client send lat and long
         */
        if ($send_lat != 0 && $send_long != 0) {

            $tasks = SubOrder::select(
                            'sub_orders.unique_suborder_id', 'sub_orders.delivery_image', 'orders.delivery_address1', 'orders.delivery_latitude', 'orders.delivery_longitude', DB::raw('3959 * ACOS( COS( RADIANS(' . $send_lat . ') ) * COS( RADIANS( "orders.delivery_latitude" ) ) * COS( RADIANS( "orders.delivery_longitude" ) - RADIANS(' . $send_long . ') ) + SIN( RADIANS(' . $send_lat . ') ) * SIN( RADIANS("orders.delivery_latitude") ) ) as distance')
                    )
                    ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                    ->join('delivery_task', 'delivery_task.unique_suborder_id', '=', 'sub_orders.unique_suborder_id')
                    ->join('consignments', 'consignments.id', '=', 'delivery_task.consignment_id')
                    ->where('consignments.rider_id', $user_id)
                    ->where('sub_orders.deliveryman_id', $user_id)
                    ->where('sub_orders.sub_order_status', 29)
                    // ->where('sub_orders.no_of_delivery_attempts', '<', 3)
                    ->where('sub_orders.status', 1)
                    // added by masud 
                    ->where('delivery_task.status', '!=', 4)
                    // masud
                    // ->whereDate('sub_orders.updated_at', '=', Carbon::today('Asia/Dhaka')->toDateString())
                    ->orderby('distance')
                    ->get();
        } else {

            $tasks = SubOrder::select(
                            'sub_orders.unique_suborder_id', 'sub_orders.delivery_image', 'orders.delivery_address1', 'orders.delivery_latitude', 'orders.delivery_longitude'
                    )
                    ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                    ->join('delivery_task', 'delivery_task.unique_suborder_id', '=', 'sub_orders.unique_suborder_id')
                    ->join('consignments', 'consignments.id', '=', 'delivery_task.consignment_id')
                    ->where('sub_orders.deliveryman_id', $user_id)
                    ->where('sub_orders.sub_order_status', 29)
                    // ->where('sub_orders.no_of_delivery_attempts', '<', 3)
                    ->where('sub_orders.status', 1)
                    // added by masud 
                    ->where('delivery_task.status', '!=', 4)
                    // masud
                    // ->whereDate('sub_orders.updated_at', '=', Carbon::today('Asia/Dhaka')->toDateString())
                    ->get();
        }

        // return $tasks;

        $feedback = [];
        if (count($tasks)) {
            $status = 'success';
            $status_code = 200;
            $message[] = 'tasks found';

            /**
             * prepare and show task lists
             */
            $task_lists = [];
            foreach ($tasks as $key => $task) {

                $task_lists[$key]['task_name'] = $task->unique_suborder_id;
                $task_lists[$key]['task_id'] = $task->unique_suborder_id;
                $task_lists[$key]['address'] = $task->delivery_address1;
                $task_lists[$key]['latitude'] = $task->delivery_latitude;
                $task_lists[$key]['longitude'] = $task->delivery_longitude;
                $task_lists[$key]['distance'] = $task->distance ?: "";
                // added by masud 
                $task_lists[$key]['type'] = 'Delivery';
                // end masud
            }

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            $feedback['response'] = $task_lists;
            // original key and value
            $feedback['meta'] = $tasks;
        } else {
            /**
             * No task in the lists
             * @return status code 200
             */
            $status = 'Not Found';
            $status_code = 200;
            $message[] = 'no delivery task in the lists';

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            // $feedback['response']      =  [];
        }
        return response($feedback, $status_code);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($task_id) {
        /**
         * show task of specific task unique ID
         * @param task_id
         * @return task details
         */
        $user_id = Auth::guard('api')->user()->id;

        $task = DB::table('sub_orders')
                ->select(array(
                    'sub_orders.unique_suborder_id',
                    'sub_orders.delivery_image',
                    'order_product.product_unique_id',
                    'orders.delivery_address1',
                    'orders.delivery_latitude',
                    'orders.delivery_longitude',
                    'orders.delivery_name',
                    'orders.delivery_email',
                    'orders.delivery_msisdn',
                    'orders.merchant_order_id',
                    'zones.name as zone_name',
                    'cities.name as city_name',
                    'states.name as state_name',
                    'countries.name as country_name',
                    'order_product.quantity as quantity',
                    'order_product.unit_price as product_unit_price',
                    'order_product.unit_deivery_charge as product_unit_deivery_charge',
                    'order_product.sub_total as product_actual_price',
                    'order_product.payable_product_price as product_payable_price',
                    'order_product.delivery_pay_by_cus as product_delivery_pay_by_cus',
                    'order_product.total_delivery_charge as product_total_delivery_charge',
                    'order_product.total_payable_amount as product_total_payable_amount',
                    'merchants.name as merchant_name',
                    'merchants.email as merchant_email',
                    'merchants.msisdn as merchant_msisdn',
                    'consignments.consignment_unique_id',
                ))
                ->leftJoin('order_product', 'order_product.sub_order_id', '=', 'sub_orders.id')
                ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->leftJoin('zones', 'zones.id', '=', 'orders.delivery_zone_id')
                ->leftJoin('cities', 'cities.id', '=', 'orders.delivery_city_id')
                ->leftJoin('states', 'states.id', '=', 'orders.delivery_state_id')
                ->leftJoin('countries', 'countries.id', '=', 'orders.delivery_country_id')
                ->join('stores', 'stores.id', '=', 'orders.store_id')
                ->join('merchants', 'merchants.id', '=', 'stores.merchant_id')
                ->join('delivery_task', 'delivery_task.unique_suborder_id', '=', 'sub_orders.unique_suborder_id')
                ->join('consignments', 'consignments.id', '=', 'delivery_task.consignment_id')
                ->where('sub_orders.deliveryman_id', $user_id)
                ->where('sub_orders.sub_order_status', '29')
                ->where('sub_orders.status', '1')
                ->where('order_product.status', '!=', '0')
                ->where('sub_orders.unique_suborder_id', $task_id)
                ->get();

        $feedback = [];

        if ($task) {
            $status = 'success';
            $status_code = 200;
            $message[] = 'task found';

            /**
             * prepare and show task lists
             */
            $product = [];
            $shipping = [];
            $merchant = [];
            foreach ($task as $key => $tsk) {
                $consignment_unique_id = $tsk->consignment_unique_id;

                $merchant['merchant']['merchant_name'] = $tsk->merchant_name;
                $merchant['merchant']['merchant_email'] = $tsk->merchant_email;
                $merchant['merchant']['merchant_msisdn'] = $tsk->merchant_msisdn;
                $merchant['merchant']['merchant_order_id'] = $tsk->merchant_order_id;

                $product[$key]['product_unique_id'] = $tsk->product_unique_id;
                $product[$key]['quantity'] = $tsk->quantity;
                // $product[$key]['product_actual_price'] = $tsk->product_actual_price;
                // $product[$key]['product_payable_price'] = $tsk->product_payable_price;
                // $product[$key]['product_total_delivery_charge'] = $tsk->product_total_delivery_charge;
                // $product[$key]['unit_deivery_charge'] = $tsk->product_delivery_charge;
                // $product[$key]['COD'] = $tsk->COD;
                $product[$key]['product_unit_price'] = $tsk->product_unit_price;
                $product[$key]['product_unit_deivery_charge'] = $tsk->product_unit_deivery_charge;
                $product[$key]['product_actual_price'] = $tsk->product_actual_price;
                $product[$key]['product_payable_price'] = $tsk->product_payable_price;
                $product[$key]['product_delivery_pay_by_cus'] = $tsk->product_delivery_pay_by_cus;
                $product[$key]['product_total_delivery_charge'] = $tsk->product_total_delivery_charge;
                $product[$key]['product_total_payable_amount'] = $tsk->product_total_payable_amount;

                $shipping['delivery_name'] = $tsk->delivery_name;
                $shipping['delivery_email'] = $tsk->delivery_email;
                $shipping['delivery_msisdn'] = $tsk->delivery_msisdn;
                $shipping['delivery_address1'] = $tsk->delivery_address1;
                $shipping['delivery_latitude'] = $tsk->delivery_latitude;
                $shipping['delivery_longitude'] = $tsk->delivery_longitude;
                $shipping['delivery_image'] = $tsk->delivery_image;
                $shipping['zone_name'] = $tsk->zone_name;
                $shipping['city_name'] = $tsk->city_name;
                $shipping['state_name'] = $tsk->state_name;
                $shipping['country_name'] = $tsk->country_name;
            }

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            $feedback['consignment'] = $consignment_unique_id;
            $feedback['response'] = $merchant;
            $feedback['response']['address'] = $shipping;
            $feedback['response']['product'] = $product;
        } else {
            /**
             * task details empty
             * @return status code 200
             */
            $status = 'Not Found';
            $status_code = 200;
            $message[] = 'no task in the list';

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            // $feedback['response']      =  [];
        }
        return response($feedback, 200);
    }

    public function delivery_complete($task_id) {
        /**
         * show task of specific task unique ID
         * @param task_id
         * @return task details
         */
        $user_id = Auth::guard('api')->user()->id;

        $task = DeliveryTask::select(array(
                    'sub_orders.unique_suborder_id',
                    'sub_orders.delivery_image',
                    'order_product.product_unique_id',
                    'orders.delivery_address1',
                    'orders.delivery_latitude',
                    'orders.delivery_longitude',
                    'orders.delivery_name',
                    'orders.delivery_email',
                    'orders.delivery_msisdn',
                    'orders.merchant_order_id',
                    'zones.name as zone_name',
                    'cities.name as city_name',
                    'states.name as state_name',
                    'countries.name as country_name',
                    'order_product.quantity as quantity',
                    'order_product.unit_price as product_unit_price',
                    'order_product.unit_deivery_charge as product_unit_deivery_charge',
                    'order_product.sub_total as product_actual_price',
                    'order_product.payable_product_price as product_payable_price',
                    'order_product.delivery_pay_by_cus as product_delivery_pay_by_cus',
                    'order_product.total_delivery_charge as product_total_delivery_charge',
                    'order_product.total_payable_amount as product_total_payable_amount',
                    'returned_products.quantity as return_quantity',
                    'merchants.name as merchant_name',
                    'merchants.email as merchant_email',
                    'merchants.msisdn as merchant_msisdn',
                ))
                ->leftJoin('sub_orders', 'sub_orders.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
                ->leftJoin('order_product', 'order_product.sub_order_id', '=', 'sub_orders.id')
                ->leftJoin('returned_products', 'returned_products.product_unique_id', '=', 'order_product.product_unique_id')
                ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->leftJoin('zones', 'zones.id', '=', 'orders.delivery_zone_id')
                ->leftJoin('cities', 'cities.id', '=', 'orders.delivery_city_id')
                ->leftJoin('states', 'states.id', '=', 'orders.delivery_state_id')
                ->leftJoin('countries', 'countries.id', '=', 'orders.delivery_country_id')
                ->join('stores', 'stores.id', '=', 'orders.store_id')
                ->join('merchants', 'merchants.id', '=', 'stores.merchant_id')
                // ->where('sub_orders.deliveryman_id', $user_id)
                // ->where('sub_orders.sub_order_status', 8)
                ->where('sub_orders.status', 1)
                ->where('delivery_task.unique_suborder_id', $task_id)
                ->get();

        // $task    =  DB::table('sub_orders')
        //                ->select(array(
        //                   'sub_orders.unique_suborder_id',
        //                   'sub_orders.delivery_image',
        //                   'order_product.product_unique_id',
        //                   'orders.delivery_address1',
        //                   'orders.delivery_latitude',
        //                   'orders.delivery_longitude',
        //                   'orders.delivery_name',
        //                   'orders.delivery_email',
        //                   'orders.delivery_msisdn',
        //                   'orders.merchant_order_id',
        //                   'zones.name as zone_name',
        //                   'cities.name as city_name',
        //                   'states.name as state_name',
        //                   'countries.name as country_name',
        //                   'order_product.quantity as quantity',
        //                   'order_product.unit_price as product_unit_price',
        //                   'order_product.unit_deivery_charge as product_unit_deivery_charge',
        //                   'order_product.sub_total as product_actual_price',
        //                   'order_product.payable_product_price as product_payable_price',
        //                   'order_product.delivery_pay_by_cus as product_delivery_pay_by_cus',
        //                   'order_product.total_delivery_charge as product_total_delivery_charge',
        //                   'order_product.total_payable_amount as product_total_payable_amount',
        //                   'merchants.name as merchant_name',
        //                   'merchants.email as merchant_email',
        //                   'merchants.msisdn as merchant_msisdn',
        //                ))
        //                ->leftJoin('order_product', 'order_product.sub_order_id', '=', 'sub_orders.id')
        //                ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
        //                ->leftJoin('zones', 'zones.id', '=', 'orders.delivery_zone_id')
        //                ->leftJoin('cities', 'cities.id', '=', 'orders.delivery_city_id')
        //                ->leftJoin('states', 'states.id', '=', 'orders.delivery_state_id')
        //                ->leftJoin('countries', 'countries.id', '=', 'orders.delivery_country_id')
        //                ->join('stores', 'stores.id', '=', 'orders.store_id')
        //                ->join('merchants', 'merchants.id', '=', 'stores.merchant_id')
        //                ->where('sub_orders.deliveryman_id', $user_id)
        //                ->where('sub_orders.sub_order_status', 8)
        //                ->where('sub_orders.status', 1)
        //                ->where('sub_orders.unique_suborder_id', $task_id)
        //                ->get();
        $feedback = [];

        if ($task) {
            $status = 'success';
            $status_code = 200;
            $message[] = 'task found';

            /**
             * prepare and show task lists
             */
            $product = [];
            $shipping = [];
            $merchant = [];
            foreach ($task as $key => $tsk) {

                $merchant['merchant']['merchant_name'] = $tsk->merchant_name;
                $merchant['merchant']['merchant_email'] = $tsk->merchant_email;
                $merchant['merchant']['merchant_msisdn'] = $tsk->merchant_msisdn;
                $merchant['merchant']['merchant_order_id'] = $tsk->merchant_order_id;

                $product[$key]['product_unique_id'] = $tsk->product_unique_id;
                $product[$key]['quantity'] = $tsk->quantity;
                if ($tsk->return_quantity == null) {
                    $return_quantity = 0;
                } else {
                    $return_quantity = $tsk->return_quantity;
                }
                $product[$key]['return_quantity'] = $return_quantity;
                // $product[$key]['product_actual_price'] = $tsk->product_actual_price;
                // $product[$key]['product_payable_price'] = $tsk->product_payable_price;
                // $product[$key]['product_total_delivery_charge'] = $tsk->product_total_delivery_charge;
                // $product[$key]['unit_deivery_charge'] = $tsk->product_delivery_charge;
                // $product[$key]['COD'] = $tsk->COD;
                $product[$key]['product_unit_price'] = $tsk->product_unit_price;
                $product[$key]['product_unit_deivery_charge'] = $tsk->product_unit_deivery_charge;
                $product[$key]['product_actual_price'] = $tsk->product_actual_price;
                $product[$key]['product_payable_price'] = $tsk->product_payable_price;
                $product[$key]['product_delivery_pay_by_cus'] = $tsk->product_delivery_pay_by_cus;
                $product[$key]['product_total_delivery_charge'] = $tsk->product_total_delivery_charge;
                $product[$key]['product_total_payable_amount'] = $tsk->product_total_payable_amount;

                $shipping['delivery_name'] = $tsk->delivery_name;
                $shipping['delivery_email'] = $tsk->delivery_email;
                $shipping['delivery_msisdn'] = $tsk->delivery_msisdn;
                $shipping['delivery_address1'] = $tsk->delivery_address1;
                $shipping['delivery_latitude'] = $tsk->delivery_latitude;
                $shipping['delivery_longitude'] = $tsk->delivery_longitude;
                $shipping['delivery_image'] = $tsk->delivery_image;
                $shipping['zone_name'] = $tsk->zone_name;
                $shipping['city_name'] = $tsk->city_name;
                $shipping['state_name'] = $tsk->state_name;
                $shipping['country_name'] = $tsk->country_name;
            }

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            $feedback['response'] = $merchant;
            $feedback['response']['address'] = $shipping;
            $feedback['response']['product'] = $product;
        } else {
            /**
             * task details empty
             * @return status code 200
             */
            $status = 'Not Found';
            $status_code = 200;
            $message[] = 'no task in the list';

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            // $feedback['response']      =  [];
        }
        return response($feedback, $status_code);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    /**
     * Do start a single delivery task
     * @request POST
     * @param product_id, start_lat, start_long
     * @note product_id = unique_suborder_id not product_unique_id
     * @return success after update delivery_task table
     */
    public function do_start_delivery(Request $request, $product_id) {
        $user_id = Auth::guard('api')->user()->id;
        $product_id = ( $product_id ) ? $product_id : 0;
        $start_lat = $request->has('start_lat') ? $request->start_lat : 0;
        $start_long = $request->has('start_long') ? $request->start_long : 0;

        /**
         * update delivery attempt
         */
        $sop = SubOrder::where('unique_suborder_id', $product_id)->first();
        if ($sop) {
            $sop->no_of_delivery_attempts = $sop->no_of_delivery_attempts + 1;
            $sop->save();

            // Update Sub-Order Status
            $this->suborderStatus($sop->id, '30');
        }

        /**
         * update picking task to picking_task table
         */
        $dtask = DeliveryTask::where('unique_suborder_id', $product_id)->first();

        if ($dtask) {
            $dtask->status = $dtask->status ? $dtask->status + 1 : 1;
        } else {
            $dtask = new DeliveryTask();
            $dtask->status = 1;
        }

        $dtask->start_lat = $start_lat;
        $dtask->start_long = $start_long;
        $dtask->start_time = new \DateTime;
        $dtask->unique_suborder_id = $product_id;
        $dtask->deliveryman_id = $user_id;

        $dtask->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($dtask->deliveryman_id, $sop->order_id, $sop->id, '', $dtask->deliveryman_id, 'delivery_task', 'Delivery man started delivery');

        /**
         * Sending response
         * @return picking_task ID
         */
        $status = 'success';
        $status_code = 200;
        $message[] = 'Task has been started';

        $feedback['status'] = $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        $feedback['response'] = ['delivery_task_id' => $dtask->id, 'status' => $dtask->status];

        return response($feedback, $status_code);
    }

    /**
     * Do complete a single delivery task
     * @request POST
     * @param delivery_task_id, type, end_lat, end_long, photo, signature, survey = [{"qus": 1,"ans": 2},{"qus": 2,"ans": 5}]
     * @return success after update picking_task table
     */
    public function do_complete_delivery(Request $request, $delivery_task_id) {

        $user_id = Auth::guard('api')->user()->id;
        $delivery_task_id = ( $delivery_task_id ) ? $delivery_task_id : 0;
        $end_lat = $request->has('end_lat') ? $request->end_lat : 0;
        $end_long = $request->has('end_long') ? $request->end_long : 0;

        /**
         * If delivery failed
         * @return failed feedback
         * */
        $failed = $request->has('type') ? $request->type : 0;
        // return $request->type ;
        if ($failed === 'failed') {

            return $this->delivery_complete_failed($request, $user_id, $delivery_task_id);
        }
        //return 0;
        /**
         * If attach photo
         * upload this photo to /uploads/picking/
         */
        $photo_path = '';
        if ($request->hasFile('photo')) {
            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = str_random(15) . '.jpg';
            $upload_path = 'uploads/picking/';

            $img = \Image::make($request->file('photo'))->save($upload_path . $fileName);
            $photo_path = \URL::to('/') . "/" . $upload_path . $fileName;
        }
        /**
         * If attach signature
         * upload this signature to /uploads/picking/signature
         */
        $signature_path = '';
        if ($request->hasFile('signature')) {
            $extension = $request->file('signature')->getClientOriginalExtension();
            $fileName = str_random(15) . '.jpg';
            $upload_path = 'uploads/picking/signature/';

            $img = \Image::make($request->file('signature'))->save($upload_path . $fileName);
            $signature_path = \URL::to('') . "/" . $upload_path . $fileName;
        }

        $remarks = $request->has('remarks') ? $request->remarks : '';
        // return 1;
        $dtask = DeliveryTask::find($delivery_task_id);
        $dtask->end_lat = $end_lat;
        $dtask->end_long = $end_long;
        $dtask->end_time = new \DateTime;
        $dtask->image = $photo_path;
        $dtask->signature = $signature_path;
        $dtask->status = 2; /* compelete */
        $dtask->remarks = $remarks;

        $dtask->save();

        // suborder
        // make total_payable_amount = delivery_paid_amount
        $so = SubOrder::where('unique_suborder_id', $dtask->unique_suborder_id)->first();

        // Update Sub-Order Status
        $this->suborderStatus($so->id, '31');

        // Send Mail
        $this->mailFullDelivered($dtask->unique_suborder_id);

        // Send Mail to Merchant
        // $this->setMail(Auth::guard('api')->user()->id, 'system', 'Delivery Confirmation', $dtask->id, 'delivery_task', $user->email, 'Picking Confirmation', $this->pickingConfirmation($ptask->id), '', '');

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($dtask->deliveryman_id, $so->order_id, $so->id, '', $dtask->deliveryman_id, 'delivery_task', 'Delivery man completed delivery');

        $suborder_product = OrderProduct::where('sub_order_id', $so->id)->get();
        foreach ($suborder_product as $key => $sop) {
            $suborder_product[$key]->delivery_paid_amount = $suborder_product[$key]->total_payable_amount;
            $suborder_product[$key]->save();
        }

        /**
         * update order product status
         * update sub_order_status to 9
         * comment for testing
         */
        // $suborder_product = SubOrder::where('unique_suborder_id', $dtask->unique_suborder_id)->first();
        // $suborder_product->sub_order_status = 9;
        // $suborder_product->save();

        /**
         * If all product delivery completed
         * match with sub_orders status != 9 and order_id
         * comment for testing
         */
        // $order_due = SubOrder::where('sub_order_status', '!=', '9')->where('order_id', '=', $suborder_product->order_id)->count();
        // // dd($order_due);
        // if ($order_due == 0) {
        //     $order = Order::find($suborder_product->order_id);
        //     if ($order) {
        //         $order->updated_by = $user_id;
        //         $order->order_status = '9';
        //         $order->save();
        //     }
        // }

        /**
         * Check if anything returned or not
         * if yes, then save returned details in delivery_returns table
         */
        $item_list = json_decode($request->input('item_list'));
        if (count($item_list) > 0) {
            foreach ($item_list as $item) {

                $productUniqueOrder = OrderProduct::whereProductUniqueId($item->product_unique_id)->firstOrFail();
                $productUniqueOrder->delivery_paid_amount = $item->product_total_payable_amount;
                $productUniqueOrder->delivered_quantity = $item->quantity;
                $productUniqueOrder->save();

                // Update Consignment
                $consignment_id = $dtask->consignment_id;
                $consignment = Consignment::where('id', $consignment_id)->first();
                $consignment->amount_collected = $consignment->amount_collected + $item->product_total_payable_amount;
                $consignment->quantity_available = $consignment->quantity_available + $item->quantity;
                $consignment->save();

                // return $item->quantity;

                if ($productUniqueOrder->quantity > $item->quantity) {

                    $dtask = DeliveryTask::find($delivery_task_id);
                    $dtask->status = 3; /* pertial */
                    $dtask->save();

                    // Update Sub-Order Status
                    $this->suborderStatus($dtask->suborder->id, '32');

                    $order_id = $productUniqueOrder->order_id;
                    $sub_order_id = $productUniqueOrder->sub_order_id;
                    $product_title = $productUniqueOrder->product_title;
                    $url = $productUniqueOrder->url;
                    $product_category_id = $productUniqueOrder->product_category_id;
                    $unit_price = $productUniqueOrder->unit_price;
                    $quantity = $productUniqueOrder->quantity - $item->quantity;
                    $width = $productUniqueOrder->width;
                    $height = $productUniqueOrder->height;
                    $length = $productUniqueOrder->length;
                    $weight = $productUniqueOrder->weight;
                    $order_product_id = $productUniqueOrder->id;

                    ReturnedProduct::create([
                        'product_unique_id' => $productUniqueOrder->product_unique_id . "-R",
                        'updated_by' => $user_id,
                        'sub_order_id' => $sub_order_id,
                        'order_id' => $order_id,
                        'order_product_id' => $order_product_id,
                        'product_title' => $product_title,
                        'url' => $url,
                        'product_category_id' => $product_category_id,
                        'quantity' => $quantity,
                        'width' => $width,
                        'height' => $height,
                        'length' => $length,
                        'weight' => $weight,
                        'status' => 0,
                        'unit_deivery_charge' => $productUniqueOrder->unit_deivery_charge / 2,
                        'total_delivery_charge' => ($productUniqueOrder->unit_deivery_charge / 2) * $quantity,
                    ]);
                }
            }
        }


        /**
         * If survey added
         * add survey to survey table
         */
        // $survey  =  [];
        // if( $request->has('survey') )
        // {
        //    $survey  =  json_decode( $request->survey );
        // }
        //
      // /**
        // * update survey with picking_task
        // */
        // if( count($survey) )
        // {
        //    $bulk = [];
        //    foreach ($survey as $srv)
        //    {
        //       $srv                    = (array) $srv;
        //       $srv['picking_task_id'] = $dtask->id;
        //       $srv['created_by']      = $user_id;
        //       $srv['created_at']      = new \DateTime;
        //       $bulk[]                 =  $srv;
        //    }
        //
      //    /**
        //    * Insert bulk survey
        //    */
        //    DelivarySurvey::insert($bulk);
        // }

        /**
         * Sending response
         * @return picking_task ID, status
         */
        $status = 'success';
        $status_code = 200;
        $message[] = 'Delivery has been completed';

        $feedback['status'] = $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        $feedback['response'] = ['delivery_task_id' => $dtask->id, 'status' => $dtask->status];

        return response($feedback, $status_code);
    }

    /**
     * failed to delivery
     * @param $request, $delivery_task_id, $user_id
     */
    private function delivery_complete_failed($request, $user_id, $delivery_task_id) {
        //return $delivery_task_id ;
        $dtask = DeliveryTask::find($delivery_task_id);
        //return $dtask->toArray();
        $dtask->end_lat = $request->end_lat;
        $dtask->end_long = $request->end_long;
        $dtask->end_time = new \DateTime;
        $dtask->status = 4; /* failed */
        $dtask->reason_id = $request->input('reason_id');
        $dtask->remarks = $request->remarks;

        $dtask->save();

        // Update Sub-Order Status
        $this->suborderStatus($dtask->suborder->id, '33');

        //return $dtask->toArray();

        /**
         * update suborder status failed to 100
         * 100 == failed
         */
        // $sub_order              =  SubOrder::where('unique_suborder_id', $dtask->unique_suborder_id)->first();
        // $sub_order->sub_order_status =  100;
        // $sub_order->save();
        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        // $this->orderLog($dtask->deliveryman_id, $sub_order->order_id, $sub_order->id, '', $dtask->deliveryman_id, 'delivery_task', 'Delivery man failed to deliver');

        /**
         * Sending response
         * @return failed status
         */
        $status = 'failed';
        $status_code = 200;
        $message[] = 'Delivery has been failed.';

        $feedback['status'] = $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        $feedback['response'] = ['delivery_task_id' => $dtask->id, 'status' => $dtask->status, 'unique_suborder_id' => $dtask->unique_suborder_id];

        return response($feedback, $status_code);
    }

    /**
     * Delivery verify
     * @request POST
     * @param lat,long, api_token
     * @note $product_id = unique_suborder_id
     * @return response verified
     */
    public function delivery_verify(Request $request, $product_id) {
        $user_id = Auth::guard('api')->user()->id;
        $product_id = ( $product_id ) ? $product_id : 0;
        $lat = $request->has('lat') ? $request->lat : 0;
        $long = $request->has('long') ? $request->long : 0;

        $suborder_product = SubOrder::where('unique_suborder_id', $product_id)->first();

        $order = Order::find($suborder_product->order_id);
        $order->delivery_latitude = $lat;
        $order->delivery_longitude = $long;
        $order->save();

        /**
         * Sending response
         * @return varified or updated
         */
        $status = 'success';
        $status_code = 200;
        $message[] = 'Delivery address has been varified';

        $feedback['status'] = $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  [];

        return response($feedback, $status_code);
    }

}
