<?php

namespace App\Http\Controllers\ApiV2;

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

class TaskController extends Controller
{
	use TaskTrait;

    public function tasks_list(Request $request){

    	// Get User Id
    	$user_id    =  Auth::guard('api')->user()->id;

    	// Get Consignment
    	$consignment = Consignment::where('status', 2)->where('rider_id', $user_id)->first();

    	// Consignment Breakdown
    	if(count($consignment) == 0){

    		// Not Found
    		$status        =  'Not Found';
	        $message[]       =  'No data found';

	        $feedback['status']        =  $status;
	        $feedback['status_code']   =  204;
	        $feedback['message']       =  $message;
	        // $feedback['response']      =  array();

    	}else{

    		// Get Task Locations
    		$tasks = $this->getTasksList($consignment->id, $consignment->type);

    		// Get Consignment Status
    		$consignment_status = $this->getConsignmentStatus($consignment->status);

			$status        =  'Data Found';
	        $message[]       =  'Data available on response';

	        $feedback['status']        =  $status;
	        $feedback['status_code']   =  200;
	        $feedback['message']       =  $message;
	        $feedback['response']['consignment'] = array(
	        											'consignment_unique_id' => $consignment->consignment_unique_id,
	        											'consignment_type' => $consignment->type,
	        											'consignment_status' => $consignment_status,
	        										);
	        $feedback['response']['tasks']      =  $tasks;

    	}

    	return response($feedback, 200);
    	
    }

    public function task_detail(Request $request){

    	if($request->has('task_group_id') && $request->has('consignment_unique_id')){

    		// Get Consignment
    		$consignment = Consignment::where('consignment_unique_id', $request->consignment_unique_id)->first();

    		// Consignment Breakdown
	    	if(count($consignment) == 0){

	    		// Not Found
	    		$status        =  'Not Found';
		        $message[]       =  'No data found';

		        $feedback['status']        =  $status;
		        $feedback['status_code']   =  204;
		        $feedback['message']       =  $message;
		        $feedback['response']      =  [];

	    	}else{

	    		// Get Task Detail
				$products = $this->getProducts($consignment->id, $consignment->type, $request->task_group_id);

				// Get Merchant Detail
				$merchant = $this->getMerchant($consignment->type, $request->task_group_id);

				// Get Address
				$address = $this->getAddress($consignment->type, $request->task_group_id);

				// FeedBack
				$status        =  'Data Found';
		        $message[]       =  'Data available on response';

		        $feedback['status']        =  $status;
		        $feedback['status_code']   =  200;
		        $feedback['message']       =  $message;
		        $feedback['response']['merchant']   =  $merchant;
		        $feedback['response']['address']   =  $address;
		        $feedback['response']['products']   =  $products;

	    	}

    	}else{

    		// Invalid
    		$status        =  'Invalid';
	        $message[]       =  'Invalid request';

	        $feedback['status']        =  $status;
	        $feedback['status_code']   =  400;
	        $feedback['message']       =  $message;
	        $feedback['response']      =  [];

    	}

    	return response($feedback, 200);

    }

    public function task_start(Request $request){

    	if($request->has('task_group_id') && $request->has('consignment_unique_id')){

    		// Get Consignment
    		$consignment = Consignment::where('consignment_unique_id', $request->consignment_unique_id)->first();

    		// Consignment Breakdown
	    	if(count($consignment) == 0){

	    		// Not Found
	    		$status        =  'Not Found';
		        $message[]       =  'No data found';

		        $feedback['status']        =  $status;
		        $feedback['status_code']   =  204;
		        $feedback['message']       =  $message;
		        $feedback['response']      =  [];

	    	}else{

	    		// Check Start
				$count = $this->checkStart($consignment->id, $consignment->type);

				if($count != 0){

					// Invalid
		    		$status        =  'Not Modified';
			        $message[]       =  'A task already running';

			        $feedback['status']        =  $status;
			        $feedback['status_code']   =  304;
			        $feedback['message']       =  $message;
			        $feedback['response']      =  [];

				}else{

					// Start Task
					$startTask = $this->startTask($consignment->id, $consignment->type, $request->task_group_id, $request->start_lat, $request->start_lon);

					if($startTask == 1){

						// FeedBack
						$status        =  'Task Start';
				        $message[]       =  'On the way to task location';

				        $feedback['status']        =  $status;
				        $feedback['status_code']   =  200;
				        $feedback['message']       =  $message;

					}

				}

	    	}

    	}else{

    		// Invalid
    		$status        =  'Invalid';
	        $message[]       =  'Invalid request';

	        $feedback['status']        =  $status;
	        $feedback['status_code']   =  400;
	        $feedback['message']       =  $message;
	        $feedback['response']      =  [];

    	}

    	return response($feedback, 200);

    }

