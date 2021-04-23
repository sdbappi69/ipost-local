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
          'so.unique_suborder_id',
          'r.reason',
          ))
        ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
        ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
        ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
        ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
        ->join('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
        ->join('stores AS st', 'st.id', '=', 'o.store_id')
        ->join('merchants AS m', 'm.id', '=', 'st.merchant_id')
        ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
        ->join('cities AS c', 'c.id', '=', 'z.city_id')
        ->join('states AS s', 's.id', '=', 'c.state_id')
        ->join('picking_task AS ptask', 'ptask.product_unique_id', '=', 'order_product.product_unique_id')
        ->leftJoin('reasons AS r', 'r.id', '=', 'ptask.reason_id')
            //->where('order_product.status', '=', '1')
        ->whereIn('order_product.product_unique_id',function($q) use ($consignments)
        {
          $q->from('picking_task')
          ->selectRaw('product_unique_id')
          ->where('consignment_id', $consignments->id)->get();
        })
        ->orderBy('order_product.id', 'desc')->get();

        $sub_order_status_list = Status::where('active', '1')->whereIn('code', [2, 13, 14])->orderBy('id', 'asc')->lists('title', 'id')->toArray();

        return view('reconciliation.picking', compact('details','riders', 'consignments','products', 'sub_order_status_list'));
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
      $consignments = Consignment::findOrFail($id);

      $consignments->status = 4;

      $consignments->save();

      Session::flash('message', "Reconciliation Done.");

      return redirect('consignments-all');
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

    $picking_task = PickingTask::where('product_unique_id',$request->product_unique_id)->first();
    if(!count($picking_task) > 0){

      return redirect()->back()->WithErrors('Picking Task not found.');
    }

    $picking_task->quantity = $request->picking_task_quantity;

    if($picking_task->save()){

      // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
      $this->orderLog($picking_task->picker_id, $picking_task->product->sub_order->order_id, $picking_task->product->sub_order->id, '', $picking_task->id, 'picking_tasks', 'Product quantity reconcilied');

      // Update product quantity
      $product = OrderProduct::where('product_unique_id', $request->product_unique_id)->first();
      $product->quantity = $request->picking_task_quantity;
      $product->unit_price = $product->unit_price;
      $product->unit_deivery_charge = $product->unit_deivery_charge;
      $product->quantity = $request->rest_quantity;
      $product->sub_total = $product->unit_price * $request->rest_quantity;
      $product->payable_product_price = $cur_payable_product_price = $product->unit_price * $request->rest_quantity;
      $product->total_delivery_charge = $cur_total_delivery_charge = $product->unit_deivery_charge * $request->rest_quantity;
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

        $sub_order = new SubOrder();
        $sub_order->unique_suborder_id = $new_unique_suborder_id;
        $sub_order->order_id = $product->order_id;
        $sub_order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: '.$sub_order->unique_suborder_id);

        // Update Sub-Order Status
        $this->suborderStatus($sub_order->id, $request->sub_order_status);

        // Create Product
        $order_product = new OrderProduct();
        $order_product->product_unique_id = $product->order->unique_order_id."-P".$new_unique_suborder_id_part_2;
        $order_product->product_category_id = $product->product_category_id;
        $order_product->order_id = $product->order_id;
        $order_product->sub_order_id = $sub_order->id;
        $order_product->pickup_location_id = $product->pickup_location_id;
        $order_product->picking_date = $request->picking_date;
        $order_product->picking_time_slot_id = $request->picking_time_slot_id;
        $order_product->product_title = $product->product_title;
        $order_product->unit_price = $product->unit_price;
        $order_product->unit_deivery_charge = $product->unit_deivery_charge;
        $order_product->quantity = $request->rest_quantity;
        $order_product->sub_total = $product->unit_price * $request->rest_quantity;
        $order_product->payable_product_price = $payable_product_price = $product->unit_price * $request->rest_quantity;
        $order_product->total_delivery_charge = $total_delivery_charge = $product->unit_deivery_charge * $request->rest_quantity;
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

  $temp->delivered_quantity = $request->delivered_quantity;
  $temp->delivery_paid_amount = $request->delivery_paid_amount;

  if($temp->save()){
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
            //dd($total_amount_to_collect);
    }
    //dd($total_amount_collected);
    Consignment::where('id',$request->consignments_id)
    ->update(['amount_collected' => $total_amount_collected,'quantity_available' => $available_qty]);

    Session::flash('message', "Product Information Updated.");

    return redirect()->back();
  }
  else{

    return redirect()->back()->WithErrors('Product Information  update failed.');
  }

}
}
