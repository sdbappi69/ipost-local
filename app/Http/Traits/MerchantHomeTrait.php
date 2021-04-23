<?php

namespace App\Http\Traits;

use App\PickingTask;
use App\DeliveryTask;
use App\OrderProduct;
use App\SubOrder;
use App\Order;
use App\Store;
use App\PickingLocations;
use App\ConsignmentTask;

trait MerchantHomeTrait {

    public function MerchantInfo($from_date, $to_date) {
        $query = ConsignmentTask::join('consignments_common', 'consignments_common.id', '=', 'consignments_tasks.consignment_id')
                ->select('task_type_id', 'consignments_tasks.status')
                ->join('sub_orders', 'sub_orders.id', '=', 'consignments_tasks.sub_order_id')
                ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->join('stores', 'stores.id', '=', 'orders.store_id')
                ->WhereBetween('consignments_tasks.created_at', array($from_date . ' 00:00:01', $to_date . ' 23:59:59'))
                ->where('stores.merchant_id', auth()->user()->reference_id);

        $tasks = $query->get();
        
        // PickUp
        $total_pickup_req = $tasks->where('task_type_id', 1)->count();
        $panding_pickup_req = $tasks->where('task_type_id', 1)->where('status', 0)->count();
        $processing_pickup_req = $tasks->where('task_type_id', 1)->where('status', 1)->count();
        $success_pickup_req = $tasks->where('task_type_id', 1)->where('status', 2)->count();
        $partial_pickup_req = $tasks->where('task_type_id', 1)->where('status', 3)->count();
        $failed_pickup_req = $tasks->where('task_type_id', 1)->where('status', 4)->count();
        // Delivery
        $total_delivery_req = $tasks->where('task_type_id', 2)->count();
        $panding_delivery_req = $tasks->where('task_type_id', 2)->where('status', 0)->count();
        $processing_delivery_req = $tasks->where('task_type_id', 2)->where('status', 1)->count();
        $success_delivery_req = $tasks->where('task_type_id', 2)->where('status', 2)->count();
        $partial_delivery_req = $tasks->where('task_type_id', 2)->where('status', 3)->count();
        $failed_delivery_req = $tasks->where('task_type_id', 2)->where('status', 4)->count();
        // Return
        $total_return_req = $tasks->where('task_type_id', 4)->count();
        $panding_return_req = $tasks->where('task_type_id', 4)->where('status', 0)->count();
        $processing_return_req = $tasks->where('task_type_id', 4)->where('status', 1)->count();
        $success_return_req = $tasks->where('task_type_id', 4)->where('status', 2)->count();
        $failed_return_req = $tasks->where('task_type_id', 4)->where('status', 4)->count();


        $orders_in_draft = Order::leftJoin('stores', 'stores.id', '=', 'orders.store_id')
                ->where('orders.order_status', '=', '1')
                ->where('stores.merchant_id', auth()->user()->reference_id)
                ->count();

        $total_stores = Store::whereStatus(true)->where('merchant_id', auth()->user()->reference_id)->count();

        $total_picking_locations = PickingLocations::whereStatus(true)->where('merchant_id', auth()->user()->reference_id)->count();

        return $merchant_info = array(
            'total_pickup_req' => $total_pickup_req,
            'panding_pickup_req' => $panding_pickup_req,
            'processing_pickup_req' => $processing_pickup_req,
            'success_pickup_req' => $success_pickup_req,
            'partial_pickup_req' => $partial_pickup_req,
            'failed_pickup_req' => $failed_pickup_req,
            'total_delivery_req' => $total_delivery_req,
            'panding_delivery_req' => $panding_delivery_req,
            'processing_delivery_req' => $processing_delivery_req,
            'success_delivery_req' => $success_delivery_req,
            'partial_delivery_req' => $partial_delivery_req,
            'failed_delivery_req' => $failed_delivery_req,
            'total_return_req' => $total_return_req,
            'panding_return_req' => $panding_return_req,
            'processing_return_req' => $processing_return_req,
            'success_return_req' => $success_return_req,
            'failed_return_req' => $failed_return_req,
            'orders_in_draft' => $orders_in_draft,
            'total_stores' => $total_stores,
            'total_picking_locations' => $total_picking_locations
        );
    }

}
