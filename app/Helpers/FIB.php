<?php

function fibStatusUpdate($sub_order) {
    if (!in_array($sub_order->sub_order_status, [4, 7, 31, 37])) {
        return true;
    }
    $baseUrl = config('app.fib_url');
    $url = "$baseUrl/public/v1/deliveries/status";
    switch ($sub_order->sub_order_status) {
        case 4:
            $status = "CONFIRMED";
            break;
        case 7:
            $status = "IN_TRANSIT";
            break;
        case 31:
            $status = "DELIVERED";
            break;
        case 37: // return to buyer
            $status = "CANCELED";
            break;
    }
    try {
        dispatch(new \App\Jobs\FIBOrderStatus($sub_order->id, $sub_order->order->merchant_order_id, $url, $status));
        \Illuminate\Support\Facades\Log::info('Job Created (merchant order id): ' . $sub_order->order->merchant_order_id . ', status: ' . $sub_order->sub_order_status . ', title: ' . $status . ', suborderid: ' . $sub_order->id . " url: $url");
        return TRUE;
    } catch (Exception $e) {
        \Illuminate\Support\Facades\Log::error($e);
    }
}
