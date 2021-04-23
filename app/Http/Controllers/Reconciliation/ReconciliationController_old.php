<?php

namespace App\Http\Controllers\Reconciliation;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\User;
use App\PickingTask;
use Carbon\Carbon;
use App\DeliveryTask;
use App\OrderProduct;
use App\SubOrder;
use App\Order;
use App\Status;
use DB;

use Session;

use App\Consignment;

class ReconciliationController extends Controller
{

    use LogsTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id,$type,$details = null)
    {
        
      $consignments = Consignment::findOrFail($id);
        //dd($consignments->toArray());
      if($type === 'picking'){
        $products = OrderProduct::select(array(
          'order_product.id AS id',
          'order_product.product_unique_id',
          'order_product.product_title',
          'order_product.quantity',
          'order_product.picking_date',
          'order_product.sub_total',
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
          'm.name as merchant_name',
          'm.email as merchant_email',
          'm.msisdn as merchant_msisdn',
          'ptask.quantity as picking_task_quantity',
          'ptask.updated_at as picke_at',
          'ptask.status as pickUpStatus',
          'ptask.reconcile',
          'so.unique_suborder_id',
          'so.return',
          'r.reason',
          ))
        ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
        ->leftJoin('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
        ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
        ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
        ->leftJoin('stores AS st', 'st.id', '=', 'o.store_id')
        ->leftJoin('merchants AS m', 'm.id', '=', 'st.merchant_id')
        ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id')
        ->leftJoin('cities AS c', 'c.id', '=', 'z.city_id')
        ->leftJoin('states AS s', 's.id', '=', 'c.state_id')
        ->leftJoin('picking_task AS ptask', 'ptask.product_unique_id', '=', 'order_product.product_unique_id')
        ->leftJoin('reasons AS r', 'r.id', '=', 'ptask.reason_id')
            //->where('order_product.status', '=', '1')
        ->whereIn('order_product.product_unique_id',function($q) use ($consignments)
        {
          $q->from('picking_task')
          ->selectRaw('product_unique_id')
          ->where('consignment_id', $consignments->id)->get();
        })
        ->orderBy('order_product.id', 'desc')->get();

        $sub_order_status_list = Status::where('active', '1')->whereIn('code', [2, 13])->orderBy('id', 'asc')->lists('title', 'id')->toArray();

        $sub_order_status_return_list = Status::where('active', '1')->whereIn('code', [35])->orderBy('id', 'asc')->lists('title', 'id')->toArray();

        return view('reconciliation.picking', compact('details','riders', 'consignments','products', 'sub_order_status_list', 'sub_order_status_return_list'));
      }
      elseif ($type === 'delivery') {
        $sub_orders = SubOrder::whereStatus(true)
        ->orderBy('id', 'desc')
        ->where('destination_hub_id', '=', auth()->user()->reference_id)
        ->where('deliveryman_id', '!=', null)
        ->whereIn('unique_suborder_id',function($q) use ($consignments)
        {
          $q->from('delivery_task')
          ->selectRaw('unique_suborder_id')
          ->where('consignment_id',$consignments->id)->get();
        })
        ->get();
        //dd($sub_orders->toArray());
        // return $sub_orders->dTask->status;

        $sub_order_status_list = Status::where('active', '1')->whereIn('code', [34, 35])->orderBy('id', 'asc')->lists('title', 'id')->toArray();

        return view('reconciliation.delivery', compact('details','riders', 'consignments','sub_orders', 'sub_order_status_list'));
      }
      else{
            return redirect()->back();
      }

      $riders = User::
      select('users.name','users.id')
      ->leftJoin('hubs','hubs.id','=','users.reference_id')
      ->where('reference_id', '=', auth()->user()->reference_id)
      ->where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id')->toArray();
      $consignment = '';
      return view('reconciliation.index', compact('riders', 'consignment'));
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
        // return $request->all();

      $riders = User::
      select('users.name','users.id')
      ->leftJoin('hubs','hubs.id','=','users.reference_id')
      ->where('reference_id', '=', auth()->user()->reference_id)
      ->where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id')->toArray();

      $user_id    = $rider_id =  $request->rider_id;

      // $consignment = Consignment::whereStatus(true)->where('rider_id', $user_id)->where('created_at','>=', date('Y-m-d')." 00:00:00")->where('created_at','<=', date('Y-m-d')." 23:59:59")->first();

      $CompletedPickingTasks      =  PickingTask::select(
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
      ->where('order_product.status', '!=', 100)
      ->where('picking_task.status', 2)
      ->whereDate('picking_task.start_time', '>=', Carbon::today()->toDateString())
      ->whereDate('picking_task.end_time', '<', Carbon::tomorrow('Asia/Dhaka')->toDateString())
      ->get();
      /**
      * Delivery completed Task
      */
      $CompletedDeliveryTasks  =  DeliveryTask::select(
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
      ->where('orders.order_status', '>', 8)
      ->where('sub_orders.status', '!=', 100)
      ->where('delivery_task.status', 2)
      ->whereDate('delivery_task.start_time', '>=', Carbon::today()->toDateString())
      ->whereDate('delivery_task.end_time', '<', Carbon::tomorrow('Asia/Dhaka')->toDateString())
      ->get();

      $feedback = [];

      if ( count($CompletedPickingTasks) || count($CompletedDeliveryTasks) )
      {
       $status        =  'success';
       $status_code   =  200;
       $message       =  'tasks found';

         /**
         * prepare and show task lists
         */
         $picking_task_lists = [];
         foreach( $CompletedPickingTasks as $key => $task )
         {
          $pickup_location  =  \APIHelper::get_pickup_location_by_id($task->pickup_location_id)  ? \APIHelper::get_pickup_location_by_id($task->pickup_location_id)->title : null;
          $picking_task_lists[$key]['task_name']   =  $task->product_title;
          $picking_task_lists[$key]['task_id']     =  $task->id;
          $picking_task_lists[$key]['address']     =  $pickup_location;
          $picking_task_lists[$key]['date']        =  $task->picking_date;
          $picking_task_lists[$key]['latitude']    =  $task->latitude;
          $picking_task_lists[$key]['longitude']   =  $task->longitude;
          if($task->status == 4){
            $picking_task_lists[$key]['status']   =  'Failed';
          }else if($task->status == 3){
            $picking_task_lists[$key]['status']   =  'Pertial';
          }else if($task->status == 2){
            $picking_task_lists[$key]['status']   =  'Success';
          }else if($task->status == 1){
            $picking_task_lists[$key]['status']   =  'Running';
          }else{
            $picking_task_lists[$key]['status']   =  'NoAction';
          }
        }

         /**
         * Delivery Completed Task
         */
         $delivery_task_lists = [];
         $amount_to_collect = 0;
         $amount_collected = 0;
         foreach ($CompletedDeliveryTasks as $key => $task)
         {
          $time = strtotime($task->end_time);

          $delivery_task_lists[$key]['task_name']   =  $task->unique_suborder_id;
          $delivery_task_lists[$key]['task_id']     =  $task->id;
          $delivery_task_lists[$key]['address']     =  $task->delivery_address1;
          $delivery_task_lists[$key]['date']        =  date('Y-m-d', $time);
          $delivery_task_lists[$key]['latitude']    =  $task->delivery_latitude;
          $delivery_task_lists[$key]['longitude']   =  $task->delivery_longitude;
          if($task->status == 4){
            $delivery_task_lists[$key]['status']   =  'Failed';
          }else if($task->status == 3){
            $delivery_task_lists[$key]['status']   =  'Pertial';
          }else if($task->status == 2){
            $delivery_task_lists[$key]['status']   =  'Success';
          }else if($task->status == 1){
            $delivery_task_lists[$key]['status']   =  'Running';
          }else{
            $delivery_task_lists[$key]['status']   =  'NoAction';
          }

            // Temp Consignment
          $tmp_condignment = OrderProduct::select(DB::raw("sum(total_payable_amount) as amount_to_collect"), DB::raw("sum(delivery_paid_amount) as amount_collected"))
          ->where('status', '!=', '0')
          ->where('sub_order_id', $task->sub_order_id)
          ->first();
          $amount_to_collect = $amount_to_collect + $tmp_condignment->amount_to_collect;
          $amount_collected = $amount_collected + $tmp_condignment->amount_collected;
        }

        $consignment['amount_to_collect'] = $amount_to_collect;
        $consignment['amount_collected'] = $amount_collected;
        $consignment['type'] = 'delivery';

         // $feedback['status']        =  $status;
         // $feedback['status_code']   =  $status_code;
         // $feedback['message']       =  $message;
         // $feedback['consignment']   =  $consignment;
         // $feedback['response']['picking_task']      =  $picking_task_lists;
         // $feedback['response']['delivery_task']     =  $delivery_task_lists;
      }else{
        $consignment = '';
        $picking_task_lists = '';
        $delivery_task_lists = '';
      }

        // return $consignment;

      return view('reconciliation.index', compact('riders', 'consignment', 'picking_task_lists', 'delivery_task_lists', 'rider_id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $deliveryman_id = $id;

      $sub_orders = SubOrder::select(
        'sub_orders.id AS id',
        'sub_orders.unique_suborder_id AS unique_suborder_id',
        'sub_orders.order_id AS order_id'
        )
      ->join('delivery_task', 'delivery_task.unique_suborder_id', '=', 'sub_orders.unique_suborder_id')
      ->where('delivery_task.deliveryman_id', $deliveryman_id)
      ->where('delivery_task.updated_at','>=', date('Y-m-d')." 00:00:00")
      ->where('delivery_task.updated_at','<=', date('Y-m-d')." 23:59:59")
      ->get();

      foreach ($sub_orders as $sub_order) {
            // return $sub_order->id;
        $sub_order_update = SubOrder::findOrFail($sub_order->id);
        $sub_order_update->sub_order_status = 10;
        $sub_order_update->updated_by   = auth()->user()->id;
        $sub_order_update->save();

        $order_due = SubOrder::where('sub_order_status', '!=', '10')->where('order_id', '=', $sub_order->order_id)->count();
            // dd($order_due);
        if($order_due == 0){
         $order = Order::find($sub_order->order_id);
         if( $order )
         {
          $order->updated_by   = auth()->user()->id;
          $order->order_status = '10';
          $order->save();
        }
      }
    }

    $riders = User::
    select('users.name','users.id')
    ->leftJoin('hubs','hubs.id','=','users.reference_id')
    ->where('reference_id', '=', auth()->user()->reference_id)
    ->where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id')->toArray();
    $consignment = '';
    return view('reconciliation.index', compact('riders', 'consignment'));
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

    public function picking_done($id){

      $due_reconcile = PickingTask::where('consignment_id', $id)->where('reconcile', 0)->count();

      if($due_reconcile == 0){
        $consignments = Consignment::findOrFail($id);

        $consignments->status = 4;

        $consignments->save();

        Session::flash('message', "Reconciliation Done.");
        return redirect('consignments-all');

      }else{

        Session::flash('message', "Reconciliation Due.");
        return redirect()->back();

      }
    }
    public function delivery_done($id){
      $consignments = Consignment::findOrFail($id);

      $consignments->status = 4;

      $consignments->save();

      $sub_orders = SubOrder::whereStatus(true)
      ->orderBy('id', 'desc')
      ->where('destination_hub_id', '=', auth()->user()->reference_id)
      ->where('deliveryman_id', '!=', null)
      ->whereIn('unique_suborder_id',function($q) use ($consignments)
      {
        $q->from('delivery_task')
        ->selectRaw('unique_suborder_id')
        ->where('status','!=','4')
        ->where('consignment_id',$consignments->id)->get();
      })
      ->get();
      foreach ($sub_orders as $sub_order) {
            // return $sub_order->id;
        $sub_order_update = SubOrder::findOrFail($sub_order->id);
        $sub_order_update->sub_order_status = 10;
        $sub_order_update->updated_by   = auth()->user()->id;
        $sub_order_update->save();

        $order_due = SubOrder::where('sub_order_status', '!=', '10')->where('order_id', '=', $sub_order->order_id)->count();
            // dd($order_due);
        if($order_due == 0){
         $order = Order::find($sub_order->order_id);
         if( $order )
         {
          $order->updated_by   = auth()->user()->id;
          $order->order_status = '10';
          $order->save();
        }
      }
    }

    Session::flash('message', "Reconciliation Done.");

    return redirect('consignments-all');
  }

  public function update_picking(Request $request){
    // return $request->all();
    $picking_task = PickingTask::where('product_unique_id',$request->product_unique_id)->first();
    $picking_task->reconcile = 1;
    $picking_task->save();

    if(!count($picking_task) > 0){

      return redirect()->back()->WithErrors('Picking Task not found.');
    }

    $picking_task->quantity = $request->picking_task_quantity;

    if($picking_task->save()){

      // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
      $this->orderLog($picking_task->picker_id, $picking_task->product->sub_order->order_id, $picking_task->product->sub_order->id, '', $picking_task->id, 'picking_tasks', 'Product quantity reconcilied');

      // Update product quantity
      $product = OrderProduct::where('product_unique_id', $request->product_unique_id)->first();

      // if($request->sub_order_status == 13){
      //   // Update Sub-Order Status
      //   $this->suborderStatus($picking_task->product->sub_order->id, $request->sub_order_status);
      // }else if($product->quantity == $request->quantity){
      //   // Update Sub-Order Status
      //   $this->suborderStatus($product->sub_order->id, '15');
      // }else{
      //   // Update Sub-Order Status
      //   $this->suborderStatus($product->sub_order->id, '16');
      // }

      if($request->sub_order_status == 13 && $request->picking_task_quantity == 0){

        // Update Sub-Order Status
        $this->suborderStatus($picking_task->product->sub_order->id, $request->sub_order_status);

      }else{

        // Update Sub-Order Status
        // $this->suborderStatus($picking_task->product->sub_order->id, 13);
        // return $request->sub_order_status;
        if($request->picking_task_quantity == 0 && $request->sub_order_status == 2){
          // Update Sub-Order Status
          $this->suborderStatus($picking_task->product->sub_order->id, 13);
        }

        if($request->picking_task_quantity == 0 && $request->sub_order_status == 35){
          // Update Sub-Order Status
          $this->suborderStatus($picking_task->product->sub_order->id, 13);
        }

        $product->quantity = $request->picking_task_quantity;
        $product->unit_price = $product->unit_price;
        $product->unit_deivery_charge = $product->unit_deivery_charge;
        $product->sub_total = $product->unit_price * $request->picking_task_quantity;
        if($request->rest_quantity == $request->quantity){
          $product->payable_product_price = $cur_payable_product_price = 0;
          $product->total_delivery_charge = $cur_total_delivery_charge = 0;
        }else{
          $product->payable_product_price = $cur_payable_product_price = $product->unit_price * $request->picking_task_quantity;
          $product->total_delivery_charge = $cur_total_delivery_charge = $product->unit_deivery_charge * $request->picking_task_quantity;
        }
        if($product->delivery_pay_by_cus == '1'){
            $product->delivery_pay_by_cus = 1;
            $product->total_payable_amount = $cur_payable_product_price + $cur_total_delivery_charge;
        }else{
            $product->delivery_pay_by_cus = '0';
            $product->total_payable_amount = $cur_payable_product_price;
        }
        $product->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog($picking_task->picker_id, $picking_task->product->sub_order->order_id, $picking_task->product->sub_order->id, '', $product->id, 'order_product', 'Product quantity updated');

        if($request->rest_quantity > 0){

          // Create Sub-Order
          $last_sub_order = SubOrder::where('order_id', $product->order_id)->orderBy('id', 'desc')->first();
          $new_unique_suborder_id_part_1 = substr_replace($last_sub_order->unique_suborder_id, "", -1);
          $new_unique_suborder_id_part_2 = substr($last_sub_order->unique_suborder_id, -1)+1;

          $new_unique_suborder_id = $new_unique_suborder_id_part_1.$new_unique_suborder_id_part_2;
          if(SubOrder::where('unique_suborder_id', $new_unique_suborder_id)->count() > 0){
            $new_add = $new_unique_suborder_id_part_2+1;
            $new_unique_suborder_id_part_2 = $new_unique_suborder_id_part_2."-".$new_add;
            $new_unique_suborder_id = $new_unique_suborder_id_part_1.$new_unique_suborder_id_part_2;
            // $new_unique_suborder_id = $new_unique_suborder_id_part_1.$new_unique_suborder_id_part_2+7;
          }

          // return $new_unique_suborder_id;

          $sub_order = new SubOrder();
          $sub_order->unique_suborder_id = $new_unique_suborder_id;
          $sub_order->order_id = $product->order_id;
          if($request->sub_order_status == 35){
            $sub_order->return = 1;
            $sub_order->source_hub_id = $picking_task->product->sub_order->source_hub_id;
            $sub_order->destination_hub_id = $picking_task->product->sub_order->destination_hub_id;
            $sub_order->next_hub_id = $picking_task->product->sub_order->next_hub_id;
          }
          $sub_order->save();

          // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
          $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: '.$sub_order->unique_suborder_id);

          if($request->sub_order_status == 35){
            // Update Sub-Order Status
            $this->suborderStatus($sub_order->id, 26);
          }else{
            // Update Sub-Order Status
            $this->suborderStatus($sub_order->id, $request->sub_order_status);
          }

          // Create Product
          $order_product = new OrderProduct();
          if($request->sub_order_status == 35){
            $order_product->product_unique_id = $new_unique_suborder_id;
          }else{
            $order_product->product_unique_id = $product->order->unique_order_id."-P".$new_unique_suborder_id_part_2;
          }
          $order_product->product_category_id = $product->product_category_id;
          $order_product->order_id = $product->order_id;
          $order_product->sub_order_id = $sub_order->id;
          $order_product->pickup_location_id = $product->pickup_location_id;
          if($request->picking_date != ''){
            $order_product->picking_date = $request->picking_date;
            $order_product->picking_time_slot_id = $request->picking_time_slot_id;
          }
          $order_product->product_title = $product->product_title;
          $order_product->unit_price = $product->unit_price;
          // if($request->sub_order_status == 13){
          //   $order_product->unit_deivery_charge = $unit_deivery_charge = 0;
          // }else{
            $order_product->unit_deivery_charge = $unit_deivery_charge = $product->unit_deivery_charge;
          // }
          $order_product->quantity = $request->rest_quantity;
          $order_product->sub_total = $product->unit_price * $request->rest_quantity;
          // if($request->sub_order_status == 13){
          //   $order_product->payable_product_price = 0;
          // }else{
            $order_product->payable_product_price = $payable_product_price = $product->unit_price * $request->rest_quantity;
          // }
          $order_product->total_delivery_charge = $total_delivery_charge = $unit_deivery_charge * $request->rest_quantity;
          if($product->delivery_pay_by_cus == '1'){
              $order_product->delivery_pay_by_cus = 1;
              $order_product->total_payable_amount = $payable_product_price + $total_delivery_charge;
          }else{
              $order_product->delivery_pay_by_cus = '0';
              $order_product->total_payable_amount = $payable_product_price;
          }
          $order_product->width = $product->width;
          $order_product->height = $product->height;
          $order_product->length = $product->length;
          $order_product->weight = $product->weight;
          $order_product->status = 1;
          $order_product->save();
          
        }

      }

      
      // else{

      //   if($product->sub_order->return == 1){
      //     // Update Sub-Order Status
      //     $this->suborderStatus($product->sub_order->id, '36');
      //   }

      // }

     $quantity_available = PickingTask::where('consignment_id',$request->consignments_id)->sum('quantity');

     Consignment::where('id',$request->consignments_id)
     ->update(['quantity_available' => $quantity_available]);

     Session::flash('message', "Picking Task Updated");

     return redirect()->back();
   }
   else{

    return redirect()->back()->WithErrors('Picking Task collected quantity cannot update.');
  }

}

public function update_delivery(Request $request){
  // dd($request->all());

  $temp = OrderProduct::where('product_unique_id',$request->product_unique_id)
  ->where('sub_order_id',$request->sub_order_id)
  ->first();

 //dd($temp->toArray());
  if(!count($temp) > 0){
    return redirect()->back()->WithErrors('Product not found.');
  }

  $delivery_task = DeliveryTask::where('unique_suborder_id',$temp->sub_order->unique_suborder_id)->first();
  $delivery_task->reconcile = 1;
  $delivery_task->save();

  $temp->quantity = $request->delivery_task_quantity;
  $temp->delivered_quantity = $request->delivery_task_quantity;
  $temp->delivery_paid_amount = $request->delivery_paid_amount;
  $temp->sub_total = $temp->unit_price * $request->delivery_task_quantity;
  $temp->payable_product_price = $temp->unit_price * $request->delivery_task_quantity;
  $temp->total_delivery_charge = $temp->unit_deivery_charge * $request->delivery_task_quantity;
  if($temp->delivery_pay_by_cus == '1'){
      $temp->delivery_pay_by_cus = 1;
      $temp->total_payable_amount = $temp->payable_product_price + $temp->total_delivery_charge;
  }else{
      $temp->delivery_pay_by_cus = '0';
      $temp->total_payable_amount = $temp->payable_product_price;
  }

  if($temp->save()){

    $dtask = DeliveryTask::where('unique_suborder_id', $temp->sub_order->unique_suborder_id)->first();
    $dtask->status = 4;
    $dtask->save();

    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
    $this->orderLog(auth()->user()->id, $temp->order_id, $temp->sub_order_id, '', $temp->sub_order_id, 'sub_orders', 'Sub-Order reconcilied');

    $total_amount_collected = 0;
    $available_qty = 0;
    //dd($request->sub_orders_id_for_consingment);
    $sub_orders_for_theConsignment = explode(',',$request->sub_orders_id_for_consingment);
   // dd($sub_orders_for_theConsignment);
    foreach ( $sub_orders_for_theConsignment as $value) {
      //dd($value);
      $temp_amount_collected =  OrderProduct::where('status','!=',0)->where('sub_order_id',$value)->get();
      //dd($temp_amount_to_collect);
      if(count($temp_amount_collected) > 0 and !is_null($temp_amount_collected)){
        foreach ($temp_amount_collected as $x) {
          //dd($x->delivery_paid_amount);
          $total_amount_collected  = $total_amount_collected + (int)$x->delivery_paid_amount;
          $available_qty  = $available_qty + (int)$x->delivered_quantity;
        }
      }
    }    

    if($request->rest_quantity == 0){
      // return 1;
      // Update Sub-Order Status
      $this->suborderStatus($temp->sub_order_id, '38');

    }else{
      // return 2;
      if($request->rest_quantity == $request->quantity){
        $this->suborderStatus($temp->sub_order_id, '33');
      }else{
        $this->suborderStatus($temp->sub_order_id, '32');
      }

      // Create Sub-Order
      if($request->sub_order_status == 34){
        // return 1;
        $last_sub_order = SubOrder::where('id', $request->sub_order_id)->first();
        $new_unique_suborder_id_part_1 = substr_replace($last_sub_order->unique_suborder_id, "", -1);
        $new_unique_suborder_id_part_2 = substr($last_sub_order->unique_suborder_id, -1)+1;

        $source_hub_id = $last_sub_order->source_hub_id;
        $destination_hub_id = $last_sub_order->destination_hub_id;
        $next_hub_id = $last_sub_order->next_hub_id;
        // return "S:".$source_hub_id." D:".$destination_hub_id." N:".$next_hub_id;
      }else{
        // return 2;
        $last_sub_order = SubOrder::where('id', $request->sub_order_id)->first();
        $new_unique_suborder_id_part_1 = substr_replace($last_sub_order->unique_suborder_id, "", -2);
        $new_unique_suborder_id_part_2 = 'R'.substr($last_sub_order->unique_suborder_id, -1);

        $source_hub_id = auth()->user()->reference_id;
        $destination_hub_id = $temp->pickup_location->zone->hub_id;
        $next_hub_id = $temp->pickup_location->zone->hub_id;
      }
      $new_unique_suborder_id = $new_unique_suborder_id_part_1.$new_unique_suborder_id_part_2;

      $sub_order = new SubOrder();
      $sub_order->unique_suborder_id = $new_unique_suborder_id;
      $sub_order->order_id = $temp->order_id;
      $sub_order->source_hub_id = $source_hub_id;
      $sub_order->destination_hub_id = $destination_hub_id;
      $sub_order->next_hub_id = $next_hub_id;
      if($request->sub_order_status == 35){
        $sub_order->return = 1;
      }else{
        $sub_order->return = 0;
      }
      $sub_order->save();

      // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
      $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: '.$sub_order->unique_suborder_id);

      // Update Sub-Order Status
      if($request->sub_order_status == 35){
        if($sub_order->source_hub_id == $sub_order->destination_hub_id){
          $this->suborderStatus($sub_order->id, 26);
        }else{
          $this->suborderStatus($sub_order->id, 16);
        }
      }else{
        $this->suborderStatus($sub_order->id, $request->sub_order_status);
      }

      // Create Product
      $order_product = new OrderProduct();
      $order_product->product_unique_id = $new_unique_suborder_id;
      $order_product->product_category_id = $temp->product_category_id;
      $order_product->order_id = $temp->order_id;
      $order_product->sub_order_id = $sub_order->id;
      $order_product->pickup_location_id = $temp->pickup_location_id;
      if($request->sub_order_status == 34){
        $order_product->picking_date = $temp->picking_date;
        $order_product->picking_time_slot_id = $temp->picking_time_slot_id;
      }
      $order_product->product_title = $temp->product_title;
      $order_product->unit_price = $temp->unit_price;
      if($request->sub_order_status == 34){
        $order_product->unit_deivery_charge = $unit_deivery_charge = $temp->unit_deivery_charge;
      }else{
        $order_product->unit_deivery_charge = $unit_deivery_charge = $temp->unit_deivery_charge + ($temp->unit_deivery_charge/2);
      }      
      $order_product->quantity = $request->rest_quantity;
      $order_product->sub_total = $temp->unit_price * $request->rest_quantity;
      if($request->sub_order_status == 34){
        $order_product->payable_product_price = $payable_product_price = $temp->unit_price * $request->rest_quantity;
      }
      $order_product->total_delivery_charge = $total_delivery_charge = $unit_deivery_charge * $request->rest_quantity;
      if($request->sub_order_status == 34){
        if($temp->delivery_pay_by_cus == '1'){
            $order_product->delivery_pay_by_cus = 1;
            $order_product->total_payable_amount = $payable_product_price + $total_delivery_charge;
        }else{
            $order_product->delivery_pay_by_cus = '0';
            $order_product->total_payable_amount = $payable_product_price;
        }
      }
      $order_product->width = $temp->width;
      $order_product->height = $temp->height;
      $order_product->length = $temp->length;
      $order_product->weight = $temp->weight;
      $order_product->status = 1;
      $order_product->save();

    }

    //dd($total_amount_collected);
    // if($request->rest_quantity != 0){
      Consignment::where('id',$request->consignments_id)
      ->update(['amount_collected' => $total_amount_collected,'quantity_available' => $available_qty]);
    // }

    Session::flash('message', "Product Information Updated.");

    return redirect()->back();

  }else{
    return redirect()->back()->WithErrors('Product Information update failed.');
  }
}


}