<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Hub;
use App\Merchant;
use App\OrderLog;
use App\Store;
use App\TatDelivery;
use App\User;
use DB;
use Excel;
use Illuminate\Http\Request;

class TatController extends Controller
{

    public function delivery(Request $request){
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

        $query = TatDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('order_id')){
            $query->where('order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_order_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('customer_mobile_no', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('store', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('merchant', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_hub_id')){
            $query->whereIn('pickup_hub', $request->pickup_hub_id);
        }

        if($request->has('delivery_hub_id')){
            $query->whereIn('delivery_hub', $request->delivery_hub_id);
        }

        $order_logs = $query->paginate(10);

		// Resource
        $stores = Store::whereStatus(true)->lists('store_id', 'store_id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
        $pickupman = User::select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
        ->leftJoin('hubs','hubs.id','=','users.reference_id')
        ->where('users.status',true)
        ->where('users.user_type_id', '=', '8')
        ->lists('name','users.id')
        ->toArray();

        $hubs = Hub::select('title','id')
        ->where('hubs.status',true)
        ->lists('title','title')
        ->toArray();

        return view('reports.tat.delivery', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

    }

    public function deliveryexport(Request $request, $type){
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

        $query = TatDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('order_id')){
            $query->where('order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_order_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('customer_mobile_no', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('store', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('merchant', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_hub_id')){
            $query->whereIn('pickup_hub', $request->pickup_hub_id);
        }

        if($request->has('delivery_hub_id')){
            $query->whereIn('delivery_hub', $request->delivery_hub_id);
        }

        $order_logs = $query->get();

        return Excel::create('tat_delivery_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'TAT', 'Order created', 'Picked at', 'Delivered at', 'Picking Attempt', 'Delivery Attempt', 'Product', 'Quantity', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){
                 $datasheet[$i] = array(
                    $i,
                    $order_log->order_id,
                    $order_log->sub_order_id,
                    $order_log->merchant_order_id,
                    $order_log->merchant,
                    $order_log->store,
                    $order_log->tat,
                    $order_log->order_created,
                    $order_log->picked_at,
                    $order_log->delivered_at,
                    $order_log->picking_attempt,
                    $order_log->delivery_attempt,
                    $order_log->product,
                    $order_log->quantity,
                    $order_log->current_status
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

    public function returnorder(Request $request){
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

        $query = TatDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('order_id')){
            $query->where('order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_order_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('customer_mobile_no', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('store', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('merchant', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_hub_id')){
            $query->whereIn('pickup_hub', $request->pickup_hub_id);
        }

        if($request->has('delivery_hub_id')){
            $query->whereIn('delivery_hub', $request->delivery_hub_id);
        }

        $order_logs = $query->paginate(10);

		// Resource
        $stores = Store::whereStatus(true)->lists('store_id', 'store_id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
        $pickupman = User::select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
        ->leftJoin('hubs','hubs.id','=','users.reference_id')
        ->where('users.status',true)
        ->where('users.user_type_id', '=', '8')
        ->lists('name','users.id')
        ->toArray();

        $hubs = Hub::select('title','id')
        ->where('hubs.status',true)
        ->lists('title','title')
        ->toArray();

        return view('reports.tat.returnorder', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

    }

    public function returnorderexport(Request $request, $type){
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

        $query = TatDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('order_id')){
            $query->where('order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_order_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('customer_mobile_no', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('store', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('merchant', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_hub_id')){
            $query->whereIn('pickup_hub', $request->pickup_hub_id);
        }

        if($request->has('delivery_hub_id')){
            $query->whereIn('delivery_hub', $request->delivery_hub_id);
        }

        $order_logs = $query->get();

        return Excel::create('tat_return_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'TAT', 'Order created', 'Picked at', 'Returned at', 'Picking Attempt', 'Return Attempt', 'Product', 'Quantity', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){
                    $datasheet[$i] = array(
                        $i,
                        $order_log->order_id,
                        $order_log->sub_order_id,
                        $order_log->merchant_order_id,
                        $order_log->merchant,
                        $order_log->store,
                        $order_log->tat,
                        $order_log->order_created,
                        $order_log->picked_at,
                        $order_log->returned_at,
                        $order_log->picking_attempt,
                        $order_log->return_attempt,
                        $order_log->product,
                        $order_log->quantity,
                        $order_log->current_status
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

    public function tit(Request $request){
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

        $query = TatDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('order_id')){
            $query->where('order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_order_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('customer_mobile_no', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('store', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('merchant', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_hub_id')){
            $query->whereIn('pickup_hub', $request->pickup_hub_id);
        }

        if($request->has('delivery_hub_id')){
            $query->whereIn('delivery_hub', $request->delivery_hub_id);
        }

        $order_logs = $query->paginate(10);

		// Resource
        $stores = Store::whereStatus(true)->lists('store_id', 'store_id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
        $pickupman = User::select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
        ->leftJoin('hubs','hubs.id','=','users.reference_id')
        ->where('users.status',true)
        ->where('users.user_type_id', '=', '8')
        ->lists('name','users.id')
        ->toArray();

        $hubs = Hub::select('title','id')
        ->where('hubs.status',true)
        ->lists('title','title')
        ->toArray();

        return view('reports.tat.tit', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

    }

    public function titexport(Request $request, $type){
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

        $query = TatDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        if($request->has('order_id')){
            $query->where('order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('sub_order_id', $request->sub_order_id);
        }

        if($request->has('customer_mobile_no')){
            $query->where('customer_mobile_no', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->whereIn('store', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->whereIn('merchant', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->whereIn('picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->whereIn('deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_hub_id')){
            $query->whereIn('pickup_hub', $request->pickup_hub_id);
        }

        if($request->has('delivery_hub_id')){
            $query->whereIn('delivery_hub', $request->delivery_hub_id);
        }

        $order_logs = $query->get();

        return Excel::create('tat_trip_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'TAT', 'Order created', 'Picked at', 'Triped at', 'Picking Attempt', 'Product', 'Quantity', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){
                    $datasheet[$i] = array(
                        $i,
                        $order_log->order_id,
                        $order_log->sub_order_id,
                        $order_log->merchant_order_id,
                        $order_log->merchant,
                        $order_log->store,
                        $order_log->tat,
                        $order_log->order_created,
                        $order_log->picked_at,
                        $order_log->triped_at,
                        $order_log->picking_attempt,
                        $order_log->product,
                        $order_log->quantity,
                        $order_log->current_status
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

}
