<?php

namespace App\Http\Controllers\Consignments;

use App\ConsignmentCommon;
use App\ConsignmentTask;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsTrait;
use App\User;
use DB;
use Illuminate\Support\Facades\Log;
use PDF;
use App\OrderProduct;
use App\Consignment;
use App\PickingTask;
use App\DeliveryTask;
use App\Order;
use Validator;
use App\Http\Traits\LogsTrait;
use Session;
use App\SubOrder;
use mPDF;

class ConsignmentsControllerAll extends Controller {

    use LogsTrait;
    use SmsTrait;

    //
    public function __construct() {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|inboundmanager|vehiclemanager|merchantadmin');
    }

    //
    public function index() {
        
    }

    public function followup_consignments($id) {

        $consignment = Consignment::findorfail($id);

        if ($consignment->type == 'picking') {
            $tasks = PickingTask::where('consignment_id', $consignment->id)->get();
        } else {
            $tasks = DeliveryTask::where('consignment_id', $consignment->id)->get();
        }

        // foreach ($tasks as $task) {
        // 	return $task->start_time;
        // }

        return view('consignments.followup', compact('consignment', 'tasks'));
    }

    public function all_pick_up_cl(Request $request) {

        $query = Consignment::orderBy('id', 'desc')->where('hub_id', auth()->user()->reference_id)->where('status', '!=', 0);
        ($request->has('c_unique_id')) ? $query->where('consignment_unique_id', trim($request->c_unique_id)) : null;
        ($request->has('type')) ? $query->where('type', trim($request->type)) : null;
        ($request->has('rider_id')) ? $query->where('rider_id', trim($request->rider_id)) : null;
        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;
        ($request->has('search_date')) ? $query->whereDate('created_at', '=', trim($request->search_date)) : null;

        $consignments = $query->paginate(10);


        $rider = User::
                        select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'), 'users.id')
                        ->leftJoin('hubs', 'hubs.id', '=', 'users.reference_id')
                        ->where('reference_id', '=', auth()->user()->reference_id)
                        ->where('users.status', true)->where('users.user_type_id', '=', '8')->lists('name', 'users.id')->toArray();
        //dd($consignments->toArray());
        return view('consignments.all', compact('consignments', 'rider'));
    }

    public function view_consignment($id) {
        $consignment = ConsignmentCommon::findorfail($id);

//        return view('consignments.pdf.consignments-view', ['consignment' => $consignment]);
        $pdf = PDF::loadView('consignments.pdf.consignments-view', ['consignment' => $consignment])->setPaper('a4', 'landscape');

        return $pdf->stream($consignment->consignment_unique_id . '_consignment.pdf');
    }

