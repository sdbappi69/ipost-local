<?php

namespace App\Http\Traits;

use App\ConsignmentCommon;
use App\ConsignmentTask;
use App\PickingTask;
use App\DeliveryTask;
use App\OrderProduct;
use App\PickingLocations;
use App\Consignment;
use App\Order;
use App\SubOrder;
use App\Http\Traits\LogsTrait;
use Auth;
use DB;
use Log;

trait TaskTrait {

    use LogsTrait;

    // Get Task Locations
    public function getTasksList($user_id) {
        $consignments = ConsignmentCommon::whereStatus(2)->whereRiderId($user_id)->get();

        $all_task = array();
        $key = 0;

        foreach ($consignments as $consignment) {
            foreach ($consignment->task as $task) {
                if ($task->status > 1) {
                    continue; // complete/failed task not handle in app task list
                }
                switch ($task->task_type_id) {
                    case 1:
                        $type = 'Pick';
                        $title = $task->suborder->product->product_unique_id;
                        $picking_title = $task->suborder->product->pickup_location->title;
                        $picking_address = $task->suborder->product->pickup_location->address1;
                        $delivery_title = $task->suborder->product->pickup_location->zone->hub->title;
                        $delivery_address = $task->suborder->product->pickup_location->zone->hub->address1;
                        break;
                    case 2:
                        $type = 'Delivery';
                        $title = $task->suborder->order->delivery_name;
                        $picking_title = $task->suborder->order->delivery_zone->hub->title;
                        $picking_address = $task->suborder->order->delivery_zone->hub->address1;
                        $delivery_title = $task->suborder->order->delivery_name;
                        $delivery_address = $task->suborder->order->delivery_address1;
                        break;
                    case 6: // post delivery order return to buyer
                        $type = 'Return';
                        $title = $task->suborder->order->delivery_name;
                        $picking_title = $task->suborder->order->delivery_zone->hub->title;
                        $picking_address = $task->suborder->order->delivery_zone->hub->address1;
                        $delivery_title = $task->suborder->order->delivery_name;
                        $delivery_address = $task->suborder->order->delivery_address1;
                        break;
                    case 3: // pick & delivery
                        $type = 'Delivery';
                        $title = $task->suborder->order->delivery_name;
                        $picking_title = $task->suborder->product->pickup_location->title;
                        $picking_address = $task->suborder->product->pickup_location->address1;
                        $delivery_title = $task->suborder->order->delivery_name;
                        $delivery_address = $task->suborder->order->delivery_address1;
                        break;
                    case 7: // delivery to return
                        $type = 'Return';
                        $title = $task->suborder->product->pickup_location->title;
                        $picking_title = $task->suborder->order->delivery_name;
                        $picking_address = $task->suborder->order->delivery_address1;
                        $delivery_title = $task->suborder->product->pickup_location->title;
                        $delivery_address = $task->suborder->product->pickup_location->address1;
                        break;
                    case 4:
                        $type = 'Return';
                        $title = $task->suborder->product->pickup_location->title;
                        $picking_title = $task->suborder->product->pickup_location->zone->hub->title;
                        $picking_address = $task->suborder->product->pickup_location->zone->hub->address1;
                        $delivery_title = $task->suborder->product->pickup_location->title;
                        $delivery_address = $task->suborder->product->pickup_location->address1;
                        break;
                    case 5:
                        $type = 'Pick';
                        $title = $task->suborder->product->product_unique_id;
                        $picking_title = $task->suborder->order->delivery_name;
                        $picking_address = $task->suborder->order->delivery_address1;
                        $delivery_title = $task->suborder->order->delivery_zone->hub->title;
                        $delivery_address = $task->suborder->order->delivery_zone->hub->address1;
                        break;
                    default:
                        $type = '';
                        $title = '';
                        $picking_title = '';
                        $picking_address = '';
                        $delivery_title = '';
                        $delivery_address = '';
                        break;
                }
                if ($task->status > 1) {
                    $task_status_text = trans('api.task_status.done');
                } else if ($task->status == 1) {
                    $task_status_text = trans('api.task_status.processing');
                } else {
                    $task_status_text = trans('api.task_status.pending');
                }
                $all_task[$key]['task_id'] = $task->id;
                $all_task[$key]['task_type'] = $type;
                $all_task[$key]['task_title'] = str_replace('Package', trans('api.package'), $task->suborder->product->product_title) or '';
                $all_task[$key]['product_unique_id'] = $task->suborder->product->product_unique_id or '';
                $all_task[$key]['picking_title'] = $picking_title;
                $all_task[$key]['picking_address'] = $picking_address;
                $all_task[$key]['picking_latitude'] = $task->start_lat;
                $all_task[$key]['picking_longitude'] = $task->start_long;
                $all_task[$key]['delivery_title'] = $delivery_title;
                $all_task[$key]['delivery_address'] = $delivery_address;
                $all_task[$key]['delivery_latitude'] = $task->end_lat;
                $all_task[$key]['delivery_longitude'] = $task->end_long;
                $all_task[$key]['distance'] = $task->distance ? $task->distance : '0.00';
                $all_task[$key]['otp'] = $task->otp;
                $all_task[$key]['quantity'] = (int) $task->quantity;
                $all_task[$key]['amount'] = ($task->suborder->order->paymentMethod->id == 2) ? 0 : (int) $task->amount;
                $all_task[$key]['consignment_unique_id'] = $consignment->consignment_unique_id;
                $all_task[$key]['task_status'] = $task_status_text;
                $key++;
            }
        }
        $completed = $this->completedTaskList($user_id);

        $all_task = array_merge($all_task, $completed);

        return $all_task;
    }

