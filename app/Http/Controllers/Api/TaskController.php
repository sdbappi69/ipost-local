<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\LogsTrait;
use App\Http\Traits\MailsTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use App\OrderProduct;
use App\ReturnedProduct;
use App\PickingLocations;
use App\PickingTimeSlot;
use Carbon\Carbon;
use App\PickingTask;
use App\DeliveryTask;
use App\DeliverySurvey;
use App\Order;
use App\Consignment;
use Log;

class TaskController extends Controller
{
    use LogsTrait;
    use MailsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
      // dd('1');
       // return 'masud';
      /**
      * show all tasklist of specific api_token user
      * @get api_token
      * @return set of task lists of current date
      */
      $user_id    =  Auth::guard('api')->user()->id;
      $send_lat   =  $request->has('lat')    ? $request->lat   : 0;
      $send_long  =  $request->has('long')   ? $request->long  : 0;

      /**
      * If client send lat and long
      */
      if( $send_lat != 0 && $send_long != 0 )
      {
         $tasks      =  OrderProduct::select(
                           'order_product.product_unique_id',
                           'order_product.product_title',
                           'order_product.image',
                           'order_product.pickup_location_id',
                           'order_product.picking_date',
                           'pickup_locations.latitude',
                           'pickup_locations.longitude',
                           'picking_time_slots.start_time',
                           'picking_time_slots.end_time',
                           'o.delivery_name as cus_name',
                           'o.delivery_email as cus_email',
                           'o.delivery_msisdn as cus_msisdn',
                           'o.delivery_alt_msisdn as cus_alt_msisdn',
                           'pt.type as picking_type',
                           'so.return',
                           DB::raw('3959 * ACOS( COS( RADIANS('. $send_lat .') ) * COS( RADIANS( "pickup_locations.latitude" ) ) * COS( RADIANS( "pickup_locations.longitude" ) - RADIANS('. $send_long .') ) + SIN( RADIANS('. $send_lat .') ) * SIN( RADIANS("pickup_locations.latitude") ) ) as distance')
                        )
                        ->leftJoin('pickup_locations','pickup_locations.id', '=', 'order_product.pickup_location_id')
                        ->leftJoin('picking_time_slots','picking_time_slots.id', '=', 'order_product.picking_time_slot_id')
                        ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                        ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                        ->leftJoin('picking_task AS pt', 'pt.product_unique_id', '=', 'order_product.product_unique_id')
                        ->leftJoin('consignments AS consignments', 'consignments.id', '=', 'pt.consignment_id')
                        // ->where('order_product.picker_id', $user_id)
                        ->where('order_product.picking_attempts', '<=', 3)
                        ->whereIn('so.sub_order_status', [4, 35])
                        // ->whereDate('picking_date', '=', Carbon::today()->toDateString())
                        ->where('consignments.rider_id', $user_id)
                        ->where('consignments.status', 2)
                        ->orderby('distance')
                        ->get();
      }
      else
      {
         $tasks      =  OrderProduct::select(
                           'order_product.product_unique_id',
                           'order_product.product_title',
                           'order_product.image',
                           'order_product.pickup_location_id',
                           'order_product.picking_date',
                           'pickup_locations.latitude',
                           'pickup_locations.longitude',
                           'picking_time_slots.start_time',
                           'picking_time_slots.end_time',
                           'o.delivery_name as cus_name',
                           'o.delivery_email as cus_email',
                           'o.delivery_msisdn as cus_msisdn',
                           'o.delivery_alt_msisdn as cus_alt_msisdn',
                           'pt.type as picking_type',
                           'so.return'
                        )
                        ->leftJoin('pickup_locations','pickup_locations.id', '=', 'order_product.pickup_location_id')
                        ->leftJoin('picking_time_slots','picking_time_slots.id', '=', 'order_product.picking_time_slot_id')
                        ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                        ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                        ->leftJoin('picking_task AS pt', 'pt.product_unique_id', '=', 'order_product.product_unique_id')
                        ->leftJoin('consignments AS consignments', 'consignments.id', '=', 'pt.consignment_id')
                        // ->where('order_product.picker_id', $user_id)
                        ->where('order_product.picking_attempts', '<=', 3)
                        // ->where('so.sub_order_status', 4)
                        ->whereIn('so.sub_order_status', [4, 36])
                        ->where('consignments.rider_id', $user_id)
                        ->where('consignments.status', 2)
                        // ->whereDate('picking_date', '=', Carbon::today()->toDateString())
                        ->get();
      }

