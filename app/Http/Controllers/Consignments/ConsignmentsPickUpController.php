<?php

namespace App\Http\Controllers\Consignments;

use App\ConsignmentCommon;
use App\ConsignmentTask;
use App\Hub;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use DB;
use PDF;
use App\OrderProduct;
use App\Consignment;
use App\PickingTask;
use App\Order;
use App\SubOrder;
use App\ReturnedProduct;
use Validator;
use App\Http\Traits\LogsTrait;
use Session;
use Illuminate\Support\Facades\Redirect;

use App\Store;
use App\Merchant;
use App\City;
use App\Zone;
use App\Trip;

class ConsignmentsPickUpController extends Controller
{

    use LogsTrait;

    //
    public function __construct()
    {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|inboundmanager');
    }

    public function delivery(Request $request)
    {
        $consignments = null;
        $pickupman = User::
        select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'), 'users.id')
            ->leftJoin('hubs', 'hubs.id', '=', 'users.reference_id')
            ->where('reference_id', '=', auth()->user()->reference_id)
            ->where('users.status', true)->where('users.user_type_id', '=', '8')->lists('name', 'users.id')->toArray();
        return view('consignments.delivery.index', compact('consignments', 'pickupman'));
    }

    public function pick_up(Request $request)
    {
        // Session::forget('pickup_cart');
        $query = SubOrder::select(
            'sub_orders.id AS suborder_id',
            'sub_orders.unique_suborder_id',
            'sub_orders.return',
            'orders.delivery_address1 AS delivery_address',
            'orders.delivery_name',
            'pickup_locations.title AS pickup_title',
            'pickup_locations.address1 AS pickup_address',
            'zones_p.name AS pickup_zone',
            'cities_p.name AS pickup_city',
            'states_p.name AS pickup_state',
            'zones_d.name AS delivery_zone',
            'cities_d.name AS delivery_city',
            'states_d.name AS delivery_state'
        )
            // ->distinct()
            // ->groupBy('sub_orders.id')
            ->where('sub_orders.status', 1)
            // ->whereIn('sub_orders.sub_order_status', [2, 6])
            ->whereIn('sub_orders.sub_order_status', [2])
            ->where('sub_orders.tm_picking_status', '=', 1)
            ->where('zones_p.hub_id', '=', auth()->user()->reference_id)
            ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->join('order_product AS op', 'op.sub_order_id', '=', 'sub_orders.id')
            ->join('stores', 'stores.id', '=', 'orders.store_id')
            ->join('pickup_locations', 'pickup_locations.id', '=', 'op.pickup_location_id')
            ->join('zones AS zones_p', 'zones_p.id', '=', 'pickup_locations.zone_id')
            ->join('cities AS cities_p', 'cities_p.id', '=', 'zones_p.city_id')
            ->join('states AS states_p', 'states_p.id', '=', 'cities_p.state_id')
            ->join('zones AS zones_d', 'zones_d.id', '=', 'orders.delivery_zone_id')
            ->join('cities AS cities_d', 'cities_d.id', '=', 'orders.delivery_city_id')
            ->join('states AS states_d', 'states_d.id', '=', 'orders.delivery_state_id');

        if ($request->has('order_id')) {
            $query->where('orders.unique_order_id', $request->order_id);
        }

        if ($request->has('merchant_order_id')) {
            $query->where('orders.merchant_order_id', $request->merchant_order_id);
        }

        if ($request->has('sub_order_id')) {
            $query->where('sub_orders.unique_suborder_id', $request->sub_order_id);
        }

        if ($request->has('sub_order_status')) {
            $query->where('sub_orders.sub_order_status', $request->sub_order_status);
        }

        if ($request->has('customer_mobile_no')) {
            $query->where('orders.delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if ($request->has('store_id')) {
            $query->where('orders.store_id', $request->store_id);
        }

        if ($request->has('merchant_id')) {
            $query->where('stores.merchant_id', $request->merchant_id);
        }

        if ($request->has('pickup_zone_id')) {
            $query->where('pickup_locations.zone_id', $request->pickup_zone_id);
        }

        if ($request->has('delivery_zone_id')) {
            $query->where('orders.delivery_zone_id', $request->delivery_zone_id);
        }

        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = '2017-03-21';
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query->WhereBetween('sub_orders.updated_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));

        $sub_orders = $query->orderBy('sub_orders.id', 'desc')->get();

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'), 'zones.id')
            ->leftJoin('cities', 'cities.id', '=', 'zones.city_id')->
            where('zones.status', true)->lists('name', 'zones.id')->toArray();

        $pickupman = $deliveryman = User::select(DB::raw('CONCAT(users.name, " - ", vehicle_types.title) as name'), 'users.id')
                ->join('rider_references','users.id','=','rider_references.user_id')
                ->join('vehicle_types','users.transparent_mode','=','vehicle_types.id')
                ->where('users.status',1)->where('user_type_id', '=', '8')
                ->where('rider_references.reference_id', '=', auth()->user()->reference_id)
                ->where('users.online_status', 1)
                ->lists('name', 'id')->toArray();

        return view('consignments.pick-up.index', compact('sub_orders', 'pickupman', 'merchants', 'stores', 'zones'));
    }

    public function pick_up_submit(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'picker_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if (Session::has('pickup_cart') && count(Session::get('pickup_cart')) > 0) {

            foreach (Session::get('pickup_cart') as $unique_suborder_id) {

                $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

                $this->fcm_task_req($sub_order->id, 1, $request->picker_id);

            }

            Session::forget('pickup_cart');

            Session::flash('message', "Tasks requested to rider.");
            
            return Redirect::back();

        } else {
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

//         if (Session::has('pickup_cart') && count(Session::get('pickup_cart')) > 0) {
//             try {
//                 DB::beginTransaction();
//                 $consignments = ConsignmentCommon::where('status', '>', 0)->where('status', '<', 3)->whereDate('created_at', '=', date('Y-m-d'))->where('rider_id', $request->picker_id)->first();
//                 if (!$consignments) {
//                     $consignments = new ConsignmentCommon();
//                     $temp = "CL" . time() . rand(10, 99);
//                     $consignments->consignment_unique_id = $temp;
//                     $consignments->rider_id = $request->picker_id;
//                     $consignments->hub_id = auth()->user()->reference_id;
//                     $consignments->status = 1;
//                     $consignments->created_by = auth()->user()->id;
//                     $consignments->updated_by = auth()->user()->id;
//                     $consignments->save();
//                 }

// //                $total_amount_to_collect = 0;
//                 $total_qty = 0;
//                 foreach (Session::get('pickup_cart') as $unique_suborder_id) {
//                     $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();
//                     $temp_amount_to_collect = OrderProduct::where('status', '!=', 0)->where('sub_order_id', $sub_order->id)->get();

//                     if (count($temp_amount_to_collect) > 0 and !is_null($temp_amount_to_collect)) {
//                         foreach ($temp_amount_to_collect as $x) {
// //                            $total_amount_to_collect = $total_amount_to_collect + $x->total_payable_amount;
//                             $total_qty = $total_qty + $x->quantity;
//                         }
//                     }

//                     // Update Sub-Order
// //                    $suborderUp = SubOrder::where('unique_suborder_id', $unique_suborder_id)->first();
// //                    $suborderUp->delivery_assigned_by = auth()->user()->id;
// //                    $suborderUp->deliveryman_id = $request->picker_id; //need clarification
// //                    $suborderUp->delivery_status = '0';
// //                    $suborderUp->save();

//                     $products = OrderProduct::where('sub_order_id', $sub_order->id)->first();
//                     $products->updated_by = auth()->user()->id;
//                     $products->picker_assign_by = auth()->user()->id;
//                     $products->picker_id = $request->picker_id;
//                     $products->save();

//                     // Update Sub-Order Status
//                     $this->suborderStatus($sub_order->id, '3');

//                     $hub = Hub::findOrFail(auth()->user()->reference_id);

//                     /**consignment id wise task find**/
// //                    $temp_check_d_task = ConsignmentTask::where('consignment_id', $consignments->id)->where('sub_order_id', $sub_order->id)->first();
// //                    if ($temp_check_d_task) {
// //                        $temp_check_d_task->rider_id = $request->picker_id;
// //                        $temp_check_d_task->consignment_id = $consignments->id;
// //                        $temp_check_d_task->task_type_id = 1; //picking
// //                        $temp_check_d_task->start_lat = $products->pickup_location->latitude;
// //                        $temp_check_d_task->start_long = $products->pickup_location->longitude;
// //                        $temp_check_d_task->end_lat = $hub->latitude;
// //                        $temp_check_d_task->end_long = $hub->longitude;
// //                        $temp_check_d_task->quantity = $total_qty;
// ////                        $temp_check_d_task->amount = $total_amount_to_collect;
// //                        $temp_check_d_task->save();
// //                    } else {
//                         $ctask = new ConsignmentTask();
//                         $ctask->rider_id = $request->picker_id;
//                         $ctask->sub_order_id = $sub_order->id;
//                         $ctask->consignment_id = $consignments->id;
//                         $ctask->task_type_id = 1; //picking
//                         $ctask->start_lat = $products->pickup_location->latitude;
//                         $ctask->start_long = $products->pickup_location->longitude;
//                         $ctask->end_lat = $hub->latitude;
//                         $ctask->end_long = $hub->longitude;
//                         $ctask->quantity = $total_qty;
//                         $ctask->amount = 0;
// //                        $ctask->otp = rand(100000, 999999);
//                         $ctask->otp = 123456; // for testing
//                         $ctask->save();
// //                    }

// //                    $total_amount_to_collect = 0;
//                     $total_qty = 0;
//                 }

//                 DB::commit();

//                 Session::forget('pickup_cart');

//                 Session::flash('message', "Consignment added successfully with $consignments->consignment_unique_id unique id.");
//                 return redirect('v2consignment');

//             } catch (Exception $e) {
//                 DB::rollback();

//                 Session::flash('message', "Failed to create the consignment");
//                 return redirect('v2consignment');
//             }

//         } else {
//             Session::flash('message', "Cart is empty");
//             return Redirect::back();
//         }

    }

    public function pick_up_submit_old(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'picker_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if (Session::has('pickup_cart') && count(Session::get('pickup_cart')) > 0) {

            foreach (Session::get('pickup_cart') as $unique_suborder_id) {

                $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

                $this->fcm_task_req($sub_order->id);

            }

            Session::forget('pickup_cart');

            Session::flash('message', "Tasks requested to rider.");
            
            return redirect('v2consignment');

        } else {
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

        // $rider_on_the_way = Consignment::where('status', '>', 0)->where('status', '<', 3)->where('rider_id', $request->picker_id)->count();
        // if ($rider_on_the_way > 0) {
        //     Session::flash('message', "Rider is busy with another consignment.");
        //     return Redirect::back();
        // }

        // $driver_on_the_way = Trip::where('trip_status', '>', 0)->where('trip_status', '<', 3)->where('driver_id', $request->picker_id)->count();
        // if ($driver_on_the_way > 0) {
        //     Session::flash('message', "Rider is busy with a trip.");
        //     return Redirect::back();
        // }

        // if (Session::has('pickup_cart') && count(Session::get('pickup_cart')) > 0) {

        //     try {
        //         DB::beginTransaction();

        //         $total_qty = 0;
        //         if (count(Session::get('pickup_cart')) != 0) {
        //             foreach (Session::get('pickup_cart') as $unique_suborder_id) {
        //                 $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();
        //                 $temp_qty = OrderProduct::where('status', '!=', 0)->where('sub_order_id', $sub_order->id)->first();

        //                 if (count($temp_qty) > 0 and !is_null($temp_qty)) {
        //                     $total_qty = $total_qty + $temp_qty->quantity;
        //                 }
        //             }
        //         }

        //         $consignments = new Consignment();
        //         $temp = "CL" . time() . rand(10, 99);
        //         $consignments->consignment_unique_id = $temp;
        //         $consignments->rider_id = $request->picker_id;
        //         $consignments->type = 'picking';
        //         $consignments->status = 1;
        //         $consignments->quantity = $total_qty;
        //         $consignments->hub_id = auth()->user()->reference_id;
        //         $consignments->created_by = auth()->user()->id;
        //         $consignments->updated_by = auth()->user()->id;
        //         $consignments->save();

        //         foreach (Session::get('pickup_cart') as $unique_suborder_id) {
        //             $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

        //             $products = OrderProduct::where('sub_order_id', $sub_order->id)->first();
        //             $products->updated_by = auth()->user()->id;
        //             $products->picker_assign_by = auth()->user()->id;
        //             $products->picker_id = $request->picker_id;
        //             $products->save();

        //             // Update Sub-Order Status
        //             $this->suborderStatus($products->sub_order_id, '3');

        //             PickingTask::where('product_unique_id', $products->product_unique_id)->delete();

        //             $ptask = new PickingTask();
        //             $ptask->product_unique_id = $products->product_unique_id;
        //             $ptask->picker_id = $request->picker_id;
        //             $ptask->consignment_id = $consignments->id;
        //             $ptask->save();
        //         }

        //         DB::commit();

        //         Session::forget('pickup_cart');

        //         Session::flash('message', "Consignment addedd successfully with $temp unique id.");
        //         return redirect('v2consignment');

        //     } catch (Exception $e) {
        //         DB::rollback();

        //         Session::flash('message', "Failed to create the consignment");
        //         return redirect('v2consignment');
        //     }

        // } else {
        //     Session::flash('message', "Cart is empty");
        //     return Redirect::back();
        // }

    }

}
