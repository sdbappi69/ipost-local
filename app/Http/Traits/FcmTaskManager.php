<?php

namespace App\Http\Traits;

// use Illuminate\Support\Facades\Log;
use Log;
use App\SubOrder;
use App\CartProduct;
use App\OrderProduct;
use App\TmTask;
use App\ConsignmentTask;
use App\Http\Traits\LogsTrait;
use DB;
use App\User;

trait FcmTaskManager {

    use LogsTrait;

    public function fcm_task_req($sub_order_id, $fource = 0, $user_id = null) {
        $sub_order = SubOrder::findOrFail($sub_order_id);
        $picking_hub_id = $sub_order->product->pickup_location->zone->hub_id;
        $picking_hub_title = $sub_order->product->pickup_location->zone->hub->title;
        $delivery_hub_id = $sub_order->order->delivery_zone->hub_id;
        $delivery_hub_title = $sub_order->order->delivery_zone->hub->title;

        // if($picking_hub_id == $delivery_hub_id){
        //     $task_type_id = 3;
        // }else if($sub_order->sub_order_status == 2){
        //     $task_type_id = 1;
        // }else{
        //     $task_type_id = 2;
        // }

        $task_hub = $sub_order->source_hub_id; // to find hub wise rider

        if ($sub_order->sub_order_status == 2 || $sub_order->sub_order_status == 3) {
            if ($sub_order->order->store->merchant_id == 12) { // FIB Merchant
                $task_type_id = 3;
            } else {
                if ($sub_order->return == 1) {
                    $task_type_id = 5;
                } else {
                    $task_type_id = 1;
                }
            }
        } elseif ($sub_order->sub_order_status == 9 || $sub_order->sub_order_status == 49) {
            $task_type_id = 6; // post delivery order return to buyer
        } else {
            $task_type_id = 2;
        }

        if ($task_type_id == 3) {

            $sub_order_id = $sub_order->id;
            $unique_suborder_id = $sub_order->unique_suborder_id;
            $picking_title = $sub_order->product->product_title;

            if ($sub_order->return == 1) {

                $picking_address = $sub_order->order->delivery_address1;
                $picking_latitude = $sub_order->order->delivery_latitude;
                $picking_longitude = $sub_order->order->delivery_longitude;
                $delivery_title = $sub_order->product->pickup_location->title;
                $delivery_address = $sub_order->product->pickup_location->address1;
                $delivery_latitude = $sub_order->product->pickup_location->latitude;
                $delivery_longitude = $sub_order->product->pickup_location->longitude;
            } else {

                $picking_address = $sub_order->product->pickup_location->address1;
                $picking_latitude = $sub_order->product->pickup_location->latitude;
                $picking_longitude = $sub_order->product->pickup_location->longitude;
                $delivery_title = $sub_order->order->delivery_name;
                $delivery_address = $sub_order->order->delivery_address1;
                $delivery_latitude = $sub_order->order->delivery_latitude;
                $delivery_longitude = $sub_order->order->delivery_longitude;
            }

            $quantity = $sub_order->product->quantity;
            // $quantity = $this->get_sub_order_quantity($sub_order->id);            
            if ($sub_order->order->paymentMethod->id == 2) {
                $amount = 0;
            } else {
                $amount = $sub_order->product->total_payable_amount;
            }
            $hub_id = $delivery_hub_id;
            $hub_title = $delivery_hub_title;
        } else if ($task_type_id == 1) {

            $sub_order_id = $sub_order->id;
            $unique_suborder_id = $sub_order->unique_suborder_id;
            $picking_title = $sub_order->product->product_title;
            $picking_address = $sub_order->product->pickup_location->address1;
            $picking_latitude = $sub_order->product->pickup_location->latitude;
            $picking_longitude = $sub_order->product->pickup_location->longitude;
            $delivery_title = $picking_hub_title;
            $delivery_address = $sub_order->product->pickup_location->zone->hub->address1;
            $delivery_latitude = $sub_order->product->pickup_location->zone->hub->latitude;
            $delivery_longitude = $sub_order->product->pickup_location->zone->hub->longitude;
            $quantity = $sub_order->product->quantity;
            // $quantity = $this->get_sub_order_quantity($sub_order->id);
            $amount = 0;
            $hub_id = $picking_hub_id;
            $hub_title = $picking_hub_title;
        } else if ($task_type_id == 5) {

            $sub_order_id = $sub_order->id;
            $unique_suborder_id = $sub_order->unique_suborder_id;
            $picking_title = $sub_order->order->delivery_name;
            $picking_address = $sub_order->order->delivery_address1;
            $picking_latitude = $sub_order->order->delivery_latitude;
            $picking_longitude = $sub_order->order->delivery_longitude;
            $delivery_title = $delivery_hub_title;
            $delivery_address = $sub_order->source_hub->address1;
            $delivery_latitude = $sub_order->source_hub->latitude;
            $delivery_longitude = $sub_order->source_hub->longitude;
            $quantity = $sub_order->product->quantity;
            // $quantity = $this->get_sub_order_quantity($sub_order->id);
            $amount = 0;
            $hub_id = $delivery_hub_id;
            $hub_title = $delivery_hub_title;
        } elseif ($task_type_id == 6) { //post delivery order return to buyer
            $sub_order_id = $sub_order->id;
            $unique_suborder_id = $sub_order->unique_suborder_id;
            $quantity = $sub_order->product->quantity;
            // $quantity = $this->get_sub_order_quantity($sub_order->id);
            $picking_title = $sub_order->product->product_title;

            $picking_address = $sub_order->order->delivery_zone->hub->address1;
            $picking_latitude = $sub_order->order->delivery_zone->hub->latitude;
            $picking_longitude = $sub_order->order->delivery_zone->hub->longitude;
            $delivery_title = $sub_order->order->delivery_name;
            $delivery_address = $sub_order->order->delivery_address1;
            $delivery_latitude = $sub_order->order->delivery_latitude;
            $delivery_longitude = $sub_order->order->delivery_longitude;

            $amount = 0;

            $hub_id = $delivery_hub_id;
            $hub_title = $delivery_hub_title;
        } else {  //task type 2 or 4
            $sub_order_id = $sub_order->id;
            $unique_suborder_id = $sub_order->unique_suborder_id;
            $quantity = $sub_order->product->quantity;
            // $quantity = $this->get_sub_order_quantity($sub_order->id);
            $picking_title = $sub_order->product->product_title;

            if ($sub_order->return == 1) {

                $picking_address = $sub_order->source_hub->address1;
                $picking_latitude = $sub_order->source_hub->latitude;
                $picking_longitude = $sub_order->source_hub->longitude;
                $delivery_title = $sub_order->product->pickup_location->title;
                $delivery_address = $sub_order->product->pickup_location->address1;
                $delivery_latitude = $sub_order->product->pickup_location->latitude;
                $delivery_longitude = $sub_order->product->pickup_location->longitude;
                $task_type_id = 4;  // return

                $amount = 0;
            } else {

                $picking_address = $sub_order->order->delivery_zone->hub->address1;
                $picking_latitude = $sub_order->order->delivery_zone->hub->latitude;
                $picking_longitude = $sub_order->order->delivery_zone->hub->longitude;
                $delivery_title = $sub_order->order->delivery_name;
                $delivery_address = $sub_order->order->delivery_address1;
                $delivery_latitude = $sub_order->order->delivery_latitude;
                $delivery_longitude = $sub_order->order->delivery_longitude;

                if ($sub_order->order->paymentMethod->id == 2) {
                    $amount = 0;
                } else {
                    $amount = $sub_order->product->total_payable_amount;
                }

                $task_hub = $sub_order->destination_hub_id;

                // Update Sub-Order Status
                // $this->suborderStatus($product->sub_order->id, '28');
            }


            $hub_id = $delivery_hub_id;
            $hub_title = $delivery_hub_title;
        }

        $myBody['sub_order_id'] = (string) $sub_order_id;
        $myBody['unique_suborder_id'] = (string) $unique_suborder_id;
        $myBody['task_type_id'] = (string) $task_type_id;
        $myBody['picking_title'] = (string) $picking_title;
        $myBody['picking_address'] = (string) $picking_address;
        $myBody['picking_latitude'] = (string) $picking_latitude;
        $myBody['picking_longitude'] = (string) $picking_longitude;
        $myBody['delivery_title'] = (string) $delivery_title;
        $myBody['delivery_address'] = (string) $delivery_address;
        $myBody['delivery_latitude'] = (string) $delivery_latitude;
        $myBody['delivery_longitude'] = (string) $delivery_longitude;
        $myBody['quantity'] = (string) $quantity;
        $myBody['amount'] = (string) $amount;
        $myBody['hub_id'] = (string) $hub_id;
        $myBody['hub_title'] = (string) $hub_title;

        // Revarse FCM notification
        $sub_order_count = SubOrder::where('id', $sub_order_id)->count();
        if ($sub_order_count == 0) {
            Log::info("SubOrder not found");
        } else {

            $now = date("Y-m-d H:i:s");
            $attempt_start = $now;
            $attempt_end = date("Y-m-d H:i:s", (strtotime(date($attempt_start)) + config('app.attempt_expire')));
            $task_status = 1;
            $created_at = $now;

            $exist = TmTask::where('sub_order_id', $sub_order_id)
                    ->where('task_type_id', $task_type_id)
                    ->orderBy('id', 'desc')
                    ->count();

            $task = TmTask::where('sub_order_id', $sub_order_id)
//                ->where('task_type_id', $task_type_id)
                    ->whereStatus(1)
                    ->orderBy('id', 'desc')
                    ->first();
            if ($task) {
                $task->status = 3;
                $task->save();
                $attempt = $task->attempt + 1;
            } else {
                $attempt = 1;
            }

            if ($user_id) {
                $user = User::select("users.id as user_id")
                        ->where('users.status', 1)
                        ->where('users.online_status', 1)
                        ->where('id', $user_id)
                        ->first();
            } else if ($exist == 0) {
                $user = User::select("users.id as user_id", \DB::raw("6371 * acos(cos(radians(" . $picking_latitude . "))
                                     * cos(radians(users.latitude))
                                     * cos(radians(users.longitude) - radians(" . $picking_longitude . "))
                                     + sin(radians(" . $picking_latitude . "))
                                     * sin(radians(users.latitude))) AS distance"))
                        ->join('role_user', 'role_user.user_id', '=', 'users.id')
                        ->join('rider_references', 'rider_references.user_id', '=', 'users.id')
                        ->where('rider_references.reference_id', '=', $task_hub)
                        ->where('role_user.role_id', 8)
                        ->where('users.status', 1)
                        ->where('users.online_status', 1)
                        ->orderBy('distance', 'asc')
                        ->first();
            } else {
                if ($attempt > config('app.max_attempt')) {

                    $this->setSubOrderTaskStatus($sub_order_id, $task_type_id);

                    if ($task->task_type_id == 1 || $task->task_type_id == 5 || $task->task_type_id == 3) {
                        $status_code = 2;
                    } else {
                        $status_code = 26;
                    }

                    // Log Status
                    $this->suborderStatus($task->sub_order_id, $status_code);

                    return 0;
                } else {
                    $used_users = TmTask::where('sub_order_id', $sub_order_id)
                            ->where('task_type_id', $task_type_id)
                            ->pluck('user_id')
                            ->toArray();

                    $user = User::select("users.id as user_id", \DB::raw("6371 * acos(cos(radians(" . $picking_latitude . "))
                                     * cos(radians(users.latitude))
                                     * cos(radians(users.longitude) - radians(" . $picking_longitude . "))
                                     + sin(radians(" . $picking_latitude . "))
                                     * sin(radians(users.latitude))) AS distance"))
                            ->join('role_user', 'role_user.user_id', '=', 'users.id')
                            ->join('rider_references', 'rider_references.user_id', '=', 'users.id')
                            ->where('rider_references.reference_id', '=', $task_hub)
                            ->where('role_user.role_id', 8)
                            ->where('users.status', 1)
                            ->where('users.online_status', 1)
                            ->whereNotIn('users.id', $used_users)
                            ->orderBy('distance', 'asc')
                            ->first();
                }
            }

            if (!$user) {

                $this->setSubOrderTaskStatus($sub_order_id, $task_type_id);

                Log::info("No rider found");

                if ($task) {
                    if ($task->task_type_id == 1 || $task->task_type_id == 5 || $task->task_type_id == 3) {
                        $status_code = 2;
                    } else {
                        $status_code = 26;
                    }
                } else {
                    $status_code = 2;
                }

                // Log Status
                $this->suborderStatus($sub_order->id, $status_code);
            } else {

                // to prevent single order, multi notification
                if (
                        (TmTask::whereSubOrderId($sub_order_id)->whereTaskTypeId($task_type_id)->whereFource(0)->where('status', '<', 3)->exists() || TmTask::whereSubOrderId($sub_order_id)->whereTaskTypeId($task_type_id)->whereFource(1)->whereStatus(1)->exists() ) && 
                        ConsignmentTask::whereSubOrderId($sub_order_id)->whereTaskTypeId($task_type_id)->where('status', '<', 3)->exists()
                ) {
                    Log::info("Multi Notification Attempt: suborder: $unique_suborder_id, user: $user_id, attampt: $attempt, task type: $task_type_id");

                    return 1;
                }
                $user_id = $user->user_id;

                $tm_task = new TmTask;
                $tm_task->sub_order_id = $sub_order_id;
                $tm_task->unique_suborder_id = $unique_suborder_id;
                $tm_task->task_type_id = $task_type_id;
                $tm_task->picking_title = $picking_title;
                $tm_task->picking_address = $picking_address;
                $tm_task->picking_latitude = $picking_latitude;
                $tm_task->picking_longitude = $picking_longitude;
                $tm_task->delivery_title = $delivery_title;
                $tm_task->delivery_address = $delivery_address;
                $tm_task->delivery_latitude = $delivery_latitude;
                $tm_task->delivery_longitude = $delivery_longitude;
                $tm_task->quantity = $quantity;
                $tm_task->amount = $amount;
                $tm_task->hub_id = $hub_id;
                $tm_task->hub_title = $hub_title;
                $tm_task->attempt = $attempt;
                $tm_task->attempt_start = $attempt_start;
                $tm_task->attempt_end = $attempt_end;
                $tm_task->user_id = $user_id;
                $tm_task->status = $task_status;
                $tm_task->created_at = $created_at;
                $tm_task->fource = $fource;
                $tm_task->save();

                if ($task_type_id == 1 || $task_type_id == 3 || $task_type_id == 5) {
                    $status_code = 3;
                } else if ($task_type_id == 2 || $task_type_id == 6) {
                    $status_code = 28;
                } else {
                    $status_code = 35;
                }

                // Log Status
                $this->suborderStatus($sub_order_id, $status_code);

                Log::info("Task Created: $unique_suborder_id");
                Log::info($tm_task);
            }
        }

        return 1;

        // $jsonData = json_encode($myBody);
        // Log::info($jsonData);
        // dd($jsonData);
        // $ch = curl_init("http://tm-ipost.publicdemo.xyz/gen_fcm");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt(
        //       $ch,
        //       CURLOPT_HTTPHEADER,
        //       array(
        //             'Content-Type: application/json',
        //             'Content-Length: ' . strlen($jsonData)
        //       )
        // );
        // $response = curl_exec($ch);
        // // dd($response);
        // //echo '<pre>';print_r($result);echo '</pre>';die();
        // if (curl_errno($ch)) {
        //         $err = curl_error($curl);
        //       // writeLog('<br />Curl Error No: ' . curl_errno($ch));
        //       // writeLog('<br />Curl error: ' . curl_error($ch));
        //         // $info = curl_getinfo($ch);
        //       // writeLog(json_encode($info));
        //         Log::error("cURL Error #:" . $err);
        //         $err = "cURL failed to create FCM";
        //         return $err;
        // }else{
        //     Log::info($response);
        //     return $response;
        // }
        // //close connection
        // curl_close($ch);
    }

    public function setSubOrderTaskStatus($sub_order_id, $task_type_id) {

        $sub_order_query = SubOrder::where('id', $sub_order_id);

        if ($sub_order_query->count() > 0) {
            $sub_order = $sub_order_query->first();
            if ($task_type_id == 1 || $task_type_id == 5) {
                $sub_order->tm_picking_status = 1;
            } elseif ($task_type_id == 2) {
                $sub_order->tm_delivery_status = 1;
            } elseif ($task_type_id == 4) {
                $sub_order->tm_delivery_status = 1;
            } else {
                $sub_order->tm_picking_status = 1;
                $sub_order->tm_delivery_status = 1;
            }
            $sub_order->save();
        }

        return 0;
    }

    public function get_sub_order_quantity($sub_order_id) {

        $quantity = 0;

        $cart_products = CartProduct::where('sub_order_id', $sub_order_id)->get();

        if ($cart_products) {

            foreach ($cart_products as $cart_product) {
                $quantity = $quantity + $cart_product->quantity;
            }
        } else {

            $order_product = OrderProduct::where('sub_order_id', $sub_order_id)->first();
            $quantity = $order_product->quantity;
        }

        return $quantity;
    }

}
