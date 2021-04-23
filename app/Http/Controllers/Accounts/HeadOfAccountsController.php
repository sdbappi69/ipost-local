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
use App\Hub;
class HeadOfAccountsController  extends Controller
{
	use LogsTrait;
    //
	public function __construct()
	{
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
		$this->middleware('role:head_of_accounts');
	}
	
	public function manage_vault(Request $request){

		$query = HubHistory::orderBy('id','desc');
		//
		( $request->has('status') )           ? $query->where('status',trim($request->status))  : null;
		( $request->has('search_date') )           ? $query->whereDate('created_at','=',trim($request->search_date))  : null;
		( $request->has('hub_id') )           ? $query->where('hub_id','=',trim($request->hub_id))  : null;
		//
		$manage_vault = $query->paginate(10);
		
		$status = ['0' => 'Declined','1' => 'Not Approved yet','2' => 'Approved to transfer','3' => 'Transfered to Bank'];
		$hub = Hub::whereStatus(true)->pluck('title', 'id');
		return view('accounts.head_of_accounts.manage-vault',compact('manage_vault','status','hub'));
	}

	public function manage_checkout(Request $request){
		
		$query = HubCheckout::orderBy('id','desc');
		//
		( $request->has('status'))   		? $query->where('status',trim($request->status))  : null;
		( $request->has('search_date'))		? $query->whereDate('created_at','=',trim($request->search_date))  : null;
		( $request->has('hub_id') )			? $query->where('hub_id','=',trim($request->hub_id))  : null;
		
		$manage_checkout = $query->paginate(10);
		$status = ['0' => 'Declined','1' => 'Not Approved yet','2' => 'Approved', '3' => 'Canceled'];
		$hub = Hub::whereStatus(true)->pluck('title', 'id');

		return view('accounts.head_of_accounts.manage-checkout',compact('manage_checkout','status','hub'));
	}

	public function cancel_checkout(Request $request)
	{
		$hubCheckoutInfo = HubCheckout::findOrFail($request->id);
		$hubCheckoutInfo->status = '3';
		$hubCheckoutInfo->save();

		echo '1';
		// return redirect('manage-checkout-account');
	}

}
