<?php

function merchantAllStatus() {

    $sub_order_status = [
                            '1' => 'Pickup requested',
                            '2' => 'Being Picked',
                            '3' => 'Picked',
                            '4' => 'Full order Racked at Pickup hub',
                            '5' => 'Pick up failed',
                            '6' => 'Pickup order Cancelled',
                            '7' => 'Trip in Transit',
                            '8' => 'Full order racked at Destination Hub',
                            '9' => 'In Delivery',
                            '10' => 'Product delivered to customer',
                            '11' => 'Delivery Completed',
                            '12' => 'Product delivery failed',
                            '13' => 'Product waiting to be reassigned',
                            '14' => 'Product to be returned',
                            '15' => 'Being Returned',
                            '16' => 'Return Completed',
                            '17' => 'Product return failed',
                            '18' => 'Destination Changed',
                        ];

    return $sub_order_status;

}

function merchantWhereInStatus($sub_order_status){

    switch ($sub_order_status) {
        case "1":
            $whereIn = array(2, 3, 4);
            break;
        case "2":
            $whereIn = array(5);
            break;
        case "3":
            $whereIn = array(7, 8);
            break;
        case "4":
            $whereIn = array(10, 11, 15, 16);
            break;
        case "5":
            $whereIn = array(9, 6, 12);
            break;
        case "6":
            $whereIn = array(13);
            break;
        case "7":
            $whereIn = array(18, 19, 20, 21);
            break;
        case "8":
            $whereIn = array(22, 26, 27);
            break;
        case "9":
            $whereIn = array(28, 29, 30);
            break;
        case "10":
            $whereIn = array(31, 32);
            break;
        case "11":
            $whereIn = array(38, 39, 41, 42, 43, 44, 45);
            break;
        case "12":
            $whereIn = array(33);
            break;
        case "13":
            $whereIn = array(34);
            break;
        case "14":
            $whereIn = array(35);
            break;
        case "15":
            $whereIn = array(36);
            break;
        case "16":
            $whereIn = array(37);
            break;
        case "17":
            $whereIn = array(40);
            break;
        case "18":
            $whereIn = array(47);
            break;
        default:
            $whereIn = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47);
    }

    return $whereIn;

}

function merchantWhereInStatusText($sub_order_status){

    switch ($sub_order_status) {
        case "1":
            $whereIn = array(merchantGetRealStatus(2), merchantGetRealStatus(3), merchantGetRealStatus(4));
            break;
        case "2":
            $whereIn = array(merchantGetRealStatus(5));
            break;
        case "3":
            $whereIn = array(merchantGetRealStatus(7), merchantGetRealStatus(8));
            break;
        case "4":
            $whereIn = array(merchantGetRealStatus(10), merchantGetRealStatus(11), merchantGetRealStatus(15), merchantGetRealStatus(16));
            break;
        case "5":
            $whereIn = array(merchantGetRealStatus(9), merchantGetRealStatus(6), merchantGetRealStatus(12));
            break;
        case "6":
            $whereIn = array(merchantGetRealStatus(13));
            break;
        case "7":
            $whereIn = array(merchantGetRealStatus(18), merchantGetRealStatus(19), merchantGetRealStatus(20), merchantGetRealStatus(21));
            break;
        case "8":
            $whereIn = array(merchantGetRealStatus(22), merchantGetRealStatus(26), merchantGetRealStatus(27));
            break;
        case "9":
            $whereIn = array(merchantGetRealStatus(28), merchantGetRealStatus(29), merchantGetRealStatus(30));
            break;
        case "10":
            $whereIn = array(merchantGetRealStatus(31), merchantGetRealStatus(32));
            break;
        case "11":
            $whereIn = array(merchantGetRealStatus(38), merchantGetRealStatus(39), merchantGetRealStatus(41), merchantGetRealStatus(42), merchantGetRealStatus(43), merchantGetRealStatus(44), merchantGetRealStatus(45));
            break;
        case "12":
            $whereIn = array(merchantGetRealStatus(33));
            break;
        case "13":
            $whereIn = array(merchantGetRealStatus(34));
            break;
        case "14":
            $whereIn = array(merchantGetRealStatus(35));
            break;
        case "15":
            $whereIn = array(merchantGetRealStatus(36));
            break;
        case "16":
            $whereIn = array(merchantGetRealStatus(37));
            break;
        case "17":
            $whereIn = array(merchantGetRealStatus(40));
            break;
        case "18":
            $whereIn = array(merchantGetRealStatus(47));
            break;
        default:
            $whereIn = array('Unknown');
    }

    return $whereIn;

}

