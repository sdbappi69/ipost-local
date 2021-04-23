<?php

namespace App\Http\Controllers\Accounts;

use App\BankTransactionDoc;
use App\CashCollection;
use App\Consignment;
use App\Del;
use App\DeliveryTask;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Traits\LogsTrait;
use App\HubBankAccounts;
use App\Merchant;
use App\MerchantBankAccounts;
use App\MerchantCheckout;
use App\Order;
use App\OrderProduct;
use App\Store;
use App\SubOrder;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PDF;
use Session;
use Validator;
use mPDF;
class MerchantCheckoutController extends Controller
{
	use LogsTrait;
    //
	public function __construct()
	{
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
		$this->middleware('role:head_of_accounts|merchantadmin');
	}

	public function index(Request $request){
		DB::enableQueryLog();
		$query = CashCollection::where('status', 5);
		( $request->has('merchant_id') )  ? $query->where('merchant_id',$request->merchant_id)
		: null;
		if($request->has('store_id')){
			if( $request->store_id != 'all'){
				$query->where('store_id',$request->store_id);
			}

		}

		$invoices = [];
		if($request->has('merchant_id')){
			$merchant_checkout = $query->orderBy('id','desc')->paginate(50);
			$store = CashCollection::select(DB::raw("distinct stores.store_id as storeName, cash_collections.store_id as storeId"))->join('stores','stores.id','=','cash_collections.store_id')
			->where('cash_collections.merchant_id',$request->merchant_id)->pluck('storeName','storeId')->toArray();

			//dd(DB::getQueryLog());
			//dd($store);
			if($request->has('store_id')){
				if( $request->store_id == 'all'){
					$accounts = MerchantBankAccounts::where('status',true)->where('merchant_id',$request->merchant_id)->pluck('name','id')->toArray();
				}
				else{
					$accounts = MerchantBankAccounts::where('status',true)->where('merchant_id',$request->merchant_id)->where('store_id',$request->store_id)->pluck('name','id')->toArray();
				}
				$invoices = MerchantCheckout::
				where('merchant_id',$request->merchant_id)
				->where('store_id',$request->store_id)
				->pluck('invoice_no','invoice_no')
				->toArray();
			}
			else{
				$accounts = [];
			}
			//dd($store);
		}
		else{
			$store = [];
			$accounts = [];
			$merchant_checkout = $query->orderBy('id','desc')->paginate(50);
		}


		$merchant = CashCollection::join('merchants','merchants.id','=','cash_collections.merchant_id')->groupBy('cash_collections.merchant_id')->pluck('merchants.name','cash_collections.merchant_id')->toArray();
		//dd($merchant);
		//dd($merchant_checkout->toArray());





		return view('accounts.merchant-checkout.all',compact('invoices','merchant_checkout','merchant','store','accounts'));

		///dd($temp->toArray());
	}