    public function all_invoice($id, $type) {
        $picking = Consignment::findorfail($id);
        if ($type === 'picking') {

            $products = OrderProduct::select(array(
                        'order_product.id AS id',
                        'order_product.product_unique_id',
                        'order_product.product_title',
                        'order_product.quantity',
                        'order_product.picking_date',
                        'order_product.sub_total',
                        'order_product.unit_deivery_charge',
                        'order_product.unit_price',
                        'order_product.total_payable_amount',
                        'pl.title',
                        'pl.msisdn',
                        'pl.alt_msisdn',
                        'pl.address1',
                        'pt.start_time',
                        'pt.end_time',
                        'pc.name AS product_category',
                        'z.name AS zone_name',
                        'c.name AS city_name',
                        's.name AS state_name',
                        'm.name as merchant_name',
                        'm.email as merchant_email',
                        'm.msisdn as merchant_msisdn',
                        'o.delivery_name AS cus_name',
                        'o.delivery_email as cus_email',
                        'o.delivery_msisdn as cus_msisdn',
                        'o.delivery_alt_msisdn as cus_alt_msisdn',
                        'o.merchant_order_id',
                        'o.delivery_address1',
                        'o.order_remarks',
                        'o.created_at as order_created_at',
                        'dz.name as delivery_zone',
                        'dc.name as delivery_city',
                        'so.unique_suborder_id',
                        'so.return',
                        'so.created_at',
                        'so.sub_order_note',
                        'st.store_id AS store_name',
                    ))
                    ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                    ->leftJoin('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                    ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                    ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                    ->leftJoin('zones AS dz', 'dz.id', '=', 'o.delivery_zone_id')
                    ->leftJoin('cities AS dc', 'dc.id', '=', 'dz.city_id')
                    ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                    ->leftJoin('stores AS st', 'st.id', '=', 'o.store_id')
                    ->leftJoin('merchants AS m', 'm.id', '=', 'st.merchant_id')
                    ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id')
                    ->leftJoin('cities AS c', 'c.id', '=', 'z.city_id')
                    ->leftJoin('states AS s', 's.id', '=', 'c.state_id')
                    // ->where('order_product.status', '=', '1')
                    ->whereIn('order_product.product_unique_id', function ($q) use ($picking) {
                        $q->from('picking_task')
                        ->selectRaw("product_unique_id")
                        ->where('consignment_id', $picking->id)->get();
                    })
                    // ->orderBy('order_product.id', 'desc')
                    ->get();

            $pdf = PDF::loadView('consignments.pdf.consignments-all-invoice-pickup', ['products' => $products, 'picking' => $picking])->setPaper([0, 0, 250, 750], 'portrait');
        } elseif ($type === 'delivery') {
            $sub_orders = SubOrder::whereStatus(true)
                    ->orderBy('id', 'desc')
                    ->where('destination_hub_id', '=', auth()->user()->reference_id)
                    ->where('deliveryman_id', '!=', null)
                    ->whereIn('unique_suborder_id', function ($q) use ($picking) {
                        $q->from('delivery_task')
                        ->selectRaw('unique_suborder_id')
                        ->where('consignment_id', $picking->id)->get();
                    })
                    ->get();
            $pdf = PDF::loadView('consignments.pdf.consignments-all-invoice-delivery', ['sub_orders' => $sub_orders, 'picking' => $picking])->setPaper([0, 0, 250, 750], 'portrait');
        } else {
            return redirect()->back()->withErrors('Invalid Action.');
        }


        return $pdf->stream($picking->consignment_unique_id . '_invoice.pdf');
    }

    public function start_consignments($id) {
        $consignments = ConsignmentCommon::findorfail($id);
        try {
            DB::beginTransaction();
            $consignments->status = 2;
            $consignments->save();

            $tasks = ConsignmentTask::whereStatus(0)->whereConsignmentId($id)->get();

            foreach ($tasks as $task) {
                $sms = "";
                $sms2 = "";
                $merchantOrderPrefix = substr($task->suborder->order->merchant_order_id, 0, 1);
                switch ($merchantOrderPrefix) {
                    case 3:
                        // arabic
                        $sms = "منتجك في الطريق. كود التوصيل الخاص بك : $task->otp";
                        $sms2 = "يتم إرجاع منتجك.رمز أمان فاست بازار الخاص بك هو: $task->otp";
                        break;
                    case 2:
                        // kurdis
                        $sms = "کاڵاکەت لە ڕێگادایە. کۆدی گەیاندنت: $task->otp";
                        $sms2 = " کاڵاکەت دەگەڕێنرێتەوە. کۆدی دڵنیابوونی فاست بازاڕت: $task->otp";
                        break;
                    default:
                        $sms = "Your product is on the way. Your FastBazzar Delivery code is: " . $task->otp;
                        $sms2 = "Your product is being returned. Your FastBazzar security code is: " . $task->otp;
                        break;
                }

                switch ($task->task_type_id) {
                    case 1:
                        $this->suborderStatus($task->sub_order_id, '4');
                        break;
                    case 2:
                        $this->suborderStatus($task->sub_order_id, '29');

                        // Send SMS
                        $this->sendCustomMessage($task->suborder->order->delivery_msisdn, $sms, $task->suborder->id);
                        break;
                    case 4:
                        $this->suborderStatus($task->sub_order_id, '36');
                        $this->sendCustomMessage($task->suborder->order->products->pickup_location->msisdn, $sms2, $task->suborder->id);
                        break;
                }

                $task->save();
            }
            DB::commit();
            Session::flash('message', "Consignment on the way.");
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return redirect()->back()->withErrors("Something went wrong, please try again.");
        }
    }

    public function cancel_consignments($id) {
        $consignments = ConsignmentCommon::findorfail($id);
        try {
            DB::beginTransaction();
            $consignments->status = 0;
            $consignments->save();

            $tasks = ConsignmentTask::whereStatus(0)->whereConsignmentId($id)->get();

            foreach ($tasks as $task) {
                switch ($task->task_type_id) {
                    case 1:
                        $this->suborderStatus($task->sub_order_id, '2');
                        break;
                    case 2:
                        $this->suborderStatus($task->sub_order_id, '26');
                        break;
                    case 4:
                        $this->suborderStatus($task->sub_order_id, '26');
                        break;
                }

                $task->status = 3; //cancel task
                $task->save();
            }
            DB::commit();
            Session::flash('message', "Consignment canceled.");
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return redirect()->back()->withErrors("Something went wrong, please try again.");
        }
    }

    public function edit_consignments($id) {
        //dd($id);
        $consignments = Consignment::findorfail($id);

        if ($consignments->type == 'picking') {
            $selected_products = OrderProduct::select(array(
                                'order_product.id AS id',
                                'order_product.product_unique_id',
                                'order_product.product_title',
                                'order_product.quantity',
                                'order_product.picking_date',
                                'order_product.sub_total',
                                'pl.title',
                                'pl.msisdn',
                                'pl.alt_msisdn',
                                'pl.address1',
                                'pt.start_time',
                                'pt.end_time',
                                'pc.name AS product_category',
                                'z.name AS zone_name',
                                'c.name AS city_name',
                                's.name AS state_name',
                                'm.name as merchant_name',
                                'm.email as merchant_email',
                                'm.msisdn as merchant_msisdn',
                            ))
                            ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                            ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                            ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                            ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                            ->join('stores AS st', 'st.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 'st.merchant_id')
                            ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                            ->join('cities AS c', 'c.id', '=', 'z.city_id')
                            ->join('states AS s', 's.id', '=', 'c.state_id')
                            //->where('order_product.status', '=', '1')
                            ->whereIn('order_product.product_unique_id', function ($q) use ($consignments) {
                                $q->from('picking_task')
                                ->selectRaw('product_unique_id')
                                ->where('consignment_id', $consignments->id)->get();
                            })
                            ->orderBy('order_product.id', 'desc')->get();


            // rest of pickup
            $products = OrderProduct::select(array(
                                'order_product.id AS id',
                                'order_product.product_unique_id',
                                'order_product.product_title',
                                'order_product.quantity',
                                'order_product.picking_date',
                                'order_product.sub_total',
                                'pl.title',
                                'pl.msisdn',
                                'pl.alt_msisdn',
                                'pl.address1',
                                'pt.start_time',
                                'pt.end_time',
                                'pc.name AS product_category',
                                'z.name AS zone_name',
                                'c.name AS city_name',
                                's.name AS state_name',
                                'm.name as merchant_name',
                                'm.email as merchant_email',
                                'm.msisdn as merchant_msisdn',
                                'o.delivery_name AS cus_name',
                                'o.delivery_email as cus_email',
                                'o.delivery_msisdn as cus_msisdn',
                                'o.delivery_alt_msisdn as cus_alt_msisdn',
                            ))
                            ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                            ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                            ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                            ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                            ->join('stores AS st', 'st.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 'st.merchant_id')
                            ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                            ->join('cities AS c', 'c.id', '=', 'z.city_id')
                            ->join('states AS s', 's.id', '=', 'c.state_id')
                            ->where('o.order_status', '=', '2')
                            ->where('order_product.status', '=', '1')
                            ->orderBy('order_product.id', 'desc')
                            ->where('z.hub_id', '=', auth()->user()->reference_id)->get();

            //dd($selected_products->toArray());
            //dd($products->toArray());


            $products = $products->merge($selected_products);
            foreach ($products as $p) {
                //dd($p->product_unique_id);
                //var_dump($p->id); die();
                //echo $p->id."<br>";
                //var_dump($selected_products->search($p->id));
                if ($selected_products->id->search(1)) {
                    echo "ase<br>";
                } else {
                    echo "nai<br>";
                }
            }
            die();
            //dd($products);

            $pickupman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

            return view('consignments.pick-up.edit', compact('pickupman', 'selected_products', 'products', 'consignments'));
        } elseif ($consignments->type == 'delivery') {
            
        } else {
            return redirect()->back()->withErrors('Invalid consignment type.');
        }

        dd($consignments->toArray());
    }

    public function suborder_invoice($unique_suborder_id) {

        $sub_order = SubOrder::whereStatus(true)
                // ->where('destination_hub_id', '=', auth()->user()->reference_id)
                // ->where('deliveryman_id', '!=', null)
                ->where('unique_suborder_id', $unique_suborder_id)
                ->first();
        $pdf = PDF::loadView('consignments.pdf.sub_order-invoice-delivery', ['sub_order' => $sub_order])->setPaper([0, 0, 250, 750], 'portrait');

        return $pdf->stream($sub_order->unique_suborder_id . '_invoice.pdf');
    }

    public function common_awb_single($sub_order_id) {

        $sub_order = SubOrder::whereStatus(true)
                // ->where('destination_hub_id', '=', auth()->user()->reference_id)
                // ->where('deliveryman_id', '!=', null)
                ->where('id', $sub_order_id)
                ->first();
        $pdf = PDF::loadView('consignments.pdf.sub_order-common-single-awb', ['sub_order' => $sub_order])->setPaper([0, 0, 250, 750], 'portrait');

        return $pdf->stream($sub_order->unique_suborder_id . '_invoice.pdf');
    }

    public function common_invoice_single($sub_order_id) {

        $sub_order = SubOrder::whereStatus(true)
                // ->where('destination_hub_id', '=', auth()->user()->reference_id)
                // ->where('deliveryman_id', '!=', null)
                ->where('id', $sub_order_id)
                ->first();
        $pdf = PDF::loadView('consignments.pdf.sub_order-common-single', ['sub_order' => $sub_order])->setPaper([0, 0, 250, 750], 'portrait');

        return $pdf->stream($sub_order->unique_suborder_id . '_invoice.pdf');
    }

    public function common_awb_multi(Request $request, $consignment_id) {
        if ($request->has('page')) {
            $take = 15;
            if ($request->page == 1) {
                $skip = 0;
            } else {
                $skip = ($request->page - 1) * 15;
            }
            $consignment = ConsignmentCommon::with(['task' => function ($q) use ($take, $skip) {
                            $q->take($take)->skip($skip);
                        }])
                    ->findOrfail($consignment_id);
        } else {
            $consignment = ConsignmentCommon::with('task')->findOrfail($consignment_id);
        }
//        return view('consignments.pdf.sub_order-common-multi', compact('consignment'));
        $pdf = PDF::loadView('consignments.pdf.sub_order-common-multi-awb', ['consignment' => $consignment])->setPaper([0, 0, 250, 750], 'portrait');

        return $pdf->stream($consignment->consignment_unique_id . '_invoice.pdf');
    }

    public function common_invoice_multi(Request $request, $consignment_id) {
        if ($request->has('page')) {
            $take = 15;
            if ($request->page == 1) {
                $skip = 0;
            } else {
                $skip = ($request->page - 1) * 15;
            }
            $consignment = ConsignmentCommon::with(['task' => function ($q) use ($take, $skip) {
                            $q->take($take)->skip($skip);
                        }])
                    ->findOrfail($consignment_id);
        } else {
            $consignment = ConsignmentCommon::with('task')->findOrfail($consignment_id);
        }
//        return view('consignments.pdf.sub_order-common-multi', compact('consignment'));
        $pdf = PDF::loadView('consignments.pdf.sub_order-common-multi', ['consignment' => $consignment])->setPaper([0, 0, 250, 750], 'portrait');

        return $pdf->stream($consignment->consignment_unique_id . '_invoice.pdf');
    }

}