    public function task_submit(Request $request){

    	// return $request->all();

    	if($request->has('task_group_id') && $request->has('consignment_unique_id')){

    		// Get Consignment
    		$consignment = Consignment::where('consignment_unique_id', $request->consignment_unique_id)->first();

    		// Consignment Breakdown
	    	if(count($consignment) == 0){

	    		// Not Found
	    		$status        =  'Not Found';
		        $message[]       =  'No data found';

		        $feedback['status']        =  $status;
		        $feedback['status_code']   =  204;
		        $feedback['message']       =  $message;
		        $feedback['response']      =  [];

	    	}else{

	    		// Check Start
				$count = $this->checkStart($consignment->id, $consignment->type);

				if($count == 0){

					// Invalid
		    		$status        =  'Not Modified';
			        $message[]       =  "This task isn't running yet or completed";

			        $feedback['status']        =  $status;
			        $feedback['status_code']   =  304;
			        $feedback['message']       =  $message;
			        $feedback['response']      =  [];

				}else{

					// End Task
					// $data = $request->all();
					$endTask = $this->endTask($consignment, $request);

					if($endTask != 0){

						// FeedBack
						$status        =  'Task Complete';
				        $message[]       =  'The task is complete';

				        $feedback['status']        =  $status;
				        $feedback['status_code']   =  200;
				        $feedback['message']       =  $message;
				        $feedback['response']      =  $endTask;

					}

				}

	    	}

    	}else{

    		// Invalid
    		$status        =  'Invalid';
	        $message[]       =  'Invalid request';

	        $feedback['status']        =  $status;
	        $feedback['status_code']   =  400;
	        $feedback['message']       =  $message;
	        $feedback['response']      =  [];

    	}

    	return response($feedback, 200);

    }

    public function reconciliation_req(Request $request)
    {

      $consignment          =  Consignment::where('consignment_unique_id', '=', $request->consignment_unique_id)->first();
      if(count($consignment) == 1){

      	switch (strtolower($consignment->type)) {

            case 'picking':

            	$due = PickingTask::where('consignment_id', $consignment->id)
            						->where('status', '<', 2)
            						->count();

                break;

            case 'delivery':
                
                $due = DeliveryTask::where('consignment_id', $consignment->id)
            						->where('status', '<', 2)
            						->count();

                break;

        }

        if($due == 0){

        	$consignment->status  =  3;
	        $consignment->save();

	        if(count($consignment->picking) > 0){
	          foreach ($consignment->picking as $task) {

	            if($task->product->sub_order->return == '0'){
	              // Update Sub-Order Status
	              $this->suborderStatus($task->product->sub_order->id, '9');
	            }

	          }
	        }

	        /**
	        * Sending response
	        * @return varified or updated
	        */
	        $status        =  'success';
	        $status_code   =  200;
	        $message[]       =  'Reconcilation Requested';

        }else{

        	$status        =  'Not Modified';
        	$status_code   =  304;
	        $message[]       =  'All task need to be finished';

        }
       
      }else{
        /**
        * Sending response
        * @return varified or updated
        */
        $status        =  'failed';
        $status_code   =  404;
        $message[]       =  'Reconcilation request Failed';
      }

      $feedback['status']        =  $status;
      $feedback['status_code']   =  $status_code;
      $feedback['message']       =  $message;
      // $feedback['response']      =  [];

      return response($feedback, 200);
    }

