<?php


namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\OrderProduct;
use App\Reason;
use App\Status;
use App\Store;
use App\SubOrder;
use App\User;
use App\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\ReconciliationTrait;

class DeliveryOfficeController extends Controller
{
    use LogsTrait, ReconciliationTrait;

    public function index(Request $request)
    {
        $query = SubOrder::select(
            'sub_orders.id AS suborder_id',
            'sub_orders.unique_suborder_id',
            'sub_orders.order_id',
            'sub_orders.no_of_delivery_attempts',
            'sub_orders.return',
            'sub_orders.sub_order_status',
            'sub_orders.sub_order_last_status',
            'sub_orders.sub_order_note',
            'orders.unique_order_id',
            'orders.delivery_name',
            'orders.delivery_email',
            'orders.delivery_msisdn',
            'orders.delivery_alt_msisdn',
            'orders.delivery_address1 AS delivery_address',
            'orders.created_at',
            'orders.merchant_order_id',
            // 'hubs_d.title AS delivery_hub',
            'stores.store_id AS store_name',
            // 'merchants.name AS merchant_name',
            // 'zones_d.name AS delivery_zone',
            // 'cities_d.name AS delivery_city',
            // 'states_d.name AS delivery_state',
            'order_product.product_title',
            'order_product.quantity',
            'order_product.picking_attempts',
            'order_product.weight',
            'order_product.sub_total',
            'order_product.delivery_paid_amount'
        // 'cart_product.weight AS proposed_weight',
        // 'cart_product.weight AS proposed_weight',
        // 'product_categories.name AS product_category',
        // 'pickup_locations.title AS pickup_name',
        // 'pickup_locations.email AS pickup_email',
        // 'pickup_locations.msisdn AS pickup_msisdn',
        // 'pickup_locations.alt_msisdn AS pickup_alt_msisdn',
        // 'pickup_locations.address1 AS pickup_address',
        // 'hubs_p.title AS pickup_hub',
        // 'zones_p.name AS pickup_zone',
        // 'cities_p.name AS pickup_city',
        // 'states_p.name AS pickup_state',
        // 'status.title AS sub_order_status'
        // 'p_reason.reason AS d_reason',
        // 'd_reason.reason AS d_reason',
        // 'delivery_task.updated_at AS final_delivery_attempt'
        )
            ->where('sub_orders.delivery_from_office', '=', 1)
            ->where('sub_orders.status', '!=', 0)
            ->where('sub_orders.sub_order_status', '=', 26)
            ->orWhere('sub_orders.sub_order_status', '=', 27)
            ->where('sub_orders.parent_sub_order_id', 0)
            ->where(function ($query) {
                $query->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id);
                $query->orWhere('sub_orders.source_hub_id', '=', auth()->user()->reference_id);
                $query->orWhere('sub_orders.next_hub_id', '=', auth()->user()->reference_id);
            })
            // ->leftJoin('delivery_task','delivery_task.unique_suborder_id','=','sub_orders.unique_suborder_id')
            ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
            // ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
            // ->leftJoin('zones AS zones_d','zones_d.id','=','orders.delivery_zone_id')
            // ->leftJoin('cities AS cities_d','cities_d.id','=','orders.delivery_city_id')
            // ->leftJoin('states AS states_d','states_d.id','=','orders.delivery_state_id')
            ->leftJoin('order_product', 'order_product.sub_order_id', '=', 'sub_orders.id')
            // ->leftJoin('picking_task','picking_task.product_unique_id','=','order_product.product_unique_id')
            // ->leftJoin('cart_product','cart_product.order_product_id','=','order_product.id')
            // ->leftJoin('pickup_locations','pickup_locations.id','=','order_product.pickup_location_id')
            // ->leftJoin('zones AS zones_pl','zones_pl.id','=','pickup_locations.zone_id')
            // ->leftJoin('product_categories','product_categories.id','=','order_product.product_category_id')
            // ->leftJoin('zones AS zones_p','zones_p.id','=','pickup_locations.zone_id')
            // ->leftJoin('cities AS cities_p','cities_p.id','=','pickup_locations.city_id')
            // ->leftJoin('states AS states_p','states_p.id','=','pickup_locations.state_id')

            // ->leftJoin('reasons AS p_reason','picking_task.reason_id','=','p_reason.id')
            // ->leftJoin('reasons AS d_reason','delivery_task.reason_id','=','d_reason.id')

            ->leftJoin('status', 'status.code', '=', 'sub_orders.sub_order_status');
        // ->leftJoin('hubs AS hubs_p','hubs_p.id','=','zones_pl.hub_id')
        // ->leftJoin('hubs AS hubs_d','hubs_d.id','=','zones_d.hub_id');

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

