<?php

namespace App\Http\Controllers\Accounts;

use App\CollectedCashAccumulated;
use App\Http\Traits\LogsTrait;
use App\MerchantAccumulatedCash;
use App\ProductCategory;
use App\SubOrder;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;
use App\ExtractLog;
use Excel;
use Auth;

class CollectedCashAmountController extends Controller {

    use LogsTrait;

    public function __construct() {
        $this->middleware('role:hubmanager|head_of_accounts');
    }

    public function index(Request $request) {
        $query = $this->__getQuery($request);

        $cash_collection = $query->paginate(50);

        $rider = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();
        $product_categories = ProductCategory::whereStatus(true)->orderBy('name', 'asc')->lists('name', 'id')->toArray();

        return view('accounts.collected_cash.all', compact('cash_collection', 'rider', 'product_categories'));
    }

    private function __getQuery($request) {
        $query = SubOrder::select('sub_orders.*', 'order_product.product_title', 'order_product.product_category_id', 'users.name as rider_name', 'consignments_tasks.end_time as delivery_time', 'orders.merchant_order_id', 'payment_types.name as payment_name')
                ->leftJoin('order_product', 'order_product.product_unique_id', '=', 'sub_orders.unique_suborder_id')
                ->leftJoin('consignments_tasks', 'consignments_tasks.sub_order_id', '=', 'sub_orders.id')
                ->leftJoin('users', 'consignments_tasks.rider_id', '=', 'users.id')
                ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->leftJoin('payment_types', 'payment_types.id', '=', 'orders.payment_type_id')
                ->whereIn('sub_orders.sub_order_status', [37, 38, 39])
                ->where('sub_orders.accounts', 0)
                ->where('consignments_tasks.task_type_id', 2)
                ->where('sub_orders.collected_cash_status', 0);

        ($request->has('sub_unique_id')) ? $query->where('sub_orders.unique_suborder_id', trim($request->sub_unique_id)) : null;

        ($request->has('product_name')) ? $query->where('product_title', $request->product_name) : null;

        ($request->has('product_category_id')) ? $query->where('product_category_id', $request->product_category_id) : null;

        $query->where('destination_hub_id', auth()->user()->reference_id);

        return $query;
    }

