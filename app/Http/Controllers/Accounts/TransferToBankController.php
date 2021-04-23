<?php

namespace App\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use DB;
use PDF;
use App\Del;

use Validator;
use App\Http\Traits\LogsTrait;

use Illuminate\Support\Facades\Redirect;
use Session;

use App\CashCollection;
use App\HubVaultAccounts;
use App\HubHistory;
use App\HubBankAccounts;
use App\HubCheckout;

class TransferToBankController extends Controller
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
		$transferToBank = HubHistory::where('status', 2)
		->where('hub_id',auth()->user()->reference_id)
		->get();
		//dd($transferToBank->toArray());
		$hub_bank_accounts = HubBankAccounts::where('status',true)->where('hub_id',auth()->user()->reference_id)->pluck('name','id');
		//dd($hub_vault_accounts);

	//dd($transferToVault->toArray());

		return view('accounts.transferToBank.all',compact('transferToBank','hub_bank_accounts'));

		///dd($temp->toArray());
	}

	public function transfer_to_bank_submit(Request $request){
		$validation = Validator::make($request->all(), [
			'vault_id' => 'required',
			'hub_bank_account_id' => 'required',
		]);
		//dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		//dd($request->all());
		$selected_vault_data = $request->vault_id;
		//$selected_sub_uniqe_id = $request->input("sub_unique_id_");
		$amount = 0;
		foreach($selected_vault_data as $val){
			if($request->input("hub_id_$val") == auth()->user()->reference_id)
			{
				$amount += $request->input("amount_$val");
			}
			else{
				Session::flash('error', "Another HUB vault cash cannot process to bank. Invalid data.");
				return redirect()->back();
			}

		}
		//dd($amount);
		$hub_checkout = new HubCheckout();
		$hub_checkout->hub_bank_account_id = $request->input("hub_bank_account_id");
		$hub_checkout->hub_id = auth()->user()->reference_id;
		$hub_checkout->amount = $amount;
		$hub_checkout->status = 1;
		//$hub_checkout->accountant_id = auth()->user()->id;
		$hub_checkout->created_by = auth()->user()->id;
		//dd($hub_checkout->toArray());
		try {
			DB::beginTransaction();
			$hub_checkout->save();
			foreach($selected_vault_data as $val){
				HubHistory::where('id',$val)->update(['status' => 3]);
				CashCollection::where('hub_volt_history_id',$val)->update(['status' => 3,'updated_by' => auth()->user()->id,'hub_checkout_id' => $hub_checkout->id]);
			}
			DB::commit();
			Session::flash('message', "Vault data transfered to bank successfully ");
			return redirect('transfer-to-bank');
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error($e->getMessage());
			return redirect()->back()->withErrors('Vault data transfered to bank failed.');
		}

	}
}