    public function location(Request $request){

        $validator = Validator::make($request->all(), [
        	'consignment_unique_id' => 'sometimes',
            'lat'  =>  'required',
            'lon' =>  'required'
        ]);

        if($validator->fails()) {
            return $this->sendResponse('error', 422, $validator->errors()->all(), []);
        }

        $location =  new RiderLocation();
        $location->rider_id = Auth::guard('api')->user()->id;
        $location->latitude = $request->lat;
        $location->longitude = $request->lon;
        if($request->has('consignment_unique_id')){
	        $location->consignment_unique_id = $request->consignment_unique_id;
	    }
        $location->created_at = date("Y-m-d H:i:s");
        $location->updated_at = date("Y-m-d H:i:s");
        $location->save();

        $user = User::where('id', Auth::guard('api')->user()->id)->first();
        $user->latitude = $location->latitude;
        $user->longitude = $location->longitude;
        $user->save();

        $feedback['status']        =  'success';
	    $feedback['status_code']   =  200;
	    $feedback['message']       =  'Locations updated successfully.';

		return response($feedback, 200);

    }

    public function location_verify(Request $request){

    	$validator = Validator::make($request->all(), [
        	'consignment_unique_id' => 'required',
        	'task_group_id' => 'required',
            'lat'  =>  'required',
            'lon' =>  'required'
        ]);

        if($validator->fails()) {
            return $this->sendResponse('error', 422, $validator->errors()->all(), []);
        }

        $consignment = Consignment::where('consignment_unique_id', $request->consignment_unique_id)->first();

        if(count($consignment) > 0){

        	switch ($consignment->type) {
	        	case 'picking':
	        		
	        		$tasks = PickingTask::select('picking_task.id', 'op.pickup_location_id')
	        							->leftJoin('order_product AS op','op.product_unique_id', '=', 'picking_task.product_unique_id')
	        							->where('picking_task.consignment_id', $consignment->id)
	        							->where('op.pickup_location_id', $request->task_group_id)
	        							->groupBy('op.pickup_location_id')->get();

	        		foreach ($tasks as $task) {
	        			$picking_task = PickingTask::where('id', $task->id)->first();
	        			$picking_task->end_lat = $request->lat;
	        			$picking_task->end_long = $request->lon;
	        			$picking_task->save();

	        			$picking_location = PickingLocations::where('id', $task->pickup_location_id)->first();
	        			$picking_location->latitude = $request->lat;
	        			$picking_location->longitude = $request->lon;
	        			$picking_location->save();
	        		}

	        		break;
	        	
	        	case 'delivery':
	        		
	        		$tasks = DeliveryTask::select('delivery_task.id', 'so.order_id')
	        							->leftJoin('sub_orders AS so','so.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
	        							->where('delivery_task.consignment_id', $consignment->id)
	        							->where('so.order_id', $request->task_group_id)
	        							->groupBy('so.order_id')->get();

	        		foreach ($tasks as $task) {
	        			$delivery_task = DeliveryTask::where('id', $task->id)->first();
	        			$delivery_task->end_lat = $request->lat;
	        			$delivery_task->end_long = $request->lon;
	        			$delivery_task->save();

	        			$order = Order::where('id', $task->order_id)->first();
	        			$order->delivery_latitude = $request->lat;
	        			$order->delivery_longitude = $request->lon;
	        			$order->save();
	        		}

	        		break;
	        }

	        $status        =  'success';
	        $status_code   =  200;
	        $message[]       =  'Location Verified';
			$feedback['status']        =  $status;
		    $feedback['status_code']   =  $status_code;
		    $feedback['message']       =  $message;

        }else{
        	// Invalid
    		$status        =  'Invalid';
	        $message[]       =  'Invalid request';

	        $feedback['status']        =  $status;
	        $feedback['status_code']   =  400;
	        $feedback['message']       =  $message;
        }

        return response($feedback, 200);

    }

}
