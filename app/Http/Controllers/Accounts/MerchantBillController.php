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
use App\MerchantBill;
use App\MerchantBillSubOrder;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PDF;
use Session;
use Validator;
use Auth;
use mPDF;

class MerchantBillController extends Controller
{
	use LogsTrait;
    //
	public function __construct()
	{
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator');
		$this->middleware('role:head_of_accounts|merchantadmin');
	}

	public function index(Request $request){
		// DB::enableQueryLog();
		$query = CashCollection::where('due_amount', '>', 0)->where('status', '!=', 9);
		( $request->has('merchant_id') )  ? $query->where('merchant_id',$request->merchant_id)
		: null;
		if($request->has('store_id')){
			if( $request->store_id != 'all'){
				$query->where('store_id',$request->store_id);
			}

		}

		$invoices = [];
		if($request->has('merchant_id')){
			$merchant_bill = $query->orderBy('id','desc')->paginate(50);
			$stores = CashCollection::select(DB::raw("distinct stores.store_id as storeName, cash_collections.store_id as storeId"))->join('stores','stores.id','=','cash_collections.store_id')
			->where('cash_collections.merchant_id',$request->merchant_id)->pluck('storeName','storeId')->toArray();

			//dd(DB::getQueryLog());
			//dd($store);
			if($request->has('store_id')){
				if( $request->store_id == 'all'){
					$invoices = MerchantBill::where('merchant_id',$request->merchant_id)->pluck('invoice_no','invoice_no')->toArray();
				}
				else{
					$invoices = MerchantBill::where('merchant_id',$request->merchant_id)->where('store_id',$request->store_id)->pluck('invoice_no','invoice_no')->toArray();
				}

			}
		}
		else{
			$store = [];
			$merchant_bill = $query->orderBy('id','desc')->paginate(50);
		}


		$merchants = CashCollection::join('merchants','merchants.id','=','cash_collections.merchant_id')->groupBy('cash_collections.merchant_id')->pluck('merchants.name','cash_collections.merchant_id')->toArray();

		return view('accounts.merchant-bill.index',compact('merchant_bill', 'merchants', 'stores', 'invoices'));
	}


