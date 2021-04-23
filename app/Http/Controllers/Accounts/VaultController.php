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


class VaultController extends Controller
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
		$vault_list = HubHistory::where('status',1)
		->where('hub_id',auth()->user()->reference_id)
		->get();

		return view('accounts.Vault.all',compact('vault_list'));

		///dd($temp->toArray());
	}

	public function vault_approval_submit(Request $request){
		//dd($request->all());
		$validation = Validator::make($request->all(), [
			'vault_id' => 'required',
			'submit_btn' => 'required'
		]);
		//dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$selected_vault = $request->vault_id;
		try {
			DB::beginTransaction();
			foreach($selected_vault as $val){
				if($request->input("hub_id_$val") == auth()->user()->reference_id)
				{
					$vault = HubHistory::findOrFail($val);
					if($request->submit_btn == 1){
						$vault->status = 2;
					}
					elseif($request->submit_btn == 0){
						$vault->status = 0;
					}
					else{
						return Redirect::back()->withErrors('Invalid Action.');
					}
					$vault->hub_manager_id = auth()->user()->id;
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
				Session::flash('message', "Vault item approved successfully ");
			}
			elseif($request->submit_btn == 0){
				Session::flash('message', "Vault item decliend successfully ");
			}
			return redirect('vault-list');
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error($e->getMessage());
			return redirect('vault-list')->withErrors($e->getMessage());
		}

	}

	public function manage_vault(Request $request){
		$query = HubHistory::where('hub_id',auth()->user()->reference_id);
		//
		( $request->has('status') )           ? $query->where('status',trim($request->status))  : null;
		( $request->has('search_date') )           ? $query->whereDate('created_at','=',trim($request->search_date))  : null;
		//
		$manage_vault = $query->orderBy('id','desc')->paginate(10);

		$status = ['0' => 'Declined','1' => 'Not Approved yet','2' => 'Approved to transfer','3' => 'Transfered to Bank'];
		return view('accounts.Vault.manage-vault',compact('manage_vault','status'));
	}
}
