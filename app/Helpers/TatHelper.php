<?php
// Customer Support
function __get_reaction_dropdown(){
	$reactions = \App\CustomerSupportModel\Reaction::whereStatus(1)->pluck('title','id');
	return ($reactions ? $reactions->toArray() : null);
}
function __get_unique_heads_dropdown(){
	$unique_heads = \App\CustomerSupportModel\UniqueHead::whereStatus(1)->pluck('title','id');
	return ($unique_heads ? $unique_heads->toArray() : null);
}
function __get_mail_gropus_dropdown(){
	$mail_groups = \App\CustomerSupportModel\MailGroup::whereStatus(1)->pluck('team_title','id');
	return ($mail_groups ? $mail_groups->toArray() : null);
}
function __get_source_of_information_dropdown(){
	$src_of_infos = \App\CustomerSupportModel\SourceOfInformation::whereStatus(1)->pluck('title','id');
	return ($src_of_infos ? $src_of_infos->toArray() : null);
}
function __get_query_dropdown(){
	$querys = \App\CustomerSupportModel\Query::whereStatus(1)->pluck('title','id');
	return ($querys ? $querys->toArray() : null);
}
function __get_complain_submitted_user_dropdown(){
	$users = \App\CustomerSupportModel\Complain::with(['createdBy'])->where('created_by','!=',null)->groupBy('created_by')->get()->pluck('createdBy.name','created_by');
	return ($users ? $users->toArray() : null);
}
function __get_complain_status_dropdown(){
	$status = ['0'=> 'Unsolved','1'=>'In process','2'=>'Solved'];
	return $status;
}
function __get_feedback_status_dropdown(){
	$status = ['0'=> 'Pending','1'=>'Collected'];
	return $status;
}
function __get_feedback_rating_dropdown(){
	$ratings = [];
	for($i=1;$i<=5;$i++){
		$ratings["$i"] = $i;
	}
	return $ratings;
}
function __get_feedback_updated_user_dropdown(){
	$users = \App\CustomerSupportModel\FeedBack::with(['updatedBy'])->where('updated_by','!=',null)->groupBy('updated_by')->get()->pluck('updatedBy.name','updated_by');
	return ($users ? $users->toArray() : null);
}
function __get_rider_dropdown(){
	$rider = \App\User::select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
	->leftJoin('hubs','hubs.id','=','users.reference_id')->
	where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id');
	return ($rider ? $rider->toArray() : null);
}
function __get_inquiry_submitted_user_dropdown(){
	$users = \App\CustomerSupportModel\Inquiry::with(['createdBy'])->where('created_by','!=',null)->groupBy('created_by')->get()->pluck('createdBy.name','created_by');
	return ($users ? $users->toArray() : null);
}
function __get_inquiry_status_dropdown(){
	$status = \App\CustomerSupportModel\InquiryStatus::whereStatus(1)->pluck('title','id');
	return ($status ? $status->toArray() : null);
}
// End Customer Support
function pickingTasks($sub_order_id){
	$product = DB::table('order_product')->where('sub_order_id', $sub_order_id)->first();
	return $pickingTasks = DB::table('picking_task')
	->where('type', 'Picking')
	->where('product_unique_id', $product->product_unique_id)
	->orderBy('id', 'desc')
	->get();
}

function deliveryTasks($sub_order_id){
	$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
	return $deliveryTasks = DB::table('delivery_task')
	->where('unique_suborder_id', $sub_order->unique_suborder_id)
	->orderBy('id', 'desc')
	->get();
}

function returnTasks($sub_order_id){
	$product = DB::table('order_product')->where('sub_order_id', $sub_order_id)->first();
	return $pickingTasks = DB::table('picking_task')
	->where('product_unique_id', $product->product_unique_id)
	->where('type', 'Return')
	->orderBy('id', 'desc')
	->get();
}

function titLog($sub_order_id){
	return $log = DB::table('order_logs')
	->where('sub_order_id', $sub_order_id)
	->where('text', 'Product Trip in Transit')
	->where('type', '!=', 'reference')
	->orderBy('id', 'desc')
	->first();
}