        if ($request->has('sub_order_status')) {

            $query->leftJoin('order_logs', 'order_logs.sub_order_id', '=', 'sub_orders.id');

            $codes = array();
            foreach ($request->sub_order_status as $sos) {
                $ocs = hubWhereInStatus($sos);
                foreach ($ocs as $sos_codes) {
                    $codes[] = $sos_codes;
                }
            }

            $statusInfo = Status::whereIn('code', $codes)->get();
            $whereStatusText = array();
            foreach ($statusInfo as $si) {
                $whereStatusText[] = $si->title;
            }

            $query->whereIn('order_logs.text', $whereStatusText)
                ->where('order_logs.created_at', '!=', '0000-00-00 00:00:00')
                ->WhereBetween('order_logs.created_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));

        } else {
            $query->where('sub_orders.updated_at', '!=', '0000-00-00 00:00:00')->WhereBetween('sub_orders.updated_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));
        }

        if ($request->has('order_id')) {
            $query->where('orders.unique_order_id', $request->order_id);
        }

        if ($request->has('merchant_order_id')) {
            $query->where('orders.merchant_order_id', $request->merchant_order_id);
        }

        if ($request->has('sub_order_id')) {
            $query->where('sub_orders.unique_suborder_id', $request->sub_order_id);
        }

        if ($request->has('customer_mobile_no')) {
            $query->where('orders.delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if ($request->has('store_id')) {
            $query->whereIn('orders.store_id', $request->store_id);
        }

        if ($request->has('merchant_id')) {
            $query->whereIn('stores.merchant_id', $request->merchant_id);
        }

        if ($request->has('pickup_man_id')) {
            $query->whereIn('order_product.picker_id', $request->pickup_man_id);
        }

        if ($request->has('delivary_man_id')) {
            $query->whereIn('sub_orders.deliveryman_id', $request->delivary_man_id);
        }

        // if($request->has('pickup_zone_id')){
        //     $query->where('pickup_locations.zone_id', $request->pickup_zone_id);
        // }

        // if($request->has('delivery_zone_id')){
        //     $query->where('orders.delivery_zone_id', $request->delivery_zone_id);
        // }


        // if($request->all()){
        $sub_orders = $query->groupBy('sub_orders.id')->orderBy('sub_orders.id', 'desc')->paginate(10);
        // }else{
        //     $sub_orders = null;
        // }


        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
        $sub_order_status = hubAllStatus();
        // pick man

        $pickupman = User::select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'), 'users.id')
            ->leftJoin('hubs', 'hubs.id', '=', 'users.reference_id')
            ->where('reference_id', '=', auth()->user()->reference_id)
            ->where('users.status', true)->where('users.user_type_id', '=', '8')->lists('name', 'users.id')->toArray();

        $zones = Zone::select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'), 'zones.id')
            ->leftJoin('cities', 'cities.id', '=', 'zones.city_id')->
            where('zones.status', true)->lists('name', 'zones.id')->toArray();

        return view('hub-orders.delivery-from-office.index', compact('pickupman', 'merchants', 'stores', 'sub_order_status', 'sub_orders', 'zones'));

    }

    public function delivery($id)
    {
        $deliveryProduct = SubOrder::select(
            'sub_orders.id AS suborder_id',
            'sub_orders.unique_suborder_id',
            'sub_orders.order_id',
            'sub_orders.no_of_delivery_attempts',
            'sub_orders.return',
            'sub_orders.sub_order_status',
            'sub_orders.sub_order_last_status',
            'status.title as status',
            'product_categories.name as product_category',
            'orders.unique_order_id',
            'orders.delivery_name',
            'orders.delivery_email',
            'orders.delivery_msisdn',
            'orders.delivery_alt_msisdn',
            'orders.delivery_address1 AS delivery_address',
            'orders.created_at',
            'orders.merchant_order_id',
            'stores.store_id AS store_name',
            'order_product.product_title',
            'order_product.quantity',
            'order_product.picking_attempts',
            'order_product.weight',
            'order_product.sub_total',
            'order_product.delivery_paid_amount',
            'order_product.total_payable_amount'
        )
            ->where('sub_orders.id', $id)
            ->where('sub_orders.delivery_from_office', '=', 1)
            ->where('sub_orders.status', '!=', 0)
            ->where('sub_orders.sub_order_status', '=', 26)
            ->orWhere('sub_orders.sub_order_status', '=', 27)
            ->where('sub_orders.parent_sub_order_id', 0)
            ->where(function ($query) {
                $query->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id);
                $query->orWhere('sub_orders.source_hub_id', '=', auth()->user()->reference_id);
                $query->orWhere('sub_orders.next_hub_id', '=', auth()->user()->reference_id);
            })
            ->leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
            ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
//            ->leftJoin('order_product', 'order_product.sub_order_id', '=', 'sub_orders.id')
            ->leftJoin('order_product', 'order_product.product_unique_id', '=', 'sub_orders.unique_suborder_id')
            ->leftJoin('product_categories', 'order_product.product_category_id', '=', 'product_categories.id')
            ->leftJoin('status', 'status.code', '=', 'sub_orders.sub_order_status')
            ->first();

        if (!$deliveryProduct) {
            abort(404);
        }
//dd($deliveryProduct);
        $reasons = Reason::where('type', 'Pickup')->orWhere('type', 'Both')->orderBy('reason', 'asc')->lists('reason', 'id')->toArray();
        $sub_order_status_list = Status::where('active', '1')->whereIn('code', [34, 35])->orderBy('id', 'asc')->lists('title', 'id')->toArray();
        return view('hub-orders.delivery-from-office.confirm-delivery', compact('deliveryProduct', 'reasons', 'sub_order_status_list'));
    }

    public function confirmDelivery(Request $request, $id)
    {
        $this->validate($request, [
            'filled_quantity' => 'required|numeric|min:0',
            'paid_amount' => 'required',
            'due_quantity' => 'required|numeric|min:0',
        ]);
        $subOrder = SubOrder::findOrFail($id);

        if ($request->filled_quantity > $subOrder->product->quantity or $request->due_quantity > $subOrder->product->quantity or ($subOrder->product->quantity != ($request->filled_quantity + $request->due_quantity))) {
            return redirect()->back()->withErrors('Invalid Quantity');
        }

        if ($request->due_quantity > 0) {
            $this->validate($request, [
                'sub_order_status' => 'required|exists:status,code',
                'reason_id' => 'required|exists:reasons,id',
                'remarks' => 'required',
            ]);
        }
        try {
            DB::beginTransaction();
            if ($request->due_quantity > 0) {
                $subOrder->reason_id = $request->reason_id;
                $subOrder->remarks = $request->remarks;
                $subOrder->save();

                if ($request->due_quantity == $subOrder->product->quantity) {
                    $this->failedDelivery($request, $subOrder);
                } else {
                    $this->pertialDelivery($request, $subOrder);
                }

            } else {
                $this->successDelivery($request, $subOrder);
            }
            DB::commit();
            return redirect(url('office-delivery-list'))->with('message', 'Delivery Success');
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return redirect()->back()->withErrors('Something went wrong, please try again.');
        }
    }

    private function successDelivery($request, $subOrder)
    {
        $order_product = OrderProduct::where('id', $subOrder->product->id)->first();
        $order_product->delivery_paid_amount = $request->paid_amount;
        $order_product->save();

        $this->suborderStatus($subOrder->id, 38);
    }

    private function failedDelivery($request, $subOrder)
    {
        $order_product = OrderProduct::where('id', $subOrder->product->id)->first();
        $order_product->delivery_paid_amount = $request->paid_amount;
        $order_product->save();

        // Update Sub-Order Status
        $this->suborderStatus($subOrder->id, 33); // Products Delivery Failed

        // Send SMS
//                $this->smsDeliveryFailed($subOrder->unique_suborder_id);

        // Create Due Sub-Order
        $new_sub_order = $this->CreateNewSubOrderDelivery($subOrder->id, $request->sub_order_status);
        $new_sub_order = SubOrder::findOrFail($new_sub_order->id);
        $new_sub_order->reason_id = $request->reason_id;
        $new_sub_order->remarks = $request->remarks;
        $new_sub_order->save();

        // Create Due Product
        $new_product = $this->CreateNewProductDelivery($new_sub_order, $subOrder->product);

    }

    private function pertialDelivery($request, $subOrder)
    {
        $required_quantity = $subOrder->product->quantity - $request->due_quantity;
        $required_sub_total = $subOrder->product->unit_price * $required_quantity;
        if ($request->sub_order_status == 35) {
            $required_total_delivery_charge = $subOrder->product->total_delivery_charge;
        } else {
            $required_total_delivery_charge = $subOrder->product->unit_deivery_charge * $required_quantity;
        }
        if ($subOrder->product->delivery_pay_by_cus == 1) {
            $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
        } else {
            $required_total_payable_amount = $required_sub_total;
        }

        $order_product = OrderProduct::where('id', $subOrder->product->id)->first();
        $order_product->quantity = $required_quantity;
        $order_product->sub_total = $required_sub_total;
        $order_product->total_delivery_charge = $required_total_delivery_charge;
        $order_product->total_payable_amount = $required_total_payable_amount;
        $order_product->delivery_paid_amount = $request->paid_amount;
        $order_product->save();

        // Update Sub-Order Status
        $this->suborderStatus($order_product->sub_order_id, 39); // Delivery Partial Completed

        // Create Due Sub-Order
        $new_sub_order = $this->CreatePertialSubOrderDelivery($subOrder->id, $request->sub_order_status);
        $new_sub_order = SubOrder::findOrFail($new_sub_order->id);
        $new_sub_order->reason_id = $request->reason_id;
        $new_sub_order->remarks = $request->remarks;
        $new_sub_order->save();

        // Create Due Product
        $new_product = $this->CreatePertialProductDelivery($new_sub_order, $subOrder->product, $request->due_quantity);
    }
}