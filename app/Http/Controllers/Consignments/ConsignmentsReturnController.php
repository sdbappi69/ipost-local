<?php

namespace App\Http\Controllers\Consignments;

use App\ConsignmentCommon;
use App\ConsignmentTask;
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
use App\Hub;
use App\Trip;

class ConsignmentsReturnController extends Controller
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

    public function return_con(Request $request)
    {
        // Session::forget('return_cart');
        $query = SubOrder::select(
            'sub_orders.id AS suborder_id',
            'sub_orders.unique_suborder_id',
            'hubs_s.title AS return_hub_name',
            'pickup_locations.title AS return_title',
            'pickup_locations.address1 AS return_address',
            'zones_p.name AS return_zone',
            'cities_p.name AS return_city',
            'states_p.name AS return_state'
        )
            ->distinct()
            ->where('sub_orders.status', 1)
            ->whereIn('sub_orders.sub_order_status', [26, 27])
            ->where('sub_orders.return', '=', 1)
            ->where('sub_orders.tm_delivery_status', '=', 1)
            ->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id)
            ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
            ->leftJoin('hubs AS hubs_s', 'hubs_s.id', '=', 'sub_orders.source_hub_id')
            ->leftJoin('order_product AS op', 'op.sub_order_id', '=', 'sub_orders.id')
            ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'op.pickup_location_id')
            ->leftJoin('zones AS zones_p', 'zones_p.id', '=', 'pickup_locations.zone_id')
            ->leftJoin('cities AS cities_p', 'cities_p.id', '=', 'zones_p.city_id')
            ->leftJoin('states AS states_p', 'states_p.id', '=', 'cities_p.state_id');

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

        if ($request->has('return_hub_id')) {
            $query->where('sub_orders.source_hub_id', $request->return_hub_id);
        }

        if ($request->has('delivery_hub_id')) {
            $query->where('sub_orders.destination_hub_id', $request->delivery_hub_id);
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

        $consignments = Consignment::select(DB::raw('CONCAT(consignments.consignment_unique_id, " - ",users.name) AS name'), 'consignments.id')->leftJoin('users', 'users.id', '=', 'consignments.rider_id')->where('consignments.status', 1)->where('consignments.type', 'picking')->where('consignments.hub_id', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'), 'zones.id')
            ->leftJoin('cities', 'cities.id', '=', 'zones.city_id')->
            where('zones.status', true)->lists('name', 'zones.id')->toArray();

        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

        $pickupman = $deliveryman = User::join('rider_references','users.id','=','rider_references.user_id')
                ->whereStatus(true)->where('user_type_id', '=', '8')
                ->where('rider_references.reference_id', '=', auth()->user()->reference_id)
                ->where('users.online_status', 1)
                ->lists('name', 'id')->toArray();

        return view('consignments.return.index', compact('sub_orders', 'pickupman', 'merchants', 'stores', 'zones', 'consignments', 'hubs'));
    }

    public function return_submit(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'picker_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if (Session::has('return_cart') && count(Session::get('return_cart')) > 0) {

            foreach (Session::get('return_cart') as $unique_suborder_id) {

                $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

                $this->fcm_task_req($sub_order->id, 1, $request->picker_id);

            }

            Session::forget('return_cart');

            Session::flash('message', "Tasks requested to rider.");
            return Redirect::back();
        }else{
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

//         if (Session::has('return_cart') && count(Session::get('return_cart')) > 0) {
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
//                 foreach (Session::get('return_cart') as $unique_suborder_id) {
//                     //dd($unique_suborder_id);
//                     $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();
//                     $temp_amount_to_collect = OrderProduct::where('status', '!=', 0)->where('sub_order_id', $sub_order->id)->get();
//                     //dd($temp_amount_to_collect);
//                     if (count($temp_amount_to_collect) > 0 and !is_null($temp_amount_to_collect)) {
//                         foreach ($temp_amount_to_collect as $x) {
//                             $total_qty = $total_qty + $x->quantity;
//                         }
//                     }

//                     $products = OrderProduct::where('sub_order_id', $sub_order->id)->first();
//                     $products->updated_by = auth()->user()->id;
//                     $products->picker_assign_by = auth()->user()->id;
//                     $products->picker_id = $consignments->rider_id;
//                     $products->save();

//                     // Update Sub-Order Status
//                     $this->suborderStatus($sub_order->id, '35');

//                     $hub = Hub::findOrFail(auth()->user()->reference_id);

//                     $ctask = new ConsignmentTask();
//                     $ctask->rider_id = $request->picker_id;
//                     $ctask->sub_order_id = $sub_order->id;
//                     $ctask->consignment_id = $consignments->id;
//                     $ctask->task_type_id = 4; //return
//                     $ctask->start_lat = $hub->latitude;
//                     $ctask->start_long = $hub->longitude;
//                     $ctask->end_lat = $products->pickup_location->latitude;
//                     $ctask->end_long = $products->pickup_location->longitude;
//                     $ctask->quantity = $total_qty;
//                     $ctask->amount = 0;
// //                    $ctask->otp = rand(100000, 999999);
//                         $ctask->otp = 123456; // for testing
//                     $ctask->save();
// //                    }

// //                    $total_amount_to_collect = 0;
//                     $total_qty = 0;
//                 }

//                 DB::commit();

//                 Session::forget('return_cart');

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

    public function return_submit_old(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'picker_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $rider_on_the_way = Consignment::where('status', '>', 0)->where('status', '<', 3)->where('rider_id', $request->picker_id)->count();
        if ($rider_on_the_way > 0) {
            Session::flash('message', "Rider is busy with another consignment.");
            return Redirect::back();
        }

        $driver_on_the_way = Trip::where('trip_status', '>', 0)->where('trip_status', '<', 3)->where('driver_id', $request->picker_id)->count();
        if ($driver_on_the_way > 0) {
            Session::flash('message', "Rider is busy with a trip.");
            return Redirect::back();
        }

        if (Session::has('return_cart') && count(Session::get('return_cart')) > 0) {

            try {
                DB::beginTransaction();

                $consignments = ConsignmentCommon::where('status', '>', 0)->where('status', '<', 3)->where('rider_id', $request->picker_id)->first();
                if (!$consignments) {
                    $consignments = new ConsignmentCommon();
                    $temp = "CL" . time() . rand(10, 99);
                    $consignments->consignment_unique_id = $temp;
                    $consignments->rider_id = $request->picker_id;
                    $consignments->hub_id = auth()->user()->reference_id;
                    $consignments->status = 1;
                    $consignments->created_by = auth()->user()->id;
                    $consignments->updated_by = auth()->user()->id;
                    $consignments->save();
                }

                $total_qty = 0;
                $total_amount_to_collect = 0;
                if (count(Session::get('return_cart')) != 0) {
                    foreach (Session::get('return_cart') as $unique_suborder_id) {
                        $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();
                        $temp_qty = OrderProduct::where('status', '!=', 0)->where('sub_order_id', $sub_order->id)->first();

                        if (count($temp_qty) > 0 and !is_null($temp_qty)) {
                            $total_qty = $total_qty + $temp_qty->quantity;
                        }
                    }
                }

                foreach (Session::get('return_cart') as $unique_suborder_id) {
                    $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

                    $products = OrderProduct::where('sub_order_id', $sub_order->id)->first();
                    $products->updated_by = auth()->user()->id;
                    $products->picker_assign_by = auth()->user()->id;
                    $products->picker_id = $consignments->rider_id;
                    $products->save();

                    // PickingTask::where('product_unique_id', $products->product_unique_id)->delete();

                    // $ptask = new PickingTask();
                    // $ptask->product_unique_id = $products->product_unique_id;
                    // $ptask->picker_id = $request->picker_id;
                    // $ptask->consignment_id = $consignments->id;
                    // $ptask->type = 'Return';
                    // $ptask->otp = 123456; // for testing
                    // $ptask->save();

                    $ctask = new ConsignmentTask();
                    $ctask->rider_id = $request->deliveryman_id;
                    $ctask->sub_order_id = $suborderUp->id;
                    $ctask->consignment_id = $consignments->id;
                    $ctask->task_type_id = 4; //delivery
                    $ctask->start_lat = $hub->latitude;
                    $ctask->start_long = $hub->longitude;
                    $ctask->end_lat = $order->delivery_latitude;
                    $ctask->end_long = $order->delivery_longitude;
                    $ctask->quantity = $total_qty;
                    $ctask->amount = $total_amount_to_collect;
//                        $ctask->otp = rand(100000, 999999);
                    $ctask->otp = 123456; // for testing
                    $ctask->save();

                    // Update Sub-Order Status
                    $this->suborderStatus($products->sub_order_id, '35');

                    // Send SMS
                    $sms = "Dear Seller, Your return product is on the way. Your security code is: ".$ctask->otp;
                    $this->sendCustomMessage($products->pickup_location->msisdn, $sms, $products->sub_order->id);
                }

                DB::commit();

                Session::forget('return_cart');

                Session::flash('message', "Consignment addedd successfully with $temp unique id.");
                return redirect('v2consignment');

            } catch (Exception $e) {
                DB::rollback();

                Session::flash('message', "Failed to create the consignment");
                return redirect('v2consignment');
            }

        } else {
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

    }

    public function return_submit_load(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'consignment_id' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if (Session::has('return_cart') && count(Session::get('return_cart')) > 0) {

            $consignments = Consignment::findOrFail($request->consignment_id);
            $total_qty = $consignments->return_quantity;
            if (count(Session::get('return_cart')) != 0) {
                foreach (Session::get('return_cart') as $unique_suborder_id) {
                    $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();
                    $temp_qty = OrderProduct::where('status', '!=', 0)->where('sub_order_id', $sub_order->id)->first();

                    if (count($temp_qty) > 0 and !is_null($temp_qty)) {
                        $total_qty = $total_qty + $temp_qty->quantity;
                    }
                }
            }

            $temp = $consignments->consignment_unique_id;
            // $consignments->consignment_unique_id = $temp;
            // $consignments->rider_id = $request->picker_id;
            // $consignments->type = 'picking';
            // $consignments->status = 1;
            $consignments->return_quantity = $total_qty;
            // $consignments->hub_id = auth()->user()->reference_id;
            // $consignments->created_by = auth()->user()->id;
            // $consignments->updated_by = auth()->user()->id;
            $consignments->save();

            foreach (Session::get('return_cart') as $unique_suborder_id) {
                $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

                $products = OrderProduct::where('sub_order_id', $sub_order->id)->first();
                $products->updated_by = auth()->user()->id;
                $products->picker_assign_by = auth()->user()->id;
                $products->picker_id = $request->picker_id;
                $products->save();

                // Update Sub-Order Status
                $this->suborderStatus($products->sub_order_id, '35');

                $ptask = new PickingTask();
                $ptask->product_unique_id = $products->product_unique_id;
                $ptask->picker_id = $request->picker_id;
                $ptask->consignment_id = $consignments->id;
                $ptask->type = 'Return';
                $ptask->save();
            }

            Session::forget('return_cart');

            Session::flash('message', "Consignment addedd successfully with $temp unique id.");
            return redirect('v2consignment');

        } else {
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

    }

}