    public function completedTaskList($user_id) {

        return $all_completed = [];

        $consignments = ConsignmentCommon::where('updated_at', '>', date('Y-m-d') . ' 00:00:00')
                ->where('updated_at', '<', date('Y-m-d') . ' 23:59:59')
                ->where('status', '>', 2)
                ->where('rider_id', $user_id)
                ->get();

        foreach ($consignments as $consignment) {

            $completed = array();
            foreach ($consignment->task as $key => $task) {
                switch ($task->task_type_id) {
                    case 1:
                        $type = 'Pick';
                        $title = $task->suborder->product->product_unique_id;
                        $picking_title = $task->suborder->product->pickup_location->title;
                        $picking_address = $task->suborder->product->pickup_location->address1;
                        $delivery_title = $task->suborder->product->pickup_location->zone->hub->title;
                        $delivery_address = $task->suborder->product->pickup_location->zone->hub->address1;
                        break;
                    case 2:
                    case 6: // post delivery order return to buyer
                        $type = 'Delivery';
                        $title = $task->suborder->order->delivery_name;
                        $picking_title = $task->suborder->order->delivery_zone->hub->title;
                        $picking_address = $task->suborder->order->delivery_zone->hub->address1;
                        $delivery_title = $task->suborder->order->delivery_name;
                        $delivery_address = $task->suborder->order->delivery_address1;
                        break;
                    case 3:
                        $type = 'Pick & Deliver';
                        $title = $task->suborder->order->delivery_name;
                        $picking_title = $task->suborder->product->pickup_location->title;
                        $picking_address = $task->suborder->product->pickup_location->address1;
                        $delivery_title = $task->suborder->order->delivery_name;
                        $delivery_address = $task->suborder->order->delivery_address1;
                        break;
                    case 7: // delivery to return
                        $type = 'Return';
                        $title = $task->suborder->product->pickup_location->title;
                        $picking_title = $task->suborder->order->delivery_name;
                        $picking_address = $task->suborder->order->delivery_address1;
                        $delivery_title = $task->suborder->product->pickup_location->title;
                        $delivery_address = $task->suborder->product->pickup_location->address1;
                        break;
                    case 4:
                        $type = 'Return';
                        $title = $task->suborder->product->pickup_location->title;
                        $picking_title = $task->suborder->product->pickup_location->title;
                        $picking_address = $task->suborder->product->pickup_location->address1;
                        $delivery_title = $task->suborder->product->pickup_location->zone->hub->title;
                        $delivery_address = $task->suborder->product->pickup_location->zone->hub->address1;
                        break;
                    case 5:
                        $type = 'Pick';
                        $title = $task->suborder->product->product_unique_id;
                        $picking_title = $task->suborder->order->delivery_name;
                        $picking_address = $task->suborder->order->delivery_address1;
                        $delivery_title = $task->suborder->order->delivery_zone->hub->title;
                        $delivery_address = $task->suborder->order->delivery_zone->hub->address1;
                        break;
                    default:
                        $type = '';
                        $title = '';
                        $picking_title = '';
                        $picking_address = '';
                        $delivery_title = '';
                        $delivery_address = '';
                        break;
                }
                $task_status_text = trans('api.task_status.completed');
                $completed[$key]['task_id'] = $task->id;
                $completed[$key]['task_type'] = $type;
                $completed[$key]['task_title'] = str_replace('Package', trans('api.package'), $task->suborder->product->product_title) or '';
                $completed[$key]['picking_title'] = $picking_title;
                $completed[$key]['picking_address'] = $picking_address;
                $completed[$key]['picking_latitude'] = $task->start_lat;
                $completed[$key]['picking_longitude'] = $task->start_long;
                $completed[$key]['delivery_title'] = $delivery_title;
                $completed[$key]['delivery_address'] = $delivery_address;
                $completed[$key]['delivery_latitude'] = $task->end_lat;
                $completed[$key]['delivery_longitude'] = $task->end_long;
                $completed[$key]['distance'] = $task->distance ? $task->distance : '0.00';
                $completed[$key]['consignment_unique_id'] = $consignment->consignment_unique_id;
                $completed[$key]['task_status'] = $task_status_text;
                $completed[$key]['quantity'] = (int) $task->quantity;
                $completed[$key]['amount'] = ($task->suborder->order->paymentMethod->id == 2) ? 0 : (int) $task->amount;
            }

            $all_completed = array_merge($all_completed, $completed);
        }

        return $all_completed;
    }

