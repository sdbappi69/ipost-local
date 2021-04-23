<?php

namespace App\Http\Traits;

use App\PickingTask;
use App\DeliveryTask;
use App\OrderProduct;
use App\SubOrder;
use App\Merchant;
use App\Order;
use App\ConsignmentTask;

trait HomeTrait {

    public function HubManagerInfo($from_date, $to_date) {
        $hubTasks = ConsignmentTask::join('consignments_common', 'consignments_common.id', '=', 'consignments_tasks.consignment_id')
                ->select('task_type_id', 'consignments_tasks.status')
                ->where('consignments_common.hub_id', auth()->user()->reference_id)
                ->WhereBetween('consignments_tasks.created_at', array($from_date . ' 00:00:01', $to_date . ' 23:59:59'))
                ->get();
//        dd($hubTasks->where('status',0)->count());
        // PickUp
        $total_pickup_req = $hubTasks->where('task_type_id', 1)->count();
        $panding_pickup_req = $hubTasks->where('task_type_id', 1)->where('status', 0)->count();
        $processing_pickup_req = $hubTasks->where('task_type_id', 1)->where('status', 1)->count();
        $success_pickup_req = $hubTasks->where('task_type_id', 1)->where('status', 2)->count();
        $partial_pickup_req = $hubTasks->where('task_type_id', 1)->where('status', 3)->count();
        $failed_pickup_req = $hubTasks->where('task_type_id', 1)->where('status', 4)->count();
        // Delivery
        $total_delivery_req = $hubTasks->where('task_type_id', 2)->count();
        $panding_delivery_req = $hubTasks->where('task_type_id', 2)->where('status', 0)->count();
        $processing_delivery_req = $hubTasks->where('task_type_id', 2)->where('status', 1)->count();
        $success_delivery_req = $hubTasks->where('task_type_id', 2)->where('status', 2)->count();
        $partial_delivery_req = $hubTasks->where('task_type_id', 2)->where('status', 3)->count();
        $failed_delivery_req = $hubTasks->where('task_type_id', 2)->where('status', 4)->count();
        // Return
        $total_return_req = $hubTasks->where('task_type_id', 4)->count();
        $panding_return_req = $hubTasks->where('task_type_id', 4)->where('status', 0)->count();
        $processing_return_req = $hubTasks->where('task_type_id', 4)->where('status', 1)->count();
        $success_return_req = $hubTasks->where('task_type_id', 4)->where('status', 2)->count();
        $failed_return_req = $hubTasks->where('task_type_id', 4)->where('status', 4)->count();

        return $hub_manager_info = array(
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
            'failed_return_req' => $failed_return_req
        );
    }

    public function AdministratorInfo($request, $from_date, $to_date) {

        $count_merchant = Merchant::where('status', true)->WhereBetween('created_at', array($from_date . ' 00:00:01', $to_date . ' 23:59:59'))->count();
        $orders = Order::where('orders.status', true)->WhereBetween('orders.created_at', array($from_date . ' 00:00:01', $to_date . ' 23:59:59'));
        if ($request->has('merchant_id')) {
            $orders->leftJoin('stores AS s', 's.id', '=', 'orders.store_id')
                    ->where('s.merchant_id', $request->merchant_id);
        }
        $count_orders = $orders->count();

        $query = ConsignmentTask::join('consignments_common', 'consignments_common.id', '=', 'consignments_tasks.consignment_id')
                ->select('task_type_id', 'consignments_tasks.status')
                ->WhereBetween('consignments_tasks.created_at', array($from_date . ' 00:00:01', $to_date . ' 23:59:59'));
        ($request->hub_id) ? $query->whereIn('consignments_common.hub_id', $request->hub_id) : '';
        if ($request->merchant_id) {
            $query->join('sub_orders', 'sub_orders.id', '=', 'consignments_tasks.sub_order_id')
                    ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
                    ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
                    ->whereIn('stores.merchant_id', $request->merchant_id);
        }
        $tasks = $query->get();
//        dd($hubTasks->where('status',0)->count());
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

        return $hub_manager_info = array(
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
            'new_merchant_count' => $count_merchant,
            'new_order_count' => $count_orders
        );
    }

}