	public function checkout_submit(Request $request){
		//dd($request->all());
		$validation = Validator::make($request->all(), [
			'merchant_id' => 'required|numeric',
			'store_id' => 'required',
			'account' => 'required',
			'cashCollectionId' => 'required',
			]);
		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$store_data = Store::findOrFail($request->store_id);

		if($store_data->account_synq_cod == 0){
			$account_synq_cod = 0;
		}else{
			$account_synq_cod = 1;
		}

		if($store_data->account_synq_dc == 0){
			$account_synq_dc = 0;
		}else{
			$account_synq_dc = 1;
		}

		if($store_data->vat_include == 1){
			if(isset($store_data->vat_percentage)){
				$vat_percentage = $store_data->vat_percentage;
			}else{
				$vat_percentage = 0;
			}
		}else{
			$vat_percentage = 0;
		}

		$selected_cash_col_id = $request->cashCollectionId;

		$amount = 0;

		foreach($selected_cash_col_id as $val){

			$cash_collection = CashCollection::findOrFail($val);

			if(isset($cash_collection->collected_amount)){
				$temp_amount = $cash_collection->collected_amount;
			}else{
				$temp_amount = 0;
			}

			if($account_synq_cod == 1){
				$cod_charge = $cash_collection->cod_charge;
			}else{
				$cod_charge = 0;
			}

			if(isset($cash_collection->sub_order->product->total_delivery_charge)){
				$delivery_charge = $cash_collection->sub_order->product->total_delivery_charge;
			}else{
				$delivery_charge = 0;
			}

			$vat = ($cash_collection->store->vat_percentage / 100) * $delivery_charge;

			$delivery_charge = $delivery_charge + $vat + $cod_charge;

			if($account_synq_dc == 1){
				$temp_amount = $temp_amount - $delivery_charge;
				$due_amount = 0;

				// Change sub order status
				$this->suborderStatus($cash_collection->sub_order->id, 45);

			}else{
				$due_amount = $delivery_charge;

				// Change sub order status
				$this->suborderStatus($cash_collection->sub_order->id, 46);
			}

			$amount += $temp_amount;

			$cash_collection->total_bill_amount = $delivery_charge;
			$cash_collection->cod_charge = $cod_charge;
			$cash_collection->due_amount = $due_amount;
			$cash_collection->vat_amount = $vat;
			$cash_collection->save();
		}

		if($request->store_id == 'all'){
			//echo "a"; die();
			$store = CashCollection::select(DB::raw("distinct store_id"))->where('merchant_id',$request->merchant_id)->get()->toArray();
			//dd($store);
			$store_id = '';
			foreach($store as $s){

				$store_id .= $s['store_id'].',';
			}
			$store_id = rtrim($store_id, ',');

		}
		else{

			$store_id = $request->store_id;
		}
		$invoice_no = $request->merchant_id.date('y').date('m').date('d').rand(1,100);
		if($request->has('invoice')){
			if($request->invoice == 'new'){
				$merchant_checkout = new MerchantCheckout();
				$merchant_checkout->created_by = auth()->user()->id;
			}
			else{
				$invoice_no = $request->invoice;
				$merchant_checkout = MerchantCheckout::where('invoice_no',$request->invoice)->first();
				$amount = $amount + $merchant_checkout->amount;
				$merchant_checkout->updated_by = auth()->user()->id;
			}
		}
		else{
			$merchant_checkout = new MerchantCheckout();
			$merchant_checkout->created_by = auth()->user()->id;
		}
		//$merchant_checkout = new MerchantCheckout();
		$merchant_checkout->merchant_id = $request->merchant_id;
		$merchant_checkout->store_id = $store_id;
		$merchant_checkout->amount = $amount;
		$merchant_checkout->total_amount = $amount;
		$merchant_checkout->merchant_bank_account_id = $request->account;
		$merchant_checkout->status = 1;
		$merchant_checkout->invoice_no = $invoice_no;

		///dd($merchant_checkout->toArray());
		if($merchant_checkout->save()){
			foreach($selected_cash_col_id as $val){
				$cashCollection = CashCollection::findOrFail($val);
				$cashCollection->status = 6;
				$cashCollection->merchant_checkout_id = $merchant_checkout->id;
				$cashCollection->updated_by = auth()->user()->id;
				$cashCollection->save();
			}
			Session::flash('message', "Merchant check out done successfully ");
		}
		else{
			return redirect()->back()->withErrors('Merchant check failed');
		}

		return redirect('create-merchant-checkout');
	}

	public function merchant_bank_account(Request $request){

		if( $request->has('all')){
			$accounts = MerchantBankAccounts::where('status',true)->where('merchant_id',$request->merchant_id)->get()->toArray();
		}
		else{
			$accounts = MerchantBankAccounts::where('status',true)->where('merchant_id',$request->merchant_id)->where('store_id',$request->store_id)->get()->toArray();
		}

		return $accounts;
	}

	// Manage merchant check out
	public function manage_merchant_checkout(Request $request){

		$query = MerchantCheckout::orderBy('id','desc');
		( $request->has('status') )           ? $query->where('status',trim($request->status))  : null;
		( $request->has('search_date') )           ? $query->whereDate('created_at','=',trim($request->search_date))  : null;
		( $request->has('merchant_id') )           ? $query->where('merchant_id','=',trim($request->merchant_id))  : null;
		( $request->has('invoice_no') )           ? $query->where('invoice_no','=',trim($request->invoice_no))  : null;
		$manage_merchant_checkout = $query->paginate(10);
		$status = ['1' => 'Active','2' => 'Document Uploaded','3' => 'Transaction ID Set'];
		$bank_account = MerchantBankAccounts::where('status',1)->pluck('name','id')->toArray();
		$merchant = Merchant::whereStatus(true)->pluck('name','id');

               // dd($manage_merchant_checkout->toArray());
		return view('accounts.merchant-checkout.manage-merchant-checkout',compact('manage_merchant_checkout','status','bank_account','merchant'));
	}

