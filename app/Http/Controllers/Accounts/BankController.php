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

use App\HubVaultAccounts;
use App\HubHistory;
use App\HubCheckout;
use App\BankTransactionDoc;
use Auth;
use App\Hub;
class BankController  extends Controller
{
	use LogsTrait;
    //
	public function __construct()
	{
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
		$this->middleware('role:hubmanager|head_of_accounts');
	}

	public function index(Request $request){


		$bank_list = HubCheckout::
		where('status',1)
		->where('hub_id',auth()->user()->reference_id)
		->get();

		$depositor = User::where('status',true)->where('reference_id',auth()->user()->reference_id)->pluck('name','id');
		//dd($depositor->toArray());
		return view('accounts.Bank.all',compact('bank_list','depositor'));

		///dd($temp->toArray());
	}


	public function bank_approval_submit(Request $request){
		//dd($request->all());
		$validation = Validator::make($request->all(), [
			'bank_id' => 'required',
			'submit_btn' => 'required',
			'depositor_id' => 'required',
		]);

		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		//dd($request->all());
		$selected_bank = $request->bank_id;
		try {
			DB::beginTransaction();
			foreach($selected_bank as $val){
				if($request->input("hub_id_$val") == auth()->user()->reference_id)
				{
					$vault = HubCheckout::findOrFail($val);
					if($request->submit_btn == 1){
						$vault->status = 2;
					}
					elseif($request->submit_btn == 0){
						$vault->status = 0;
					}
					else{
						return Redirect::back()->withErrors('Invalid Action.');
					}
					$vault->hub_manger_id = auth()->user()->id;
					$vault->depositor_id = $request->depositor_id;
					$vault->updated_by = auth()->user()->id;
					$vault->save();
				}
				else{
					return Redirect::back()->withErrors('Another hub data cannot be processed.');
					return redirect()->back();
				}
			}
			DB::commit();
			if($request->submit_btn == 1){
				Session::flash('message', "Bank item and depositor assigned  successfully ");
			}
			elseif($request->submit_btn == 0){
				Session::flash('message', "Bank item and depositor assigned decliend successfully ");
			}
			return redirect('bank-list');
		} catch (\Exception $e) {
			Db::rollBack();
			\Log::error($e->getMessage());
			return redirect('bank-list')->withErrors($e->getMessage());
		}
	}
	public function manage_checkout(Request $request){

		$query = HubCheckout::where('hub_id',auth()->user()->reference_id);
		//
		( $request->has('status') ) ? $query->where('status',trim($request->status))  : null;
		( $request->has('search_date') ) ? $query->whereDate('created_at','=',trim($request->search_date))  : null;
		//
		$manage_checkout = $query->orderBy('id','desc')->paginate(10);

		// foreach ($manage_checkout as  $value) {
		// 	dd($value->manager_id);
		// }
		//dd($manage_checkout->toArray());
		$status = ['0' => 'Declined','1' => 'Not Approved yet','2' => 'Approved','3' => 'Canceled'];

		return view('accounts.Bank.manage-checkout',compact('manage_checkout','status'));
	}

	public function upload_bank_doc(Request $request){
		$validation = Validator::make($request->all(), [
			'checkout_id' => 'required|numeric',
			'doc' => 'required|mimes:jpeg,jpg,png'

		]);
		//dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		$id = $request->checkout_id;

		if($request->hasFile('doc')) {
			if ($request->file('doc')->isValid()) {
				$extension = $request->file('doc')->getClientOriginalExtension();
				$fileName = $id.'.'.$extension;
				$url = 'uploads/bank_transaction_doc/';
				$request->file('doc')->move($url, $fileName);
            // return env('APP_URL').$url.$fileName;
				$doc_url = env('APP_URL').$url.$fileName;
			}
			else{
				return Redirect::back()->withErrors("Invalid file.");
			}
		}
		else{
			return Redirect::back()->withErrors("No file attached.");
		}

		$bankTransactionDoc = new BankTransactionDoc();
		$bankTransactionDoc->doc_url = $doc_url;
		$bankTransactionDoc->status = 1;
		$bankTransactionDoc->created_by = auth()->user()->id;
		try {
			DB::beginTransaction();
			$bankTransactionDoc->save();
			HubCheckout::where('id',$id)->update(['bank_transection_doc_id' => $bankTransactionDoc->id,'updated_by' => auth()->user()->id]);
			$get_cash_collection_data_by_hub_checckout_id = CashCollection::where('hub_checkout_id',$id)->get();
			if($get_cash_collection_data_by_hub_checckout_id){
				foreach ($get_cash_collection_data_by_hub_checckout_id as $key => $cash_collection_data) {
					# code...
					$cash_collection_data->updated_by = auth()->user()->id;
					$cash_collection_data->status = 4;
					$cash_collection_data->save();
					$this->suborderStatus($cash_collection_data->sub_order_id,42);
				}
			}
			// ->update(['updated_by' => auth()->user()->id,'status' => 4]);
			DB::commit();
			Session::flash('message', "File uploaded successfully.");
			return redirect('manage-checkout');
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error($e->getMessage());
			return Redirect::back()->withErrors("File upload failed.");
		}
	}

