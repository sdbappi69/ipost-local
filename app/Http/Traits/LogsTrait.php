<?php
namespace App\Http\Traits;

use App\ActivityLog;
use App\OrderLog;
use App\SubOrder;
use App\Order;
use App\Status;
use App\PickingTask;
use App\DeliveryTask;
use Auth;
use Log;

use App\Http\Traits\FcmTaskManager;

trait LogsTrait {

    use FcmTaskManager;

    public function activityLog($user_id, $ref_id, $ref_table, $text) {
        $activity = new ActivityLog();
        $activity->user_id = $user_id;
        $activity->ref_id = $ref_id;
        $activity->ref_table = $ref_table;
        $activity->text = $text;
        $activity->created_by = $user_id;
        $activity->updated_by = $user_id;
        $activity->save();
        return $activity->id;
    }

    public function orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text) {

        // activityLog('user_id', 'ref_id', 'ref_table', 'text')
        $this->activityLog($user_id, $ref_id, $ref_table, $text);
        
        // $order = new OrderLog();
        // $order->user_id = $user_id;
        // $order->order_id = $order_id;
        // $order->sub_order_id = $sub_order_id;
        // $order->product_id = $product_id;
        // $order->ref_id = $ref_id;
        // $order->ref_table = $ref_table;
        // $order->text = $text;
        // $order->created_by = $user_id;
        // $order->updated_by = $user_id;
        // $order->save();
        return $order_id;
    }

    public function suborderStatus($sub_order_id, $status_code) {

        try {

            $sub_order = SubOrder::findOrFail($sub_order_id);
            $sub_order->sub_order_status = $status_code;
            if($sub_order->parent_sub_order_id == 0){
                $sub_order->sub_order_last_status = $status_code;
            }else{
                $parent_sub_order = SubOrder::findOrFail($sub_order->parent_sub_order_id);
                $parent_sub_order->sub_order_last_status = $status_code;
                $parent_sub_order->save();
            }

            $order_status = $sub_order->order->order_status;

            // Get Order Status
            $total = SubOrder::where('sub_order_status', '>', '0')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();

            if(in_array($sub_order->sub_order_status, array(14, 24))){

                $order_status = 0;

            }else if($sub_order->sub_order_status == 49){

                $order_status = 7;

            }else if($sub_order->sub_order_status == 47){

                $order_status = 6;

            }else if($sub_order->sub_order_status > 36){

                $count = SubOrder::where('sub_order_status', '>', '36')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                // return "Total: ".$total." | Count: ".$count;
                if($total == $count){ 
                    if($order_status < 10){
                        $order_status = 10;
                    }
                }

            }else if($sub_order->sub_order_status > 30){

                // if($sub_order->sub_order_status == 35){
                //     $fcm_task = $this->fcm_task_req($sub_order->id);
                //     $fcm_task = json_decode($fcm_task, true);

                //     if(isset($fcm_task["status_code"]) && $fcm_task["status_code"] == 200){
                //         Log::info("TmTask created for SubOrder: ".$sub_order->id);
                //     }else{
                //         Log::info("Failed to create TmTask for SubOrder: ".$sub_order->id);
                //     }
                // }
                
                $count = SubOrder::where('sub_order_status', '>', '30')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                
                Log::info("Total: ".$total." | Count: ".$count);
                
                if($total == $count){ 
                    if($order_status < 9){
                        $order_status = 9;
                    }
                }

            }else if($sub_order->sub_order_status > 27){
                
                $count = SubOrder::where('sub_order_status', '>', '27')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 8){
                        $order_status = 8;
                    }
                }

            }else if($sub_order->sub_order_status > 21){
                
                if($sub_order->sub_order_status == 26 || $sub_order->sub_order_status == 27){

                    if($sub_order->order->delivery_zone->status == 0){

                        $sub_order->delivery_from_office == 1;

                    }

                    // FCM disable
                    // $fcm_task = $this->fcm_task_req($sub_order->id);
                    // $fcm_task = json_decode($fcm_task, true);

                    // if(isset($fcm_task["status_code"]) && $fcm_task["status_code"] == 200){
                    //     Log::info("TmTask created for SubOrder: ".$sub_order->id);
                    //     $sub_order->sub_order_status = 28;
                    // }else{
                    //     Log::info("Failed to create TmTask for SubOrder: ".$sub_order->id);
                    // }
                }

                $count = SubOrder::where('sub_order_status', '>', '21')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 7){
                        $order_status = 7;
                    }
                }

            }else if($sub_order->sub_order_status > 17){
                
                $count = SubOrder::where('sub_order_status', '>', '17')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 6){
                        $order_status = 6;
                    }
                }

            }else if($sub_order->sub_order_status > 8){
                
                $count = SubOrder::where('sub_order_status', '>', '8')->where('sub_order_status', '!=', '13')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 5){
                        $order_status = 5;
                    }
                }

            }else if($sub_order->sub_order_status > 5){
                
                $count = SubOrder::where('sub_order_status', '>', '5')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 4){
                        $order_status = 4;
                    }
                }

            }else if($sub_order->sub_order_status > 2){
                
                $count = SubOrder::where('sub_order_status', '>', '2')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 3){
                        $order_status = 3;
                    }
                }

            }else if($sub_order->sub_order_status > 1){

                // $fcm_task = $this->fcm_task_req($sub_order->id);
                // $fcm_task = json_decode($fcm_task, true);

                // if(isset($fcm_task["status_code"]) && $fcm_task["status_code"] == 200){
                //     Log::info("TmTask created for SubOrder: ".$sub_order->id);
                // }else{
                //     Log::info("Failed to create TmTask for SubOrder: ".$sub_order->id);
                // }
                
                $count = SubOrder::where('sub_order_status', '>', '1')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 2){
                        $order_status = 2;
                    }
                }

            }else if($sub_order->sub_order_status > 0){
                
                $count = SubOrder::where('sub_order_status', '>', '0')->where('order_id', '=', $sub_order->order_id)->whereNotIn('sub_order_status', [13,6,25,33])->count();
                if($total == $count){ 
                    if($order_status < 1){
                        $order_status = 1;
                    }
                }

            }

            $sub_order->save();

            // Update Order Status
            $order = Order::findOrFail($sub_order->order_id);
            $order->order_status = $order_status;
            $order->save();

            // Log/////////////////////////////////

            // Get User Id
            switch ($status_code) {
                case "5":
                case "6":
                case "7":
                case "8":            
                case "9":
                case "30":
                case "31":
                case "36":
                    if(auth()->user()){
                        $user_id = auth()->user()->id;
                    }else{
                        $user_id = Auth::guard('api')->user() ? Auth::guard('api')->user()->id : 3;
                    }
                    // $user_id = auth()->user()->id;
                    break;
                case "32":
                case "33":
                    if(auth()->user()){
                        $user_id = auth()->user()->id;
                    }else{
                        $user_id = Auth::guard('api')->user() ? Auth::guard('api')->user()->id : 3;
                    }
                    break;
                default:
                    if(auth()->user()){
                        $user_id = auth()->user()->id;
                    }else{
                        $user_id = Auth::guard('api')->user() ? Auth::guard('api')->user()->id : 3;
                    }
            }

            // return $status_code;
            $status = Status::where('code', $status_code)->first();
            if($status && isset($status->type)){
                if($status->type == 'SUBORDER'){
                    $ref_id = $sub_order->id;
                    $ref_table = 'sub_orders';
                }else if($status->type == 'ORDER'){
                    $ref_id = $sub_order->order_id;
                    $ref_table = 'orders';
                }else{
                    $ref_id = $status_code;
                    $ref_table = 'status';
                }
            }else{
                $ref_id = $status_code;
                $ref_table = 'status';
            }

            if(isset($status->type)){
                $text = $status->title;

                // activityLog('user_id', 'ref_id', 'ref_table', 'text')
                $this->activityLog($user_id, $ref_id, $ref_table, $text);

                if($sub_order->parent_sub_order_id == 0){
                    $order_log_type = 'parent';
                }else{  
                    
                    OrderLog::updateOrCreate(
                            [
                                'user_id' => $user_id,
                                'order_id' => $sub_order->order_id,
                                'sub_order_id' => $sub_order->parent_sub_order_id,
                                'sub_order_status' => $status_code,
                                'type' => 'reference',
                            ],
                            [
                                'product_id' => 0,
                                'ref_id' => $ref_id,
                                'ref_table' => $ref_table,
                                'text' => $text,
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ]
                            );

                    $order_log_type = 'child';
                }

                OrderLog::updateOrCreate(
                            [
                                'user_id' => $user_id,
                                'order_id' => $sub_order->order_id,
                                'sub_order_id' => $sub_order->id,
                                'sub_order_status' => $status_code,
                                'type' => $order_log_type,
                            ],
                            [
                                'product_id' => 0,
                                'ref_id' => $ref_id,
                                'ref_table' => $ref_table,
                                'text' => $text,
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ]
                            );
                // Keep Note
                // $this->keepNote($order_log, $status_code);

                // $api_token = $this->lpLogin();
                // $this->lpUpdate($api_token, $sub_order->unique_suborder_id, $sub_order->sub_order_status);

                // Notify to Merchant
                if ($sub_order->order->store->merchant_id == 12) {
                    fibStatusUpdate($sub_order);
                } else {
                    fbStatusUpdate($sub_order);
                }

                return $order->order_status;
            }else{
                return 0;
            }
            
        } catch (\Exception $e) {
            Log::error($e);
        }

    }

    public function keepNote($order_log, $status_code) {

        $sub_order = SubOrder::whereStatus(true)->where('id', $order_log->sub_order_id)->first();
        $sub_order_note = json_decode($sub_order->sub_order_note,true);

        if($sub_order->parent_sub_order_id != 0){
            $parent_sub_order = SubOrder::findOrFail($sub_order->parent_sub_order_id);
            $parent_sub_order_note = json_decode($parent_sub_order->sub_order_note,true);
        }

        switch ($status_code) {

            case "2":
                $sub_order_note['pickup_requested'] = (string)$order_log->created_at;

                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['pickup_requested'] = (string)$order_log->created_at;
                }
                break;

            case "5":
                $pTask = PickingTask::where('product_unique_id', $sub_order->product->product_unique_id)->first();
                $sub_order_note['latest_picking_attempt'] = (string)$pTask->created_at;
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['latest_picking_attempt'] = (string)$pTask->created_at;
                }
                break;

            case "6":
            case "7":
            case "8":
                $pTask = PickingTask::where('product_unique_id', $sub_order->product->product_unique_id)->first();

                $sub_order_note['picked'] = (string)$order_log->created_at;
                if (isset($sub_order_note['pickup_requested'])){
                    $sub_order_note['pickup_aging'] = $this->date_getFullTimeDifference($sub_order_note['pickup_requested'], (string)$order_log->created_at);
                }
                if (!is_null($pTask->reason_id) && $pTask->reason_id != '0'){
                    $sub_order_note['latest_picking_reason'] = (string)$pTask->reason->reason;
                }
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['picked'] = (string)$order_log->created_at;
                    if (isset($sub_order_note['pickup_requested'])){
                        $parent_sub_order_note['pickup_aging'] = $this->date_getFullTimeDifference($parent_sub_order_note['pickup_requested'], (string)$order_log->created_at);
                    }
                    if (!is_null($pTask->reason_id) && $pTask->reason_id != '0'){
                        $parent_sub_order_note['latest_picking_reason'] = (string)$pTask->reason->reason;
                    }
                }
                break;

            case "15":
            case "16":
                if($sub_order->return == 1){
                    $sub_order_note['pickup_requested'] = (string)$order_log->created_at;
                    $sub_order_note['picked'] = (string)$order_log->created_at;
                    $sub_order_note['raked_on_pickup'] = (string)$order_log->created_at;
                }else{
                    $sub_order_note['raked_on_pickup'] = (string)$order_log->created_at;

                    if($sub_order->parent_sub_order_id != 0){
                        $sub_order_note['pickup_requested'] = (string)$order_log->created_at;
                    }

                }
                break;

            case "26":
            case "27":
                $sub_order_note['raked_on_destination'] = (string)$order_log->created_at;
                if (isset($sub_order_note['raked_on_pickup'])){
                    $sub_order_note['trip_aging'] = $this->date_getFullTimeDifference($sub_order_note['raked_on_pickup'], (string)$order_log->created_at);
                }
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['raked_on_destination'] = (string)$order_log->created_at;
                    if (isset($sub_order_note['raked_on_pickup'])){
                        $parent_sub_order_note['trip_aging'] = $this->date_getFullTimeDifference($parent_sub_order_note['raked_on_pickup'], (string)$order_log->created_at);
                    }
                }
                break;

            case "29":
                $sub_order_note['delivery_requested'] = (string)$order_log->created_at;
                if (isset($sub_order_note['raked_on_destination'])){
                    $sub_order_note['delivery_attempt_aging'] = $this->date_getFullTimeDifference($sub_order_note['raked_on_destination'], (string)$order_log->created_at);
                }                
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['delivery_requested'] = (string)$order_log->created_at;
                    if (isset($sub_order_note['raked_on_destination'])){
                        $parent_sub_order_note['delivery_attempt_aging'] = $this->date_getFullTimeDifference($parent_sub_order_note['raked_on_destination'], (string)$order_log->created_at);
                    }
                }
                break;

            case "30":
                $dTask = DeliveryTask::where('unique_suborder_id', $sub_order->unique_suborder_id)->first();
                $sub_order_note['latest_delivery_attempt'] = (string)$dTask->created_at;
                // $sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                // if (!empty($dTask->reason_id)) {
                if (!is_null($dTask->reason_id) && $dTask->reason_id != '0'){
                    $sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                }                
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['latest_delivery_attempt'] = (string)$dTask->created_at;
                    if (!is_null($dTask->reason_id) && $dTask->reason_id != '0'){
                        $parent_sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                    }  
                }
                break;

            case "31":
            case "32":
                $sub_order_note['delivered'] = (string)$order_log->created_at;
                if (isset($sub_order_note['delivery_requested'])){
                    $sub_order_note['delivery_aging'] = $this->date_getFullTimeDifference($sub_order_note['delivery_requested'], (string)$order_log->created_at);
                }
                
                if (isset($sub_order_note['picked'])){
                    $sub_order_note['tat'] = $this->date_getFullTimeDifference($sub_order_note['picked'], (string)$order_log->created_at);
                }                
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['delivered'] = (string)$order_log->created_at;
                    if (isset($sub_order_note['delivery_requested'])){
                        $parent_sub_order_note['delivery_aging'] = $this->date_getFullTimeDifference($parent_sub_order_note['delivery_requested'], (string)$order_log->created_at);
                    }
                    
                    if (isset($sub_order_note['picked'])){
                        $parent_sub_order_note['tat'] = $this->date_getFullTimeDifference($parent_sub_order_note['picked'], (string)$order_log->created_at);
                    } 
                }
                break;

            case "33":
                $dTask = DeliveryTask::where('unique_suborder_id', $sub_order->unique_suborder_id)->first();
                $sub_order_note['latest_delivery_attempt'] = (string)$dTask->created_at;
                // if (!empty($dTask->reason_id)) {
                if (!is_null($dTask->reason_id) && $dTask->reason_id != '0'){
                    $sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                }                
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['latest_delivery_attempt'] = (string)$dTask->created_at;
                    if (!is_null($dTask->reason_id) && $dTask->reason_id != '0'){
                        $parent_sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                    }
                }
                break;

            case "35":
                $sub_order_note['delivery_requested'] = (string)$order_log->created_at;
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['delivery_requested'] = (string)$order_log->created_at;
                }
                break;

            case "36":
                $pTask = PickingTask::where('product_unique_id', $sub_order->product->product_unique_id)->first();
                $sub_order_note['latest_delivery_attempt'] = (string)$pTask->created_at;
                // $sub_order_note['latest_delivery_reason'] = (string)$pTask->reason->reason;
                // if (!empty($pTask->reason_id)) {
                if (!is_null($pTask->reason_id) && $pTask->reason_id != '0'){
                    $sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                }                

                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['latest_delivery_attempt'] = (string)$pTask->created_at;
                    if (!is_null($pTask->reason_id) && $pTask->reason_id != '0'){
                        $parent_sub_order_note['latest_delivery_reason'] = (string)$dTask->reason->reason;
                    }
                }
                break;

            case "37":
                $sub_order_note['delivered'] = (string)$order_log->created_at;
                $sub_order_note['delivery_aging'] = $this->date_getFullTimeDifference($sub_order_note['delivery_requested'], (string)$order_log->created_at);
                if (isset($sub_order_note['picked'])){
                    $sub_order_note['tat'] = $this->date_getFullTimeDifference($sub_order_note['picked'], (string)$order_log->created_at);                }
                
                
                if($sub_order->parent_sub_order_id != 0){
                    $parent_sub_order_note['delivered'] = (string)$order_log->created_at;
                    $parent_sub_order_note['delivery_aging'] = $this->date_getFullTimeDifference($parent_sub_order_note['delivery_requested'], (string)$order_log->created_at);
                    if (isset($sub_order_note['picked'])){
                        $parent_sub_order_note['tat'] = $this->date_getFullTimeDifference($parent_sub_order_note['picked'], (string)$order_log->created_at);
                    }
                }
                break;

            default:
                break;
        }

        $sub_order->sub_order_note = json_encode($sub_order_note);
        $sub_order->save();

        if($sub_order->parent_sub_order_id != 0){
            $parent_sub_order->sub_order_note = json_encode($parent_sub_order_note);
            $parent_sub_order->save();
        }

        return $sub_order;

    }

    public function date_getFullTimeDifference( $start, $end ){
        // return $start;
        $uts['start']      =    strtotime( $start );
        $uts['end']        =    strtotime( $end );
        if( $uts['start']!==-1 && $uts['end']!==-1 )
        {
            if( $uts['end'] >= $uts['start'] )
            {
                $diff    =    $uts['end'] - $uts['start'];
                if( $years=intval((floor($diff/31104000))) )
                    $diff = $diff % 31104000;
                if( $months=intval((floor($diff/2592000))) )
                    $diff = $diff % 2592000;
                if( $days=intval((floor($diff/86400))) )
                    $diff = $diff % 86400;
                if( $hours=intval((floor($diff/3600))) )
                    $diff = $diff % 3600;
                if( $minutes=intval((floor($diff/60))) )
                    $diff = $diff % 60;
                $diff    =    intval( $diff );
                // return( array('years'=>$years,'months'=>$months,'days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
                $text = '';
                if($years != 0){
                    $text = $text.$years.' Years ';
                }
                if($months != 0){
                    $text = $text.$months.' Months ';
                }
                if($days != 0){
                    $text = $text.$days.' Days ';
                }
                if($hours != 0){
                    $text = $text.$hours.' Hours ';
                }
                if($minutes != 0){
                    $text = $text.$minutes.' Minutes ';
                }
                if($diff != 0){
                    $text = $text.$diff.' Seconds';
                }

                return $text;
            }
            else
            {
                return "Ending date/time is earlier than the start date/time";
            }
        }
        else
        {
            return "Invalid date/time data detected";
        }
    }

}