<?php

namespace App\Http\Traits;

use App\ConsignmentTask;
use App\PickingTask;
use App\DeliveryTask;
use App\OrderProduct;
use App\SubOrder;
use App\PickingLocations;
use App\Consignment;
use App\Order;

use Auth;

trait ReconciliationTrait
{
    public function reconcileCommon($task_id, $request)
    {

        $task = ConsignmentTask::findOrFail($task_id);

        # Require Product
        $product = $task->suborder->product;

        if ($request->due_quantity == 0) {

            $task->status = 2; // Success
            $task->collected_quantity = $request->filled_quantity;
            $task->collected = $request->paid_amount;

            if ($task->task_type_id == 1) {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 9); // Product received from rider
            } elseif ($task->task_type_id == 4) {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 37); // Return Completed
            } else {
                $order_product = OrderProduct::where('id', $product->id)->first();
                $order_product->delivery_paid_amount = $request->paid_amount;
                $order_product->save();

                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 38); // Delivery Completed
            }

        } else if ($request->due_quantity == $product->quantity) {

            $task->status = 4; // Fail

            if ($task->task_type_id == 1) {
                if ($request->sub_order_status == 13) {

                    $this->suborderStatus($product->sub_order_id, 13); // Pickup order Cancelled

                } else {

                    $this->suborderStatus($product->sub_order_id, 6); // Pick up failed

                    // Send SMS
                    $this->smsPickupFailed($task->suborder->unique_suborder_id);

                    // Create Due Sub-Order
                    $new_sub_order = $this->CreateNewSubOrder($product->sub_order_id, $request->sub_order_status);

                    // Create Due Product
                    $new_product = $this->CreateNewProduct($new_sub_order, $product);
                }

            } elseif ($task->task_type_id == 4) {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 26); // Full order racked at Destination Hub
            } else {
                $order_product = OrderProduct::where('id', $product->id)->first();
                $order_product->delivery_paid_amount = $request->paid_amount;
                $order_product->save();

                // Update Sub-Order Status
//                $this->suborderStatus($product->sub_order_id, 33); // Products Delivery Failed

                // Send SMS
                $this->smsDeliveryFailed($task->suborder->unique_suborder_id);

                // Create Due Sub-Order
                $new_sub_order = $this->CreateNewSubOrderDelivery($product->sub_order_id, $request->sub_order_status);
                $new_sub_order = SubOrder::findOrFail($new_sub_order->id);
                $new_sub_order->reason_id = $request->reason_id;
                $new_sub_order->remarks = $request->remarks;
                $new_sub_order->save();

                // Create Due Product
                $new_product = $this->CreateNewProductDelivery($new_sub_order, $product);
            }

        } else {

            $task->status = 3; // Pertial
            if ($task->task_type_id == 1) {
                // Update Require Product
                $required_quantity = $product->quantity - $request->due_quantity;
                $required_sub_total = $product->unit_price * $required_quantity;
                $required_total_delivery_charge = $product->unit_deivery_charge * $required_quantity;
                if ($product->delivery_pay_by_cus == 1) {
                    $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
                } else {
                    $required_total_payable_amount = $required_sub_total;
                }

                // Update Sub-Order Status
                $this->suborderStatus($task->sub_order_id, 9); // Product received from rider

            } else {//delivery
                // Update Require Product
                $required_quantity = $product->quantity - $request->due_quantity;
                $required_sub_total = $product->unit_price * $required_quantity;
                if ($request->sub_order_status == 35) {
                    $required_total_delivery_charge = $product->total_delivery_charge;
                } else {
                    $required_total_delivery_charge = $product->unit_deivery_charge * $required_quantity;
                }
                if ($product->delivery_pay_by_cus == 1) {
                    $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
                } else {
                    $required_total_payable_amount = $required_sub_total;
                }
                // Update Sub-Order Status
                $this->suborderStatus($task->sub_order_id, 39); // Delivery Partial Completed
            }

            $task->collected_quantity = $request->filled_quantity;
            $task->collected = $request->paid_amount;

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->quantity = $required_quantity;
            $order_product->sub_total = $required_sub_total;
            $order_product->total_delivery_charge = $required_total_delivery_charge;
            $order_product->total_payable_amount = $required_total_payable_amount;
            $order_product->delivery_paid_amount = $request->paid_amount;
            $order_product->save();

            // Create Due Sub-Order
            $new_sub_order = $this->CreatePertialSubOrder($product->sub_order_id, $request->sub_order_status);

            // Create Due Product
            $new_product = $this->CreatePertialProduct($new_sub_order, $product, $request->due_quantity);

        }

        if ($request->has('reason_id')) {
            $task->reason_id = $request->reason_id;
        }
        if ($request->has('remarks')) {
            $task->remarks = $request->remarks;
        }
        $task->reconcile = 1;
        $task->save();

        return $task->consignment_id;

    }

    // picking Reconcile
    public function pickingReconcile($task_id, $request)
    {

        $task = PickingTask::findOrFail($task_id);

        # Require Product
        $product = $task->product;

        if ($request->due_quantity == 0) {

            $task->status = 2; // Success

            if (strtolower($task->type) == 'picking') {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 9); // Product received from rider
            } else {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 37); // Return Completed
            }

        } else if ($request->due_quantity == $product->quantity) {

            $task->status = 4; // Fail

            if (strtolower($task->type) == 'picking') {

                // Update Sub-Order Status
                // $this->suborderStatus($product->sub_order_id, 13); // Pickup order Cancelled
                // $this->suborderStatus($product->sub_order_id, 6); // Pick up failed

                if ($request->sub_order_status == 13) {

                    $this->suborderStatus($product->sub_order_id, 13); // Pickup order Cancelled

                } else {

                    $this->suborderStatus($product->sub_order_id, 6); // Pick up failed

                    if ($request->has('filled_quantity')) {
                        $task->quantity = $request->filled_quantity;
                    }

                    // Send SMS
                    $this->smsPickupFailed($task->product_unique_id);

                    // Create Due Sub-Order
                    $new_sub_order = $this->CreateNewSubOrder($product->sub_order_id, $request->sub_order_status);

                    // Create Due Product
                    $new_product = $this->CreateNewProduct($new_sub_order, $product);

                }

            } else {

                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 26); // Full order racked at Destination Hub

            }

        } else {

            $task->status = 3; // Pertial

            // Update Require Product
            $required_quantity = $product->quantity - $request->due_quantity;
            $required_sub_total = $product->unit_price * $required_quantity;
            $required_total_delivery_charge = $product->unit_deivery_charge * $required_quantity;
            if ($product->delivery_pay_by_cus == 1) {
                $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
            } else {
                $required_total_payable_amount = $required_sub_total;
            }

            if ($request->has('filled_quantity')) {
                $task->quantity = $request->filled_quantity;
            }

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->quantity = $required_quantity;
            $order_product->sub_total = $required_sub_total;
            $order_product->total_delivery_charge = $required_total_delivery_charge;
            $order_product->total_payable_amount = $required_total_payable_amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($order_product->sub_order_id, 9); // Product received from rider

            // Create Due Sub-Order
            $new_sub_order = $this->CreatePertialSubOrder($product->sub_order_id, $request->sub_order_status);

            // Create Due Product
            $new_product = $this->CreatePertialProduct($new_sub_order, $product, $request->due_quantity);

        }

        if ($request->has('reason_id')) {
            $task->reason_id = $request->reason_id;
        }
        if ($request->has('remarks')) {
            $task->remarks = $request->remarks;
        }
        $task->reconcile = 1;
        $task->save();

        return $task->consignment_id;

    }

    // delivery Reconcile
    public function deliveryReconcile($task_id, $request)
    {

        $task = DeliveryTask::findOrFail($task_id);

        # Require Product
        $product = $task->suborder->product;

        $subOrder = SubOrder::find($task->suborder->id);
        $subOrder->reason_id = $request->reason_id;
        $subOrder->remarks = $request->remarks;
        $subOrder->save();

        if ($request->due_quantity == 0) {

            $task->status = 2; // Success

            if ($request->has('filled_quantity')) {
                $task->quantity = $request->filled_quantity;
            }
            if ($request->has('paid_amount')) {
                $task->amount = $request->paid_amount;
            }

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->delivery_paid_amount = $request->paid_amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($product->sub_order_id, 38); // Delivery Completed

            // AjkerDeal Operation
            if ($order_product->order->store_id == 83) {
                $this->ajkerDealOrderStatus($order_product->order->merchant_order_id);
            }

            // Ajker Deal Delivery Status Update
            // if($order_product->order->store->merchant->id == 78){

            //     $sub_order_merchant_order_id = $order_product->order->merchant_order_id;

            //     // Call to AjkerDeal API
            //     $this->CallToAjkerDeal($sub_order_merchant_order_id);

            // }

        } else if ($request->due_quantity == $product->quantity) {

            $task->status = 4; // Fail

            if ($request->has('filled_quantity')) {
                $task->quantity = $request->filled_quantity;
            }
            if ($request->has('paid_amount')) {
                $task->amount = $request->paid_amount;
            }

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->delivery_paid_amount = $request->paid_amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($product->sub_order_id, 33); // Products Delivery Failed

            // Send SMS
            $this->smsDeliveryFailed($task->suborder->unique_suborder_id);

            // Create Due Sub-Order
            $new_sub_order = $this->CreateNewSubOrderDelivery($product->sub_order_id, $request->sub_order_status);
            $new_sub_order = SubOrder::findOrFail($new_sub_order->id);
            $new_sub_order->reason_id = $request->reason_id;
            $new_sub_order->remarks = $request->remarks;
            $new_sub_order->save();

            // Create Due Product
            $new_product = $this->CreateNewProductDelivery($new_sub_order, $product);

        } else {

            $task->status = 3; // Pertial

            // Update Require Product
            $required_quantity = $product->quantity - $request->due_quantity;
            $required_sub_total = $product->unit_price * $required_quantity;
            if ($request->sub_order_status == 35) {
                $required_total_delivery_charge = $product->total_delivery_charge;
            } else {
                $required_total_delivery_charge = $product->unit_deivery_charge * $required_quantity;
            }
            if ($product->delivery_pay_by_cus == 1) {
                $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
            } else {
                $required_total_payable_amount = $required_sub_total;
            }

            if ($request->has('filled_quantity')) {
                $task->quantity = $request->filled_quantity;
            }
            if ($request->has('paid_amount')) {
                $task->amount = $request->paid_amount;
            }

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->quantity = $required_quantity;
            $order_product->sub_total = $required_sub_total;
            $order_product->total_delivery_charge = $required_total_delivery_charge;
            $order_product->total_payable_amount = $required_total_payable_amount;
            $order_product->delivery_paid_amount = $request->paid_amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($order_product->sub_order_id, 39); // Delivery Partial Completed

            // Create Due Sub-Order
            $new_sub_order = $this->CreatePertialSubOrderDelivery($product->sub_order_id, $request->sub_order_status);
            $new_sub_order = SubOrder::findOrFail($new_sub_order->id);
            $new_sub_order->reason_id = $request->reason_id; //reasons add
            $new_sub_order->remarks = $request->remarks;
            $new_sub_order->save();

            // Create Due Product
            $new_product = $this->CreatePertialProductDelivery($new_sub_order, $product, $request->due_quantity);

        }

        if ($request->has('reason_id')) {
            $task->reason_id = $request->reason_id;
        }
        if ($request->has('remarks')) {
            $task->remarks = $request->remarks;
        }
        $task->reconcile = 1;
        $task->save();

        return $task->consignment_id;

    }

    // Create New Sub-Order
    public function CreateNewSubOrder($sub_order_id, $sub_order_status)
    {

        $exist_sub_order = SubOrder::where('id', $sub_order_id)->first();

        $last_sub_order = SubOrder::where('order_id', $exist_sub_order->order_id)->where('return', $exist_sub_order->return)->orderBy('id', 'desc')->first();
        $last_sub_order_number = substr($last_sub_order->unique_suborder_id, -2);
        $new_unique_suborder_id = substr($last_sub_order->unique_suborder_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);

        $sub_order = new SubOrder();
        $sub_order->unique_suborder_id = $new_unique_suborder_id;
        $sub_order->order_id = $exist_sub_order->order_id;
        $sub_order->return = $exist_sub_order->return;
        $sub_order->source_hub_id = $exist_sub_order->source_hub_id;
        $sub_order->destination_hub_id = $exist_sub_order->destination_hub_id;
        $sub_order->next_hub_id = $exist_sub_order->next_hub_id;
        if ($exist_sub_order->parent_sub_order_id == 0) {
            $sub_order->parent_sub_order_id = $sub_order_id;
        } else {
            $sub_order->parent_sub_order_id = $exist_sub_order->parent_sub_order_id;
        }
        $sub_order->save();

        // Update Sub-Order Status
        $this->suborderStatus($sub_order->id, $sub_order_status);

        return $sub_order;

    }

    // Create New Product
    public function CreateNewProduct($new_sub_order, $product)
    {

        $order_product = new OrderProduct();
        $order_product->product_unique_id = $new_sub_order->unique_suborder_id;
        $order_product->product_category_id = $product->product_category_id;
        $order_product->order_id = $product->order_id;
        $order_product->sub_order_id = $new_sub_order->id;
        $order_product->pickup_location_id = $product->pickup_location_id;
        $order_product->picking_date = $product->picking_date;
        $order_product->picking_time_slot_id = $product->picking_time_slot_id;
        $order_product->product_title = $product->product_title;
        $order_product->unit_price = $product->unit_price;
        $order_product->unit_deivery_charge = $unit_deivery_charge = $product->unit_deivery_charge;
        $order_product->quantity = $product->quantity;
        $order_product->sub_total = $product->unit_price * $product->quantity;
        $order_product->payable_product_price = $payable_product_price = $product->unit_price * $product->quantity;
        $order_product->total_delivery_charge = $total_delivery_charge = $unit_deivery_charge * $product->quantity;
        $order_product->delivery_pay_by_cus = $product->delivery_pay_by_cus;
        $order_product->total_payable_amount = $product->total_payable_amount;
        $order_product->width = $product->width;
        $order_product->height = $product->height;
        $order_product->length = $product->length;
        $order_product->weight = $product->weight;
        $order_product->status = 1;
        $order_product->save();

        return $order_product;

    }

    // Create New Sub-Order
    public function CreatePertialSubOrder($sub_order_id, $sub_order_status)
    {

        $exist_sub_order = SubOrder::where('id', $sub_order_id)->first();

        $last_sub_order = SubOrder::where('order_id', $exist_sub_order->order_id)->where('return', $exist_sub_order->return)->orderBy('id', 'desc')->first();
        $last_sub_order_number = substr($last_sub_order->unique_suborder_id, -2);
        $new_unique_suborder_id = substr($last_sub_order->unique_suborder_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);

        $sub_order = new SubOrder();
        $sub_order->unique_suborder_id = $new_unique_suborder_id;
        $sub_order->order_id = $exist_sub_order->order_id;
        $sub_order->return = $exist_sub_order->return;
        $sub_order->source_hub_id = $exist_sub_order->source_hub_id;
        $sub_order->destination_hub_id = $exist_sub_order->destination_hub_id;
        $sub_order->next_hub_id = $exist_sub_order->next_hub_id;
        // $sub_order->parent_sub_order_id = $sub_order_id;
        $sub_order->save();

        // Update Sub-Order Status
        $this->suborderStatus($sub_order->id, $sub_order_status);

        return $sub_order;

    }

    // Create New Product
    public function CreatePertialProduct($new_sub_order, $product, $due_quantity)
    {

        // Update Due Product
        $due_sub_total = $product->unit_price * $due_quantity;
        $due_total_delivery_charge = $product->unit_deivery_charge * $due_quantity;
        if ($product->delivery_pay_by_cus == 1) {
            $due_total_payable_amount = $due_sub_total + $due_total_delivery_charge;
        } else {
            $due_total_payable_amount = $due_sub_total;
        }

        $order_product = new OrderProduct();
        $order_product->product_unique_id = $new_sub_order->unique_suborder_id;
        $order_product->product_category_id = $product->product_category_id;
        $order_product->order_id = $product->order_id;
        $order_product->sub_order_id = $new_sub_order->id;
        $order_product->pickup_location_id = $product->pickup_location_id;
        $order_product->picking_date = $product->picking_date;
        $order_product->picking_time_slot_id = $product->picking_time_slot_id;
        $order_product->product_title = $product->product_title;
        $order_product->unit_price = $product->unit_price;
        $order_product->unit_deivery_charge = $unit_deivery_charge = $product->unit_deivery_charge;
        $order_product->quantity = $due_quantity;
        $order_product->sub_total = $due_sub_total;
        $order_product->payable_product_price = $due_sub_total;
        $order_product->total_delivery_charge = $due_total_delivery_charge;
        $order_product->delivery_pay_by_cus = $product->delivery_pay_by_cus;
        $order_product->total_payable_amount = $due_total_payable_amount;
        $order_product->width = $product->width;
        $order_product->height = $product->height;
        $order_product->length = $product->length;
        $order_product->weight = $product->weight;
        $order_product->status = 1;
        $order_product->save();

        return $order_product;

    }

    // Create New Sub-Order Delivery
    public function CreateNewSubOrderDelivery($sub_order_id, $sub_order_status)
    {

        $exist_sub_order = SubOrder::where('id', $sub_order_id)->first();

        $last_sub_order = SubOrder::where('order_id', $exist_sub_order->order_id)->where('return', $exist_sub_order->return)->orderBy('id', 'desc')->first();
        $last_sub_order_number = substr($last_sub_order->unique_suborder_id, -2);

        if ($sub_order_status == 35) {

            $new_unique_suborder_id = $this->createReturnSubOrder($exist_sub_order->unique_suborder_id);
            // $new_unique_suborder_id = 'R'.substr($last_sub_order->unique_suborder_id, 1);
            $return = 1;
            $source_hub_id = $exist_sub_order->destination_hub_id;
            $destination_hub_id = $exist_sub_order->source_hub_id;

            $exist_sub_order->return = $return;
            $exist_sub_order->save();

        } else {
            $new_unique_suborder_id = substr($last_sub_order->unique_suborder_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);
            $return = 0;

            $source_hub_id = $exist_sub_order->source_hub_id;
            $destination_hub_id = $exist_sub_order->destination_hub_id;
        }

        $sub_order = new SubOrder();
        $sub_order->unique_suborder_id = $new_unique_suborder_id;
        $sub_order->order_id = $exist_sub_order->order_id;
        $sub_order->return = $return;
        $sub_order->source_hub_id = $source_hub_id;
        $sub_order->destination_hub_id = $destination_hub_id;
        $sub_order->next_hub_id = $destination_hub_id;
        if ($exist_sub_order->parent_sub_order_id == 0) {
            $sub_order->parent_sub_order_id = $sub_order_id;
        } else {
            $sub_order->parent_sub_order_id = $exist_sub_order->parent_sub_order_id;
        }
        $sub_order->save();

        // Update Sub-Order Status
        if ($sub_order_status == 34) {
            $this->suborderStatus($sub_order->id, 34);
        } else {
            if ($sub_order->source_hub_id == $sub_order->destination_hub_id) {
                $this->suborderStatus($sub_order->id, 26);
            } else {
                $this->suborderStatus($sub_order->id, 15);
            }
        }

        return $sub_order;

    }

    // Create New Product Delivery
    public function CreateNewProductDelivery($new_sub_order, $product)
    {

        $order_product = new OrderProduct();
        $order_product->product_unique_id = $new_sub_order->unique_suborder_id;
        $order_product->product_category_id = $product->product_category_id;
        $order_product->order_id = $product->order_id;
        $order_product->sub_order_id = $new_sub_order->id;
        $order_product->pickup_location_id = $product->pickup_location_id;
        $order_product->picking_date = $product->picking_date;
        $order_product->picking_time_slot_id = $product->picking_time_slot_id;
        $order_product->product_title = $product->product_title;
        $order_product->unit_price = $product->unit_price;
        $order_product->unit_deivery_charge = $unit_deivery_charge = $product->unit_deivery_charge;
        $order_product->quantity = $product->quantity;
        $order_product->sub_total = $product->unit_price * $product->quantity;
        $order_product->payable_product_price = $payable_product_price = $product->unit_price * $product->quantity;
        $order_product->total_delivery_charge = $total_delivery_charge = $unit_deivery_charge * $product->quantity;
        $order_product->delivery_pay_by_cus = $product->delivery_pay_by_cus;
        $order_product->total_payable_amount = $product->total_payable_amount;
        $order_product->width = $product->width;
        $order_product->height = $product->height;
        $order_product->length = $product->length;
        $order_product->weight = $product->weight;
        $order_product->status = 1;
        $order_product->save();

        return $order_product;

    }

    // Create New Sub-Order Delivery
    public function CreatePertialSubOrderDelivery($sub_order_id, $sub_order_status)
    {

        $exist_sub_order = SubOrder::where('id', $sub_order_id)->first();

        $last_sub_order = SubOrder::where('order_id', $exist_sub_order->order_id)->where('return', $exist_sub_order->return)->orderBy('id', 'desc')->first();
        $last_sub_order_number = substr($last_sub_order->unique_suborder_id, -2);

        if ($sub_order_status == 35) {

            $new_unique_suborder_id = $this->createReturnSubOrder($exist_sub_order->unique_suborder_id);
            // $new_unique_suborder_id = 'R'.substr($last_sub_order->unique_suborder_id, 1);
            $return = 1;

        } else {
            $new_unique_suborder_id = substr($last_sub_order->unique_suborder_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);
            $return = 0;
        }

        $sub_order = new SubOrder();
        $sub_order->unique_suborder_id = $new_unique_suborder_id;
        $sub_order->order_id = $exist_sub_order->order_id;
        $sub_order->return = $return;
        $sub_order->source_hub_id = $exist_sub_order->source_hub_id;
        $sub_order->destination_hub_id = $exist_sub_order->destination_hub_id;
        $sub_order->next_hub_id = $exist_sub_order->next_hub_id;
        // if($sub_order_status != 35){
        //     if($exist_sub_order->parent_sub_order_id == 0){
        //         $sub_order->parent_sub_order_id = $sub_order_id;
        //     }else{
        //         $sub_order->parent_sub_order_id = $exist_sub_order->parent_sub_order_id;
        //     }
        // }
        $sub_order->save();

        // Update Sub-Order Status
        if ($sub_order->source_hub_id == $sub_order->destination_hub_id) {
            $this->suborderStatus($sub_order->id, 26);
        } else {
            $this->suborderStatus($sub_order->id, 9);
        }

        return $sub_order;

    }

    // Create New Product Delivery
    public function CreatePertialProductDelivery($new_sub_order, $product, $due_quantity)
    {

        // Update Due Product
        $due_sub_total = $product->unit_price * $due_quantity;
        $due_total_delivery_charge = $product->unit_deivery_charge * $due_quantity;
        if ($product->delivery_pay_by_cus == 1) {
            $due_total_payable_amount = $due_sub_total + $due_total_delivery_charge;
        } else {
            $due_total_payable_amount = $due_sub_total;
        }

        if ($new_sub_order->return == 1) {
            $due_sub_total = $due_sub_total / 2;
            $due_total_delivery_charge = $due_total_delivery_charge / 2;
            $due_total_payable_amount = $due_total_payable_amount / 2;
        }

        $order_product = new OrderProduct();
        $order_product->product_unique_id = $new_sub_order->unique_suborder_id;
        $order_product->product_category_id = $product->product_category_id;
        $order_product->order_id = $product->order_id;
        $order_product->sub_order_id = $new_sub_order->id;
        $order_product->pickup_location_id = $product->pickup_location_id;
        $order_product->picking_date = $product->picking_date;
        $order_product->picking_time_slot_id = $product->picking_time_slot_id;
        $order_product->product_title = $product->product_title;
        $order_product->unit_price = $product->unit_price;
        $order_product->unit_deivery_charge = $unit_deivery_charge = $product->unit_deivery_charge;
        $order_product->quantity = $due_quantity;
        $order_product->sub_total = $due_sub_total;
        $order_product->payable_product_price = $due_sub_total;
        $order_product->total_delivery_charge = $due_total_delivery_charge;
        $order_product->delivery_pay_by_cus = $product->delivery_pay_by_cus;
        $order_product->total_payable_amount = $due_total_payable_amount;
        $order_product->width = $product->width;
        $order_product->height = $product->height;
        $order_product->length = $product->length;
        $order_product->weight = $product->weight;
        $order_product->status = 1;
        $order_product->save();

        return $order_product;

    }

    // picking Bulk Reconcile
    public function pickingBulkReconcile($task_id, $request)
    {

        $task = PickingTask::findOrFail($task_id);

        # Require Product
        $product = $task->product;

        if (strtolower($task->type) == 'picking') {
            $due_quantity = $product->quantity - $task->quantity;
        } else {
            $due_quantity = $product->quantity - $task->return_quantity;
        }

        if ($due_quantity == 0) {

            if (strtolower($task->type) == 'picking') {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 9); // Product received from rider
            } else {
                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 37); // Return Completed
            }

        } else if ($due_quantity == $product->quantity) {

            if (strtolower($task->type) == 'picking') {

                if ($request->sub_order_status_picking == 13) {

                    $this->suborderStatus($product->sub_order_id, 13); // Pickup order Cancelled

                } else {

                    $this->suborderStatus($product->sub_order_id, 6); // Pick up failed

                    if ($request->has('filled_quantity')) {
                        $task->quantity = $request->filled_quantity;
                    }

                    // Send SMS
                    $this->smsPickupFailed($task->suborder->unique_suborder_id);

                    // Create Due Sub-Order
                    $new_sub_order = $this->CreateNewSubOrder($product->sub_order_id, $request->sub_order_status_picking);

                    // Create Due Product
                    $new_product = $this->CreateNewProduct($new_sub_order, $product);

                }

                // Update Sub-Order Status
                // $this->suborderStatus($product->sub_order_id, 13); // Pickup order Cancelled

                // Create Due Sub-Order
                // $new_sub_order = $this->CreateNewSubOrder($product->sub_order_id, $request->sub_order_status_picking);

                // Create Due Product
                // $new_product = $this->CreateNewProduct($new_sub_order, $product);

            } else {

                // Update Sub-Order Status
                $this->suborderStatus($product->sub_order_id, 26); // Full order racked at Destination Hub

            }

        } else {

            // Update Require Product
            $required_quantity = $product->quantity - $due_quantity;
            $required_sub_total = $product->unit_price * $required_quantity;
            $required_total_delivery_charge = $product->unit_deivery_charge * $required_quantity;
            if ($product->delivery_pay_by_cus == 1) {
                $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
            } else {
                $required_total_payable_amount = $required_sub_total;
            }

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->quantity = $required_quantity;
            $order_product->sub_total = $required_sub_total;
            $order_product->total_delivery_charge = $required_total_delivery_charge;
            $order_product->total_payable_amount = $required_total_payable_amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($order_product->sub_order_id, 9); // Product received from rider

            // Create Due Sub-Order
            $new_sub_order = $this->CreatePertialSubOrder($product->sub_order_id, $request->sub_order_status_picking);

            // Create Due Product
            $new_product = $this->CreatePertialProduct($new_sub_order, $product, $due_quantity);

        }

        if ($request->has('reason_id')) {
            $task->reason_id = $request->reason_id;
        }
        if ($request->has('remarks')) {
            $task->remarks = $request->remarks;
        }
        $task->reconcile = 1;
        $task->save();

        return $task->consignment_id;

    }

    // delivery Bulk Reconcile
    public function deliveryBulkReconcile($task_id, $request)
    {

        $task = DeliveryTask::findOrFail($task_id);

        # Require Product
        $product = $task->suborder->product;

        $due_quantity = $product->quantity - $task->quantity;

        if ($due_quantity == 0) {

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->delivery_paid_amount = $task->amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($product->sub_order_id, 38); // Delivery Completed

            // AjkerDeal Operation
            if ($order_product->order->store_id == 83) {
                $this->ajkerDealOrderStatus($order_product->order->merchant_order_id);
            }

        } else if ($due_quantity == $product->quantity) {

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->delivery_paid_amount = $task->amount;
            $order_product->save();

            // Update Sub-Order Status
//            $this->suborderStatus($product->sub_order_id, 33); // Products Delivery Failed

            // Send SMS
            $this->smsDeliveryFailed($task->suborder->unique_suborder_id);

            // Create Due Sub-Order
            $new_sub_order = $this->CreateNewSubOrderDelivery($product->sub_order_id, $request->sub_order_status_delivery);

            // Create Due Product
            $new_product = $this->CreateNewProductDelivery($new_sub_order, $product);

        } else {

            // Update Require Product
            $required_quantity = $product->quantity - $due_quantity;
            $required_sub_total = $product->unit_price * $required_quantity;
            if ($request->sub_order_status_delivery == 35) {
                $required_total_delivery_charge = $product->total_delivery_charge;
            } else {
                $required_total_delivery_charge = $product->unit_deivery_charge * $required_quantity;
            }
            if ($product->delivery_pay_by_cus == 1) {
                $required_total_payable_amount = $required_sub_total + $required_total_delivery_charge;
            } else {
                $required_total_payable_amount = $required_sub_total;
            }

            $order_product = OrderProduct::where('id', $product->id)->first();
            $order_product->quantity = $required_quantity;
            $order_product->sub_total = $required_sub_total;
            $order_product->total_delivery_charge = $required_total_delivery_charge;
            $order_product->total_payable_amount = $required_total_payable_amount;
            $order_product->delivery_paid_amount = $task->amount;
            $order_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($order_product->sub_order_id, 39); // Delivery Partial Completed

            // Create Due Sub-Order
            return $new_sub_order = $this->CreatePertialSubOrderDelivery($product->sub_order_id, $request->sub_order_status_delivery);

            // Create Due Product
            $new_product = $this->CreatePertialProductDelivery($new_sub_order, $product, $due_quantity);

        }

        if ($request->has('reason_id')) {
            $task->reason_id = $request->reason_id;
        }
        if ($request->has('remarks')) {
            $task->remarks = $request->remarks;
        }
        $task->reconcile = 1;
        $task->save();

        return $task->consignment_id;

    }

    public function createReturnSubOrder($unique_suborder_id)
    {

        $new_unique_suborder_id = 'R' . substr($unique_suborder_id, 1);
        $count = SubOrder::where('unique_suborder_id', $new_unique_suborder_id)->count();

        if ($count == 0) {
            return $new_unique_suborder_id;
        } else {

            while (1) {
                $last_sub_order_number = substr($new_unique_suborder_id, -2);
                $new_unique_suborder_id = substr($new_unique_suborder_id, 0, 8) . sprintf("%02d", $last_sub_order_number + 1);

                $count = SubOrder::where('unique_suborder_id', $new_unique_suborder_id)->count();

                if ($count == 0) {
                    return $new_unique_suborder_id;
                }
            }

        }

    }

    public function CallToAjkerDeal($sub_order_merchant_order_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateCouruierStatus",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"CouponId\":" . $sub_order_merchant_order_id . ",\"Status\":\"90\",\"Comments\":\"Biddyut delivered the product to the customer\",\"CommentedBy\":26}\r\n",
            CURLOPT_HTTPHEADER => array(
                "api_key: Ajkerdeal_~La?Rj73FcLm",
                "authorization: Basic QmlkZHl1dDpoamRzNzQ4NDg5Mw==",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: e5944196-206f-b128-a7dc-c985d9a54556"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }

    }

}