	public function bill_submit(Request $request){
		// dd($request->all());
		$validation = Validator::make($request->all(), [
			'merchant_id' => 'required|numeric',
			'store_id' => 'required',
			// 'account' => 'required',
			'cashCollectionId' => 'required',
		]);
		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$selected_cash_col_id = $request->cashCollectionId;
		$amount = 0;

		foreach($selected_cash_col_id as $val){

			$cash_collection = CashCollection::findOrFail($val);

			if(isset($cash_collection->bill_amount)){
				$temp_amount = $cash_collection->bill_amount;
			}else{
				$temp_amount = 0;
			}

			$amount += $temp_amount;
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
		else {
			$store_id = $request->store_id;
		}

		$invoice_no = $request->merchant_id.date('y').date('m').date('d').rand(1,100);

		if($request->has('invoice')){
			if($request->invoice == 'new'){
				$merchant_bill = new MerchantBill();
				$merchant_bill->created_by = auth()->user()->id;
			}
			else{
				$invoice_no = $request->invoice;
				$merchant_bill = MerchantBill::where('invoice_no',$request->invoice)->first();
				$amount = $amount + $merchant_bill->amount;
				$merchant_bill->updated_by = auth()->user()->id;
			}
		}
		else {
			$merchant_bill = new MerchantBill();
			$merchant_bill->created_by = auth()->user()->id;
		}
		//$merchant_bill = new MerchantBill();
		$merchant_bill->merchant_id = $request->merchant_id;
		$merchant_bill->store_id = $store_id;
		$merchant_bill->amount = $amount;
		$merchant_bill->invoice_no = $invoice_no;

		///dd($merchant_bill->toArray());
		if($merchant_bill->save()){
			foreach($selected_cash_col_id as $val){
				$cashCollection = CashCollection::findOrFail($val);
				$cashCollection->status = 9;
				$cashCollection->merchant_checkout_id = $merchant_bill->id;
				$cashCollection->updated_by = auth()->user()->id;
				$cashCollection->save();

				$merchant_bill_suborder = new MerchantBillSubOrder();
				$merchant_bill_suborder->invoice_no = $invoice_no;
				$merchant_bill_suborder->sub_order_id = $cashCollection->sub_order_id;
				$merchant_bill_suborder->save();
			}
			Session::flash('message', "Merchant bill done successfully ");
		}
		else {
			return redirect()->back()->withErrors('Merchant bill failed');
		}

		return redirect('create-merchant-bill');
	}

	// Manage merchant bill
	public function manage_merchant_bill(Request $request){
		$query = MerchantBill::orderBy('id','desc');
		// ( $request->has('status') )           ? $query->where('status',trim($request->status))  : null;
		($request->has('search_date')) ? $query->whereDate('created_at','=',trim($request->search_date)) : null;
		($request->has('invoice_no')) ? $query->where('invoice_no','=',trim($request->invoice_no)) : null;

		if(Auth::user()->hasRole('merchantadmin')) {
			echo '<pre style="color: red;">';
			print_r(Auth::user()->reference_id);
			echo '</pre>';
			exit();
			
			$query->where('merchant_id','=',trim($request->merchant_id));
		} else {
			($request->has('merchant_id')) ? $query->where('merchant_id','=',trim($request->merchant_id)) : null;
		}


		$manage_merchant_bill = $query->paginate(10);

		$bank_account = MerchantBankAccounts::where('status',1)->pluck('name','id')->toArray();
		$merchant = Merchant::whereStatus(true)->pluck('name','id');

       // dd($manage_merchant_bill->toArray());
		if(Auth::user()->hasRole('merchantadmin')) {
			return view('accounts.merchant-bill.merchant-bills',compact('manage_merchant_bill', 'bank_account', 'merchant'));
		} else {
			return view('accounts.merchant-bill.manage-merchant-bill',compact('manage_merchant_bill', 'bank_account', 'merchant'));
		}
	}

	public function upload_bank_doc(Request $request){
		$validation = Validator::make($request->all(), [
			// 'discount_amount' => 'required|numeric',
			'bill_id' => 'required|numeric',
			'reference_no' => 'required',
			'doc' => 'required|mimes:jpeg,jpg,png'
		]);
		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		$id = $request->bill_id;

		if($request->hasFile('doc')) {
			if ($request->file('doc')->isValid()) {
				$extension = $request->file('doc')->getClientOriginalExtension();
				$fileName = $id.'.'.$extension;
				$url = 'uploads/bank_transaction_doc_merchant_bill/';
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
			MerchantBill::where('id',$id)->update(['status' => 2,'reference_no' => $request->reference_no,'bank_transection_doc_id' => $bankTransactionDoc->id,'updated_by' => auth()->user()->id]);

			Session::flash('message', "File uploaded successfully.");
			return redirect('manage-merchant-bill');
		}
		else{
			return Redirect::back()->withErrors("File upload failed.");
		}
	}

	public function invoice_details(Request $request, $invoice_no){
		$query = MerchantBillSubOrder::select('merchant_bill_suborder.invoice_no',
			'A.unique_suborder_id',
			'B.unique_order_id',
			'B.merchant_order_id',
			'C.store_id',
			'AA.bill_amount'
		)
		->where('merchant_bill_suborder.invoice_no', $invoice_no)
		->leftjoin('sub_orders AS A','A.id','=','merchant_bill_suborder.sub_order_id')
		->leftjoin('cash_collections AS AA','AA.sub_order_id','=','merchant_bill_suborder.sub_order_id')
		->leftjoin('orders AS B', 'B.id','=','A.order_id')
		->leftjoin('stores AS C','C.id','=','B.store_id');
		
		if($request->has('store_id')){
			if( $request->store_id != 'all'){
				$query->where('C.store_id',$request->store_id);
			}
		}
		if($request->has('sub_order_id')){
			if( $request->sub_order_id != 'all'){
				$query->where('A.unique_suborder_id',$request->sub_order_id);
			}
		}
		if($request->has('order_id')){
			if( $request->order_id != 'all'){
				$query->where('B.unique_order_id',$request->order_id);
			}
		}

		$merchantBill = MerchantBill::where('invoice_no', $invoice_no);

		$store = CashCollection::select(DB::raw("distinct stores.store_id as storeName, cash_collections.store_id as storeId"))->join('stores','stores.id','=','cash_collections.store_id');
		
		if($merchantBill->count() > 0){
			$merchantBillInfo = $merchantBill->first();
			$store->where('cash_collections.merchant_id', $merchantBillInfo->merchant_id);
		}
		$stores = $store->pluck('storeName','storeId')->toArray();
		
		$invoice_bills = $query->get();

		return view('accounts.merchant-bill.bill_details',compact('invoice_bills', 'stores'));
	}

	public function set_bank_transaction_id(Request $request){
		$validation = Validator::make($request->all(), [
			'bill_transaction_id' => 'required|numeric',
			'transaction_id' => 'required'
		]);

		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$id = $request->bill_transaction_id;

		MerchantBill::where('id',$id)->update(['status' => 3,'bank_transection_id' => $request->transaction_id,'updated_by' => auth()->user()->id]);

		$merchantBill = MerchantBill::findOrFail($id);

		$merchantBillSubOrders = MerchantBillSubOrder::where('invoice_no', $merchantBill->invoice_no)->get();

		foreach ($merchantBillSubOrders as $subOrder) {
			SubOrder::where('unique_suborder_id',$subOrder->sub_order_id)->update(['sub_order_status' => 45, 'updated_by' => auth()->user()->id]);
		}
		
		Session::flash('message', "Transaction id added successfully.");
		return redirect('manage-merchant-bill');
	}

	public function invoice_pdf($invoice_no, $id){
		$merchant_data = MerchantBill::where('invoice_no',$invoice_no)->where('id',$id);
		
		if($merchant_data->count() == 0){
			return redirect()->back()->withErrors("No data found for this invoice no.");
		} else {
			$merchant_bill = $merchant_data->first();
			$storeInfo = Store::findOrFail($merchant_bill->store_id);

			$store_total_vat = 0;
			if($storeInfo->vat_include == '1') {
				$store_total_vat = (($merchant_bill->amount * $storeInfo->vat_percentage) / 100);
			}
		}

		$cashCollectionInfo = MerchantBillSubOrder::select('C.delivery_name AS customer_name',
					'F.product_title AS product',
					'C.merchant_order_id AS merchant_order_id',
					'B.unique_suborder_id AS tracking_no',
					'B.return',
					'F.quantity',
					'F.weight',
					'G.created_at AS delivery_date',
					'J.title AS delivery_status',
					'H.name AS delivery_zone',
					'I.title AS delivery_hub',
					'A.cod_amount AS cod_amount',
					'A.collected_amount AS collected_amount',
					'A.bill_amount AS delivery_charge',
					'A.cod_charge AS cod_charge',
					'D.vat_include',
					'D.vat_percentage')
					->where('invoice_no', $merchant_bill->invoice_no)
					->leftJoin('cash_collections AS A','A.sub_order_id','=','merchant_bill_suborder.sub_order_id')
					->leftJoin('sub_orders AS B','B.id','=','A.sub_order_id')
					->leftJoin('orders AS C','C.id','=','A.order_id')
					->leftJoin('stores AS D','D.id','=','C.store_id')
					->leftJoin('merchants AS E','E.id','=','D.merchant_id')
					->leftJoin('order_product AS F','F.order_id','=','C.id')
					->leftJoin('delivery_task AS G','G.unique_suborder_id','=','B.unique_suborder_id')
					->leftJoin('zones AS H','H.id','=','C.delivery_zone_id')
					->leftJoin('hubs AS I','I.id','=','B.destination_hub_id')
					->leftJoin('status AS J','J.Id','=','B.sub_order_status')
					->get();

		// dd($cashCollectionInfo);

		/*if($merchantBillSubOrder->count() > 0) {
			$merchantBillSubOrders = $merchantBillSubOrder->get();

			$cashCollectionInfo = array();
			
			foreach ($merchantBillSubOrders as $billSubOrder) {
				$cashCollectionData = SubOrder::select('A.delivery_name AS customer_name',
					'D.product_title AS product',
					'A.merchant_order_id AS merchant_order_id',
					'sub_orders.unique_suborder_id AS tracking_no',
					'D.quantity',
					'D.weight',
					'E.created_at AS delivery_date',
					'I.title AS delivery_status',
					'F.name AS delivery_zone',
					'H.title AS delivery_hub',
					'J.cod_amount AS cod_amount',
					'J.collected_amount AS collected_amount',
					'J.bill_amount AS delivery_charge',
					'J.cod_charge AS cod_charge')
				->where('sub_orders.id', $billSubOrder->sub_order_id)
				->leftJoin('orders AS A','A.id','=','sub_orders.order_id')
				->leftJoin('stores AS B','B.id','=','B.store_id')
				->leftJoin('merchants AS C','C.id','=','B.merchant_id')
				->leftJoin('order_product AS D','D.order_id','=','A.id')
				->leftJoin('delivery_task AS E','E.unique_suborder_id','=','sub_orders.unique_suborder_id')
				->leftJoin('zones AS F','F.id','=','A.delivery_zone_id')
				->leftJoin('hubs AS G','G.id','=','sub_orders.source_hub_id')
				->leftJoin('hubs AS H','H.id','=','sub_orders.destination_hub_id')
				->leftJoin('status AS I','I.Id','=','sub_orders.sub_order_status')
				->leftJoin('cash_collections AS J','J.sub_order_id','=','sub_orders.id')
				->first();

				

				//array_push($cashCollectionInfo, $cashCollectionData);
			}
		}*/


		/*$cashCollectionData = CashCollection::where('merchant_checkout_id',$id)->get();
		if(!count($cashCollectionData) > 0){
			return redirect()->back()->withErrors("No data found for this invoice no.");
		}*/

		// return view('accounts.merchant-bill.pdf.bill_invoice',compact('merchant_bill', 'cashCollectionInfo'));
		$html = view('accounts.merchant-bill.pdf.bill_invoice',compact('merchant_bill', 'cashCollectionInfo', 'store_total_vat'));

		$mpdf = new mPDF('utf-8', 'A4-L');
		$mpdf = new mPDF('utf-8', 'A4-L',0, '', 10, 10, 10, 20, 9, 9 );
		// $mpdf = new mPDF('', 'A4-L', 10, 'Aril', 0, 0, 0, 10, 10, 10, '');
		$footer = 'Biddyut Ltd, HAMID PLAZA, 300/5/A/1 Bir Uttam C.R Datta Road (3rd Floor) Hatirpool, Dhaka-1205, +8809612433988, info@biddyut.com, www.biddyut.com';

		$mpdf->SetAuthor('SDBappi');
		$mpdf->SetCreator('SDBappi');
		$mpdf->SetFooter($footer);
		$mpdf->WriteHTML($html);
		$mpdf->Output($merchant_bill->invoice_no.'.pdf',I);

		/*$pdf = PDF::loadView('accounts.merchant-bill.pdf.bill_invoice',['merchant_bill' => $merchant_bill,'cashCollectionInfo' => $cashCollectionInfo])->setPaper('legal', 'landscape');

		return $pdf->stream($merchant_bill->invoice_no.'.pdf');*/
	}

	// Manage merchant check out
	public function get_bill_discount(Request $request){
		$bill = MerchantBill::findOrFail($request->id);
		echo json_encode($bill);
	}

	public function discount_merchant_bill(Request $request){
		$validation = Validator::make($request->all(), [
			'discount_amount' => 'required|numeric',
			'discount_remarks' => 'required',
			'discount_bill_id' => 'required|numeric'
			]);

		// dd($request->all());
		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}

		$id = $request->discount_bill_id;
		
		$bill = MerchantBill::findOrFail($id);

		if($request->discount_amount != 0){
			$bill->discount_amount = $request->discount_amount;
		}
		$bill->discount_remarks = $request->discount_remarks;

		$bill->save();

		Session::flash('message', "Merchant Discount Added successfully.");
		return redirect('manage-merchant-bill');
	
	}

	// Manage merchant charge
	public function get_bill_charge(Request $request){
		$bill = MerchantBill::findOrFail($request->id);
		echo json_encode($bill);
	}

	public function charge_merchant_bill(Request $request){
		$validation = Validator::make($request->all(), [
			'charge_amount' => 'required|numeric',
			'charge_remarks' => 'required',
			'charge_bill_id' => 'required|numeric'
			]);


		if($validation->fails()) {
			return Redirect::back()->withErrors($validation)->withInput();
		}
		// dd($request->all());

		$id = $request->charge_bill_id;
		
		$bill = MerchantBill::findOrFail($id);

		if($request->charge_amount != 0){
			$bill->additional_charge = $request->charge_amount;
		}
		$bill->additional_charge_remarks = $request->charge_remarks;

		$bill->save();

		Session::flash('message', "Merchant Additional Charge Added successfully.");
		return redirect('manage-merchant-bill');
	
	}

}