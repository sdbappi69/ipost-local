<?php

namespace App\Http\Controllers\Report;

use App\AgingDelivery;
use App\AgingPickup;
use App\AgingReturn;
use App\AgingTrip;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Hub;
use App\Merchant;
use App\OrderLog;
use App\Store;
use App\User;
use DB;
use Excel;
use Illuminate\Http\Request;

use App\ExtractLog;

class AgingController extends Controller
{
    public function pickup(Request $request){
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
        
        $query = AgingPickup::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        return view('reports.aging.pickup', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

    }

    public function pickupexport(Request $request, $type){
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

        $query = AgingPickup::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Pickup Export';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('aging_pickup_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'Pickup Requested', 'Picking Attempt', 'Pickup Hub', 'Picked date', 'Delivery Hub', 'Aging', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){

                    $datasheet[$i] = array(
                        $i,
                        (string)$order_log->order_id,
                        (string)$order_log->sub_order_id,
                        (string)$order_log->merchant_order_id,
                        (string)$order_log->merchant,
                        (string)$order_log->store,
                        (string)$order_log->pickup_requested,
                        (string)$order_log->pickup_attempt,
                        (string)$order_log->pickup_hub,
                        (string)$order_log->picked_date,
                        (string)$order_log->delivery_hub,
                        (string)$order_log->aging,
                        (string)$order_log->current_status
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

        $query = AgingDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        return view('reports.aging.delivery', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

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

        $query = AgingDelivery::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Delivery Export';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('aging_delivery_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'Pickup Hub', 'Racked at destination hub', 'Delivery Attempt', 'Delivery date', 'Delivery Hub', 'Aging', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){
                    $datasheet[$i] = array(
                        $i,
                        $order_log->order_id,
                        $order_log->sub_order_id,
                        $order_log->merchant_order_id,
                        $order_log->merchant,
                        $order_log->store,
                        $order_log->pickup_hub,
                        $order_log->racked_at_destination_hub,
                        $order_log->delivery_attempt,
                        $order_log->delivery_date,
                        $order_log->delivery_hub,
                        $order_log->aging,
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


    public function returnaging(Request $request){
        if($request->has('start_date')){
            $start_date = $request->start_date;
        } else{
            $start_date = '2017-03-21';
        }

        if($request->has('end_date')){
            $end_date = $request->end_date;
        } else{
            $end_date = date('Y-m-d');
        }

        $query = AgingReturn::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        return view('reports.aging.return', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

    }

    public function returnexport(Request $request, $type){
        if($request->has('start_date')){
            $start_date = $request->start_date;
        } else{
            $start_date = '2017-03-21';
        }

        if($request->has('end_date')){
            $end_date = $request->end_date;
        } else{
            $end_date = date('Y-m-d');
        }

        $query = AgingReturn::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Return Export';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('aging_return_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'Pickup Hub', 'Racked at destination hub', 'Delivery Attempt', 'Return date', 'Delivery Hub', 'Aging', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){
                    $datasheet[$i] = array(
                        $i,
                        $order_log->order_id,
                        $order_log->sub_order_id,
                        $order_log->merchant_order_id,
                        $order_log->merchant,
                        $order_log->store,
                        $order_log->pickup_hub,
                        $order_log->racked_at_destination_hub,
                        $order_log->delivery_attempt,
                        $order_log->return_date,
                        $order_log->delivery_hub,
                        $order_log->aging,
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


    public function trip(Request $request){
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

        $query = AgingTrip::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        return view('reports.aging.trip', compact('order_logs', 'stores', 'merchants', 'pickupman', 'hubs'));

    }


    public function tripexport(Request $request, $type){
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

        $query = AgingTrip::WhereBetween('order_created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));

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

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Trip Export';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('aging_trip_'.time(), function($excel) use ($order_logs) {
            $excel->sheet('orders', function($sheet) use ($order_logs)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N', 'Order Id', 'Sub-Order Id', 'Merchant Order Id', 'Merchant', 'Store', 'Pickup Hub', 'Product racked at Pickup hub', 'Product racked at Delivery hub', 'Delivery Hub', 'Aging', 'Current Status');
                $i=1;
                foreach($order_logs as $order_log){
                        $datasheet[$i] = array(
                            $i,
                            $order_log->order_id,
                            $order_log->sub_order_id,
                            $order_log->merchant_order_id,
                            $order_log->merchant,
                            $order_log->store,
                            $order_log->pickup_hub,
                            $order_log->product_racked_at_pickup_hub,
                            $order_log->product_racked_at_delivery_hub,
                            $order_log->delivery_hub,
                            $order_log->aging,
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
