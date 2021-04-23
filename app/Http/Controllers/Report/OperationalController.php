<?php

namespace App\Http\Controllers\Report;

use App\City;
use App\Country;
use App\DeliveryTask;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Hub;
use App\Merchant;
use App\Order;
use App\OrderLog;
use App\OrderProduct;
use App\PickingLocations;
use App\PickingTimeSlot;
use App\ProductCategory;
use App\State;
use App\Status;
use App\Store;
use App\SubOrder;
use App\User;
use App\Zone;
use App\ExtractLog;
use App\OperationSales;
use App\OperationPickupFailed;
use App\OperationDeliveryFailed;
use App\OperationReturnFailed;
use App\OperationIntransit;

use Auth;
use DB;
use Entrust;
use Excel;
use Illuminate\Http\Request;
use Redirect;
use Session;
use Validator;

class OperationalController extends Controller {

	public function sales(Request $request){
		$inputs = $request->all();
		if($request->has('from_date')){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->has('to_date')){
            $to_date = $request->to_date;
        }else{
            $to_date = date('Y-m-d');
        }
        
        $query = OperationSales::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
        if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
        
		$salesData = $query->paginate(10);

		$merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'title')->toArray();

        return view('reports.operation.sales', compact('salesData', 'inputs', 'from_date', 'to_date', 'merchants', 'hubs'));
	}

	public function salesexport(Request $request, $type) {
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		}else{
			$to_date = date('Y-m-d');
		}

		$query = OperationSales::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
        if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
        
		$salesData = $query->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Automated Sales Report';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('Automated_Sales_Report_'.time(), function($excel) use ($salesData) {
			$excel->sheet('orders', function($sheet) use ($salesData){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Merchant Order Id',
					'Merchant Name',
					'Pickup Hub',
					'Delivered Date',
					'Status',
					'Cash On Delivery',
					'Collected Amount',
					'Remarks'
				);

				$i=1;

				foreach($salesData as $sales){
					$datasheet[$i] = array(
						(string)$sales->order_id,
						(string)$sales->merchant_order_id,
						(string)$sales->merchant_name,
						(string)$sales->pickup_hub,
						(string)$sales->delivery_date,
						(string)$sales->current_status,
						(string)$sales->cash_on_delivery,
						(string)$sales->collected_amount,
						(string)$sales->remarks
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

	public function pickup(Request $request){
		$inputs = $request->all();
		if($request->has('from_date')){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->has('to_date')){
            $to_date = $request->to_date;
        }else{
            $to_date = date('Y-m-d');
        }
        
        $query = OperationPickupFailed::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
        
		$pickupFailedData = $query->paginate(10);

		$merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
		
        return view('reports.operation.pickup', compact('pickupFailedData', 'inputs', 'from_date', 'to_date', 'merchants'));
	}

	public function pickupexport(Request $request, $type) {
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		}else{
			$to_date = date('Y-m-d');
		}

		$query = OperationPickupFailed::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
        
		$pickupFailedData = $query->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Pickup Failed Report';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('Pickup_Failed_Report_'.time(), function($excel) use ($pickupFailedData) {
			$excel->sheet('orders', function($sheet) use ($pickupFailedData){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Order Created Date',
					'Merchant Name',
					'Merchant Id',
					'Status',
					'Failed Reason',
					'Remarks'
				);

				$i=1;

				foreach($pickupFailedData as $pickupFailed){
					$datasheet[$i] = array(
						(string)$pickupFailed->order_id,
						(string)$pickupFailed->order_created_at,
						(string)$pickupFailed->merchant_name,
						(string)$pickupFailed->merchant_order_id,
						(string)$pickupFailed->current_status,
						(string)$pickupFailed->failed_reason,
						(string)$pickupFailed->remarks
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
		$inputs = $request->all();
		if($request->has('from_date')){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->has('to_date')){
            $to_date = $request->to_date;
        }else{
            $to_date = date('Y-m-d');
        }
        
        $query = OperationDeliveryFailed::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
		if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
        
		$deliveryFailedData = $query->paginate(10);

		$merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'title')->toArray();
		
        return view('reports.operation.delivery', compact('deliveryFailedData', 'inputs', 'from_date', 'to_date', 'merchants', 'hubs'));
	}

	public function deliveryexport(Request $request, $type) {
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		}else{
			$to_date = date('Y-m-d');
		}

		$query = OperationDeliveryFailed::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
		if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
        
		$deliveryFailedData = $query->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Delivery Failed Report';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('Delivery_Failed_Report_'.time(), function($excel) use ($deliveryFailedData) {
			$excel->sheet('orders', function($sheet) use ($deliveryFailedData){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Order Created Date',
					'Merchant Name',
					'Merchant Id',
					'Status',
					'Pickup Hub',
					'Delivery Hub',
					'Failed Reason',
					'Remarks'
				);

				$i=1;

				foreach($deliveryFailedData as $deliveryFailed){
					$datasheet[$i] = array(
						(string)$deliveryFailed->order_id,
						(string)$deliveryFailed->order_created_at,
						(string)$deliveryFailed->merchant_name,
						(string)$deliveryFailed->merchant_order_id,
						(string)$deliveryFailed->current_status,
						(string)$deliveryFailed->pickup_hub,
						(string)$deliveryFailed->delivery_hub,
						(string)$deliveryFailed->failed_reason,
						(string)$deliveryFailed->remarks
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

	public function return(Request $request){
		$inputs = $request->all();
		if($request->has('from_date')){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->has('to_date')){
            $to_date = $request->to_date;
        }else{
            $to_date = date('Y-m-d');
        }
        
        $query = OperationReturnFailed::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
		if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
        
		$returnFailedData = $query->paginate(10);

		$merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'title')->toArray();
		
        return view('reports.operation.return', compact('returnFailedData', 'inputs', 'from_date', 'to_date', 'merchants', 'hubs'));
	}

	public function returnexport(Request $request, $type) {
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		}else{
			$to_date = date('Y-m-d');
		}

		$query = OperationReturnFailed::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
		if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
        
		$returnFailedData = $query->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Delivery Failed Report';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('Return_Failed_Report_'.time(), function($excel) use ($returnFailedData) {
			$excel->sheet('orders', function($sheet) use ($returnFailedData){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Order Created Date',
					'Merchant Name',
					'Merchant Id',
					'Status',
					'Pickup Hub',
					'Delivery Hub',
					'Failed Reason',
					'Remarks'
				);

				$i=1;

				foreach($returnFailedData as $returnFailed){
					$datasheet[$i] = array(
						(string)$returnFailed->order_id,
						(string)$returnFailed->order_created_at,
						(string)$returnFailed->merchant_name,
						(string)$returnFailed->merchant_order_id,
						(string)$returnFailed->current_status,
						(string)$returnFailed->pickup_hub,
						(string)$returnFailed->delivery_hub,
						(string)$returnFailed->failed_reason,
						(string)$returnFailed->remarks
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

	public function intransit(Request $request){
		$inputs = $request->all();
		if($request->has('from_date')){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->has('to_date')){
            $to_date = $request->to_date;
        }else{
            $to_date = date('Y-m-d');
        }
        
        $query = OperationIntransit::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
		if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
        
		$intransitData = $query->paginate(10);

		$merchants = Merchant::whereStatus(true)->lists('name', 'name')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'title')->toArray();
		
        return view('reports.operation.intransit', compact('intransitData', 'inputs', 'from_date', 'to_date', 'merchants', 'hubs'));
	}

	public function intransitexport(Request $request, $type) {
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		}else{
			$to_date = date('Y-m-d');
		}

		$query = OperationIntransit::WhereBetween('order_created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));
		if(isset($inputs['merchant_id'])){
			$query->whereIn('merchant_name', $inputs['merchant_id']);
		}
		if(isset($inputs['hub_id'])){
			$query->whereIn('pickup_hub', $inputs['hub_id']);
		}
        
		$intransitData = $query->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Delivery Failed Report';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('Intransit_Report_'.time(), function($excel) use ($intransitData) {
			$excel->sheet('orders', function($sheet) use ($intransitData){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Order Created Date',
					'Merchant Name',
					'Merchant Id',
					'Pickup Hub',
					'Delivery Hub',
					'Trip Created Date'
				);

				$i=1;

				foreach($intransitData as $intransit){
					$datasheet[$i] = array(
						(string)$intransit->order_id,
						(string)$intransit->order_created_at,
						(string)$intransit->merchant_name,
						(string)$intransit->merchant_order_id,
						(string)$intransit->pickup_hub,
						(string)$intransit->delivery_hub,
						(string)$intransit->trip_created_date
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

	public function quantitative(Request $request){
		$inputs = $request->all();
		if($request->has('from_date')){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->has('to_date')){
            $to_date = $request->to_date;
        }else{
            $to_date = date('Y-m-d');
        }

		$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

		$query1 = OrderLog::WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
						->whereIn('order_logs.text',['Picked', 'Partial Picked'])
						->where('order_logs.type', '!=', 'reference')
						->join('orders AS B', 'B.id','=','order_logs.order_id')
                        ->join('stores AS C','C.id','=','B.store_id');
		if(isset($inputs['merchant_id'])){
			$query1->whereIn('C.merchant_id', $inputs['merchant_id']);
		}
		$total_pickup = $query1->count();



		$query2 = OrderLog::WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
						->whereIn('order_logs.text',['Delivery Completed', 'Delivery Partial Completed'])
						->where('order_logs.type', '!=', 'reference')
						->join('orders AS D', 'D.id','=','order_logs.order_id')
                        ->join('stores AS E','E.id','=','D.store_id');
		if(isset($inputs['merchant_id'])){
			$query2->whereIn('E.merchant_id', $inputs['merchant_id']);
		}
		$total_delivered = $query2->count();



		$query3 = OrderLog::WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
						->whereIn('order_logs.text',['Products Delivery Failed'])
						->where('order_logs.type', '!=', 'reference')
						->join('orders AS F', 'F.id','=','order_logs.order_id')
                        ->join('stores AS G','G.id','=','F.store_id');
		if(isset($inputs['merchant_id'])){
			$query3->whereIn('G.merchant_id', $inputs['merchant_id']);
		}
		$total_delivery_failed = $query3->count();


		$query4 = OrderLog::WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
						->whereIn('order_logs.text',['Return Completed'])
						->where('order_logs.type', '!=', 'reference')
						->join('orders AS H', 'H.id','=','order_logs.order_id')
                        ->join('stores AS I','I.id','=','H.store_id');
		if(isset($inputs['merchant_id'])){
			$query4->whereIn('I.merchant_id', $inputs['merchant_id']);
		}
		$total_returned = $query4->count();
		

        return view('reports.operation.dashboard', compact('total_pickup', 'total_delivered', 'total_delivery_failed', 'total_returned', 'inputs', 'from_date', 'to_date', 'merchants'));
	}

	public function quantitativeexport(Request $request, $type) {
		$inputs = $request->all();
		
		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		}else{
			$to_date = date('Y-m-d');
		}

		$type_title = '';
		$extract_type = '';

		$query = OrderLog::WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
						->where('order_logs.type', '!=', 'reference')
						->join('orders AS B', 'B.id','=','order_logs.order_id')
                        ->join('stores AS C','C.id','=','B.store_id');
		
		$query1 = OrderLog::select(DB::raw('count(order_logs.id) as breakdown_total, D.name as merchant_name'))
						->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
						->where('order_logs.type', '!=', 'reference')
						->join('orders AS B', 'B.id','=','order_logs.order_id')
                        ->join('stores AS C','C.id','=','B.store_id')
                        ->join('merchants AS D','D.id','=','C.merchant_id');

		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
			$query1->whereIn('D.id', $inputs['merchant_id']);
		} else {
			$query1->groupBy('D.id');			
		}

		if($type == 'pickup') {
			$query->whereIn('order_logs.text',['Picked', 'Partial Picked']);
			$query1->whereIn('order_logs.text',['Picked', 'Partial Picked']);

			$type_title = 'Number of Total Pickup';
			$extract_type = 'Total Pickup';
		}
		else if($type == 'delivered') {
			$query->whereIn('order_logs.text',['Delivery Completed', 'Delivery Partial Completed']);
			$query1->whereIn('order_logs.text',['Delivery Completed', 'Delivery Partial Completed']);

			$type_title = 'Number of Delivered Order';
			$extract_type = 'Total Delivered';
		}
		else if($type == 'delivery_failed') {
			$query->whereIn('order_logs.text',['Products Delivery Failed']);
			$query1->whereIn('order_logs.text',['Products Delivery Failed']);

			$type_title = 'Number of Failed Delivery Order';
			$extract_type = 'Delivery Failed';
		}
		else if($type == 'returned') {
			$query->whereIn('order_logs.text',['Return Completed']);
			$query1->whereIn('order_logs.text',['Return Completed']);

			$type_title = 'Number of Return Completed Order';
			$extract_type = 'Return Completed';
		}

		$total_number = $query->count();

		$criteriaData = $query1->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = $extract_type;
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create($extract_type.'_'.time(), function($excel) use ($criteriaData, $type_title, $total_number) {
			$excel->sheet('orders', function($sheet) use ($criteriaData, $type_title, $total_number){
				$datasheet = array();

				$datasheet[0] = array(
					$type_title,
					(string)$total_number
				);

				$datasheet[1] = array(
					'Merchantwise Breakdown',
					''
				);

				$i=2;

				foreach($criteriaData as $criteria){
					$datasheet[$i] = array(
						(string)$criteria->merchant_name,
						(string)$criteria->breakdown_total
					);

					$i++;
				}

				$sheet->setOrientation('landscape');

                // Freeze first row
				$sheet->freezeFirstRow();
				$sheet->fromArray($datasheet);
			});
		})->download('xls');
	}
}