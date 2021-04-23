<?php

function fbStatusUpdate($sub_order) {
    $baseUrl = "https://test.fastbazzar.com";
    if ($sub_order->post_delivery_return == 0 && in_array($sub_order->sub_order_status, [7, 31, 37])) {
        $fastbazzar = getDeliveryUrlStatus($sub_order);
    } elseif ($sub_order->post_delivery_return == 1 && in_array($sub_order->sub_order_status, [7, 26, 31, 36, 37, 49])) {
        $fastbazzar = getPostDeliveryUrlStatus($sub_order);
    } else {
        return true;
    }
    try {
        dispatch(new \App\Jobs\FastBazzarOrderStatus($sub_order->id, $sub_order->order->merchant_order_id, $baseUrl . $fastbazzar['url'], $fastbazzar['status']));
        return TRUE;
    } catch (Exception $e) {
        Illuminate\Support\Facades\Log::error($e);
    }
}

function getDeliveryUrlStatus($sub_order) {
    $url = "/rest/default/V1/fastbazzar-ipost/updateorderstatus";
    switch ($sub_order->sub_order_status) {
        case 7:
            $status = "shipped";
            break;
        case 31:
            $status = "delivered";
            break;
        case 37:
            $status = "cancelled_by_buyer";
            break;
    }
    return [
        'url' => $url,
        'status' => $status
    ];
}

function getPostDeliveryUrlStatus($sub_order) {
    $url = "/rest/default/V1/fastbazzar-ipost/updateorderreturnstatus";

    switch ($sub_order->sub_order_status) {
        case 7:
            $status = "picked_up_in_transit";
            break;
        case 26:
            $status = "received_by_hub";
            break;
        case 36:
            $status = "dispatched_to_seller";
            break;
        case 49:
            $status = "hub_declined_dispatched_to_buyer";
            break;
        case 37:
            $status = "received_by_seller";
            break;
        case 31:
            $status = "hub_declined_received_by_buyer";
            break;
    }
    return [
        'url' => $url,
        'status' => $status
    ];
}