function deliveryTat($sub_order_id){
	$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
	$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

	if(count($child_sub_orders) == 0){
			$allPickingTasks = pickingTasks($sub_order_id); // Picking Attempts
			$allDeliveryTasks = deliveryTasks($sub_order_id); // Delivery Attempts

			$picking_attempt = count($allPickingTasks);
			$delivery_attempt = count($allDeliveryTasks);

			$start = $sub_order->created_at;
			foreach ($allPickingTasks as $pickingTask) {
				if($pickingTask->status == 2 || $pickingTask->status == 3){
					$start = $pickingTask->created_at;
					break;
				}
			}

			$end = $sub_order->updated_at;
			foreach ($allDeliveryTasks as $deliveryTask) {
				if($deliveryTask->status == 2 || $deliveryTask->status == 3){
					$end = $deliveryTask->created_at;
					break;
				}
			}

			$tat = timeDifference($start, $end);

			return $output = array(
				"picking_attempt" => $picking_attempt,
				"delivery_attempt" => $delivery_attempt,
				"picked_at" => $start,
				"delivered_at" => $end,
				"tat" => $tat
			);

		}else{
			// Picking Attempts
			$allPickingTasks = collect([]);
			foreach ($child_sub_orders as $child_sub_order) {
				$pickingTasks = pickingTasks($child_sub_order->id);
				$allPickingTasks->push($pickingTasks);
			}

			// Delivery Attempts
			$allDeliveryTasks = collect([]);
			foreach ($child_sub_orders as $child_sub_order) {
				$deliveryTasks = deliveryTasks($child_sub_order->id);
				$allDeliveryTasks->push($deliveryTasks);
			}

			$picking_attempt = count($allPickingTasks);
			$delivery_attempt = count($allDeliveryTasks);

			$start = $sub_order->created_at;
			foreach ($allPickingTasks as $pickingTasks) {
				$i = 0;
				foreach ($pickingTasks as $pickingTask) {
					if($pickingTask->status == 2 || $pickingTask->status == 3){
						$start = $pickingTask->created_at;
						$i = 1;
						break;
					}
				}
				if($i == 1){ break; }
			}

			$end = $sub_order->updated_at;
			foreach ($allDeliveryTasks as $deliveryTasks) {
				$i = 0;
				foreach ($deliveryTasks as $deliveryTask) {
					if($deliveryTask->status == 2 || $deliveryTask->status == 3){
						$end = $deliveryTask->created_at;
						$i = 1;
						break;
					}
				}
				if($i == 1){ break; }
			}

			$tat = timeDifference($start, $end);

			return $output = array(
				"picking_attempt" => $picking_attempt,
				"delivery_attempt" => $delivery_attempt,
				"picked_at" => $start,
				"delivered_at" => $end,
				"tat" => $tat
			);
		}

	}

	function returnTat($sub_order_id){
		$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
		$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

		if(count($child_sub_orders) == 0){
			$allPickingTasks = pickingTasks($sub_order_id); // Picking Attempts
			$allDeliveryTasks = returnTasks($sub_order_id); // Delivery Attempts

			$picking_attempt = count($allPickingTasks);
			$delivery_attempt = count($allDeliveryTasks);

			$start = $sub_order->created_at;
			foreach ($allPickingTasks as $pickingTask) {
				if($pickingTask->status == 2 || $pickingTask->status == 3){
					$start = $pickingTask->created_at;
					break;
				}
			}

			$end = $sub_order->updated_at;
			foreach ($allDeliveryTasks as $deliveryTask) {
				if($deliveryTask->status == 2 || $deliveryTask->status == 3){
					$end = $deliveryTask->created_at;
					break;
				}
			}

			$tat = timeDifference($start, $end);

			return $output = array(
				"picking_attempt" => $picking_attempt,
				"delivery_attempt" => $delivery_attempt,
				"picked_at" => $start,
				"delivered_at" => $end,
				"tat" => $tat
			);

		}else{
			// Picking Attempts
			$allPickingTasks = collect([]);
			foreach ($child_sub_orders as $child_sub_order) {
				$pickingTasks = pickingTasks($child_sub_order->id);
				$allPickingTasks->push($pickingTasks);
			}

			// Delivery Attempts
			$allDeliveryTasks = collect([]);
			foreach ($child_sub_orders as $child_sub_order) {
				$deliveryTasks = returnTasks($child_sub_order->id);
				$allDeliveryTasks->push($deliveryTasks);
			}

			$picking_attempt = count($allPickingTasks);
			$delivery_attempt = count($allDeliveryTasks);

			$start = $sub_order->created_at;
			foreach ($allPickingTasks as $pickingTasks) {
				$i = 0;
				foreach ($pickingTasks as $pickingTask) {
					if($pickingTask->status == 2 || $pickingTask->status == 3){
						$start = $pickingTask->created_at;
						$i = 1;
						break;
					}
				}
				if($i == 1){ break; }
			}

			$end = $sub_order->updated_at;
			foreach ($allDeliveryTasks as $deliveryTasks) {
				$i = 0;
				foreach ($deliveryTasks as $deliveryTask) {
					if($deliveryTask->status == 2 || $deliveryTask->status == 3){
						$end = $deliveryTask->created_at;
						$i = 1;
						break;
					}
				}
				if($i == 1){ break; }
			}

			$tat = timeDifference($start, $end);

			return $output = array(
				"picking_attempt" => $picking_attempt,
				"delivery_attempt" => $delivery_attempt,
				"picked_at" => $start,
				"delivered_at" => $end,
				"tat" => $tat
			);
		}

	}

	function titTat($sub_order_id){
		$sub_order = DB::table('sub_orders')->where('id', $sub_order_id)->first();
		$child_sub_orders = DB::table('sub_orders')->where('parent_sub_order_id', $sub_order->id)->orderBy('id', 'desc')->get();

		if(count($child_sub_orders) == 0){
			$allPickingTasks = pickingTasks($sub_order_id); // Picking Attempts
			$tit = titLog($sub_order_id); // Trip in Transit

			$picking_attempt = count($allPickingTasks);

			$start = $sub_order->created_at;
			foreach ($allPickingTasks as $pickingTask) {
				if($pickingTask->status == 2 || $pickingTask->status == 3){
					$start = $pickingTask->created_at;
					break;
				}
			}

			$end = $tit->created_at;

			$tat = timeDifference($start, $end);

			return $output = array(
				"picking_attempt" => $picking_attempt,
				"picked_at" => $start,
				"triped_at" => $end,
				"tat" => $tat
			);

		}else{
			// Picking Attempts
			$allPickingTasks = collect([]);
			foreach ($child_sub_orders as $child_sub_order) {
				$pickingTasks = pickingTasks($child_sub_order->id);
				$allPickingTasks->push($pickingTasks);
			}

			// TIT Attempts
			$tits = collect([]);
			foreach ($child_sub_orders as $child_sub_order) {
				$deliveryTasks = titLog($child_sub_order->id);
				$tits->push($deliveryTasks);
			}

			$picking_attempt = count($allPickingTasks);

			$start = $sub_order->created_at;
			foreach ($allPickingTasks as $pickingTasks){
				$i = 0;
				foreach ($pickingTasks as $pickingTask) {
					if($pickingTask->status == 2 || $pickingTask->status == 3){
						$start = $pickingTask->created_at;
						$i = 1;
						break;
					}
				}
				if($i == 1){ break; }
			}

			$end = $sub_order->updated_at;
			foreach ($tits as $tit){
				$i = 0;
				foreach ($tit as $t) {
					$end = @$t->created_at;
					$i = 1;
					break;
				}
				if($i == 1){ break; }
			}

			$tat = timeDifference($start, $end);

			return $output = array(
				"picking_attempt" => $picking_attempt,
				"picked_at" => $start,
				"triped_at" => $end,
				"tat" => $tat
			);
		}

	}