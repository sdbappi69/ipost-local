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
use Auth;
use DB;
use App\Consignment;
use App\User;
use App\RiderLocation;
use App\PickingTask;
use App\DeliveryTask;
use App\PickingLocations;
use App\Order;
use Validator;
use Log;

class TaskController extends Controller {

    use TaskTrait;

    public function tasks_list(Request $request) {

        // Get User Id
        $user_id = Auth::guard('api')->user()->id;

        // Get Consignment
        $consignments = ConsignmentCommon::where('status', 2)->where('rider_id', $user_id)->get();

        // Consignment Breakdown
        if (!$consignments->count()) {

            $status = 'Data Found';
            $message[] = 'Data available on response';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;
//            $feedback['response']['tasks'] = $this->completedTaskList($user_id); // complete/failed task not handle in app task list
            $feedback['response']['tasks'] = [];
        } else {
            $tasks = $this->getTasksList($user_id);
//dd($tasks);

            $status = 'Data Found';
            $message[] = 'Data available on response';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;
            $feedback['response']['tasks'] = $tasks;
        }

        return response($feedback, 200);
    }

    public function task_detail(Request $request) {

        if ($request->has('task_id')) {

            // Get Consignment
            $task = ConsignmentTask::where('id', $request->task_id)
                            ->whereRiderId(Auth::guard('api')->user()->id)->first();

            // Consignment Breakdown
            if (!$task) {

                // Not Found
                $status = 'Not Found';
                $message[] = 'No data found';

                $feedback['status'] = $status;
                $feedback['status_code'] = 204;
                $feedback['message'] = $message;
                $feedback['response'] = [];

                return response($feedback, 200);
            }

            // Get Task Detail
            $products = $this->getProducts($task->id);

            // Get Merchant Detail
            $merchant = $this->getMerchant($task->id);

            // Get Address
            $address = $this->getAddress($task->id);

            // FeedBack
            $status = 'Data Found';
            $message[] = 'Data available on response';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;
            $feedback['response']['otp'] = $task->otp;
            $feedback['response']['merchant'] = $merchant;
            $feedback['response']['address'] = $address;
            $feedback['response']['products'] = $products;
        } else {

            // Invalid
            $status = 'Invalid';
            $message[] = 'Invalid request';

            $feedback['status'] = $status;
            $feedback['status_code'] = 400;
            $feedback['message'] = $message;
            $feedback['response'] = [];
        }

        return response($feedback, 200);
    }

    public function task_start(Request $request) {
        if (!$request->has('task_id')) {
            $status = 'Invalid';
            $message[] = 'Invalid request';

            $feedback['status'] = $status;
            $feedback['status_code'] = 400;
            $feedback['message'] = $message;
            $feedback['response'] = [];
            return response($feedback, 200);
        }

        $task = ConsignmentTask::whereRiderId(Auth::guard('api')->user()->id)
                        ->whereId($request->task_id)->whereStatus(0)->first();

        if (!$task) {
            $status = 'Not Found';
            $message[] = 'No data found';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;
            $feedback['response'] = [];
            return response($feedback, 200);
        }

        try {
            DB::beginTransaction();

            $task->status = 1;
            $task->start_lat = $request->start_lat;
            $task->start_long = $request->start_lon;
            $task->start_time = date("Y-m-d H:i:s");
            $task->updated_at = Auth::guard('api')->user()->id;
            $task->save();

            switch ($task->task_type_id) {
                case 1:
                    // Picking Attempt
                    $sop = SubOrder::where('id', $task->sub_order_id)->first();
                    if ($sop) {
                        $sop->picking_attempts = $sop->picking_attempts + 1;
                        $sop->save();

                        if ($sop->parent_sub_order_id != 0) {
                            $psop = SubOrder::where('id', $sop->parent_sub_order_id)->first();
                            $psop->picking_attempts = $psop->picking_attempts + 1;
                            $psop->save();
                        }
                    }

                    // Update Sub-Order Status
                    $this->suborderStatus($task->sub_order_id, '7');
                    break;
                case 2:
                    $sop = SubOrder::where('id', $task->sub_order_id)->first();
                    if ($sop) {
                        $sop->no_of_delivery_attempts = $sop->no_of_delivery_attempts + 1;
                        $sop->save();

                        if ($sop->parent_sub_order_id != 0) {
                            $psop = SubOrder::where('id', $sop->parent_sub_order_id)->first();
                            $psop->no_of_delivery_attempts = $psop->no_of_delivery_attempts + 1;
                            $psop->save();
                        }
                    }

                    // Update Sub-Order Status
                    $this->suborderStatus($task->sub_order_id, '30');
                    break;
                case 3:
                    $sop = SubOrder::where('id', $task->sub_order_id)->first();
                    if ($sop) {
                        $sop->picking_attempts = $sop->picking_attempts + 1;
                        $sop->no_of_delivery_attempts = $sop->no_of_delivery_attempts + 1;
                        $sop->save();

                        if ($sop->parent_sub_order_id != 0) {
                            $psop = SubOrder::where('id', $sop->parent_sub_order_id)->first();
                            $psop->picking_attempts = $psop->picking_attempts + 1;
                            $psop->no_of_delivery_attempts = $psop->no_of_delivery_attempts + 1;
                            $psop->save();
                        }
                    }
                    // Update Sub-Order Status
                    $this->suborderStatus($task->sub_order_id, '30');
                    break;
                case 4:
                    $sop = SubOrder::where('id', $task->sub_order_id)->first();
                    if ($sop) {
                        $sop->no_of_delivery_attempts = $sop->no_of_delivery_attempts + 1;
                        $sop->save();

                        if ($sop->parent_sub_order_id != 0) {
                            $psop = SubOrder::where('id', $sop->parent_sub_order_id)->first();
                            $psop->no_of_delivery_attempts = $psop->no_of_delivery_attempts + 1;
                            $psop->save();
                        }
                    }

                    // Update Sub-Order Status
                    $this->suborderStatus($task->sub_order_id, '36');
                    break;
                case 5:
                    // Picking Attempt
                    $sop = SubOrder::where('id', $task->sub_order_id)->first();
                    if ($sop) {
                        $sop->picking_attempts = $sop->picking_attempts + 1;
                        $sop->save();

                        if ($sop->parent_sub_order_id != 0) {
                            $psop = SubOrder::where('id', $sop->parent_sub_order_id)->first();
                            $psop->picking_attempts = $psop->picking_attempts + 1;
                            $psop->save();
                        }
                    }

                    // Update Sub-Order Status
                    $this->suborderStatus($task->sub_order_id, '7');
                    break;
            }

            $location = new RiderLocation();
            $location->rider_id = Auth::guard('api')->user()->id;
            $location->latitude = $request->start_lat;
            $location->longitude = $request->start_lon;
            $location->consignment_unique_id = $task->consignment->consignment_unique_id;
            $location->created_at = date("Y-m-d H:i:s");
            $location->updated_at = date("Y-m-d H:i:s");
            $location->save();

            // FeedBack
            $status = 'Task Start';
            $message[] = 'On the way to task location';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;


            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = 'Error';
            $message[] = 'There is a server error';

            $feedback['status'] = $status;
            $feedback['status_code'] = 500;
            $feedback['message'] = $message;
        }
        return response($feedback, 200);
    }

