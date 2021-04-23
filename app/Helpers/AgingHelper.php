<?php

	function pickReqLog($sub_order_id){
		return $log = DB::table('order_logs')
					->where('sub_order_id', $sub_order_id)
					->where('text', 'Pickup requested')
					->where('type', '!=', 'reference')
					->orderBy('id', 'desc')
					->first();
	}

	function pickedLog($sub_order_id){
		return $log = DB::table('order_logs')
					->where('sub_order_id', $sub_order_id)
					->whereIn('text', ['Picked', 'Partial Picked'])
					->where('type', '!=', 'reference')
					->orderBy('id', 'desc')
					->first();
	}

	function rackedDestinationLog($sub_order_id){
		return $log = DB::table('order_logs')
					->where('sub_order_id', $sub_order_id)
					->whereIn('text', ['Full Order Racked at Destination Hub', 'Partial Order Racked at Destination Hub'])
					->where('type', '!=', 'reference')
					->orderBy('id', 'desc')
					->first();
	}

	function rackedSourceLog($sub_order_id){
		return $log = DB::table('order_logs')
					->where('sub_order_id', $sub_order_id)
					->whereIn('text', ['Full Product Rakced at Pickup Hub', 'Partial Product Racked at Pickup Hub'])
					->where('type', '!=', 'reference')
					->orderBy('id', 'desc')
					->first();
	}

	function deliveredLog($sub_order_id){
		return $log = DB::table('order_logs')
					->where('sub_order_id', $sub_order_id)
					->whereIn('text', ['Product delivered to customer', 'Products Partial Delivered to Customer'])
					->where('type', '!=', 'reference')
					->orderBy('id', 'desc')
					->first();
	}

	function countDeliveryTasks($sub_order_id){
		$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
		return $deliveryTasks = DB::table('delivery_task')
							->where('unique_suborder_id', $sub_order->unique_suborder_id)
							->orderBy('id', 'desc')
							->count();
	}

	function returnedLog($sub_order_id){
		return $log = DB::table('order_logs')
					->where('sub_order_id', $sub_order_id)
					->whereIn('text', ['Return Completed'])
					->where('type', '!=', 'reference')
					->orderBy('id', 'desc')
					->first();
	}

	function pickupAging($sub_order_id){

		try {
			
			$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
			$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

			if(count($child_sub_orders) == 0){
				$pick_req = pickReqLog($sub_order_id); // Pickup requested
				$allPickingTasks = pickingTasks($sub_order_id); // Picking Attempts
				$picked = pickedLog($sub_order_id); // Picked

				$picking_attempt = count($allPickingTasks);

				$start = $pick_req->created_at;

				$end = $picked->updated_at;

				if($start == ''){
					$aging = "Picking Request time not found";
				}else if($end == ''){
					$aging = "Picked time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"picking_attempt" => $picking_attempt,
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);

			}else{
				// Picking Attempts
				$allPickingTasks = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$pickingTasks = pickingTasks($child_sub_order->id);
					$allPickingTasks->push($pickingTasks);
				}

				// pick_reqs Attempts
				$pick_reqs = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = pickReqLog($child_sub_order->id);
					$pick_reqs->push($logs);
				}

				// picked Attempts
				$pickeds = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = pickedLog($child_sub_order->id);
					$pickeds->push($logs);
				}

				$picking_attempt = count($allPickingTasks);

				$start = '';
				foreach ($pick_reqs as $pick_req){
					$start = $pick_req->created_at;
					break;
				}

				$end = '';
				foreach ($pickeds as $picked){
					$end = $picked->created_at;
					break;
				}

				if($start == ''){
					$aging = "Picking Request time not found";
				}else if($end == ''){
					$aging = "Picked time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"picking_attempt" => $picking_attempt,
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);
			}

		} catch (\Exception $e) {
			\Log::info($e);
		}

	}

	function deliveryAging($sub_order_id){

		try {
			
			$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
			$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

			if(count($child_sub_orders) == 0){
				$racked = rackedDestinationLog($sub_order_id); // Racked
				$delivered = deliveredLog($sub_order_id); // Delivered

				$delivery_attempt = countDeliveryTasks($sub_order_id);

				$start = $racked->created_at;

				$end = $delivered->updated_at;

				if($start == ''){
					$aging = "Racked at destination hub time not found";
				}else if($end == ''){
					$aging = "Delivered time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"delivery_attempt" => $delivery_attempt,
									// "pickup_requested_at" => $pickup_requested_at,
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);

			}else{

				$delivery_attempt = 0;

				// rackeds Attempts
				$rackeds = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = rackedDestinationLog($child_sub_order->id);
					$rackeds->push($logs);

					$delivery_attempt = $delivery_attempt + countDeliveryTasks($sub_order_id);
				}

				// delivered Attempts
				$delivereds = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = deliveredLog($child_sub_order->id);
					$delivereds->push($logs);
				}

				$start = '';
				foreach ($rackeds as $racked){
					$start = $racked->created_at;
					break;
				}

				$end = '';
				foreach ($delivereds as $delivered){
					$end = $delivered->created_at;
					break;
				}

				if($start == ''){
					$aging = "Racked at destination hub time not found";
				}else if($end == ''){
					$aging = "Delivered time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"delivery_attempt" => $delivery_attempt,
									// "pickup_requested_at" => $pickup_requested_at,
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);
			}

		} catch (\Exception $e) {
			\Log::info($e);
		}

	}

	function returnAging($sub_order_id){

		try {
			
			$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
			$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

			if(count($child_sub_orders) == 0){
				$racked = rackedDestinationLog($sub_order_id); // Racked
				
				$returned = returnedLog($sub_order_id); // Returned

				$delivery_attempt = countDeliveryTasks($sub_order_id);

				$start = $racked->created_at;

				$end = $returned->updated_at;

				if($start == ''){
					$aging = "Racked at destination hub time not found";
				}else if($end == ''){
					$aging = "Returned time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"delivery_attempt" => $delivery_attempt,
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);

			}else{

				$delivery_attempt = 0;

				// rackeds Attempts
				$rackeds = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = rackedDestinationLog($child_sub_order->id);
					$rackeds->push($logs);

					$delivery_attempt = $delivery_attempt + countDeliveryTasks($sub_order_id);
				}

				// returned Attempts
				$returneds = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = returnedLog($child_sub_order->id);
					$returneds->push($logs);
				}

				$start = '';
				foreach ($rackeds as $racked){
					$start = $racked->created_at;
					break;
				}

				$end = '';
				// return $returneds;
				foreach ($returneds as $returned){
					$end = $returned->created_at;
					break;
				}

				if($start == ''){
					$aging = "Racked at destination hub time not found";
				}else if($end == ''){
					$aging = "Returned time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"delivery_attempt" => $delivery_attempt,
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);
			}

		} catch (\Exception $e) {
			\Log::info($e);
		}

	}

	function tripAging($sub_order_id){

		try {
			
			$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
			$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

			if(count($child_sub_orders) == 0){
				$racked_destination = rackedDestinationLog($sub_order_id); // Racked
				$racked_source = rackedSourceLog($sub_order_id); // Racked Pickup

				$end = $racked_destination->created_at;

				$start = $racked_source->updated_at;

				if($start == ''){
					$aging = "Racked at destination hub time not found";
				}else if($end == ''){
					$aging = "Delivered time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);

			}else{

				// racked_destinations
				$racked_destinations = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = rackedDestinationLog($child_sub_order->id);
					$racked_destinations->push($logs);
				}

				// racked_sources
				$racked_sources = collect([]);
				foreach ($child_sub_orders as $child_sub_order) {
					$logs = rackedSourceLog($child_sub_order->id);
					$racked_sources->push($logs);
				}

				$end = '';
				foreach ($racked_destinations as $racked){
					$end = $racked->created_at;
					break;
				}

				$start = '';
				foreach ($racked_sources as $racked_source){
					$start = $racked_source->created_at;
					break;
				}

				if($start == ''){
					$aging = "Racked at destination hub time not found";
				}else if($end == ''){
					$aging = "Delivered time not found";
				}else{
					$aging = timeDifference($start, $end);
				}

				return $output = array(
									"start_at" => $start,
									"end_at" => $end,
									"aging" => $aging
								);
			}

		} catch (\Exception $e) {
			\Log::info($e);
		}

	}


	