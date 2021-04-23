<?php

namespace App\Http\Controllers\ApiV3;

use App\ConsignmentCommon;
use App\ConsignmentTask;
use App\Hub;
use App\SubOrder;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\TaskTrait;
use App\Http\Traits\SmsTrait;
use Auth;
use DB;
use App\Consignment;
use App\User;
use App\RiderLocation;
use App\PickingTask;
use App\DeliveryTask;
use App\PickingLocations;
use App\Order;
use App\TmTask;
use Validator;
use Log;

class OrderNotificationController extends Controller {

    use TaskTrait;
    use SmsTrait;

    public function pullNotification(Request $request){

        // Get User Id
        $user_id = Auth::guard('api')->user()->id;

        $tm_tasks = TmTask::orderBy('id', 'asc')
                            ->where('user_id', $user_id)
                            ->where('status', '<', 2)
                             ->where('show', 0)
                            ->where('attempt_end', '>', date('Y-m-d H:i:s'));

        if($tm_tasks->count() == 0){

            $status = 'Not Found';
            $message[] = 'No data found';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;

        }else{

            $status = 'Success';
            $message[] = 'Data available';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;
            $tasks = [];

            foreach ($tm_tasks->get() as $tm_task) {

                $task['priority'] = "HIGH";
                
                $data['title'] = "iPost";
                $data['notification'] = $tm_task->unique_suborder_id;
                $data['body'] = "You have a new task request";

                $task['data'] = $data;

                $description['tm_task_id'] = $tm_task->id;
                $description['sub_order_id'] = $tm_task->sub_order_id;
                $description['unique_suborder_id'] = $tm_task->unique_suborder_id;
                $description['tm_task_type_id'] = $tm_task->task_type_id;
                $description['picking_title'] = $tm_task->picking_title;
                $description['picking_address'] = $tm_task->picking_address;
                $description['picking_latitude'] = $tm_task->picking_latitude;
                $description['picking_longitude'] = $tm_task->picking_longitude;
                $description['delivery_title'] = $tm_task->delivery_title;
                $description['delivery_address'] = $tm_task->delivery_address;
                $description['delivery_latitude'] = $tm_task->delivery_latitude;
                $description['delivery_longitude'] = $tm_task->delivery_longitude;
                $description['quantity'] = $tm_task->quantity;
                $description['amount'] = $tm_task->amount;
                $description['hub_id'] = $tm_task->hub_id;
                $description['hub_title'] = $tm_task->hub_title;
                $description['attempt_end'] = $tm_task->attempt_end;
                $description['now'] = date('Y-m-d H:i:s');

                $task['description'] = $description;

                $tasks[] = $task;

                // Update show status
                $task_update = TmTask::where('id', $tm_task->id)->first();
                $task_update->show = 1;
                $task_update->save();

            }

            $feedback['response'] = $tasks;

        }

        return response($feedback, 200);

    }

    public function declineNotification(Request $request){

        $user_id = Auth::guard('api')->user()->id;
        $task_id = $request->tm_task_id;

        $task_query = TmTask::where('id', $task_id)
                        ->where('user_id', $user_id)
                        ->where('status', '<', 2)
                        ->where('attempt_end', '>', date('Y-m-d H:i:s'));
        
        if($task_query->count() == 0){

            $status = 'Expired';
            $message[] = 'Task invalid or Expired';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;

        }else{

            $task = $task_query->first();

            if($task->fource == 1){

                $task->status = 3;
                $task->save();
                $this->setSubOrderTaskStatus($task->sub_order_id, $task->task_type_id);

                if ($task->task_type_id == 1){
                    $status_code = 2;
                }else{
                    $status_code = 26;
                }

                // Log Status
                $this->suborderStatus($task->sub_order_id, $status_code);

            }else{
                $this->fcm_task_req($task->sub_order_id, $task->fource);
            }

            $status = 'Success';
            $message[] = 'Task decline succesfully';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;

        }

        return response($feedback, 200);

    }

    public function acceptNotification(Request $request){

        $user_id = Auth::guard('api')->user()->id;
        $task_id = $request->tm_task_id;

        $task_query = TmTask::where('id', $task_id)
                        ->where('user_id', $user_id)
                        ->where('status', '<', 2)
                        ->where('attempt_end', '>', date('Y-m-d H:i:s'));

        if($task_query->count() == 0){

            $status = 'Expired';
            $message[] = 'Task invalid or Expired';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;

        }else{

            $task = $task_query->first();
            $task->status = 2;
            $task->save();
            
            $this->setSubOrderTaskStatus($task->sub_order_id, $task->task_type_id);
            $consignment_query = ConsignmentCommon::where('rider_id', $user_id)
                                                    ->whereDate('created_at', '=', date('Y-m-d'))
                                                    ->where('status', '<=', 2);

            if($consignment_query->count() == 0){

                $consignment = new ConsignmentCommon;
                $temp = "CL" . time() . rand(10, 99);
                $consignment->consignment_unique_id = $temp;
                $consignment->rider_id = $user_id;
                if($task->task_type_id == 1){
                    $hub_id = $task->sub_order->source_hub_id;
                }else{
                    $hub_id = $task->sub_order->destination_hub_id;
                }
                $consignment->hub_id = $hub_id;
                $consignment->status = 2;
                $consignment->created_by = $user_id;
                $consignment->updated_by = $user_id;
                $consignment->save();

            }else{

                $consignment = $consignment_query->first();

            }

            $consignment_task = new ConsignmentTask;
            $consignment_task->consignment_id = $consignment->id;
            $consignment_task->rider_id = $user_id;
            $consignment_task->sub_order_id = $task->sub_order_id;
            $consignment_task->task_type_id = $task->task_type_id;
            $consignment_task->start_lat = $task->picking_latitude;
            $consignment_task->start_long = $task->picking_longitude;
            $consignment_task->end_lat = $task->delivery_latitude;
            $consignment_task->end_long = $task->delivery_longitude;
            $consignment_task->quantity = $task->quantity;
            $consignment_task->amount = $task->amount;
            $consignment_task->otp = rand(100000, 999999);
            // $consignment_task->otp = 123456; // for testing
            $consignment_task->created_at = date('Y-m-d H:i:s');
            $consignment_task->save();

            switch ($consignment_task->task_type_id) {
                case 1:
                    $this->suborderStatus($consignment_task->sub_order_id, '4');
                    break;
                case 2:
                case 6: // post delivery order return to buyer
                    $this->suborderStatus($consignment_task->sub_order_id, '29');

                    // Send SMS
                    $sms = "Dear ".$consignment_task->suborder->order->delivery_name.", Your product is on the way. Your security code is: ".$consignment_task->otp;
                    $this->sendCustomMessage($consignment_task->suborder->order->delivery_msisdn, $sms, $consignment_task->suborder->id);
                    break;
                case 4:
                    $this->suborderStatus($consignment_task->sub_order_id, '36');
                    // Send SMS
                    $sms = "Dear Seller, Your product is on the way. Your security code is: ".$consignment_task->otp;
                    $this->sendCustomMessage($consignment_task->suborder->product->pickup_location->msisdn, $sms, $consignment_task->suborder->id);
                    break;
                case 5:
                    $this->suborderStatus($consignment_task->sub_order_id, '4');
                    break;
                }

            $status = 'Success';
            $message[] = 'Task added to your current consignment';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;

        }

        return response($feedback, 200);

    }

}
