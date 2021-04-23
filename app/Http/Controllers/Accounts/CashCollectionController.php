<?php

namespace App\Http\Controllers\Accounts;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use DB;

use PDF;
use App\OrderProduct;
use App\Consignment;
use App\Del;
use App\Order;
use Validator;
use App\Http\Traits\LogsTrait;

use App\SubOrder;

use App\DeliveryTask;
use Illuminate\Support\Facades\Redirect;
use Session;

use App\CashCollection;

class CashCollectionController extends Controller
{
	use LogsTrait;
    //
	public function __construct()
	{
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
		$this->middleware('role:hubmanager|inboundmanager');
	}

	public function index(Request $request){

		$query = SubOrder::whereIn('sub_order_status',[37,38,39])->where('accounts',0);

		( $request->has('sub_unique_id') )           ? $query
		->where('sub_orders.unique_suborder_id',trim($request->sub_unique_id))  : null;
		
		( $request->has('order_unique_id') )           ?
		$query->where('order_id',function($q) use ($request)
		{
			$q->from('orders')
			->selectRaw('id')
			->where('unique_order_id',$request->order_unique_id)
			->first();
		})  : null;

		( $request->has('rider_id') )  ? $query->where('deliveryman_id',$request->rider_id)
		: null;

		$query->where('destination_hub_id', auth()->user()->reference_id);

		$cash_collection = $query->paginate(50);

		$rider = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

		return view('accounts.cashCollection.all',compact('cash_collection','rider'));

		///dd($temp->toArray());
	}

	public function cash_collection_submit(Request $request){
		$validation = Validator::make($request->all(), [
			'sub_order_id' => 'required',

		]);
		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		// dd($request->all());
		$selected_sub_id = $request->sub_order_id;
		//$selected_sub_uniqe_id = $request->input("sub_unique_id_");
		$error_array = null;
		foreach($selected_sub_id as $val){
			//dd($request->input("merchant_id_$val"));
			$cashCollection = new CashCollection();

			$cashCollection->hub_id = auth()->user()->reference_id;
			$cashCollection->merchant_id = $request->input("merchant_id_$val");
			$cashCollection->store_id = $request->input("store_id_$val");
			$cashCollection->order_id = $request->input("order_id_$val");
			$cashCollection->sub_order_id = $val;
			$cashCollection->collected_amount =  $request->input("collected_amount_$val");
			$cashCollection->cod_amount =  $request->input("cod_amount_$val");
			$cashCollection->cod_charge =  $request->input("cod_charge_$val");
			$cashCollection->cod_charge_info =  $request->input("cod_charge_info_$val");

			$cashCollection->bill_amount =  $request->input("bill_amount_$val");
			$cashCollection->total_bill_amount =  $request->input("total_bill_amount_$val");
			$cashCollection->bill_status =  0;

			if($request->input("cod_amount_$val") == 0){
				$cashCollection->status =  5;
			}
			else{
				$cashCollection->status =  1;
			}

			$cashCollection->paid_amount =  $request->input("paid_amount_$val");
			$cashCollection->created_by =   auth()->user()->id;
			//$cashCollection->updated_by =  	auth()->user()->id;
			//dd($cashCollection->toArray());
			try {
				DB::beginTransaction();
				$cashCollection->save();
				SubOrder::where('unique_suborder_id',$request->input("sub_unique_id_$val"))->update(['accounts' => 1]);
				// Change sub order status
				$this->suborderStatus($request->input("sub_unique_id_$val"),41);
				DB::commit();
			} catch (\Exception $e) {
				DB::rollBack();
				\Log::error($e->getMessage().' | sub order id : '.$request->input("sub_unique_id_$val"));
				$error_array[]=$e->getMessage().' | sub order id : '.$request->input("sub_unique_id_$val");
			}
		}
		if(is_null($error_array)){
			Session::flash('message', "Cash Colleted successfully ");
			return redirect('cash-collection');
		}
		else{
			return redirect('cash-collection')->withErrors($error_array);
		}

	}
}
