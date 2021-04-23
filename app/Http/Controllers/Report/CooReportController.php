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

use Auth;
use DB;
use Entrust;
use Excel;
use Illuminate\Http\Request;
use Redirect;
use Session;
use Validator;

class CooReportController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->middleware('role:superadministrator|systemadministrator|systemmoderator|head_of_accounts|customerservice|salesteam|coo|saleshead|kam');
	}
	/**
	* Order to be reassign
	*/
	public function index(Request $request)
	{
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

		$query = OrderLog::select('B.unique_order_id',
			'B.merchant_order_id',
			'E.name AS merchant_name',
			'C.store_id AS store_name',
			'B.delivery_name',
			'B.created_at',
			'F.name AS zone',
			'G.title AS pickup_hub',
			'H.title AS delivery_hub',
			'A.sub_order_status')
		->where('order_logs.text', 'Product waiting to be reassigned')
		->where('order_logs.type', '!=', 'reference')
		->join('sub_orders AS A','A.id','=','order_logs.sub_order_id')
		->join('orders AS B','B.id','=','A.order_id')
		->join('stores AS C','C.id','=','B.store_id')
		->join('merchants AS E','E.id','=','C.merchant_id')
		->join('zones AS F','F.id','=','B.delivery_zone_id')
		->join('hubs AS G','G.id','=','A.source_hub_id')
		->join('hubs AS H','H.id','=','A.destination_hub_id')
		->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}

		$order_reassign = $query->paginate(30);

		$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

		return view('coo-report.orderreassign', compact('inputs', 'merchants', 'hubs', 'from_date', 'to_date', 'order_reassign'));
	}

	public function orderreassignexport(Request $request, $type) {
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

		$query = OrderLog::select('B.unique_order_id',
			'B.merchant_order_id',
			'E.name AS merchant_name',
			'C.store_id AS store_name',
			'B.delivery_name',
			'B.created_at',
			'F.name AS zone',
			'G.title AS pickup_hub',
			'H.title AS delivery_hub',
			'A.sub_order_status')
		->where('order_logs.text', 'Product waiting to be reassigned')
		->where('order_logs.type', '!=', 'reference')
		->join('sub_orders AS A','A.id','=','order_logs.sub_order_id')
		->join('orders AS B','B.id','=','A.order_id')
		->join('stores AS C','C.id','=','B.store_id')
		->join('merchants AS E','E.id','=','C.merchant_id')
		->join('zones AS F','F.id','=','B.delivery_zone_id')
		->join('hubs AS G','G.id','=','A.source_hub_id')
		->join('hubs AS H','H.id','=','A.destination_hub_id')
		->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}

		$order_reassigned = $query->get()->toArray();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Reassign';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('order_reassign_'.time(), function($excel) use ($order_reassigned) {
			$excel->sheet('orders', function($sheet) use ($order_reassigned){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Merchant Order Id',
					'Merchant Name',
					'Store Name',
					'Customer Name',
					'Order Creation Date',
					'Pickup Hub',
					'Delivery Hub',
					'Zone',
					'Current Status'
				);

				$i=1;

				foreach($order_reassigned as $reassigned){
					$sub_order_status = '';

					if($reassigned['sub_order_status'] != '')
						$sub_order_status = hubGetStatus($reassigned['sub_order_status']);

					$datasheet[$i] = array(
						(string)$reassigned['unique_order_id'],
						(string)$reassigned['merchant_order_id'],
						(string)$reassigned['merchant_name'],
						(string)$reassigned['store_name'],
						(string)$reassigned['delivery_name'],
						(string)$reassigned['created_at'],
						(string)$reassigned['pickup_hub'],
						(string)$reassigned['delivery_hub'],
						(string)$reassigned['zone'],
						(string)$sub_order_status
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

	/*
	* Orders are un-attempted till date hub wise and merchant wise
	*/
	public function orderunattempted(Request $request) {
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

		$query = OrderLog::select('B.unique_order_id',
							'B.merchant_order_id',
							'E.name AS merchant_name',
							'C.store_id AS store_name',
							'B.delivery_name',
							'B.created_at',
							'F.name AS zone',
							'G.title AS pickup_hub',
							'H.title AS delivery_hub',
							'A.sub_order_status')
						->where('order_logs.text', 'Full Order Racked at Destination Hub')
						->where('order_logs.type', '!=', 'reference')
						->join('sub_orders AS A','A.id','=','order_logs.sub_order_id')
						->join('orders AS B','B.id','=','A.order_id')
						->join('stores AS C','C.id','=','B.store_id')
						->join('merchants AS E','E.id','=','C.merchant_id')
						->join('zones AS F','F.id','=','B.delivery_zone_id')
						->join('hubs AS G','G.id','=','A.source_hub_id')
						->join('hubs AS H','H.id','=','A.destination_hub_id')
						->where('A.sub_order_status', '26')
						->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}

		$order_unattempted = $query->paginate(30);

		$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

		return view('coo-report.orderunattempted', compact('inputs', 'merchants', 'hubs', 'from_date', 'to_date', 'order_unattempted'));
	}

	public function orderunattemptedexport(Request $request, $type) {
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

		$query = OrderLog::select('B.unique_order_id',
									'B.merchant_order_id',
									'E.name AS merchant_name',
									'C.store_id AS store_name',
									'B.delivery_name',
									'B.created_at',
									'F.name AS zone',
									'G.title AS pickup_hub',
									'H.title AS delivery_hub',
									'A.sub_order_status')
								->where('order_logs.text', 'Full Order Racked at Destination Hub')
								->where('order_logs.type', '!=', 'reference')
								->join('sub_orders AS A','A.id','=','order_logs.sub_order_id')
								->join('orders AS B','B.id','=','A.order_id')
								->join('stores AS C','C.id','=','B.store_id')
								->join('merchants AS E','E.id','=','C.merchant_id')
								->join('zones AS F','F.id','=','B.delivery_zone_id')
								->join('hubs AS G','G.id','=','A.source_hub_id')
								->join('hubs AS H','H.id','=','A.destination_hub_id')
								->where('A.sub_order_status', '26')
								->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}

		$order_unattempted = $query->get()->toArray();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Unattempted';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('order_unattempted_'.time(), function($excel) use ($order_unattempted) {
			$excel->sheet('orders', function($sheet) use ($order_unattempted){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Merchant Order Id',
					'Merchant Name',
					'Store Name',
					'Customer Name',
					'Order Creation Date',
					'Pickup Hub',
					'Delivery Hub',
					'Zone',
					'Current Status'
				);

				$i=1;

				foreach($order_unattempted as $unattempted){
					$sub_order_status = '';

					if($unattempted['sub_order_status'] != '')
						$sub_order_status = hubGetStatus($unattempted['sub_order_status']);

					$datasheet[$i] = array(
						(string)$unattempted['unique_order_id'],
						(string)$unattempted['merchant_order_id'],
						(string)$unattempted['merchant_name'],
						(string)$unattempted['store_name'],
						(string)$unattempted['delivery_name'],
						(string)$unattempted['created_at'],
						(string)$unattempted['pickup_hub'],
						(string)$unattempted['delivery_hub'],
						(string)$unattempted['zone'],
						(string)$sub_order_status
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

	/*
	* Total delivered, returned orders complete Hub wise and merchant wise
	*/
	public function ordercompleted(Request $request)
	{
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

		$query = OrderLog::select('B.unique_order_id',
							'B.merchant_order_id',
							'E.name AS merchant_name',
							'C.store_id AS store_name',
							'B.delivery_name',
							'B.created_at',
							'F.name AS zone',
							'G.title AS pickup_hub',
							'H.title AS delivery_hub',
							'A.sub_order_status')
						->whereIn('order_logs.text', ['Return Completed', 'Delivery Completed'])
						->where('order_logs.type', '!=', 'reference')
						->join('sub_orders AS A','A.id','=','order_logs.sub_order_id')
						->join('orders AS B','B.id','=','A.order_id')
						->join('stores AS C','C.id','=','B.store_id')
						->join('merchants AS E','E.id','=','C.merchant_id')
						->join('zones AS F','F.id','=','B.delivery_zone_id')
						->join('hubs AS G','G.id','=','A.source_hub_id')
						->join('hubs AS H','H.id','=','A.destination_hub_id')
						->whereIn('A.sub_order_status', ['37', '38'])
						->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}

		$order_completed = $query->paginate(30);

		$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

		return view('coo-report.ordercompleted', compact('inputs', 'merchants', 'hubs', 'from_date', 'to_date', 'order_completed'));
	}

	public function ordercompletedexport(Request $request, $type) {
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

		$query = OrderLog::select('B.unique_order_id',
							'B.merchant_order_id',
							'E.name AS merchant_name',
							'C.store_id AS store_name',
							'B.delivery_name',
							'B.created_at',
							'F.name AS zone',
							'G.title AS pickup_hub',
							'H.title AS delivery_hub',
							'A.sub_order_status')
						->whereIn('order_logs.text', ['Return Completed', 'Delivery Completed'])
						->where('order_logs.type', '!=', 'reference')
						->join('sub_orders AS A','A.id','=','order_logs.sub_order_id')
						->join('orders AS B','B.id','=','A.order_id')
						->join('stores AS C','C.id','=','B.store_id')
						->join('merchants AS E','E.id','=','C.merchant_id')
						->join('zones AS F','F.id','=','B.delivery_zone_id')
						->join('hubs AS G','G.id','=','A.source_hub_id')
						->join('hubs AS H','H.id','=','A.destination_hub_id')
						->whereIn('A.sub_order_status', ['37', '38'])
						->WhereBetween('order_logs.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));


		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}

		$order_completed = $query->get()->toArray();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Order Completed';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('order_completed_'.time(), function($excel) use ($order_completed) {
			$excel->sheet('orders', function($sheet) use ($order_completed){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Merchant Order Id',
					'Merchant Name',
					'Store Name',
					'Customer Name',
					'Order Creation Date',
					'Pickup Hub',
					'Delivery Hub',
					'Zone',
					'Current Status'
				);

				$i=1;

				foreach($order_completed as $completed){
					$sub_order_status = '';

					if($completed['sub_order_status'] != '')
						$sub_order_status = hubGetStatus($completed['sub_order_status']);

					$datasheet[$i] = array(
						(string)$completed['unique_order_id'],
						(string)$completed['merchant_order_id'],
						(string)$completed['merchant_name'],
						(string)$completed['store_name'],
						(string)$completed['delivery_name'],
						(string)$completed['created_at'],
						(string)$completed['pickup_hub'],
						(string)$completed['delivery_hub'],
						(string)$completed['zone'],
						(string)$sub_order_status
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

	/*
	* new-merchants
	*/
	public function newmerchants(Request $request)
	{
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

		$query = Merchant::select('merchants.name as merchant_name', 'merchants.created_at', 'A.name AS user_name')
						->join('users AS A','A.id','=','merchants.created_by')
						->WhereBetween('merchants.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['sales_user_id'])){
			$query->whereIn('merchants.created_by', $inputs['sales_user_id']);
		}

		$new_merchants = $query->paginate(30);

		$sales_users = User::whereIn('user_type_id', ['2', '3', '19'])->lists('name', 'id')->toArray();

		return view('coo-report.newmerchants', compact('inputs', 'sales_users', 'hubs', 'from_date', 'to_date', 'new_merchants'));
	}

	public function newmerchantsexport(Request $request, $type) {
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

		$query = Merchant::select('merchants.name as merchant_name', 'merchants.created_at', 'A.name AS user_name')
						->join('users AS A','A.id','=','merchants.created_by')
						->WhereBetween('merchants.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['sales_user_id'])){
			$query->whereIn('merchants.created_by', $inputs['sales_user_id']);
		}

		$new_merchants = $query->get()->toArray();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'New Merchants';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('new_merchants_'.time(), function($excel) use ($new_merchants) {
			$excel->sheet('orders', function($sheet) use ($new_merchants){
				$datasheet = array();

				$datasheet[0] = array(
					'Merchant Name',
					'Creation Date',
					'User Name'
				);

				$i=1;

				foreach($new_merchants as $merchants){
					$datasheet[$i] = array(
						(string)$merchants['merchant_name'],
						(string)$merchants['created_at'],
						(string)$merchants['user_name']
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


	/*
	* merchants are not ordering in a date range
	*/
	public function merchantsorder(Request $request)
	{
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		} else{
			$to_date = date('Y-m-d');
		}

		$query = Store::select('A.name as merchant_name', 'B.created_at AS last_order_date')
						->join('merchants AS A','A.id','=','stores.merchant_id')
						->join('orders AS B','B.store_id','=','stores.id');

		if(isset($inputs['merchant_id'])){
			$query->whereIn('stores.merchant_id', $inputs['merchant_id']);
		}

		$merchants_orders = $query->groupBy('B.store_id')->orderBy('B.created_at', 'desc')->paginate(1000);

		$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

		return view('coo-report.merchantsorder', compact('inputs', 'merchants', 'hubs', 'from_date', 'to_date', 'merchants_orders'));
	}

	public function merchantsorderexport(Request $request, $type) {
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

		$query = Store::select('A.name as merchant_name', 'B.created_at AS last_order_date')
						->join('merchants AS A','A.id','=','stores.merchant_id')
						->join('orders AS B','B.store_id','=','stores.id');

		if(isset($inputs['merchant_id'])){
			$query->whereIn('stores.merchant_id', $inputs['merchant_id']);
		}

		$merchants_orders = $query->groupBy('B.store_id')->orderBy('B.created_at', 'desc')->paginate(1000);

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Inactive Merchants';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('inactive_merchants_'.time(), function($excel) use ($merchants_orders, $from_date, $to_date) {
			$excel->sheet('orders', function($sheet) use ($merchants_orders, $from_date, $to_date){
				$datasheet = array();

				$datasheet[0] = array(
					'Merchant Name',
					'Last Order Date'
				);

				$i=1;

				foreach($merchants_orders as $orders){
					if((date('Y-m-d', strtotime($orders['last_order_date'])) < $from_date) && ($to_date > date('Y-m-d', strtotime($orders['last_order_date'])))) {
						$datasheet[$i] = array(
							(string)$orders['merchant_name'],
							(string)$orders['last_order_date']
						);

						$i++;
					}
				}

				$sheet->setOrientation('landscape');

                // Freeze first row
				$sheet->freezeFirstRow();
				$sheet->fromArray($datasheet);
			});
		})->download($type);
	}

	public function deliveryrevenue(Request $request)
	{
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		} else{
			$to_date = date('Y-m-d');
		}

		$query = DeliveryTask::select('B.unique_order_id',
							'B.merchant_order_id',
							'E.name AS merchant_name',
							'C.store_id AS store_name',
							'F.name AS zone',
							'G.title AS pickup_hub',
							'H.title AS delivery_hub',
							'delivery_task.amount',
							'I.name as delivery_man')
						->whereIn('delivery_task.status', ['2','3'])
						->join('sub_orders AS A','A.unique_suborder_id','=','delivery_task.unique_suborder_id')
						->join('orders AS B','B.id','=','A.order_id')
						->join('stores AS C','C.id','=','B.store_id')
						->join('merchants AS E','E.id','=','C.merchant_id')
						->join('zones AS F','F.id','=','B.delivery_zone_id')
						->join('hubs AS G','G.id','=','A.source_hub_id')
						->join('hubs AS H','H.id','=','A.destination_hub_id')
						->join('users AS I','I.id','=','delivery_task.deliveryman_id')
						->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}
		if(isset($inputs['pickup_man_id'])){
			$query->whereIn('delivery_task.deliveryman_id', $inputs['pickup_man_id']);
		}

		$delivery_revenue = $query->get();

		$merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
		$hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
		$pickupman = User::select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
					        ->leftJoin('hubs','hubs.id','=','users.reference_id')
					        ->where('users.status',true)
					        ->where('users.user_type_id', '=', '8')
					        ->lists('name','users.id')
					        ->toArray();

		return view('coo-report.deliveryrevenue', compact('pickupman', 'inputs', 'merchants', 'hubs', 'from_date', 'to_date', 'delivery_revenue'));
	}

	public function deliveryrevenueexport(Request $request, $type) {
		$inputs = $request->all();

		if($request->from_date){
			$from_date = $request->from_date;
		}else{
			$from_date = date('Y-m-d');
		}

		if($request->to_date){
			$to_date = $request->to_date;
		} else{
			$to_date = date('Y-m-d');
		}

		$query = DeliveryTask::select('B.unique_order_id',
							'B.merchant_order_id',
							'E.name AS merchant_name',
							'C.store_id AS store_name',
							'F.name AS zone',
							'G.title AS pickup_hub',
							'H.title AS delivery_hub',
							'delivery_task.amount',
							'I.name as delivery_man')
						->whereIn('delivery_task.status', ['2','3'])
						->join('sub_orders AS A','A.unique_suborder_id','=','delivery_task.unique_suborder_id')
						->join('orders AS B','B.id','=','A.order_id')
						->join('stores AS C','C.id','=','B.store_id')
						->join('merchants AS E','E.id','=','C.merchant_id')
						->join('zones AS F','F.id','=','B.delivery_zone_id')
						->join('hubs AS G','G.id','=','A.source_hub_id')
						->join('hubs AS H','H.id','=','A.destination_hub_id')
						->join('users AS I','I.id','=','delivery_task.deliveryman_id')
						->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'));

		if(isset($inputs['hub_id'])){
			$query->whereIn('A.destination_hub_id', $inputs['hub_id']);
		}
		if(isset($inputs['merchant_id'])){
			$query->whereIn('C.merchant_id', $inputs['merchant_id']);
		}
		if(isset($inputs['pickup_man_id'])){
			$query->whereIn('delivery_task.deliveryman_id', $inputs['pickup_man_id']);
		}

		$delivery_revenue = $query->get();

		$ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Delivery Revenue';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

		return Excel::create('delivery_revenue_'.time(), function($excel) use ($delivery_revenue) {
			$excel->sheet('orders', function($sheet) use ($delivery_revenue){
				$datasheet = array();

				$datasheet[0] = array(
					'Order Id',
					'Merchant Order Id',
					'Merchant Name',
					'Store Name',
					'Pickup Hub',
					'Delivery Hub',
					'Zone',
					'Delivery Man',
					'Amount'
				);

				$i=1;
				$total_amount = 0;

				foreach($delivery_revenue as $revenue) {
					$datasheet[$i] = array(
						(string)$revenue['unique_order_id'],
						(string)$revenue['merchant_order_id'],
						(string)$revenue['merchant_name'],
						(string)$revenue['store_name'],
						(string)$revenue['pickup_hub'],
						(string)$revenue['delivery_hub'],
						(string)$revenue['zone'],
						(string)$revenue['delivery_man'],
						(string)$revenue['amount']
					);

					$total_amount = $total_amount + $revenue['amount'];

					if(sizeof($delivery_revenue) == $i) {
						$datasheet[$i+1] = array(
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'Total Revenue Amount:',
							(string)$total_amount
						);
					}

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