      // $return_tasks      =  ReturnedProduct::select(
      //                      'returned_products.product_unique_id',
      //                      'returned_products.product_title',
      //                      'returned_products.image',
      //                      'order_product.pickup_location_id',
      //                      'pickup_locations.latitude',
      //                      'pickup_locations.longitude',
      //                      'o.delivery_name as cus_name',
      //                      'o.delivery_email as cus_email',
      //                      'o.delivery_msisdn as cus_msisdn',
      //                      'o.delivery_alt_msisdn as cus_alt_msisdn',
      //                      'pt.type as picking_type'
      //                   )
      //                   ->leftJoin('order_product','order_product.id', '=', 'returned_products.order_product_id')
      //                   ->leftJoin('pickup_locations','pickup_locations.id', '=', 'order_product.pickup_location_id')
      //                   ->leftJoin('picking_time_slots','picking_time_slots.id', '=', 'order_product.picking_time_slot_id')
      //                   ->leftJoin('orders AS o', 'o.id', '=', 'returned_products.order_id')
      //                   ->leftJoin('picking_task AS pt', 'pt.product_unique_id', '=', 'returned_products.product_unique_id')
      //                   ->leftJoin('consignments AS consignments', 'consignments.id', '=', 'pt.consignment_id')
      //                   ->where('returned_products.status', 3)
      //                   ->where('consignments.rider_id', $user_id)
      //                   ->where('consignments.status', 2)
      //                   ->get();

      $feedback = [];