    // Get Consignment Status
    public function getConsignmentStatus($consignment_status) {

        switch ($consignment_status) {
            case '0':
                $consignment_status = trans('api.task_status.canceled');
                break;
            case '1':
                $consignment_status = trans('api.task_status.waiting');
                break;
            case '2':
                $consignment_status = trans('api.task_status.running');
                break;
            case '3':
                $consignment_status = trans('api.task_status.requested');
                break;
            case '4':
                $consignment_status = trans('api.task_status.completed');
                break;
        }

        return $consignment_status;
    }

    // Get Task Detail
    public function getProducts($task_id) {
        $task = ConsignmentTask::find($task_id);
        $products_list = [];
        switch ($task->task_type_id) {
            case 1:
            case 5:
                $type = 'Pick';
                $langType = trans('api.pick');
                break;
            case 2:
                $type = 'Delivery';
                $langType = trans('api.delivery');
                break;
            case 3:
                $type = 'Pick & Deliver';
                $langType = trans('api.pick_deliver');
                break;
            case 4:
            case 6:
            case 7:
                $type = 'Return';
                $langType = trans('api.return');
                break;
            default:
                $type = '';
                $langType = '';
                break;
        }
        $products_list['task_id'] = $task->id;
        $products_list['task_type'] = $langType;
        $products_list['unique_suborder_id'] = $task->suborder->unique_suborder_id;
        $products_list['merchant_order_id'] = $task->suborder->order->merchant_order_id;
        $products_list['store_id'] = $task->suborder->order->store_id;
        $products_list['title'] = str_replace('Package', trans('api.package'), $task->suborder->product->product_title);
        $products_list['category'] = $task->suborder->product->product_category;
        $products_list['quantity'] = $task->quantity;
        if ($type == 'Pick' || $type == 'Return') {
            $products_list['unit_product_price'] = 0;
            $products_list['unit_delivery_charge'] = 0;
            $products_list['total_product_price'] = 0;
            $products_list['total_delivery_charge'] = 0;
            $products_list['payable_product_price'] = 0;
            $products_list['total_payable_amount'] = 0;
        } else {
            $products_list['unit_product_price'] = (int) $task->suborder->product->unit_price;
            $products_list['unit_delivery_charge'] = (int) $task->suborder->product->unit_deivery_charge;
            $products_list['total_product_price'] = (int) $task->suborder->product->sub_total;
            $products_list['total_delivery_charge'] = (int) $task->suborder->product->total_delivery_charge;
            $products_list['payable_product_price'] = ($task->suborder->order->paymentMethod->id == 2) ? 0 : (int) $task->suborder->product->payable_product_price; // for e-payment collect-able amount is 0
            $products_list['total_payable_amount'] = ($task->suborder->order->paymentMethod->id == 2 || $task->suborder->product->total_payable_amount == null) ? 0 : (int) $task->suborder->product->total_payable_amount;
        }
        $products_list['delivery_pay_by_cus'] = ($task->suborder->product->delivery_pay_by_cus == null) ? 0 : (int) $task->suborder->product->delivery_pay_by_cus;
        $products_list['start_time'] = ($task->start_time == null) ? "" : $task->start_time;
        $products_list['end_time'] = ($task->end_time == null) ? "" : $task->end_time;
        $products_list['distance'] = $task->distance ? $task->distance : '0.00';


        return $products_list;
    }

