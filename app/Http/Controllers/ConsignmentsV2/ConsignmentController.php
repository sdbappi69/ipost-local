<?php

namespace App\Http\Controllers\ConsignmentsV2;

use App\ConsignmentTask;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsTrait;

use App\User;
use DB;

use PDF;
use App\OrderProduct;
use App\ConsignmentCommon;
use App\PickingTask;
use App\DeliveryTask;
use App\Order;
use Validator;
use Excel;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\ReconciliationTrait;
use App\Http\Traits\AjkerDealTrait;

use Session;
use App\SubOrder;
use App\Status;
use App\Reason;
use App\ExtractLog;
use Log;

class ConsignmentController extends Controller
{
    use LogsTrait;
    use SmsTrait;
    use ReconciliationTrait;
    use AjkerDealTrait;

    public function index(Request $request)
    {
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = '2017-03-21';
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = ConsignmentCommon::with('task')->orderBy('id', 'desc')->where('hub_id', auth()->user()->reference_id)->where('status', '!=', 0);
        ($request->has('c_unique_id')) ? $query->where('consignment_unique_id', trim($request->c_unique_id)) : null;
        ($request->has('rider_id')) ? $query->where('rider_id', trim($request->rider_id)) : null;
        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;
        $query->WhereBetween('created_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));
        $consignments = $query->paginate(10);
        $rider = User::select(DB::raw('name'), 'users.id')
            ->join('rider_references','users.id','=','rider_references.user_id')
                ->where('users.status',1)->where('user_type_id', '=', '8')
                ->where('rider_references.reference_id', '=', auth()->user()->reference_id)
                ->lists('name', 'id')->toArray();

        return view('consignmentsv2.index', compact('consignments', 'rider'));

    }

    public function consignmentexport(Request $request, $type)
    {

        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = '2017-03-21';
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }

        $query = ConsignmentCommon::with('task')->orderBy('id', 'desc')->where('hub_id', auth()->user()->reference_id)->where('status', '!=', 0);
        ($request->has('c_unique_id')) ? $query->where('consignment_unique_id', trim($request->c_unique_id)) : null;
        ($request->has('rider_id')) ? $query->where('rider_id', trim($request->rider_id)) : null;
        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;
        $query->WhereBetween('created_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));

        $consignments = $query->get();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = auth()->user()->id;
        $ExtractLog->extract_type = 'Consignments';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('consignments_' . time(), function ($excel) use ($consignments) {
            $excel->sheet('consignments', function ($sheet) use ($consignments) {

                $datasheet = array();
                $datasheet[0] = array('S/N', 'Consignment Unique ID', 'Rider', 'Amount To Collect', 'Amount Collected', 'Picking Quantity', 'Delivery Quantity', 'Return Quantity', 'Created At', 'Status');
                $i = 1;
                foreach ($consignments as $consignment) {

                    $amount_to_collect = 0;
                    $amount_collected = 0;
                    $pickingQuantity = 0;
                    $deliveryQuantity = 0;
                    $returnQuantity = 0;

                    foreach ($consignment->task as $task) {
                        $amount_to_collect += $task->amount;
                        $amount_collected += $task->collected;
                        switch ($task->task_type_id) {
                            case 1:
                            case 5:
                                $pickingQuantity += $task->quantity;
                                break;
                            case 2:
                            case 6:
                                $deliveryQuantity += $task->quantity;
                                break;
                            case 4:
                                $returnQuantity += $task->quantity;
                                break;

                        }
                    }

                    if ($consignment->status == 0) {
                        $status = 'Cancel';
                    } else if ($consignment->status == 1) {
                        $status = 'Ready';
                    } else if ($consignment->status == 2) {
                        $status = 'On The Way';
                    } else if ($consignment->status == 3) {
                        $status = 'Submitted';
                    } else if ($consignment->status == 4) {
                        $status = 'Complete';
                    }

                    $datasheet[$i] = array(
                        $i,
                        $consignment->consignment_unique_id,
                        $consignment->rider->name ?? '',
                        $amount_to_collect,
                        $amount_collected,
                        $pickingQuantity,
                        $deliveryQuantity,
                        $returnQuantity,
                        $consignment->created_at,
                        $status
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

    public function show($id)
    {
        $consignment = ConsignmentCommon::with('task.suborder.product.product_category', 'task.reason', 'rider')->findOrFail($id);
        //dd($consignment);
        return view('consignmentsv2.view', compact('consignment'));
    }

    public function reconciliation($id)
    {
        $consignment = ConsignmentCommon::where('id', $id)->where('status', 3)->first();

        if (!$consignment) {
            abort(404);
        }

        $reasons = Reason::where('type', 'Pickup')->orWhere('type', 'Both')->orderBy('reason', 'asc')->lists('reason', 'id')->toArray();

        // Due Count
        $dueCount = ConsignmentTask::where('consignment_id', $id)->where('reconcile', 0)->where('status', '!=', 2)->count();
        $picking_sub_order_status = Status::where('active', '1')->whereIn('code', [2, 13])->orderBy('id', 'asc')->lists('title', 'id')->toArray();
        $delivery_sub_order_status = Status::where('active', '1')->whereIn('code', [34, 35])->orderBy('id', 'asc')->lists('title', 'id')->toArray();
        $return_sub_order_status = Status::where('active', '1')->where('code', 35)->orderBy('id', 'asc')->lists('title', 'id')->toArray();
        //        dd($consignment);
        return view('consignmentsv2.edit', compact('consignment', 'picking_sub_order_status', 'delivery_sub_order_status', 'return_sub_order_status', 'reasons', 'dueCount'));
    }

    public function reconcile(Request $request)
    {
        $this->validate($request, [
            'task_id' => 'required|exists:consignments_tasks,id'
        ]);
        if ($request->has('due_quantity')) {
            $due_quantity = $request->due_quantity;
        } else {
            $due_quantity = 0;
        }

        // client asked not to set zero quantity for failed reconcile
        // if zero need to alow the following condition can be removed only
        if($due_quantity == 0){
            return redirect()->back()->withErrors("Failed quantity may not be zero!");
        }
        if ($due_quantity != 0 && !$request->has('sub_order_status')) {
            return redirect()->back()->withErrors("Products due. You have to select what to do.");
        }

        $task_id = $request->task_id;

        $consignment_id = $this->reconcileCommon($task_id, $request);

        Session::flash('message', "Operation successful");
        if ($request->has('view')) {
            return redirect('v2consignment/reconciliation/' . $consignment_id . '?view=' . $request->view);
        } else {
            return redirect()->back();
        }

    }

    public function bulkreconcile(Request $request)
    {

        $consignment = Consignment::where('id', $request->consignment_id)->where('status', 3)->first();

        if (count($consignment) == 0) {
            abort(403);
        }

        switch (strtolower($consignment->type)) {

            case 'picking':

                $tasks = PickingTask::where('consignment_id', $consignment->id)->get();

                if (count($tasks) > 0) {

                    foreach ($tasks as $task) {
                        $consignment_id = $this->pickingBulkReconcile($task->id, $request);
                    }

                    // Consignment reconsilation done or ridirect
                    $count = PickingTask::where('consignment_id', $consignment_id)->where('reconcile', 0)->count();
                    if ($count == 0) {
                        $consignment = Consignment::findOrFail($consignment_id);
                        $consignment->status = 4;
                        $consignment->updated_by = auth()->user()->id;
                        $consignment->save();

                        Session::flash('message', "Reconciliation complete");
                        return redirect('/v2consignment');
                    } else {
                        Session::flash('message', "Operation successful");
                        return redirect()->back();
                    }

                } else {
                    return redirect()->back();
                }

            case 'delivery':

                $tasks = DeliveryTask::where('consignment_id', $consignment->id)->get();

                if (count($tasks) > 0) {

                    foreach ($tasks as $task) {
                        $consignment_id = $this->deliveryBulkReconcile($task->id, $request);
                    }

                    // Consignment reconsilation done or ridirect
                    $count = DeliveryTask::where('consignment_id', $consignment_id)->where('reconcile', 0)->count();
                    if ($count == 0) {
                        $consignment = Consignment::findOrFail($consignment_id);
                        $consignment->status = 4;
                        $consignment->updated_by = auth()->user()->id;
                        $consignment->save();

                        Session::flash('message', "Reconciliation complete");
                        return redirect('/v2consignment');
                    } else {
                        Session::flash('message', "Operation successful");
                        return redirect()->back();
                    }

                } else {
                    return redirect()->back();
                }

        }

    }

    public function reconciliationDone($id)
    {
        $consignment = ConsignmentCommon::where('id', $id)->where('status', 3)->first();

        if (!$consignment) {
            abort(403);
        }

        foreach ($consignment->task as $task) {

            if($task->status == 2 && ($task->task_type_id == 2 || $task->task_type_id == 3 || $task->task_type_id == 6 || $task->task_type_id == 7)){
                
                # Require Product
                $product = $task->suborder->product;

                $order_product = OrderProduct::where('id', $product->id)->first();
                $order_product->delivery_paid_amount = $task->amount;
                $order_product->save();

                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 38); // Delivery Completed

            }

        }

        ConsignmentTask::where('consignment_id', $id)
                ->whereStatus(2) // success tasks
                ->update(['reconcile' => 1]);

        $count = ConsignmentTask::where('consignment_id', $id)->where('reconcile', 0)->count();

        if ($count == 0) {
            $consignment = ConsignmentCommon::findOrFail($id);
            $consignment->status = 4;
            $consignment->updated_by = auth()->user()->id;
            $consignment->save();

            Session::flash('message', "Reconciliation complete");
            return redirect('/v2consignment');
        } else {
            Session::flash('message', "All task need to reconcile first");
            return redirect('/v2consignment');
        }

    }

}
