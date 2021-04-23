<?php

namespace App\Http\Controllers\Consignments;

use App\ConsignmentTask;
use App\Hub;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use DB;

use PDF;
use App\OrderProduct;
use App\ConsignmentCommon;
use App\Del;
use App\Order;
use Validator;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\SmsApi;

use App\SubOrder;

use App\DeliveryTask;
use Illuminate\Support\Facades\Redirect;
use Session;

use App\Store;
use App\Merchant;
use App\City;
use App\Zone;
use App\Trip;

class ConsignmentsDeliveryController extends Controller
{
    use LogsTrait;
    use SmsApi;

    //
    public function __construct()
    {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|inboundmanager|vehiclemanager');
    }

    public function delivery(Request $request)
    {
        // Session::forget('delivery_cart');
        //echo  auth()->user()->reference_id; die();
        $query = SubOrder::select(
            'sub_orders.id AS suborder_id',
            'sub_orders.unique_suborder_id',
            'orders.delivery_address1 AS delivery_address',
            'zones_d.name AS delivery_zone',
            'cities_d.name AS delivery_city',
            'states_d.name AS delivery_state'
        )
            ->distinct()
            ->where('sub_orders.status', 1)
            ->where('sub_orders.tm_delivery_status', 1)
            ->where('sub_orders.return', 0)
            ->whereIn('sub_orders.sub_order_status', [26, 34])
            ->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id)
            ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->leftJoin('order_product AS op', 'op.sub_order_id', '=', 'sub_orders.id')
            ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
            ->leftJoin('pickup_locations', 'pickup_locations.id', '=', 'op.pickup_location_id')
            ->leftJoin('zones AS zones_d', 'zones_d.id', '=', 'orders.delivery_zone_id')
            ->leftJoin('cities AS cities_d', 'cities_d.id', '=', 'orders.delivery_city_id')
            ->leftJoin('states AS states_d', 'states_d.id', '=', 'orders.delivery_state_id');

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

        if ($request->has('pickup_man_id')) {
            $query->where('order_product.picker_id', $request->pickup_man_id);
        }

        if ($request->has('delivary_man_id')) {
            $query->where('sub_orders.deliveryman_id', $request->delivary_man_id);
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


        $deliveryman = User::join('rider_references','users.id','=','rider_references.user_id')
                ->whereStatus(true)->where('user_type_id', '=', '8')
                ->where('rider_references.reference_id', '=', auth()->user()->reference_id)
                ->where('users.online_status', 1)
                ->lists('name', 'id')->toArray();

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'), 'zones.id')
            ->leftJoin('cities', 'cities.id', '=', 'zones.city_id')->
            where('zones.status', true)->lists('name', 'zones.id')->toArray();

        return view('consignments.delivery.index', compact('sub_orders', 'deliveryman', 'merchants', 'stores', 'zones'));
    }

    public function delivery_submit(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'deliveryman_id' => 'required',
            // 'order_id' => 'required',
            // 'sub_order_id' => 'required',
        ]);

        if ($validation->fails()) {
            //echo "a"; die();
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if (Session::has('delivery_cart') && count(Session::get('delivery_cart')) > 0) {

            foreach (Session::get('delivery_cart') as $unique_suborder_id) {

                $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();

                $this->fcm_task_req($sub_order->id, 1, $request->deliveryman_id);

            }

            Session::forget('delivery_cart');

            Session::flash('message', "Tasks requested to rider.");

            return Redirect::back();

            # Old way of consignment
//             try {
//                 DB::beginTransaction();
//                 $consignments = ConsignmentCommon::where('status', '>', 0)->where('status', '<', 3)->whereDate('created_at', '=', date('Y-m-d'))->where('rider_id', $request->deliveryman_id)->first();
//                 if (!$consignments) {
//                     $consignments = new ConsignmentCommon();
//                     $temp = "CL" . time() . rand(10, 99);
//                     $consignments->consignment_unique_id = $temp;
//                     $consignments->rider_id = $request->deliveryman_id;
//                     $consignments->hub_id = auth()->user()->reference_id;
//                     $consignments->status = 1;
//                     $consignments->created_by = auth()->user()->id;
//                     $consignments->updated_by = auth()->user()->id;
//                     $consignments->save();
//                 }

//                 $total_amount_to_collect = 0;
//                 $total_qty = 0;
//                 foreach (Session::get('delivery_cart') as $unique_suborder_id) {
//                     //dd($unique_suborder_id);
//                     $sub_order = SubOrder::whereStatus(true)->where('unique_suborder_id', $unique_suborder_id)->first();
//                     $temp_amount_to_collect = OrderProduct::where('status', '!=', 0)->where('sub_order_id', $sub_order->id)->get();
//                     //dd($temp_amount_to_collect);
//                     if (count($temp_amount_to_collect) > 0 and !is_null($temp_amount_to_collect)) {
//                         foreach ($temp_amount_to_collect as $x) {
//                             $total_amount_to_collect = $total_amount_to_collect + $x->total_payable_amount;
//                             $total_qty = $total_qty + $x->quantity;
//                         }
//                     }

//                     // Update Sub-Order
//                     $suborderUp = SubOrder::where('unique_suborder_id', $unique_suborder_id)->first();
//                     $suborderUp->delivery_assigned_by = auth()->user()->id;
//                     $suborderUp->deliveryman_id = $request->deliveryman_id;
//                     $suborderUp->delivery_status = '0';
//                     $suborderUp->save();

//                     $order = Order::findOrFail($sub_order->order_id);
//                     $hub = Hub::findOrFail(auth()->user()->reference_id);

//                     /**consignment id wise task find**/
//                         $ctask = new ConsignmentTask();
//                         $ctask->rider_id = $request->deliveryman_id;
//                         $ctask->sub_order_id = $suborderUp->id;
//                         $ctask->consignment_id = $consignments->id;
//                         $ctask->task_type_id = 2; //delivery
//                         $ctask->start_lat = $hub->latitude;
//                         $ctask->start_long = $hub->longitude;
//                         $ctask->end_lat = $order->delivery_latitude;
//                         $ctask->end_long = $order->delivery_longitude;
//                         $ctask->quantity = $total_qty;
//                         $ctask->amount = $total_amount_to_collect;
// //                        $ctask->otp = rand(100000, 999999);
//                         $ctask->otp = 123456; // for testing
//                         $ctask->save();
// //                    }

//                         // Update Sub-Order Status
//                     if($consignments->status == 1){
//                         $this->suborderStatus($suborderUp->id, '28');
//                     }else{
//                         $this->suborderStatus($suborderUp->id, '29');

//                         // Send SMS
//                         $sms = "Dear ".$suborderUp->order->delivery_name.", Your product is on the way. Your security code is: ".$ctask->otp;
//                         $this->sendCustomMessage($suborderUp->order->delivery_msisdn, $sms, $suborderUp->id);
//                     }

//                     $total_amount_to_collect = 0;
//                     $total_qty = 0;
//                 }

//                 DB::commit();

//                 Session::forget('delivery_cart');

//                 Session::flash('message', "Consignment added successfully with $consignments->consignment_unique_id unique id.");
//                 return redirect('v2consignment');

//             } catch (Exception $e) {
//                 DB::rollback();

//                 Session::flash('message', "Failed to create the consignment");
//                 return redirect('v2consignment');
//             }

        } else {
            Session::flash('message', "Cart is empty");
            return Redirect::back();
        }

    }
}