	public function set_bank_transaction_id(Request $request){
		$validation = Validator::make($request->all(), [
			'checkout_transaction_id' => 'required|numeric',
			'transaction_id' => 'required'

		]);

		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$id = $request->checkout_transaction_id;
		try {
			DB::beginTransaction();
			HubCheckout::where('id',$id)->update(['bank_transection_id' => $request->transaction_id,'updated_by' => auth()->user()->id]);
			$get_cash_collection_data_by_hub_checkout_id = CashCollection::where('hub_checkout_id',$id)->get();
			if($get_cash_collection_data_by_hub_checkout_id){
				foreach ($get_cash_collection_data_by_hub_checkout_id as $key => $cash_collection_data) {
					# code...
					$cash_collection_data->updated_by = auth()->user()->id;
					$cash_collection_data->status = 5;
					$cash_collection_data->save();
					$this->suborderStatus($cash_collection_data->sub_order_id,43);
				}
			}
			// ->update(['updated_by' => auth()->user()->id,'status' => 5])
			DB::commit();
			Session::flash('message', "File uploaded successfully.");
			return redirect('manage-checkout-accounts');
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error($e->getMessage());
			return redirect('manage-checkout-accounts')->withErrors('Transaction id update failed.');
		}

	}

	public function transfer_canceled()
	{
		$cancel_list = HubCheckout::where('status',3)
		->where('hub_id',auth()->user()->reference_id)
		->get();

		return view('accounts.Bank.cancel',compact('cancel_list'));
	}

	public function bank_cancel_submit(Request $request){
		//dd($request->all());
		$validation = Validator::make($request->all(), [
			'bank_id' => 'required',
			'submit_btn' => 'required'
		]);

		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$selected_bank = $request->bank_id;
		try {
			DB::beginTransaction();

			foreach($selected_bank as $val) {
				if($request->input("hub_id_$val") == auth()->user()->reference_id) {
					$vault = HubCheckout::findOrFail($val);
					if($request->submit_btn == 1){
						$vault->status = 0;
					}
					elseif($request->submit_btn == 2){
						$vault->status = 2;
					}
					else{
						return Redirect::back()->withErrors('Invalid Action.');
					}
					$vault->hub_manger_id = auth()->user()->id;
					$vault->updated_by = auth()->user()->id;
					$vault->save();

					if($request->submit_btn == 1){
						$cash_collections = CashCollection::where('hub_checkout_id',$val)->get();
						if($cash_collections){
							foreach ($cash_collections as $key => $cash_collection) {
								$cash_collection->updated_by = auth()->user()->id;
								$cash_collection->status = 1;
								$cash_collection->save();
								$this->suborderStatus($cash_collection->sub_order_id,41);
							}
						}
					}
				}
				else{
					return Redirect::back()->withErrors('Another hub data cannot be processed.');
					return redirect()->back();
				}
			}
			DB::commit();
			if($request->submit_btn == 1){
				Session::flash('message', "Transfer to Vault successfully ");
			}
			elseif($request->submit_btn == 2){
				Session::flash('message', "Apply for approval successfully ");
			}
			return redirect('bank-canceled');
		} catch (\Exception $e) {
			Db::rollBack();
			\Log::error($e->getMessage());
			return redirect('bank-canceled')->withErrors($e->getMessage());
		}
	}

}