    // Check Start
    public function checkStart($consignment_id, $consignment_type) {
        switch (strtolower($consignment_type)) {

            case 'picking':

                // Start
                $count = PickingTask::where('consignment_id', $consignment_id)
                        ->where('status', 1)
                        ->count();

                break;

            case 'delivery':

                // Start
                $count = DeliveryTask::where('consignment_id', $consignment_id)
                        ->where('status', 1)
                        ->count();

                break;
        }

        return $count;
    }

    // Start Task
    public function startTask($task_id, $start_lat, $start_lon) {
        Log::info("TaskTrait startTask in use");
        $task = ConsignmentTask::find($task_id);
        $task->status = 1;
        if (!empty(floatval($start_lat)) && !empty(floatval($start_lon))) {
            $task->start_lat = $start_lat;
            $task->start_long = $start_lon;
        }
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
                $this->suborderStatus($task->sub_order_id, '5');
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
                $this->suborderStatus($task->sub_order_id, '7');
                break;
            case 5:
                // Return Picking Attempt
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
                $this->suborderStatus($task->sub_order_id, '5');
                break;
        }

        return 1;
    }

    // Get Merchant Detail
    public function getMerchant($task_id) {
        $task = ConsignmentTask::find($task_id);
        $merchant = array(
            'merchant_name' => $task->suborder->order->store->merchant->name,
            'merchant_email' => $task->suborder->order->store->merchant->email,
            'merchant_msisdn' => $task->suborder->order->store->merchant->msisdn,
            'merchant_alt_msisdn' => ($task->suborder->order->store->merchant->alt_msisdn == null) ? "" : $task->suborder->order->store->merchant->alt_msisdn
        );

        return $merchant;
    }

    // Get Address Detail
    public function getAddress($task_id) {
        $task = ConsignmentTask::find($task_id);
//        dd($task->suborder->product);
        switch ($task->task_type_id) {
            case 1:
                $picking_title = $task->suborder->product->pickup_location->title;
                $picking_email = $task->suborder->product->pickup_location->email;
                $picking_msisdn = $task->suborder->product->pickup_location->msisdn;
                $picking_alt_msisdn = $task->suborder->product->pickup_location->alt_msisdn or '';
                $picking_address1 = $task->suborder->product->pickup_location->address1;
                $picking_address2 = $task->suborder->product->pickup_location->address2;
                $picking_zone = $task->suborder->product->pickup_location->zone->name;
                $picking_city = $task->suborder->product->pickup_location->zone->city->name;
                $picking_state = $task->suborder->product->pickup_location->zone->city->state->name;

                $delivery_title = $task->suborder->source_hub->title;
                $delivery_email = $task->suborder->source_hub->email;
                $delivery_msisdn = $task->suborder->source_hub->msisdn;
                $delivery_alt_msisdn = $task->suborder->source_hub->alt_msisdn or '';
                $delivery_address1 = $task->suborder->source_hub->address1;
                $delivery_address2 = $task->suborder->source_hub->address2;
                $delivery_zone = $task->suborder->source_hub->zone->name;
                $delivery_city = $task->suborder->source_hub->zone->city->name;
                $delivery_state = $task->suborder->source_hub->zone->city->state->name;
                break;
            case 2:
            case 6: // post delivery order return to buyer
                $picking_title = $task->suborder->destination_hub->title;
                $picking_email = $task->suborder->destination_hub->email;
                $picking_msisdn = $task->suborder->destination_hub->msisdn;
                $picking_alt_msisdn = $task->suborder->destination_hub->alt_msisdn or '';
                $picking_address1 = $task->suborder->destination_hub->address1;
                $picking_address2 = $task->suborder->destination_hub->address2;
                $picking_zone = $task->suborder->destination_hub->zone->name;
                $picking_city = $task->suborder->destination_hub->zone->city->name;
                $picking_state = $task->suborder->destination_hub->zone->city->state->name;

                $delivery_title = $task->suborder->order->delivery_name;
                $delivery_email = $task->suborder->order->delivery_email;
                $delivery_msisdn = $task->suborder->order->delivery_msisdn;
                $delivery_alt_msisdn = $task->suborder->order->delivery_alt_msisdn or '';
                $delivery_address1 = $task->suborder->order->delivery_address1;
                $delivery_address2 = $task->suborder->order->delivery_address2;
                $delivery_zone = $task->suborder->order->delivery_zone->name;
                $delivery_city = $task->suborder->order->delivery_zone->city->name;
                $delivery_state = $task->suborder->order->delivery_zone->city->state->name;
                break;
            case 3:
                $picking_title = $task->suborder->product->pickup_location->title;
                $picking_email = $task->suborder->product->pickup_location->email;
                $picking_msisdn = $task->suborder->product->pickup_location->msisdn;
                $picking_alt_msisdn = $task->suborder->product->pickup_location->alt_msisdn or '';
                $picking_address1 = $task->suborder->product->pickup_location->address1;
                $picking_address2 = $task->suborder->product->pickup_location->address2;
                $picking_zone = $task->suborder->product->pickup_location->zone->name;
                $picking_city = $task->suborder->product->pickup_location->zone->city->name;
                $picking_state = $task->suborder->product->pickup_location->zone->city->state->name;

                $delivery_title = $task->suborder->order->delivery_name;
                $delivery_email = $task->suborder->order->delivery_email;
                $delivery_msisdn = $task->suborder->order->delivery_msisdn;
                $delivery_alt_msisdn = $task->suborder->order->delivery_alt_msisdn or '';
                $delivery_address1 = $task->suborder->order->delivery_address1;
                $delivery_address2 = $task->suborder->order->delivery_address2;
                $delivery_zone = $task->suborder->order->delivery_zone->name;
                $delivery_city = $task->suborder->order->delivery_zone->city->name;
                $delivery_state = $task->suborder->order->delivery_zone->city->state->name;
                break;
            case 7:
                $picking_title = $task->suborder->order->delivery_name;
                $picking_email = $task->suborder->order->delivery_email;
                $picking_msisdn = $task->suborder->order->delivery_msisdn;
                $picking_alt_msisdn = $task->suborder->order->delivery_alt_msisdn or '';
                $picking_address1 = $task->suborder->order->delivery_address1;
                $picking_address2 = $task->suborder->order->delivery_address2;
                $picking_zone = $task->suborder->order->delivery_zone->name;
                $picking_city = $task->suborder->order->delivery_zone->city->name;
                $picking_state = $task->suborder->order->delivery_zone->city->state->name;

                $delivery_title = $task->suborder->product->pickup_location->title;
                $delivery_email = $task->suborder->product->pickup_location->email;
                $delivery_msisdn = $task->suborder->product->pickup_location->msisdn;
                $delivery_alt_msisdn = $task->suborder->product->pickup_location->alt_msisdn or '';
                $delivery_address1 = $task->suborder->product->pickup_location->address1;
                $delivery_address2 = $task->suborder->product->pickup_location->address2;
                $delivery_zone = $task->suborder->product->pickup_location->zone->name;
                $delivery_city = $task->suborder->product->pickup_location->zone->city->name;
                $delivery_state = $task->suborder->product->pickup_location->zone->city->state->name;
                break;
            case 4:
                $picking_title = $task->suborder->source_hub->title;
                $picking_email = $task->suborder->source_hub->email;
                $picking_msisdn = $task->suborder->source_hub->msisdn;
                $picking_alt_msisdn = $task->suborder->source_hub->alt_msisdn or '';
                $picking_address1 = $task->suborder->source_hub->address1;
                $picking_address2 = $task->suborder->source_hub->address2;
                $picking_zone = $task->suborder->source_hub->zone->name;
                $picking_city = $task->suborder->source_hub->zone->city->name;
                $picking_state = $task->suborder->source_hub->zone->city->state->name;

                $delivery_title = $task->suborder->product->pickup_location->title;
                $delivery_email = $task->suborder->product->pickup_location->email;
                $delivery_msisdn = $task->suborder->product->pickup_location->msisdn;
                $delivery_alt_msisdn = $task->suborder->product->pickup_location->alt_msisdn or '';
                $delivery_address1 = $task->suborder->product->pickup_location->address1;
                $delivery_address2 = $task->suborder->product->pickup_location->address2;
                $delivery_zone = $task->suborder->product->pickup_location->zone->name;
                $delivery_city = $task->suborder->product->pickup_location->zone->city->name;
                $delivery_state = $task->suborder->product->pickup_location->zone->city->state->name;
                break;
            case 5:
                $picking_title = $task->suborder->order->delivery_name;
                $picking_email = $task->suborder->order->delivery_email;
                $picking_msisdn = $task->suborder->order->delivery_msisdn;
                $picking_alt_msisdn = $task->suborder->order->delivery_alt_msisdn or '';
                $picking_address1 = $task->suborder->order->delivery_address1;
                $picking_address2 = $task->suborder->order->delivery_address2;
                $picking_zone = $task->suborder->order->delivery_zone->name;
                $picking_city = $task->suborder->order->delivery_zone->city->name;
                $picking_state = $task->suborder->order->delivery_zone->city->state->name;

                $delivery_title = $task->suborder->source_hub->title;
                $delivery_email = $task->suborder->source_hub->email;
                $delivery_msisdn = $task->suborder->source_hub->msisdn;
                $delivery_alt_msisdn = $task->suborder->source_hub->alt_msisdn or '';
                $delivery_address1 = $task->suborder->source_hub->address1;
                $delivery_address2 = $task->suborder->source_hub->address2;
                $delivery_zone = $task->suborder->source_hub->zone->name;
                $delivery_city = $task->suborder->source_hub->zone->city->name;
                $delivery_state = $task->suborder->source_hub->zone->city->state->name;
                break;
            default:
                $picking_title = '';
                $picking_email = '';
                $picking_msisdn = '';
                $picking_alt_msisdn = '';
                $picking_address1 = '';
                $picking_address2 = '';
                $picking_zone = '';
                $picking_city = '';
                $picking_state = '';

                $delivery_title = '';
                $delivery_email = '';
                $delivery_msisdn = '';
                $delivery_alt_msisdn = '';
                $delivery_address1 = '';
                $delivery_address2 = '';
                $delivery_zone = '';
                $delivery_city = '';
                $delivery_state = '';
                break;
        }

        return [
            'picking_title' => $picking_title,
            'picking_email' => $picking_email,
            'picking_msisdn' => $picking_msisdn,
            'picking_alt_msisdn' => $picking_alt_msisdn,
            'picking_lat' => $task->start_lat,
            'picking_long' => $task->start_long,
            'picking_address1' => $picking_address1,
            'picking_address2' => $picking_address2,
            'picking_zone' => $picking_zone,
            'picking_city' => $picking_city,
            'picking_state' => $picking_state,
            'delivery_title' => $delivery_title,
            'delivery_email' => $delivery_email,
            'delivery_msisdn' => $delivery_msisdn,
            'delivery_alt_msisdn' => $delivery_alt_msisdn,
            'delivery_lat' => $task->end_lat,
            'delivery_long' => $task->end_long,
            'delivery_address1' => $delivery_address1,
            'delivery_address2' => $delivery_address2,
            'delivery_zone' => $delivery_zone,
            'delivery_city' => $delivery_city,
            'delivery_state' => $delivery_state,
        ];
    }

    // End Task
    public function endTask($task, $request) {
        $end_lat = $request->has('end_lat') ? $request->end_lat : 0;
        $end_lon = $request->has('end_lon') ? $request->end_lon : 0;
        $product = json_decode($request->products, true);

        // Image
        $photo_path = '';
        if ($request->hasFile('photo')) {
            $fileName = str_random(15) . '.jpg';
            $upload_path = 'uploads/task/';

            $img = \Image::make($request->file('photo'))->save($upload_path . $fileName);
            $photo_path = \URL::to('/') . "/" . $upload_path . $fileName;
        }

        $signature_path = '';
        if ($request->hasFile('signature')) {
            $fileName = str_random(15) . '.jpg';
            $upload_path = 'uploads/task/signature/';

            $img = \Image::make($request->file('signature'))->save($upload_path . $fileName);
            $signature_path = \URL::to('') . "/" . $upload_path . $fileName;
        }

        switch ($task->task_type_id) {
            case 1:
                switch ($request->status) {
                    case 2:
                        // $status_update = 7;
                        $status_update = 9;
                        break;
                    case 3:
                        // $status_update = 8;
                        $status_update = 9;
                        break;
                    case 4:
                        $status_update = 6;
                        break;
                }
                $address = $task->suborder->source_hub->address1; // delivery address
                break;
            case 2:
            case 3: // pick & delivery
            case 6: // post delivery order return to buyer
                switch ($request->status) {
                    case 2:
                        $status_update = 31;
                        break;
                    case 3:
                        $status_update = 32;
                        break;
                    case 4:
                        $status_update = 33;
                        break;
                }
                $address = $task->suborder->order->delivery_address1;
                break;
            case 4:
            case 7:
                switch ($request->status) {
                    case 2:
                        $status_update = 37;
                        break;
                    case 3:
                        $status_update = 37;
                        break;
                    case 4:
                        $status_update = 40;
                        break;
                }
                $address = $task->suborder->product->pickup_location->address1;
                break;
            case 5:
                switch ($request->status) {
                    case 2:
                        // $status_update = 7;
                        $status_update = 9;
                        break;
                    case 3:
                        // $status_update = 8;
                        $status_update = 9;
                        break;
                    case 4:
                        $status_update = 6;
                        break;
                }
                $address = $task->suborder->source_hub->address1; // delivery address
                break;
        }

        $task = ConsignmentTask::find($task->id);
        $task->collected_quantity = $product['quantity'];
        $task->collected = $product['amount'];
        $task->distance = $request->distance;
//        if (!empty(floatval($request->end_lat)) && !empty(floatval($request->end_lon))) {
//            $task->end_lat = $request->end_lat;
//            $task->end_long = $request->end_lon;
//        }
        $task->status = $request->status;
        $task->reason_id = $product['reason_id'];
        $task->remarks = $product['remarks'];
        $task->signature = $signature_path;
        $task->image = $photo_path;
        $task->end_time = date("Y-m-d H:i:s");
        $task->save();

        Log::info("Rider End Location for task($task->id): Lat- $request->end_lat, Lang- $request->end_lon");

        // Update Sub-Order Status
        $this->suborderStatus($task->suborder->id, $status_update);

        if ($task->task_type_id == 3 && $request->status == 4) {
            $task->reconcile = 1;
            $task->save();
            $this->createDirectReturnTask($task);
        }

        $spend = timeDifference($task->start_time, $task->end_time);

        $order = $task->suborder->order;
        $summery = array(
            'title' => str_replace('Package', trans('api.package'), $task->suborder->product->product_title),
            'company' => $order->store->merchant->name,
            'address' => $address,
            'start_time' => $task->start_time,
            'end_time' => $task->end_time,
            'spend' => $spend,
            'start_lat' => $task->start_lat,
            'start_lon' => $task->start_long,
            'end_lat' => $end_lat,
            'end_lon' => $end_lon,
            'distance' => $request->distance
        );

        return $summery;
    }

    public function getConsignments($consignments) {
        $cons = [];

        foreach ($consignments as $key => $consignment) {
            $cons[$key]['consignment_unique_id'] = $consignment->consignment_unique_id;
//            $cons[$key]['created_at'] = $consignment->created_at;
            $cons[$key]['status'] = $this->getConsignmentStatus($consignment->status);
            $cons[$key]['requested_quantity'] = $consignment->task->sum('quantity');
            $cons[$key]['success_quantity'] = $consignment->task->sum('collected_quantity');
            $cons[$key]['collectable_amount'] = $consignment->task->sum('amount');
            $cons[$key]['collected_amount'] = $consignment->task->sum('collected');
            // $cons[$key]['return_quantity'] = $consignment->task->sum('amount');
            $cons[$key]['return_quantity'] = 0;
            $cons[$key]['distance'] = $consignment->task->sum('distance');

            $locations = [];
            foreach ($consignment->route as $index => $location) {
                $locations[$index]['lat'] = $location->latitude;
                $locations[$index]['lon'] = $location->longitude;
                $locations[$index]['time'] = (string) $location->created_at;
            }
            $cons[$key]['route'] = $locations;
        }

        return $cons;
    }

    public function taskHistory($user_id, $date) {
//        DB::connection()->enableQueryLog();
        $consignmentTasks = ConsignmentTask::where('created_at', '>', date('Y-m-d 00:00:01', strtotime($date)) . ' 00:00:00')
                ->where('created_at', '<', date('Y-m-d', strtotime($date)) . ' 23:59:59')
                ->where('rider_id', $user_id)
                ->get();
//        $queries = DB::getQueryLog();
//        dd($queries, $consignmentTasks);

        $allTask = array();
        $history = array();

        foreach ($consignmentTasks as $key => $task) {
            switch ($task->task_type_id) {
                case 1:
                    $type = trans('api.pick');
                    $title = $task->suborder->product->product_unique_id;
                    $picking_title = $task->suborder->product->pickup_location->title;
                    $picking_address = $task->suborder->product->pickup_location->address1;
                    $delivery_title = $task->suborder->product->pickup_location->zone->hub->title;
                    $delivery_address = $task->suborder->product->pickup_location->zone->hub->address1;
                    break;
                case 2:
                case 6: // post delivery order return to buyer
                    $type = trans('api.delivery');
                    $title = $task->suborder->product->product_unique_id;
                    $picking_title = $task->suborder->order->delivery_zone->hub->title;
                    $picking_address = $task->suborder->order->delivery_zone->hub->address1;
                    $delivery_title = $task->suborder->order->delivery_name;
                    $delivery_address = $task->suborder->order->delivery_address1;
                    break;
                case 3: // pick & delivery
                    $type = trans('api.pick_deliver');
                    $title = $task->suborder->product->product_unique_id;
                    $picking_title = $task->suborder->product->pickup_location->title;
                    $picking_address = $task->suborder->product->pickup_location->address1;
                    $delivery_title = $task->suborder->order->delivery_name;
                    $delivery_address = $task->suborder->order->delivery_address1;
                    break;
                case 7: // delivery to return
                    $type = trans('api.delivery_to_return');
                    $title = $task->suborder->product->product_unique_id;
                    $picking_title = $task->suborder->order->delivery_name;
                    $picking_address = $task->suborder->order->delivery_address1;
                    $delivery_title = $task->suborder->product->pickup_location->title;
                    $delivery_address = $task->suborder->product->pickup_location->address1;
                    break;
                case 4:
                    $type = trans('api.return');
                    $title = $task->suborder->product->product_unique_id;
                    $picking_title = $task->suborder->product->pickup_location->title;
                    $picking_address = $task->suborder->product->pickup_location->address1;
                    $delivery_title = $task->suborder->product->pickup_location->zone->hub->title;
                    $delivery_address = $task->suborder->product->pickup_location->zone->hub->address1;
                    break;
                case 5:
                    $type = trans('api.pick');
                    $title = $task->suborder->product->product_unique_id;
                    $picking_title = $task->suborder->order->delivery_zone->hub->title;
                    $picking_address = $task->suborder->order->delivery_zone->hub->address1;
                    $delivery_title = $task->suborder->order->delivery_name;
                    $delivery_address = $task->suborder->order->delivery_address1;
                    break;
                default:
                    $type = '';
                    $title = '';
                    $picking_title = '';
                    $picking_address = '';
                    $delivery_title = '';
                    $delivery_address = '';
                    break;
            }
            $allTask[$key]['task_id'] = $task->id;
            $allTask[$key]['task_type'] = $type;
            $allTask[$key]['task_title'] = str_replace('Package', trans('api.package'), $task->suborder->product->product_title) or '';
            $allTask[$key]['picking_title'] = $picking_title;
            $allTask[$key]['picking_address'] = $picking_address;
            $allTask[$key]['delivery_title'] = $delivery_title;
            $allTask[$key]['delivery_address'] = $delivery_address;
            $allTask[$key]['consignment_unique_id'] = $task->consignment->consignment_unique_id;
            $allTask[$key]['task_status'] = $this->taskStatusName($task->status, $task->reconcile);
        }
        $history['total_task'] = $consignmentTasks->count();
        $history['total_quantity'] = $consignmentTasks->sum('collected_quantity');
        $history['collected_amount'] = $consignmentTasks->sum('collected');
        $history['tasks'] = $allTask;
        return $history;
    }

    private function taskStatusName($statusId, $reconcile) {
        if ($reconcile) {
            return trans('api.task_status.completed');
        }
        switch ($statusId) {
            case 1:
                $status = trans('api.task_status.processing');
                break;
            case 2:
            case 3:
            case 4:
                $status = trans('api.task_status.submitted');
                break;
            default :
                $status = trans('api.task_status.pending');
                break;
        }
        return $status;
    }

    private function createDirectReturnTask($preTask) {
        $this->suborderStatus($preTask->suborder->id, 35); // rider requested for return 
        $consignment_task = new ConsignmentTask;
        $consignment_task->consignment_id = $preTask->consignment_id;
        $consignment_task->rider_id = $preTask->rider_id;
        $consignment_task->sub_order_id = $preTask->sub_order_id;
        $consignment_task->task_type_id = 7; // Delivery to Return
        $consignment_task->start_time = date('Y-m-d H:i:s');
        $consignment_task->start_lat = $preTask->end_lat; // Rider start this task from this location
        $consignment_task->start_long = $preTask->end_long;
        $consignment_task->end_lat = $preTask->suborder->product->pickup_location->latitude;
        $consignment_task->end_long = $preTask->suborder->product->pickup_location->longitude;
        $consignment_task->quantity = $preTask->quantity;
        $consignment_task->amount = $preTask->amount;
        $consignment_task->otp = rand(100000, 999999);
        // $consignment_task->otp = 123456; // for testing
        $consignment_task->status = 1; // already rider have the product
        $consignment_task->created_at = date('Y-m-d H:i:s');
        $consignment_task->save();
    }

}