    public function task_submit(Request $request) {
        if (!$request->has('task_id')) {
            $status = 'Invalid';
            $message[] = 'Invalid request';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;
            $feedback['response'] = [];
            return response($feedback, 200);
        }
        if (!$request->has('status') or ! in_array($request->status, [2, 3, 4])) {
            $status = 'Invalid';
            $message[] = 'Status Should be Full, Partial or Fail';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;
            $feedback['response'] = [];
            return response($feedback, 200);
        }
        if ($request->has('force_cancel') && $request->force_cancel == 1) {
            $task = ConsignmentTask::whereRiderId(Auth::guard('api')->user()->id)
                            ->whereId($request->task_id)->where('status', '<', 2)->first();
        } else {
            $task = ConsignmentTask::whereRiderId(Auth::guard('api')->user()->id)
                            ->whereId($request->task_id)->whereStatus(1)->first();
        }

        if (!$task) {
            $status = 'Not Found';
            $message[] = 'No data found';

            $feedback['status'] = $status;
            $feedback['status_code'] = 400;
            $feedback['message'] = $message;
            $feedback['response'] = [];
            return response($feedback, 200);
        }


        try {
            DB::beginTransaction();

            // End Task
            $endTask = $this->endTask($task, $request);

            $location = new RiderLocation();
            $location->rider_id = Auth::guard('api')->user()->id;
            $location->latitude = $request->end_lat;
            $location->longitude = $request->end_lon;
            $location->consignment_unique_id = $task->consignment->consignment_unique_id;
            $location->created_at = date("Y-m-d H:i:s");
            $location->updated_at = date("Y-m-d H:i:s");
            $location->save();

            $status = 'Task Complete';
            $message[] = 'The task is complete';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;
            $feedback['response'] = $endTask;

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = 'Error';
            $message[] = 'There is a server error';

            $feedback['status'] = $status;
            $feedback['status_code'] = 304;
            $feedback['message'] = $message;
        }

        return response($feedback, 200);
    }