    public function export(Request $request, $type) {
        $query = $this->__getQuery($request);

        $sub_orders = $query->get();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Hub Orders';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('cash_collection' . time(), function($excel) use ($sub_orders) {
                    $excel->sheet('collection', function($sheet) use ($sub_orders) {

                        $datasheet = array();
                        $datasheet[0] = array('Sub-Order Id', 'Product Name', 'Product Category', 'Seller', 'Deliveryman', 'Delivery Time', 'Merchant Order Id', 'Pyment Type', 'Quantity', 'Collected', 'Delivery Amount');
                        $i = 1;
                        foreach ($sub_orders as $c) {

                            $datasheet[$i] = array(
                                $c->unique_suborder_id,
                                $c->product->product_title,
                                $c->product->product_category->name,
                                $c->product->pickup_location->title,
                                $c->rider_name,
                                $c->delivery_time,
                                $c->merchant_order_id,
                                $c->payment_name,
                                $c->product->quantity,
                                $c->product->delivery_paid_amount,
                                $c->product->total_delivery_charge
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

    public function collectedCashAccumulated(Request $request) {
        $validation = Validator::make($request->all(), [
                    'sub_order_id' => 'required',
        ]);
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();
            $subOrder = SubOrder::whereIn('id', $request->sub_order_id);

            $totalQuantity = 0;
            $total_collected_amount = 0;
            $final_total_delivery_charge = 0;
            foreach ($subOrder->get() as $key => $value) {
                $totalQuantity += $value->product->quantity;
                $total_collected_amount += $value->product->delivery_paid_amount;
                $final_total_delivery_charge += $value->product->total_delivery_charge;
            }

            $cashAmount = new CollectedCashAccumulated();
            $cashAmount->batch_id = "HT" . time() . rand(1, 999999);
            $cashAmount->total_quantity = $totalQuantity;
            $cashAmount->total_collected_amount = $total_collected_amount;
            $cashAmount->total_delivery_charge = $final_total_delivery_charge;
            $cashAmount->date = date('Y-m-d');
            $cashAmount->status = 1;
            $cashAmount->transaction_id = $request->transaction_id;
            $cashAmount->remark = $request->remark;
            $cashAmount->save();
            if ($cashAmount) {
                $subOrder->update(['collected_cash_status' => 1, 'collected_cash_accumulated_id' => $cashAmount->id, 'accounts' => 1]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage() . ' | cash collected amount: ' . $request->all());
        }

        Session::flash('message', "Successfully Transfer Cash");
        return redirect('accumulated-collected-cash')->withInput($request->all());
    }

    public function accumulatedCash(Request $request) {
        $data['title'] = "Accumulated Colleted Cash";

        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = date('Y-m-d');
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = CollectedCashAccumulated::WhereBetween('date', array($start_date, $end_date));

        ($request->has('batch_id')) ? $query->where('batch_id', trim($request->batch_id)) : null;

        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;

        $data['accumulateLists'] = $query->paginate(50);
        return view('accounts.collected_cash.accumulated_cash', $data);
    }

    public function accumulatedCashConfirm(Request $request) {
        $data['title'] = "Confirm Accumulated Colleted Cash";

        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = date('Y-m-d');
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = CollectedCashAccumulated::WhereBetween('date', array($start_date, $end_date));

        ($request->has('batch_id')) ? $query->where('batch_id', trim($request->batch_id)) : null;

        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;

        $data['accumulateLists'] = $query->paginate(50);

        return view('accounts.collected_cash.accumulated_cash_confirm', $data);
    }

    public function accumulatedConfirmed(Request $request) {
        $validation = Validator::make($request->all(), [
//            'transaction_id' => 'required',
                    'accumulated_id' => 'required',
        ]);
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();
            $accumulatedConfirm = CollectedCashAccumulated::where('id', $request->accumulated_id)->update(['remark' => $request->remark, 'status' => 2]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage() . ' | Collected Cash Accumulated Confirm: ' . $request->all());
        }

        Session::flash('message', "Successfully Confirm Payment !!!");
        return redirect('accumulated-collected-cash-confirm');
    }

    public function getLists(Request $request) {
        $data['title'] = "Cash Collected For Merchant";

        $query = $this->__getListsQuery($request);

        $data['accumulateLists'] = $query->paginate(50);
        return view('accounts.collected_cash.collected_merchant_accumulated', $data);
    }

    private function __getListsQuery($request) {
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = date('Y-m-d');
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = CollectedCashAccumulated::WhereBetween('date', array($start_date, $end_date))
                ->where('merchant_accumulated_cash_status', 0)
                ->where('status', 2);
        ($request->has('batch_id')) ? $query->where('batch_id', trim($request->batch_id)) : null;
        return $query;
    }

    public function getListsExport(Request $request, $type) {
        $query = $this->__getListsQuery($request);

        $sub_orders = $query->get();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Hub Orders';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('cash_transfer' . time(), function($excel) use ($sub_orders) {
                    $excel->sheet('collection', function($sheet) use ($sub_orders) {

                        $datasheet = array();
                        $datasheet[0] = array('Cash Transfer ID', 'Date', 'Total Qty', 'Collected', 'Delivery Amount', 'Status', 'Transaction ID', 'Remarks');
                        $i = 1;
                        foreach ($sub_orders as $c) {
                            switch ($c->status) {
                                case 1:
                                    $status = 'Pending';
                                    break;
                                case 2:
                                    $status = 'Confirm';
                                    break;
                                default:
                                    $status = 'Inactive';
                                    break;
                            }
                            $datasheet[$i] = array(
                                $c->batch_id,
                                $c->date,
                                $c->total_quantity,
                                $c->total_collected_amount,
                                $c->total_delivery_charge,
                                $status,
                                $c->transaction_id,
                                $c->remark
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

    public function merchantAccumulatedCash(Request $request) {

        $validation = Validator::make($request->all(), [
                    'collected_cash_accumulated_id' => 'required',
        ]);
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();
            $subOrder = CollectedCashAccumulated::whereIn('id', $request->collected_cash_accumulated_id);

            $totalQuantity = 0;
            $total_collected_amount = 0;
            $final_total_delivery_charge = 0;
            foreach ($subOrder->get() as $key => $value) {
                $totalQuantity += $value->total_quantity;
                $total_collected_amount += $value->total_collected_amount;
                $final_total_delivery_charge += $value->total_delivery_charge;
            }

            $cashAmount = new MerchantAccumulatedCash();
            $cashAmount->merchant_batch_id = "MC" . time() . rand(1, 999999);
            $cashAmount->total_quantity = $totalQuantity;
            $cashAmount->total_collected_amount = $total_collected_amount;
            $cashAmount->total_delivery_charge = $final_total_delivery_charge;
            $cashAmount->date = date('Y-m-d');
            $cashAmount->status = 1;
            // $cashAmount->merchant_transaction_id = $request->transaction_id;
            //   $cashAmount->remark = $request->remark;
            $cashAmount->save();
            if ($cashAmount) {
                $subOrder->update(['merchant_accumulated_cash_status' => 1, 'merchant_accumulated_cash_id' => $cashAmount->id]);
            }
            DB::commit();
            Session::flash('message', "Accumulated Cash For Merchant Successfully ");
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('waning', "Something Wrong Accumulated Cash For Merchant");
            \Log::error($e->getMessage());
        }
        return redirect('collected-cash-merchant-confirm')->withInput($request->all());
    }

    public function accumulatedMerchantCashConfirm(Request $request) {
        $data['title'] = "Confirm Accumulated Colleted Cash";

        $query = $this->__getCashConfirmQuery($request);

        $data['accumulateLists'] = $query->paginate(50);

        return view('accounts.collected_cash.merchant_accumulated_cash_confirm', $data);
    }

    private function __getCashConfirmQuery($request) {
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = date('Y-m-d');
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = MerchantAccumulatedCash::WhereBetween('date', array($start_date, $end_date));
        ($request->has('merchant_batch_id')) ? $query->where('merchant_batch_id', trim($request->merchant_batch_id)) : null;

        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;
        return $query;
    }

    public function accumulatedMerchantCashConfirmExport(Request $request, $type) {
        $query = $this->__getCashConfirmQuery($request);

        $sub_orders = $query->get();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Hub Orders';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('cash_confirm' . time(), function($excel) use ($sub_orders) {
                    $excel->sheet('collection', function($sheet) use ($sub_orders) {

                        $datasheet = array();
                        $datasheet[0] = array('Merchant Checkout ID', 'Date', 'Total Qty', 'Collected', 'Delivery Amount', 'Status', 'Transaction ID', 'Remarks');
                        $i = 1;
                        foreach ($sub_orders as $c) {
                            switch ($c->status) {
                                case 1:
                                    $status = 'Pending';
                                    break;
                                case 2:
                                    $status = 'Confirm';
                                    break;
                                default:
                                    $status = 'Inactive';
                                    break;
                            }
                            $datasheet[$i] = array(
                                $c->merchant_batch_id,
                                $c->date,
                                $c->total_quantity,
                                $c->total_collected_amount,
                                $c->total_delivery_charge,
                                $status,
                                $c->merchant_transaction_id,
                                $c->remark
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

    public function merchantCashConfirm(Request $request) {
        $validation = Validator::make($request->all(), [
//            'transaction_id' => 'required',
                    'merchant_accumulated_id' => 'required',
        ]);
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();
            $accumulatedConfirm = MerchantAccumulatedCash::where('id', $request->merchant_accumulated_id)
                    ->update(['remark' => $request->remark, 'status' => 2]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage() . ' | Collected Cash Accumulated Confirm: ' . $request->merchant_accumulated_id);
        }

        Session::flash('message', "Confirm successfully ");
        return redirect('collected-cash-merchant-confirm');
    }

    public function merchantCashFinalLists(Request $request) {
        $data['title'] = "Merchant Final Accumulated Cash";
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = date('Y-m-d');
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = MerchantAccumulatedCash::WhereBetween('date', array($start_date, $end_date))->where('status', 2);
        ($request->has('merchant_batch_id')) ? $query->where('merchant_batch_id', trim($request->merchant_batch_id)) : null;

        $data['finalMerchantAccumulatedCash'] = $query->paginate(50);

        return view('accounts.collected_cash.merchant_accumulated_cash_final', $data);
    }

    public function collectionCashDetails(Request $request, $id) {

        $data['titile'] = "Cash Collection Details";
        $query = SubOrder::select('sub_orders.*', 'order_product.product_title', 'order_product.product_category_id', 'users.name as rider_name', 'consignments_tasks.end_time as delivery_time', 'orders.merchant_order_id', 'payment_types.name as payment_name')
                ->leftJoin('order_product', 'order_product.product_unique_id', '=', 'sub_orders.unique_suborder_id')
                ->leftJoin('consignments_tasks', 'consignments_tasks.sub_order_id', '=', 'sub_orders.id')
                ->leftJoin('users', 'consignments_tasks.rider_id', '=', 'users.id')
                ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->leftJoin('payment_types', 'payment_types.id', '=', 'orders.payment_type_id')
                ->whereIn('sub_orders.sub_order_status', [37, 38, 39])
                ->where('sub_orders.accounts', 1)
                ->where('sub_orders.collected_cash_status', 1)
                ->where('consignments_tasks.task_type_id', 2)
                ->where('sub_orders.collected_cash_accumulated_id', $id);

        if ($query->count() <= 0) {
            $merchantCheckOutId = CollectedCashAccumulated::where('merchant_accumulated_cash_id', $id)->first()->id;
            $query = SubOrder::select('sub_orders.*', 'order_product.product_title', 'order_product.product_category_id')
                    ->leftJoin('order_product', 'order_product.product_unique_id', '=', 'sub_orders.unique_suborder_id')
                    ->whereIn('sub_orders.sub_order_status', [37, 38, 39])
                    ->where('sub_orders.accounts', 1)
                    ->where('sub_orders.collected_cash_status', 1)
                    ->where('sub_orders.collected_cash_accumulated_id', $merchantCheckOutId);
        }
        ($request->has('sub_unique_id')) ? $query->where('sub_orders.unique_suborder_id', trim($request->sub_unique_id)) : null;

        ($request->has('product_name')) ? $query->where('product_title', $request->product_name) : null;

        ($request->has('product_category_id')) ? $query->where('product_category_id', $request->product_category_id) : null;

        $data['callectionCashDetails'] = $query->paginate(50);
        $data['product_categories'] = ProductCategory::whereStatus(true)->orderBy('name', 'asc')->lists('name', 'id')->toArray();

        return view('accounts.collected_cash.cash_collection_details', $data);
    }

}