function merchantGetStatus($sub_order_status){
    switch ($sub_order_status) {
        case "2":
        case "3":
        case "4":
            $merchant_status_id = 1;
            break;
        case "5":
            $merchant_status_id = 2;
            break;
        case "7":
        case "8":
            $merchant_status_id = 3;
            break;
        case "10":
        case "11":
        case "15":
        case "16":
            $merchant_status_id = 4;
            break;
        case "6":
        case "12":
        case "9":
            $merchant_status_id = 5;
            break;
        case "13":
            $merchant_status_id = 6;
            break;
        case "18":
        case "19":
        case "20":
        case "21":
            $merchant_status_id = 7;
            break;
        case "22":
        case "26":
        case "27":
            $merchant_status_id = 8;
            break;
        case "28":
        case "29":
        case "30":
            $merchant_status_id = 9;
            break;
        case "31":
        case "32":
            $merchant_status_id = 10;
            break;
        case "38":
        case "39":
        case "41":
        case "42":
        case "43":
        case "44":
        case "45":
            $merchant_status_id = 11;
            break;
        case "33":
            $merchant_status_id = 12;
            break;
        case "34":
            $merchant_status_id = 13;
            break;
        case "35":
            $merchant_status_id = 14;
            break;
        case "36":
            $merchant_status_id = 15;
            break;
        case "37":
            $merchant_status_id = 16;
            break;
        case "40":
            $merchant_status_id = 17;
            break;
        case "47":
            $merchant_status_id = 18;
            break;
    }

    $all_sub_order_status = merchantAllStatus();
    return $all_sub_order_status["$merchant_status_id"];
}

function merchantGetRealStatus($sub_order_status){
    $data = DB::table('status')->where('code', $sub_order_status)->first();
    return $data->title;
}

function merchantCountStatus($sub_order_group, $start_date, $end_date){

    $whereInStatus = merchantWhereInPieStatus($sub_order_group);

    return $count = DB::table('sub_orders')->where('sub_orders.status', '!=', 0)
                    ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                    ->leftJoin('stores','stores.id','=','orders.store_id')
                    ->WhereIn('sub_orders.sub_order_status',$whereInStatus)
                    ->where(function($query) use($start_date, $end_date) {
                        $query->WhereBetween('sub_orders.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
                        $query->orWhereBetween('sub_orders.updated_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
                    })
                    ->where('stores.merchant_id', '=', auth()->user()->reference_id)
                    ->count();

}

function merchantPieStatus(){
    $sub_order_status = [
                            '1' => 'In Process',
                            '2' => 'Delivery Completed',
                            '3' => 'Return Completed'
                        ];

    return $sub_order_status;
}

function merchantWhereInPieStatus($sub_order_status){

    switch ($sub_order_status) {
        case "1":
            $whereIn = array(18, 19, 20, 21, 22, 26, 27, 28, 29, 30, 47);
            break;
        case "2":
            $whereIn = array(38, 39, 41, 42, 43, 44, 45);
            break;
        case "3":
            $whereIn = array(37);
            break;
        default:
            $whereIn = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47);
    }

    return $whereIn;

}

function lastDeliveryTask($id, $unique_suborder_id){

    $child_sub_orders = DB::table('sub_orders')
                    ->where('parent_sub_order_id', $id)
                    ->orderBy('id', 'desc')
                    ->get();

    if(count($child_sub_orders) == 0){

        $unique_suborder_id = $unique_suborder_id;

        $delivery_task = DB::table('delivery_task')
                    ->select('delivery_task.updated_at', 'delivery_task.reason_id', 'reasons.reason')
                    ->leftJoin('reasons','reasons.id','=','delivery_task.reason_id')
                    ->where('delivery_task.unique_suborder_id', $unique_suborder_id)
                    ->orderBy('delivery_task.updated_at', 'desc')
                    ->first();

        if(count((array)$delivery_task) == 0){
            $updated_at = 'N/A';
            $reason = 'N/A';
        }else{
            $updated_at = $delivery_task->updated_at;
            if($delivery_task->reason_id != null && $delivery_task->reason_id != 0){
                if(isset($delivery_task->reason)){
                    $reason = $delivery_task->reason; 
                }else{
                    $reason = 'N/A';
                }            
            }else{
                $reason = 'N/A';
            }        
        }

    }else{

        foreach ($child_sub_orders as $child_sub_order) {
            
            $unique_suborder_id = $child_sub_order->unique_suborder_id;

            if($child_sub_order->return == 1){
                $task = DB::table('picking_task')
                    ->select('picking_task.updated_at', 'picking_task.reason_id', 'reasons.reason')
                    ->leftJoin('reasons','reasons.id','=','picking_task.reason_id')
                    ->where('picking_task.product_unique_id', $unique_suborder_id)
                    ->where('picking_task.type', 'Return')
                    ->first();
            }else{
                $task = DB::table('delivery_task')
                    ->select('delivery_task.updated_at', 'delivery_task.reason_id', 'reasons.reason')
                    ->leftJoin('reasons','reasons.id','=','delivery_task.reason_id')
                    ->where('delivery_task.unique_suborder_id', $unique_suborder_id)
                    ->first();
            }

            if(!$task){
                $updated_at = 'N/A';
                $reason = 'N/A';
            }else{
                $updated_at = $task->updated_at;
                if($task->reason_id != null && $task->reason_id != 0){
                    if(isset($task->reason)){
                        $reason = $task->reason; 
                    }else{
                        $reason = 'N/A';
                    }            
                }else{
                    $reason = 'N/A';
                }

                break;
            }

        }

    }

    return $task = array(
                'updated_at' => $updated_at,
                'reason' => $reason
            );

}

function orderApproveLog($order_id){

    $log = DB::table('order_logs')
            ->where('order_id', $order_id)
            ->where('type', 'parent')
            ->where('text', 'Pickup requested')
            ->orderBy('id', 'asc')
            ->first();

    if($log){
        return $log->created_at;
    }else{
        $order = DB::table('orders')->where('id', $order_id)->first();
        return $order->created_at;
    }

}
