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


class TransferToVaultController extends Controller
{
	use LogsTrait;
    //
	public function __construct()
	{
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
		$this->middleware('role:hubmanager|inboundmanager');
	}

	public function index(Request $request){
		//DB::connection()->enableQueryLog();
		//dd(auth()->user()->reference_id);
		$transferToVault = CashCollection::where('status',1)
		->where('hub_id',auth()->user()->reference_id)
		->paginate(50);

		$hub_vault_accounts = HubVaultAccounts::where('status',true)
		->where('hub_id',auth()->user()->reference_id)
		->pluck('title','id');
		//dd($hub_vault_accounts);

	//dd($transferToVault->toArray());

		return view('accounts.transferToVault.all',compact('transferToVault','hub_vault_accounts'));

		///dd($temp->toArray());
	}

	public function transfer_to_vault_submit(Request $request){
		$validation = Validator::make($request->all(), [
			'cash_collection_id' => 'required',
			'hub_volt_account_id' => 'required'
		]);
		//dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		$selected_cash_collection = $request->cash_collection_id;
		//$selected_sub_uniqe_id = $request->input("sub_unique_id_");
		$amount = 0;
		foreach($selected_cash_collection as $val){
			if($request->input("cash_collection_hub_id_$val") == auth()->user()->reference_id)
			{
				$amount += $request->input("collected_amount_$val");
			}
			else{
				Session::flash('error', "Another HUB Cash cannot process to vault. Invalid data.");
				return redirect()->back();
			}

		}

		$hub_history = new HubHistory();
		$hub_history->hub_volt_account_id = $request->input("hub_volt_account_id");
		$hub_history->hub_id = auth()->user()->reference_id;
		$hub_history->amount = $amount;
		//$hub_history->hub_manager_id = auth()->user()->id;
		$hub_history->status = 1;
		$hub_history->created_by = auth()->user()->id;

		try {
			DB::beginTransaction();
			$hub_history->save();
			foreach($selected_cash_collection as $val){
				CashCollection::where('id',$val)->update(['status' => 2 ,'hub_volt_history_id' => $hub_history->id, 'updated_by' => auth()->user()->id]);
				$cashCollection = CashCollection::findOrFail($val);
				$cashCollection->status = 2;
				$cashCollection->hub_volt_history_id = $hub_history->id;
				$cashCollection->updated_by = auth()->user()->id;
				// if($cashCollection->save()){
				// 	$sub = SubOrder::findOrFail($cashCollection->sub_order_id);
				// 	$sub->accounts = 2;
				// 	$sub->updated_by = auth()->user()->id;
				// 	$sub->save();
				// }
				$cashCollection->save();

			}
			DB::commit();
			Session::flash('message', "Collected cash transfer to vault successfully ");
			return redirect('transfer-to-vault');
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error($e->getMessage())
			;
			return redirect()->back()->withErrors('Collected cash transfer to vault failed.');
		}

	}
}