	public function upload_bank_doc(Request $request){
		$validation = Validator::make($request->all(), [
			// 'discount_amount' => 'required|numeric',
			'checkout_id' => 'required|numeric',
			'cheque_no' => 'required',
			'doc' => 'required|mimes:jpeg,jpg,png',
			'bank_account' => 'required'

			]);
		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		$id = $request->checkout_id;

		if($request->hasFile('doc')) {
			if ($request->file('doc')->isValid()) {
				$extension = $request->file('doc')->getClientOriginalExtension();
				$fileName = $id.'.'.$extension;
				$url = 'uploads/bank_transaction_doc_merchant_checkout/';
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

		if($bankTransactionDoc->save()){
			MerchantCheckout::where('id',$id)->update(['bank_id' => $request->bank_account,'status' => 2,'cheque_no' => $request->cheque_no,'bank_transection_doc_id' => $bankTransactionDoc->id,'updated_by' => auth()->user()->id]);
			CashCollection::where('merchant_checkout_id',$id)->update(['merchant_bank_account_id' => $request->bank_account,'updated_by' => auth()->user()->id,'status' => 7]);

			/*if($request->discount_amount != 0){

				$checkout = MerchantCheckout::findOrFail($id);
				$total_amount = $checkout->amount + $request->discount_amount;
				$checkout->discount_amount = $request->discount_amount;
				$checkout->total_amount = $total_amount;
				$checkout->save();

			}*/

			Session::flash('message', "File uploaded successfully.");
			return redirect('manage-merchant-checkout');
		}
		else{
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

		MerchantCheckout::where('id',$id)->update(['status' => 3,'bank_transection_id' => $request->transaction_id,'updated_by' => auth()->user()->id]);

		CashCollection::where('merchant_checkout_id',$id)->update(['updated_by' => auth()->user()->id,'status' => 8]);
		Session::flash('message', "Transaction id added successfully.");
		return redirect('manage-merchant-checkout');
	}

	public function invoice_pdf($invoice_no,$id){

		$merchant_data = MerchantCheckout::where('invoice_no',$invoice_no)
		->where('id',$id);
		
		if($merchant_data->count() == 0){
			return redirect()->back()->withErrors("No data found for this invoice no.");
		} else {
			$merchant_checkout = $merchant_data->first();
		}

		$cashCollectionData = CashCollection::where('merchant_checkout_id',$id)->get();
		if(!count($cashCollectionData) > 0){
			return redirect()->back()->withErrors("No data found for this invoice no.");
		}

		// return view('accounts.merchant-checkout.pdf.checkout_invoice',compact('merchant_checkout','cashCollectionData'));

		/*$pdf = PDF::loadView('accounts.merchant-checkout.pdf.checkout_invoice',['merchant_checkout' => $merchant_checkout,'cashCollectionData' => $cashCollectionData])->setPaper('legal', 'landscape');

		return $pdf->stream($merchant_checkout->invoice_no.'-'.$merchant_checkout->merchant->name.'.pdf');*/

		$html = view('accounts.merchant-checkout.pdf.checkout_invoice',compact('merchant_checkout','cashCollectionData'));

		$mpdf = new mPDF('utf-8', 'A4-L');
		$mpdf = new mPDF('utf-8', 'A4-L',0, '', 10, 10, 10, 20, 9, 9 );
		// $mpdf = new mPDF('', 'A4-L', 10, 'Aril', 0, 0, 0, 10, 10, 10, '');
		$footer = 'Biddyut Ltd, HAMID PLAZA, 300/5/A/1 Bir Uttam C.R Datta Road (3rd Floor) Hatirpool, Dhaka-1205, +8809612433988, info@biddyut.com, www.biddyut.com';

		$mpdf->SetAuthor('SDBappi');
		$mpdf->SetCreator('SDBappi');
		$mpdf->SetFooter($footer);
		$mpdf->WriteHTML($html);
		$mpdf->Output($merchant_checkout->invoice_no.'-'.$merchant_checkout->merchant->name.'.pdf',I);
	}

	// Manage merchant check out
	public function get_merchant_discount(Request $request){
		$checkout = MerchantCheckout::findOrFail($request->id);
		echo json_encode($checkout);
	}
	public function discount_merchant_checkout(Request $request){
		$validation = Validator::make($request->all(), [
			'discount_amount' => 'required|numeric',
			'discount_checkout_id' => 'required|numeric',
			'is_showing' => 'required'
			]);

		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$id = $request->discount_checkout_id;
		
		$checkout = MerchantCheckout::findOrFail($id);

		if($request->discount_amount != 0){
			$total_amount = $checkout->amount + $request->discount_amount;
			$checkout->discount_amount = $request->discount_amount;
			$checkout->total_amount = $total_amount;
		}
		
		$checkout->showing_status = $request->is_showing;
		$checkout->discount_remarks = $request->discount_remarks;

		$checkout->save();

		Session::flash('message', "Merchant Discount Added successfully.");
		return redirect('manage-merchant-checkout');
	
	}
}