    public function reconciliation_req(Request $request) {
        $consignment = ConsignmentCommon::where('consignment_unique_id', '=', $request->consignment_unique_id)
                        ->whereStatus(2)->whereRiderId(Auth::guard('api')->user()->id)->first();
        if (!$consignment) {
            $feedback['status'] = 'Fail';
            $feedback['status_code'] = 404;
            $feedback['message'] = ['Consignment not found'];
            $feedback['response'] = [];
            return response($feedback, 200);
        }
        $hub = Hub::find($request->hub_id);
        if (!$hub) {
            $feedback['status'] = 'Fail';
            $feedback['status_code'] = 304;
            $feedback['message'] = ['Hub not found.'];
            $feedback['response'] = [];
            return response($feedback, 200);
        }

        $dueTask = ConsignmentTask::where('consignment_id', $consignment->id)->where('status', '<', 2)->count();

        if ($dueTask > 0) {
            $feedback['status'] = 'Not Modified';
            $feedback['status_code'] = 304;
            $feedback['message'] = ['All task need to be finished'];
            $feedback['response'] = [];
            return response($feedback, 200);
        }
        try {

            $consignment->hub_id = $request->hub_id;
            $consignment->status = 3;
            $consignment->save();

            $status = 'success';
            $status_code = 200;
            $message[] = 'Reconcilation Requested';
        } catch (Exception $e) {
            Log::error($e);
            $status = 'Error';
            $status_code = 304;
            $message[] = 'There is a server error';
        }

        $feedback['status'] = $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        $feedback['response'] = [];

        return response($feedback, 200);
    }

    public function location(Request $request) {
        $validator = Validator::make($request->all(), [
                    'lat' => 'required',
                    'lon' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendResponse('error', 422, $validator->errors()->all(), []);
        }
        $consignments = ConsignmentCommon::where('status', '>', 0)->where('status', '<', 3)
                        ->where('rider_id', Auth::guard('api')->user()->id)->first();

        $location = new RiderLocation();
        $location->rider_id = Auth::guard('api')->user()->id;
        $location->latitude = $request->lat;
        $location->longitude = $request->lon;
        if ($consignments) {
            $location->consignment_unique_id = $consignments->consignment_unique_id;
        }
        $location->created_at = date("Y-m-d H:i:s");
        $location->updated_at = date("Y-m-d H:i:s");
        $location->save();

        $user = User::where('id', Auth::guard('api')->user()->id)->first();
        $user->latitude = $request->lat;
        $user->longitude = $request->lon;
        $user->save();

        $feedback['status'] = 'success';
        $feedback['status_code'] = 200;
        $feedback['message'] = 'Locations updated successfully.';

        return response($feedback, 200);
    }

    public function consignments(Request $request) {

        $validator = Validator::make($request->all(), [
                    'date' => 'sometimes|date'
        ]);
        if ($validator->fails()) {
            $feedback['status'] = 'error';
            $feedback['status_code'] = 422;
            $feedback['message'] = $validator->errors()->all();
            return response($feedback, 200);
        }

        $user_id = Auth::guard('api')->user()->id;

        if ($request->has('date')) {
            $date = $request->date;
        } else {
            $date = date('Y-m-d');
        }

        $consignments = ConsignmentCommon::with('task')->orderBy('id', 'desc')
                ->where(function($query1) use($user_id) {
                    $query1->where('status', 2);
                    $query1->where('rider_id', $user_id);
                })
                ->orWhere(function($query2) use($user_id, $date) {
                    $query2->where('created_at', '>', $date . ' 00:00:00');
                    $query2->where('created_at', '<', $date . ' 23:59:59');
                    $query2->where('status', '>', 2);
                    $query2->where('rider_id', $user_id);
                })
                ->get();

        if (count($consignments) == 0) {
            $status = 'Not Found';
            $message[] = 'No data found';

            $feedback['status'] = $status;
            $feedback['status_code'] = 204;
            $feedback['message'] = $message;
        } else {
            $cons = $this->getConsignments($consignments);

            $status = 'Data Found';
            $message[] = 'Data available on response';

            $feedback['status'] = $status;
            $feedback['status_code'] = 200;
            $feedback['message'] = $message;
            $feedback['response']['consignments'] = $cons;
        }
        return response($feedback, 200);
    }

    public function history(Request $request) {
        $validator = Validator::make($request->all(), [
                    'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            $feedback['status'] = 'Error';
            $feedback['status_code'] = 204;
            $feedback['message'] = $validator->errors();
            $feedback['response'] = [];
            return $feedback;
        }
        $user_id = Auth::guard('api')->user()->id;
        $feedback['status'] = 'Success';
        $feedback['status_code'] = 200;
        $feedback['message'] = ['Data Found.'];
        $feedback['response'] = $this->taskHistory($user_id, $request->date);

        return response($feedback, 200);
    }

    public function otp(Request $request) {
        $task = ConsignmentTask::select('consignments_tasks.otp')
                ->join('sub_orders', 'sub_orders.id', '=', 'consignments_tasks.sub_order_id')
                ->where('sub_orders.unique_suborder_id', $request->sub_order_id)
                ->where('consignments_tasks.task_type_id', '!=', 1)
                ->orderBy('consignments_tasks.id', 'desc')
                ->first();
        if ($task) {
            return $task->otp;
        } else {
            return null;
        }
    }

}