      if (count($tasks))
      {
         $status        =  'success';
         $status_code   =  200;
         $message[]       =  'tasks found';

         /**
         * prepare and show task lists
         */
         $task_lists = [];
         foreach( $tasks as $key => $task )
         {
            $pickup_location  =  \APIHelper::get_pickup_location_by_id($task->pickup_location_id)  ? \APIHelper::get_pickup_location_by_id($task->pickup_location_id)->title : null;
            $task_lists[$key]['task_name']   =  $task->product_title;
            $task_lists[$key]['task_id']     =  $task->product_unique_id;
            $task_lists[$key]['address']     =  $pickup_location;
            $task_lists[$key]['date']        =  $task->picking_date;
            $task_lists[$key]['latitude']    =  $task->latitude;
            $task_lists[$key]['longitude']   =  $task->longitude;
            $task_lists[$key]['distance']   =  $task->distance;
            $task_lists[$key]['cus_name']   =  $task->cus_name;
            $task_lists[$key]['cus_email']   =  $task->cus_email;
            $task_lists[$key]['cus_msisdn']   =  $task->cus_msisdn;
            $task_lists[$key]['cus_alt_msisdn']   =  $task->cus_alt_msisdn;
            if($task->return == 0){
              $task_lists[$key]['type']   =  'Picking';
            }else{
              $task_lists[$key]['type']   =  'Return';
            }
         }

         // if (count($return_tasks)){
         //  foreach( $return_tasks as $key => $task )
         //   {
         //      $pickup_location  =  \APIHelper::get_pickup_location_by_id($task->pickup_location_id)  ? \APIHelper::get_pickup_location_by_id($task->pickup_location_id)->title : null;
         //      $task_lists[$key]['task_name']   =  $task->product_title;
         //      $task_lists[$key]['task_id']     =  $task->product_unique_id;
         //      $task_lists[$key]['address']     =  $pickup_location;
         //      $task_lists[$key]['date']        =  '';
         //      $task_lists[$key]['latitude']    =  $task->latitude;
         //      $task_lists[$key]['longitude']   =  $task->longitude;
         //      $task_lists[$key]['distance']   =  '';
         //      $task_lists[$key]['cus_name']   =  $task->cus_name;
         //      $task_lists[$key]['cus_email']   =  $task->cus_email;
         //      $task_lists[$key]['cus_msisdn']   =  $task->cus_msisdn;
         //      $task_lists[$key]['cus_alt_msisdn']   =  $task->cus_alt_msisdn;
         //      $task_lists[$key]['type']   =  $task->picking_type;
         //   }
         // }

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']      =  $task_lists;
         // original key and value
         // $feedback['meta']          =  $tasks;
      }
      else
      {
         /**
         * No task in the lists
         * @return status code 200
         */
         $status        =  'Not Found';
         $status_code   =  200;
         $message[]       =  'no task in the lists';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']      =  [];
      }
      return response($feedback, $status_code);
    }

    /**
    * all completed tasks
    * @param api_token
    * @return two objects picking  and delivery
    */
    public function picking_delivery_completed()
    {
      
      $user_id    =  Auth::guard('api')->user()->id;

      //$consignment = Consignment::where('status', '2')->where('rider_id', $user_id)->where('created_at','>=', date('Y-m-d')." 00:00:00")->where('created_at','<=', date('Y-m-d')." 23:59:59")->first();
      $consignment = Consignment::where('status', '2')->where('rider_id', $user_id)->first();
      if(count($consignment) > 0){
        $amount_to_collect = $consignment->amount_to_collect;
        $amount_collected = $consignment->amount_collected;

        $task_lists = array();

        if($consignment->type == 'picking'){
          // return 1;
          $CompletedTasks      =  PickingTask::select(
                          'picking_task.id',
                          'order_product.product_unique_id',
                          'order_product.product_title',
                          'order_product.image',
                          'order_product.pickup_location_id',
                          'order_product.picking_date',
                          'pickup_locations.latitude',
                          'pickup_locations.longitude',
                          'picking_time_slots.start_time',
                          'picking_time_slots.end_time',
                          'picking_task.status'
                       )
                       ->leftJoin('order_product','order_product.product_unique_id', '=', 'picking_task.product_unique_id')
                       ->leftJoin('pickup_locations','pickup_locations.id', '=', 'order_product.pickup_location_id')
                       ->leftJoin('picking_time_slots','picking_time_slots.id', '=', 'order_product.picking_time_slot_id')
                       ->where('order_product.picker_id', $user_id)
                       // ->where('order_product.status', '!=', 100)
                       ->where('picking_task.status', '>', 1)
                       ->where('picking_task.consignment_id', $consignment->id)
                       // ->whereDate('picking_task.start_time', '>=', Carbon::today()->toDateString())
                       // ->whereDate('picking_task.end_time', '<', Carbon::tomorrow('Asia/Dhaka')->toDateString())
                       ->get();

          if (count($CompletedTasks)){
            $status        =  'success';
            $status_code   =  200;
            $message[]       =  'tasks found';

             foreach( $CompletedTasks as $key => $task )
             {
                 //return $task;
                $pickup_location  =  \APIHelper::get_pickup_location_by_id($task->pickup_location_id)  ? \APIHelper::get_pickup_location_by_id($task->pickup_location_id)->title : null;
                $task_lists[$key]['task_name']   =  $task->product_title;
                $task_lists[$key]['task_id']     =  $task->product_unique_id;
                $task_lists[$key]['address']     =  $pickup_location;
                $task_lists[$key]['date']        =  $task->picking_date;
                $task_lists[$key]['latitude']    =  $task->latitude;
                $task_lists[$key]['longitude']   =  $task->longitude;
                $task_lists[$key]['type']   =  ucfirst($consignment->type);
                if($task->status == 4){
                  $task_lists[$key]['status']   =  'Failed';
                }else if($task->status == 3){
                  $task_lists[$key]['status']   =  'Pertial';
                }else if($task->status == 2){
                  $task_lists[$key]['status']   =  'Success';
                }else if($task->status == 1){
                  $task_lists[$key]['status']   =  'Running';
                }else{
                  $task_lists[$key]['status']   =  'NoAction';
                }
             }
             // return $task_lists;
          }else{
            $status        =  'success';
            $status_code   =  404;
            $message[]       =  'tasks not found';
          }
        }else{
          // return 2;
            /**
          * Delivery completed Task
          */
          $CompletedTasks  =  DeliveryTask::select(
                            'delivery_task.id',
                            'sub_orders.id AS sub_order_id',
                            'sub_orders.unique_suborder_id',
                            'sub_orders.delivery_image',
                            'orders.delivery_address1',
                            'delivery_task.end_time',
                            'orders.delivery_latitude',
                            'orders.delivery_longitude',
                            'delivery_task.status'
                         )
                         ->leftJoin('sub_orders','sub_orders.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
                         ->leftJoin('orders','orders.id', '=', 'sub_orders.order_id')
                         ->where('sub_orders.deliveryman_id', $user_id)
                         // ->where('orders.order_status', '>', 8)
                         ->where('sub_orders.sub_order_status', '>', 30)
                         ->where('delivery_task.status', '>', 1)
                         ->where('delivery_task.consignment_id', $consignment->id)
                         // ->whereDate('delivery_task.start_time', '>=', Carbon::today()->toDateString())
                         // ->whereDate('delivery_task.end_time', '<', Carbon::tomorrow('Asia/Dhaka')->toDateString())
                         ->get();

          if (count($CompletedTasks)){
            $status        =  'success';
            $status_code   =  200;
            $message[]       =  'tasks found';

             foreach ($CompletedTasks as $key => $task)
             {
                $time = strtotime($task->end_time);

                $task_lists[$key]['task_name']   =  $task->unique_suborder_id;
                $task_lists[$key]['task_id']     =  $task->unique_suborder_id;
                $task_lists[$key]['address']     =  $task->delivery_address1;
                $task_lists[$key]['date']        =  date('Y-m-d', $time);
                $task_lists[$key]['latitude']    =  $task->delivery_latitude;
                $task_lists[$key]['longitude']   =  $task->delivery_longitude;
                $task_lists[$key]['type']   =  ucfirst($consignment->type);
                if($task->status == 4){
                  $task_lists[$key]['status']   =  'Failed';
                }else if($task->status == 3){
                  $task_lists[$key]['status']   =  'Pertial';
                }else if($task->status == 2){
                  $task_lists[$key]['status']   =  'Success';
                }else if($task->status == 1){
                  $task_lists[$key]['status']   =  'Running';
                }else{
                  $task_lists[$key]['status']   =  'NoAction';
                }
             }
          }else{
            $status        =  'success';
            $status_code   =  404;
            $message[]       =  'tasks not found';
          }
        }

        $feedback = [];

        $feedback['status']        =  $status;
        $feedback['status_code']   =  $status_code;
        $feedback['message']       =  $message;
        $feedback['consignment']   =  $consignment;
        $feedback['response']    =  $task_lists;

      }else{
        $status        =  'blank';
        $status_code   =  404;
        $message[]       =  'Not in a trip';

        $feedback = [];

        $feedback['status']        =  $status;
        $feedback['status_code']   =  $status_code;
        $feedback['message']       =  $message;
      }

      // $feedback = [];

      // $feedback['status']        =  $status;
      // $feedback['status_code']   =  $status_code;
      // $feedback['message']       =  $message;
      // $feedback['consignment']   =  $consignment;
      // $feedback['response']    =  $task_lists;
      // $feedback['response']['delivery_task']     =  $delivery_task_lists;

      return response($feedback, 200);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return $this->bad_request();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      return $this->bad_request();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $type)
    {
      /**
      * show all task of specific task unique ID
      * @param task_id
      * @return task details
      */
      $user_id =  Auth::guard('api')->user()->id;

      if($type == "Picking"){
        $task    =  DB::table('order_product')
                     ->select([
                        'order_product.product_unique_id',
                        'order_product.product_category_id',
                        'order_product.picking_time_slot_id',
                        'order_product.quantity',
                        'order_product.product_title',
                        'order_product.pickup_location_id',
                        'order_product.picking_date',
                        'picking_task.quantity AS picking_quantity',
                        'picking_task.type AS task_type',
                        'pickup_locations.title',
                        'pickup_locations.msisdn',
                        'pickup_locations.alt_msisdn',
                        'pickup_locations.address1',
                        'pickup_locations.address2',
                        'pickup_locations.latitude',
                        'pickup_locations.longitude',
                        'picking_time_slots.start_time',
                        'picking_time_slots.end_time',
                        'product_categories.name as cat_name',
                        'merchants.name as merchant_name',
                        'merchants.email as merchant_email',
                        'merchants.msisdn as merchant_msisdn',
                        'orders.merchant_order_id as merchant_order_id',
                        'orders.delivery_name as cus_name',
                        'orders.delivery_email as cus_email',
                        'orders.delivery_msisdn as cus_msisdn',
                        'orders.delivery_alt_msisdn as cus_alt_msisdn',
                        'consignments.consignment_unique_id',
                        'sub_orders.return'
                     ])
                     ->leftJoin('orders', 'orders.id', '=', 'order_product.order_id')
                     ->leftJoin('sub_orders', 'sub_orders.id', '=', 'order_product.sub_order_id')
                     ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                     ->leftJoin('picking_time_slots', 'picking_time_slots.id', '=', 'order_product.picking_time_slot_id')
                     ->leftJoin('product_categories', 'product_categories.id', '=', 'order_product.product_category_id')
                     ->leftJoin('picking_task', 'picking_task.product_unique_id', '=', 'order_product.product_unique_id')
                     ->leftJoin('merchants', 'merchants.id', '=', 'pickup_locations.merchant_id')
                     ->leftJoin('consignments', 'consignments.id', '=', 'picking_task.consignment_id')
                     ->where('order_product.picker_id', $user_id)
                     ->where('sub_orders.return', 0)
                     ->where('order_product.product_unique_id', $id)
                     ->first();
      }else if($type == "Return"){
        $task    =  OrderProduct::select([
                        'order_product.product_unique_id',
                        'order_product.product_category_id',
                        'order_product.picking_time_slot_id',
                        'order_product.quantity',
                        'order_product.product_title',
                        'order_product.pickup_location_id',
                        'order_product.picking_date',
                        'picking_task.quantity AS picking_quantity',
                        'picking_task.type AS task_type',
                        'pickup_locations.title',
                        'pickup_locations.msisdn',
                        'pickup_locations.alt_msisdn',
                        'pickup_locations.address1',
                        'pickup_locations.address2',
                        'pickup_locations.latitude',
                        'pickup_locations.longitude',
                        'picking_time_slots.start_time',
                        'picking_time_slots.end_time',
                        'product_categories.name as cat_name',
                        'merchants.name as merchant_name',
                        'merchants.email as merchant_email',
                        'merchants.msisdn as merchant_msisdn',
                        'orders.merchant_order_id as merchant_order_id',
                        'orders.delivery_name as cus_name',
                        'orders.delivery_email as cus_email',
                        'orders.delivery_msisdn as cus_msisdn',
                        'orders.delivery_alt_msisdn as cus_alt_msisdn',
                        'consignments.consignment_unique_id',
                        'sub_orders.return'
                     ])
                     // ->leftJoin('order_product', 'order_product.id', '=', 'returned_products.order_product_id')
                     ->leftJoin('sub_orders', 'sub_orders.id', '=', 'order_product.sub_order_id')
                     ->leftJoin('orders', 'orders.id', '=', 'order_product.order_id')
                     ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                     ->leftJoin('picking_time_slots', 'picking_time_slots.id', '=', 'order_product.picking_time_slot_id')
                     ->leftJoin('product_categories', 'product_categories.id', '=', 'order_product.product_category_id')
                     ->leftJoin('picking_task', 'picking_task.product_unique_id', '=', 'order_product.product_unique_id')
                     ->leftJoin('merchants', 'merchants.id', '=', 'pickup_locations.merchant_id')
                     ->leftJoin('consignments', 'consignments.id', '=', 'picking_task.consignment_id')
                     // ->where('order_product.picker_id', $user_id)
                     ->where('order_product.product_unique_id', $id)
                     ->where('sub_orders.return', 1)
                     ->first();
      }
      
      $feedback = [];
      if ($task)
      {
         $status        =  'success';
         $status_code   =  200;
         $message[]       =  'task found';
         $consignment_unique_id = $task->consignment_unique_id;

         /**
         * prepare and show task lists
         */
         $task_details = [];

         if($task->return == 0){
          $task_details['task_type'] = 'Picking';
          $start_time = $task->start_time;
          $end_time = $task->end_time;
         }else{
          $task_details['task_type'] = 'Return';
          $start_time = '';
          $end_time = '';
         }

         $task_details['merchant']     =  ['merchant_name'         => $task->merchant_name,  'merchant_email'  => $task->merchant_email, 'merchant_msisdn' => $task->merchant_msisdn, 'merchant_order_id' => $task->merchant_order_id];
         $task_details['packing']     =  ['date'         => $task->picking_date,  'start_time'  => $start_time, 'end_time' => $end_time];
         $task_details['address']     =  ['warehouse'    => $task->title,         'phone1'      => $task->msisdn,     'phone2'   => $task->alt_msisdn ?: "", 'address1'      => $task->address1, 'address2'   => $task->address2 ?: "", 'latitude'   => $task->latitude, 'longitude'   => $task->longitude];

         $task_details['product']     =  ['title'        => $task->product_title, 'category'    => $task->cat_name,   'quantity' => $task->quantity,   'picking_quantity' => $task->picking_quantity,   'product_unique_id'   => $task->product_unique_id];

         $task_details['customer']     =  ['cus_name'        => $task->cus_name, 'cus_email'    => $task->cus_email,   'cus_msisdn' => $task->cus_msisdn,   'cus_alt_msisdn'   => $task->cus_alt_msisdn];


         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['consignment']   =  $consignment_unique_id;
         $feedback['response']      =  $task_details;
      }
      else
      {
         /**
         * task details empty
         * @return status code 200
         */
         $status        =  'Not Found';
         $status_code   =  400;
         $message[]       =  'no task in the list';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
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
    public function edit($id)
    {
      return $this->bad_request();
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
      return $this->bad_request();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      return $this->bad_request();
    }

    /**
    * Do start a single picking task
    * @request POST
    * @param product_id, start_lat, start_long
    * @return success after update picking_task table
    */
    public function do_start_picking( Request $request, $product_id, $type )
    {
      $user_id =  Auth::guard('api')->user()->id;
      $product_id             =  ( $product_id )               ? $product_id           : 0;
      $start_lat              =  $request->has('start_lat')    ? $request->start_lat   : 0;
      $start_long             =  $request->has('start_long')   ? $request->start_long  : 0;

      /**
      * update picking attempt
      */
      if($type == 'Picking' || $type == 'picking'){
        $op                     =  OrderProduct::where('product_unique_id', $product_id)->first();
        if( $op )
        {
           $op->picking_attempts   =  $op->picking_attempts + 1;
           $op->save();
        }
      }

      /**
      * update picking task to picking_task table
      */
      $ptask                  =  PickingTask::where('product_unique_id', $product_id)->first();

      if( count($ptask) > 0 )
      {
//         $ptask->status          =  $ptask->status ? $ptask->status + 1 : 1;
          $ptask->status          =  1;

      }
      else
      {
         $ptask                  =  new PickingTask();
         $ptask->status          =  1;
      }

      $ptask->start_lat       =  $start_lat;
      $ptask->start_long      =  $start_long;
      $ptask->start_time      =  new \DateTime;
      $ptask->product_unique_id  =  $product_id;
      $ptask->picker_id       =  $user_id;

      $ptask->save();

      if($type == 'Picking' || $type == 'picking'){
        $orderProduct = OrderProduct::where('product_unique_id', $product_id)->first();

        // Update Sub-Order Status
        $this->suborderStatus($orderProduct->sub_order_id, '5');

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($ptask->picker_id, $orderProduct->order_id, $orderProduct->sub_order_id, $orderProduct->id, $ptask->picker_id, 'picking_task', 'Pickup man start picking');
      }else{
        $orderProduct = OrderProduct::where('product_unique_id', $product_id)->first();

        // Update Sub-Order Status
        // $this->suborderStatus($orderProduct->sub_order_id, '5');

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($ptask->picker_id, $orderProduct->order_id, $orderProduct->sub_order_id, $orderProduct->id, $ptask->picker_id, 'picking_task', 'Rider starts Return Products');
      }

      /**
      * Sending response
      * @return picking_task ID
      */
      $status        =  'success';
      $status_code   =  200;
      $message[]       =  'Task has been started';

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      $feedback['response']      =  ['picking_task_id' => $ptask->id, 'status'   => $ptask->status];

      return response($feedback, $status_code);
    }


    /**
    * Do complete a single picking task
    * @request POST
    * @param picking_task_id, type, end_lat, end_long, photo, signature
    * @return success after update picking_task table
    */
    public function do_complete_picking( Request $request, $picking_task_id,$type )
    {
      // return 1;
      Log::info($request->all());

      $user_id                =  Auth::guard('api')->user()->id;
      $picking_task_id        =  ( $picking_task_id )        ? $picking_task_id    : 0;
      $end_lat                =  $request->has('end_lat')    ? $request->end_lat   : 0;
      $end_long               =  $request->has('end_long')   ? $request->end_long  : 0;

      /**
      * If picking failed
      * @return failed feedback
      **/
      $failed                 =  $request->has('type')       ? $request->type      : 0;
      if( $failed === 'failed' )
      {
         return $this->picking_complete_failed( $request, $user_id, $picking_task_id );
      }


      /**
      * If attach photo
      * upload this photo to /uploads/picking/
      */
      $photo_path = '';
      if($request->hasFile('photo'))
      {
         $extension = $request->file('photo')->getClientOriginalExtension();
         $fileName = str_random(15).'.jpg';
         $upload_path = 'uploads/picking/';

         $img = \Image::make($request->file('photo'))->save($upload_path.$fileName);
         $photo_path = \URL::to('/')."/".$upload_path.$fileName;
      }
      /**
      * If attach signature
      * upload this signature to /uploads/picking/signature
      */
      $signature_path = '';
      if($request->hasFile('signature'))
      {
         $extension = $request->file('signature')->getClientOriginalExtension();
         $fileName = str_random(15).'.jpg';
         $upload_path = 'uploads/picking/signature/';

         $img = \Image::make($request->file('signature'))->save($upload_path.$fileName);
         $signature_path = \URL::to('')."/".$upload_path.$fileName;
      }

      $remarks                =  $request->has('remarks')   ?  $request->remarks :  '';
        // return $picking_task_id;
       $ptask                  =  PickingTask::find($picking_task_id);
       // return $end_lat.' '.$end_long;
       $ptask->end_lat         =  $end_lat;
       $ptask->end_long        =  $end_long;
       $ptask->end_time        =  new \DateTime;
       $ptask->image           =  $photo_path;
       $ptask->signature       =  $signature_path;
       $ptask->reason_id       = ($request->has('reason_id')) ? $request->input('reason_id') : null;

         # Find original quantity
        if($ptask->type == 'Return' || $ptask->type == 'return'){
          $orderProduct = OrderProduct::whereProductUniqueId($ptask->product_unique_id)->first();
          $ptask->status          =  ( $request->input('quantity') < $orderProduct->quantity ) ? 3 : 2; /* 2 = complete, '3 = partial */
          $ptask->quantity        =  $request->input('quantity');
          $ptask->remarks         =  $remarks;
        }else{
          $orderProduct = OrderProduct::whereProductUniqueId($ptask->product_unique_id)->first();
          $ptask->status          =  ( $request->input('quantity') < $orderProduct->quantity ) ? 3 : 2; /* 2 = complete, '3 = partial */

          // Send Mail to Merchant
          // $this->setMail(Auth::guard('api')->user()->id, 'system', 'Picking Confirmation', $ptask->id, 'picking_task', $user->email, 'Picking Confirmation', $this->pickingConfirmation($ptask->id), '', '');

          $ptask->quantity        =  $request->input('quantity');
          $ptask->remarks         =  $remarks;
        }

       $ptask->save();

       // if($ptask->type != 'Return'){
        if($ptask->status == 3){

          if($ptask->type == 'Return' || $ptask->type == 'return'){
            // Update Sub-Order Status
            $this->suborderStatus($ptask->product->sub_order_id, '37');

            // Send Mail
            $this->mailReturnComplete($ptask->product_unique_id);

            // Send Mail
            // $this->smsReturnComplete($ptask->product_unique_id);

          }else{
            // Update Sub-Order Status
            $this->suborderStatus($ptask->product->sub_order_id, '8');

            // Send Mail
            $this->mailPertialPicked($ptask->product_unique_id);
          }

          // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
          $this->orderLog($ptask->picker_id, $ptask->product->sub_order->order_id, $ptask->product->sub_order->id, '', $ptask->product->sub_order->id, 'sub_orders', 'Product partial picked');

        }else if($ptask->status == 2){

          if($ptask->type == 'Return' || $ptask->type == 'return'){
            // Update Sub-Order Status
            $this->suborderStatus($ptask->product->sub_order_id, '37');

            // Send Mail
            $this->mailReturnComplete($ptask->product_unique_id);
          }else{
            // Update Sub-Order Status
            $this->suborderStatus($ptask->product->sub_order_id, '7');

            // Send Mail
            $this->mailFullPicked($ptask->product_unique_id);
          }

          // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
          $this->orderLog($ptask->picker_id, $ptask->product->sub_order->order_id, $ptask->product->sub_order->id, '', $ptask->product->sub_order->id, 'sub_orders', 'Product full picked');
        }
       // }

       // Update Consignment
       if($type == 'Picking' || $type == 'picking'){
        // return $ptask->consignment_id;
        $consignment = Consignment::where('id', $ptask->consignment_id)->first();
        $consignment->quantity_available = $consignment->quantity_available + $request->input('quantity');
        $consignment->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($ptask->picker_id, $orderProduct->order_id, $orderProduct->sub_order_id, $orderProduct->id, $ptask->picker_id, 'picking_task', 'Pickup man completed picking');

       }else{
        // return 0;
        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($ptask->picker_id, $orderProduct->order_id, $orderProduct->sub_order_id, $orderProduct->id, $ptask->picker_id, 'picking_task', 'Rider completed return');

       }


        

//       *
//       * update order product status
//       * update to 4
//       * comment for testing
      
       if($ptask->status == 2){
          if($ptask->type == 'Return' || $ptask->type == 'return'){
            $order_product          =  OrderProduct::where('product_unique_id', $ptask->product_unique_id)->first();
            $order_product->status  =  '4';
            $order_product->save();
          }else{
            $order_product          =  OrderProduct::where('product_unique_id', $ptask->product_unique_id)->first();
            $order_product->status  =  '4';
            $order_product->save();
          }
       }

       /**
       * If all product picking completed
       * match with order_product status != 4 and order_id
       * comment for testing
       */
       // $order_due = OrderProduct::where('status', '!=', '0')->orWhere('status', '!=', '4')->where('order_id', '=', $order_product->order_id)->count();
       // if($order_due == 0){
       //    $order = Order::find($order_product->order_id);
       //    if( $order )
       //    {
       //       $order->updated_by   = $user_id;
       //       $order->order_status = '4';
       //       $order->save();
       //    }
       // }



      /**
      * Sending response
      * @return picking_task ID, status
      */
      $status        =  'success';
      $status_code   =  200;
      $message[]       =  'Packing has been completed';

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      // $feedback['response']      =  ['picking_task_id' => $ptask->id, 'status'   => $ptask->status];
      $feedback['response']      =  ['picking_task_id' => '20', 'status'   => '3'];

      return response($feedback, $status_code);

    }


    /**
    * failed to picking
    * @param $request, $picking_task_id, $user_id
    */
    private function picking_complete_failed( $request, $user_id, $picking_task_id )
    {
      $ptask                  =  PickingTask::find($picking_task_id);
      $ptask->end_lat         =  $request->end_lat;
      $ptask->end_long        =  $request->end_long;
      $ptask->end_time        =  new \DateTime;
      $ptask->status          =  4; /*failed*/
      $ptask->reason_id       = $request->input('reason_id');
      $ptask->remarks         =  $request->remarks;

      $ptask->save();

      /**
      * update order_product status failed to 1
      * 1 == failed
      */
      if($ptask->type == 'Picking'){
        $order_product         =  OrderProduct::where('product_unique_id', $ptask->product_unique_id)->first();
        $order_product->status =  1;
        $order_product->save();

        // Update Sub-Order Status
        $this->suborderStatus($order_product->sub_order_id, '6');

        // $order = Order::findOrFail($order_product->order_id);
        // $order->updated_by = $user_id;
        // $order->order_status = '2';
        // $order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($ptask->picker_id, $order_product->order_id, $order_product->sub_order_id, $order_product->id, $ptask->picker_id, 'picking_task', 'Pickup man failed to pick');
      }else if($ptask->type == 'Return'){
        // Update Sub-Order Status
        $this->suborderStatus($ptask->product->sub_order->id, '40');
      }

      /**
      * Sending response
      * @return failed status
      */
      $status        =  'failed';
      $status_code   =  200;
      $message[]       =  'Picking has been failed.';

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      $feedback['response']      =  ['picking_task_id' => $ptask->id, 'status'   => $ptask->status, 'product_unique_id'   => $ptask->product_unique_id];

      return response($feedback, 200);
    }


    /**
    * Picking verify
    * @request POST
    * @param lat,long, api_token
    * @return response verified
    */
    public function picking_verify( Request $request, $product_id )
    {
      $user_id             =  Auth::guard('api')->user()->id;
      $product_id          =  ( $product_id )         ? $product_id     : 0;
      $lat                 =  $request->has('lat')    ? $request->lat   : 0;
      $long                =  $request->has('long')   ? $request->long  : 0;

      $order_product       =  OrderProduct::where('product_unique_id', $product_id)->first();

      $pick_loc            =  PickingLocations::find($order_product->pickup_location_id);
      $pick_loc->latitude  =  $lat;
      $pick_loc->longitude =  $long;
      $pick_loc->save();

      /**
      * Sending response
      * @return varified or updated
      */
      $status        =  'success';
      $status_code   =  200;
      $message[]       =  'Packing address has been varified';

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      // $feedback['response']      =  [];

      return response($feedback, 200);
    }


    private function bad_request()
    {
      $status        =  'Bad Request';
      $status_code   =  200;
      $message[]       =  'Not Allowed';

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      // $feedback['response']      =  [];

      return response($feedback, $status_code);
    }

    public function reconciliation_req( Request $request, $consignment_unique_id )
    {
       // return 'a';
      $user_id             =  Auth::guard('api')->user()->id;
      $consignment_unique_id  =  ( $consignment_unique_id )   ? $consignment_unique_id   : 0;

      $consignment          =  Consignment::where('consignment_unique_id', '=', $consignment_unique_id)->first();
      if(count($consignment) == 1){
        $consignment->status  =  3;
        $consignment->save();

        if(count($consignment->picking) > 0){
          foreach ($consignment->picking as $task) {

            if($task->product->sub_order->return == '0'){
              // Update Sub-Order Status
              $this->suborderStatus($task->product->sub_order->id, '9');
            }

          }
        }

        /**
        * Sending response
        * @return varified or updated
        */
        $status        =  'success';
        $status_code   =  200;
        $message[]       =  'Reconcilation Requested';
      }else{
        /**
        * Sending response
        * @return varified or updated
        */
        $status        =  'failed';
        $status_code   =  404;
        $message[]       =  'Reconcilation Failed';
      }

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      // $feedback['response']      =  [];

      return response($feedback, 200);
    }
}
