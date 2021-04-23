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
use App\User;
use App\Rack;
use App\RackProduct;
use App\Shelf;
use App\ShelfProduct;
use App\ConsignmentTask;
use App\SubOrderTripMap;
use Session;
use Redirect;
use Validator;
use DB;

class HubReceiveController extends Controller {

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:vehiclemanager|hubmanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $sub_orders = SubOrder::whereStatus(true)->where('sub_order_status', '=', '6')->orderBy('id', 'desc')->where('destination_hub_id', '=', auth()->user()->reference_id)->paginate(9);
        $rider = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();
        return view('hub-receive.index', compact('sub_orders', 'rider'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
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
        $validation = Validator::make($request->all(), [
                    'order_id' => 'required',
                    // 'sub_order_id' => 'required',
                    'remarks' => 'sometimes',
                    'deliveryman_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Update Sub-Order
        $suborder = SubOrder::findOrFail($id);
        $suborder->sub_order_status = '8';
        $suborder->delivery_assigned_by = auth()->user()->id;
        $suborder->deliveryman_id = $request->deliveryman_id;
        $suborder->delivery_status = '0';
        $suborder->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $suborder->order_id, $suborder->id, '', $suborder->deliveryman_id, 'users', 'Assigned a delivery man for the Sub-Order');

        // Update Order
        $order_due = SubOrder::where('sub_order_status', '!=', '8')->where('order_id', '=', $request->order_id)->count();
        if ($order_due == 0) {
            $order = Order::findOrFail($request->order_id);
            $order->order_status = '8';
            $order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) received');
        }

        // Release Trip
        $trip = SuborderTrip::where('sub_order_id', '=', $id)->where('status', '=', '1')->firstOrFail();
        $trip->status = '2';
        $trip->save();

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
                ->where('sub_order_id', '=', $id)
                ->where('status', '!=', '0')
                ->first();

        $rackData = Rack::whereStatus(true)->where('hub_id', '=', $delivery_hub_id)->get();
        if ($rackData->count() != 0) {
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

                if ($available_width >= $sub_order_size->width && $available_height >= $sub_order_size->height && $available_length >= $sub_order_size->length) {
                    $rack_id = $rack->id;
                    $message = "Please keep the product on " . $rack->rack_title;
                    break;
                } else {
                    $rack_id = 0;
                    $message = "Dedicated rack hasn't enough space. Please use defult rack";
                }
            }
        } else {
            $rack_id = 0;
            $message = "No Rack defined for this delivery zone.";
        }

        // Insert product on rack
        $sub_order_products = OrderProduct::where('sub_order_id', '=', $id)
                ->where('status', '!=', '0')
                ->get();

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

        Session::flash('inventory', $message);
        return redirect('/hub-receive');

        // Session::flash('message', "Sub-Order received successfully");
        // return redirect('/hub-receive');
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

    public function receiveProduct(Request $request) {
        $query = DB::table('consignments_tasks')
                ->join('sub_orders', 'sub_orders.id', '=', 'consignments_tasks.sub_order_id')
                ->join('order_product', 'sub_orders.id', '=', 'order_product.sub_order_id')
                ->join('users', 'users.id', '=', 'consignments_tasks.rider_id')
                ->join('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                ->join('zones AS pick_zones', 'pick_zones.id', '=', 'pickup_locations.zone_id')
                ->select('users.name as rider_name', 'unique_suborder_id', 'product_title', 'order_product.quantity', 'consignments_tasks.otp')
                ->whereIn('task_type_id', [1, 5])
                // ->whereIn('sub_order_status', [7, 8])
                ->where('sub_order_status', 9)
                ->where('consignments_tasks.status', '>', 1)
                ->where('tm_picking_status', '=', 1)
                ->where('sub_orders.source_hub_id', '=', auth()->user()->reference_id);

        $request->has('sub_order_id') ? $query->where('unique_suborder_id', $request->sub_order_id) : null;
        $request->has('rider_id') ? $query->where('rider_id', $request->rider_id) : null;

        $receiveTasks = $query->paginate(20);
        $riders = \App\User::select('users.name', 'users.id')
                        ->where('users.status', true)->where('users.user_type_id', '=', '8')->lists('name', 'users.id')->toArray();

        return view('hub-receive.receive-product', compact('receiveTasks', 'riders'));
    }

    public function receivedAndVarified(Request $request) {
        if (is_array($request->unique_suborder_id)) {
            $subOders = SubOrder::whereIn('unique_suborder_id', $request->unique_suborder_id)->get();
        } else {
            $subOders = SubOrder::where('unique_suborder_id', $request->unique_suborder_id)->get();
        }
        $products = array();
        foreach ($subOders as $subOder) {
            ConsignmentTask::whereSubOrderId($subOder->id)->update(['reconcile' => 1]);
            $products[] = $subOder->product->id;
        }
        if (count($products) > 0) {

            foreach ($products as $id) {

                $product = OrderProduct::where('id', '=', $id)->first();

                // Select Hubs
                $picking_hub_id = $product->sub_order->source_hub_id;
                $delivery_hub_id = $product->sub_order->destination_hub_id;
                $master_hub_id = $product->order->hub_id;

                if ($master_hub_id != $picking_hub_id) {
                    // return 0;
                    $product->hub_transfer = '1';
                    // $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
                    // $delivery_hub_id = $master_hub_id;
                } else if ($delivery_hub_id != $picking_hub_id) {
                    // return 10;
                    $product->hub_transfer = '1';
                    // $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
                }
                // return 1;
                if ($master_hub_id == auth()->user()->reference_id) {
                    $product->status = '5';
                }
                $product->save();

                $message = '';

                // Decide Hub or Rack
                if ($picking_hub_id == $delivery_hub_id) { // Go for Rack
                    // $rackData = Rack::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->get();
                    // if ($rackData->count() != 0) {
                    //     foreach ($rackData as $rack) {
                    //         $rackUsed = RackProduct::select(array(
                    //                     DB::raw("SUM(op.width) AS total_width"),
                    //                     DB::raw("SUM(op.height) AS total_height"),
                    //                     DB::raw("SUM(op.length) AS total_length"),
                    //                 ))
                    //                 ->join('order_product AS op', 'op.id', '=', 'rack_products.product_id')
                    //                 ->where('rack_products.status', '=', '1')
                    //                 ->where('rack_products.rack_id', '=', $rack->id)
                    //                 ->first();
                    //         $available_width = $rack->width - $rackUsed->width;
                    //         $available_height = $rack->height - $rackUsed->height;
                    //         $available_length = $rack->length - $rackUsed->length;
                    //         if ($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length) {
                    //             $rack_id = $rack->id;
                    //             $message = $message . "$product->product_unique_id: Please keep the product on " . $rack->rack_title . "\n";
                    //             break;
                    //         } else {
                    //             $rack_id = 0;
                    //             $message = $message . "$product->product_unique_id: Dedicated rack hasn't enough space. Please use defult rack" . "\n";
                    //         }
                    //     }
                    // } else {
                    //     $rack_id = 0;
                    //     $message = $message . "$product->product_unique_id: No Rack defined for this delivery zone." . "\n";
                    // }
                    // $sub_order = SubOrder::findOrFail($product_info->sub_order_id);
                    $orderDetail = Order::findOrFail($product->order_id);
                    foreach ($orderDetail->suborders as $row) {

                        $verify_suborder = OrderProduct::where('sub_order_id', '=', $row->id)->where('status', '!=', '0')->count();
                        if ($verify_suborder == 0) {
                            $sub_order = SubOrder::where('id', '=', $row->id)->delete();
                        } else {

                            $sub_order = SubOrder::findOrFail($row->id);
                            // $sub_order->responsible_user_id = $request->responsible_user_id;
                            // $sub_order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('sub_order_id', '=', $row->id)->count();
                            // if($sub_order_due == 0){
                            //     $sub_order->sub_order_status = '7';
                            // }
                            $sub_order->destination_hub_id = $delivery_hub_id;
                            $sub_order->next_hub_id = $delivery_hub_id;
                            // $sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order->id)->orderBy('id', 'asc')->first();
                            // if ($sub_order_trip_map) {
                            //     $sub_order->next_hub_id = $sub_order_trip_map->hub_id;
                            // } else {
                            //     $sub_order->next_hub_id = $delivery_hub_id;
                            // }
                            $sub_order->current_hub_id = auth()->user()->reference_id;
                            $sub_order->save();

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            // $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $delivery_hub_id, 'hubs', 'Decided destination for Sub-Order: '.$sub_order->unique_suborder_id);
                        }
                    }

                    // Insert product on rack
                    // $rack_product = new RackProduct();
                    // $rack_product->rack_id = $rack_id;
                    // $rack_product->product_id = $product->id;
                    // $rack_product->status = '1';
                    // $rack_product->created_by = auth()->user()->id;
                    // $rack_product->updated_by = auth()->user()->id;
                    // $rack_product->save();
                    // Update Sub-Order Statuss
                    $this->suborderStatus($product->sub_order->id, '26');

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    // $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $rack_product->id, 'rack_products', 'Product racked');
                    // Update Order
                    // $order_due = SubOrder::where('sub_order_status', '!=', '7')->where('status', '!=', '0')->where('order_id', '=', $orderDetail->id)->count();
                    // if($order_due == 0){
                    //     $order = Order::findOrFail($orderDetail->id);
                    //     // $order->order_status = '7';
                    //     $order->save();
                    //     // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    //     // $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) on destination');
                    // }
                } else {

                    // return 1;

                    $shelfData = Shelf::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->where('assignd_hub_id', '=', $delivery_hub_id)->where('shelf_type', '=', 'delivery')->get();

                    if ($shelfData->count() != 0) {

                        foreach ($shelfData as $shelf) {
                            $shelfUsed = ShelfProduct::select(array(
                                        DB::raw("SUM(op.width) AS total_width"),
                                        DB::raw("SUM(op.height) AS total_height"),
                                        DB::raw("SUM(op.length) AS total_length"),
                                    ))
                                    ->join('order_product AS op', 'op.id', '=', 'shelf_products.product_id')
                                    ->where('shelf_products.status', '=', '1')
                                    ->where('shelf_products.shelf_id', '=', $shelf->id)
                                    ->first();
                            $available_width = $shelf->width - $shelfUsed->width;
                            $available_height = $shelf->height - $shelfUsed->height;
                            $available_length = $shelf->length - $shelfUsed->length;

                            if ($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length) {
                                $shelf_id = $shelf->id;
                                $message = $message . "$product->product_unique_id: Please keep the product on " . $shelf->shelf_title . "\n";
                                break;
                            } else {
                                $shelf_id = 0;
                                $message = $message . "$product->product_unique_id: Dedicated rack hasn't enough space. Please use defult rack" . "\n";
                            }
                        }
                    } else {
                        $shelf_id = 0;
                        $message = $message . "$product->product_unique_id: No Shelf defined for this delivery hub." . "\n";
                    }

                    $orderDetail = Order::findOrFail($product->order_id);
                    foreach ($orderDetail->suborders as $row) {

                        $verify_suborder = OrderProduct::where('sub_order_id', '=', $row->id)->where('status', '!=', '0')->count();
                        if ($verify_suborder == 0) {
                            $sub_order = SubOrder::where('id', '=', $row->id)->delete();
                        } else {

                            $sub_order = SubOrder::findOrFail($row->id);
                            $sub_order->responsible_user_id = $request->responsible_user_id;
                            // $sub_order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('sub_order_id', '=', $row->id)->count();
                            // if($sub_order_due == 0){
                            // $sub_order->sub_order_status = '5';
                            $sub_order->destination_hub_id = $delivery_hub_id;
                            $sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order->id)->orderBy('id', 'asc')->first();
                            if ($sub_order_trip_map) {
                                $sub_order->next_hub_id = $sub_order_trip_map->hub_id;
                            } else {
                                $sub_order->next_hub_id = $delivery_hub_id;
                            }
                            $sub_order->source_hub_id = auth()->user()->reference_id;
                            $sub_order->current_hub_id = auth()->user()->reference_id;
                            // }
                            $sub_order->save();

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            // $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $delivery_hub_id, 'hubs', 'Decided destination for Sub-Order: '.$sub_order->unique_suborder_id);
                        }
                    }

                    // Insert product on rack
                    $shelf_product = new ShelfProduct();
                    $shelf_product->shelf_id = $shelf_id;
                    $shelf_product->product_id = $product->id;
                    $shelf_product->status = '1';
                    $shelf_product->created_by = auth()->user()->id;
                    $shelf_product->updated_by = auth()->user()->id;
                    $shelf_product->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    // $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $shelf_product->shelf_id, 'shelf_products', 'Product shelfed');
                    // Update Sub-Order Status
                    $this->suborderStatus($product->sub_order->id, '15');
                }
            }
        } else {
            $message = 'Data not found.';
        }

        Session::flash('message', $message);
        return redirect('/receive-prodcut');
    }

    public function receiveAndReject(Request $request) {
        if (is_array($request->unique_suborder_id)) {
            $subOders = SubOrder::whereIn('unique_suborder_id', $request->unique_suborder_id)->get();
        } else {
            $subOders = SubOrder::where('unique_suborder_id', $request->unique_suborder_id)->get();
        }
        foreach ($subOders as $subOder) {
            ConsignmentTask::whereSubOrderId($subOder->id)->update(['reconcile' => 1]);
            if($subOder->return == 0) {
                $this->suborderStatus($subOder->id, '6'); // pickup failed
            } else {
                $this->suborderStatus($subOder->id, '49'); // post delivery order cancel for quality issue
                
                $rider = ConsignmentTask::whereSubOrderId($subOder->id)->whereTaskTypeId(5)->orderBy('id','desc')->first();
                $this->fcm_task_req($subOder->id, 1, $rider->rider_id);
                $subOder->return = 0; // post delivery order return to buyer
                $subOder->save();
            }
        }
        Session::flash('message', "Sub Ordre pickup faild.");
        return redirect('/receive-prodcut');
    }

}
