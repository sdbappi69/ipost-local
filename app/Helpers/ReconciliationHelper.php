<?php

// Get Task Detail for reconsilation
function taskDetail($task_group_id, $consignment_id, $consignment_type){

    switch (strtolower($consignment_type)) {

        case 'picking':
            
            // Products
            $products = DB::table('order_product')->select(
                            'pt.id',
                            'pt.type',
                            'pt.start_time',
                            'pt.end_time',
                            'pt.start_lat',
                            'pt.end_lat',
                            'pt.start_long',
                            'pt.end_long',
                            'pt.distance',
                            'pt.signature',
                            'pt.image',
                            'pt.status',
                            'pt.reason_id',
                            'pt.remarks',
                            'pt.reconcile',
                            'pt.quantity AS task_quantity',
                            'pt.return_quantity AS return_quantity',
                            'r.reason',
                            'so.unique_suborder_id',
                            'o.merchant_order_id',
                            'pc.name AS product_category',
                            'm.name AS merchant_name',
                            'order_product.f AS title',
                            'order_product.quantity',
                            'order_product.unit_price AS unit_product_price',
                            'order_product.unit_deivery_charge',
                            'order_product.sub_total AS total_product_price',
                            'order_product.total_delivery_charge AS total_delivery_charge',
                            'order_product.payable_product_price',
                            'order_product.total_payable_amount',
                            'order_product.delivery_pay_by_cus'
                        )
                        ->leftJoin('product_categories AS pc','pc.id', '=', 'order_product.product_category_id')
                        ->leftJoin('picking_task AS pt','pt.product_unique_id', '=', 'order_product.product_unique_id')
                        ->leftJoin('reasons AS r','r.id', '=', 'pt.reason_id')
                        ->leftJoin('sub_orders AS so','so.id', '=', 'order_product.sub_order_id')
                        ->leftJoin('orders AS o','o.id', '=', 'so.order_id')
                        ->leftJoin('stores AS s','s.id', '=', 'o.store_id')
                        ->leftJoin('merchants AS m','m.id', '=', 's.merchant_id')
                        ->where('pt.consignment_id', $consignment_id)
                        ->where('order_product.pickup_location_id', $task_group_id)
                        ->get();

            $products_list = [];
            foreach($products as $key => $product){
                $products_list[$key]['task_id'] = $product->id;
                $products_list[$key]['task_type'] = $product->type;
                $products_list[$key]['unique_suborder_id'] = $product->unique_suborder_id;
                $products_list[$key]['merchant_order_id'] = $product->merchant_order_id;
                $products_list[$key]['title'] = $product->title;
                $products_list[$key]['category'] = $product->product_category;

                $products_list[$key]['quantity'] = ($product->quantity == '') ? '0' : $product->quantity;
                $products_list[$key]['unit_product_price'] = ($product->unit_product_price == '') ? '0' : $product->unit_product_price;
                $products_list[$key]['unit_deivery_charge'] = ($product->unit_deivery_charge == '') ? '0' : $product->unit_deivery_charge;
                $products_list[$key]['total_product_price'] = ($product->total_product_price == '') ? '0' : $product->total_product_price;
                $products_list[$key]['total_delivery_charge'] = ($product->total_delivery_charge == '') ? '0' : $product->total_delivery_charge;
                $products_list[$key]['payable_product_price'] = ($product->payable_product_price == '') ? '0' : $product->payable_product_price;
                $products_list[$key]['total_payable_amount'] = ($product->total_payable_amount == '') ? '0' : $product->total_payable_amount;
                $products_list[$key]['task_quantity'] = ($product->task_quantity == '') ? '0' : $product->task_quantity;
                $products_list[$key]['return_quantity'] = ($product->return_quantity == '') ? '0' : $product->return_quantity;

                $products_list[$key]['delivery_pay_by_cus'] = $product->delivery_pay_by_cus;
                $products_list[$key]['start_time'] = ($product->start_time == null) ? "" : $product->start_time;
                $products_list[$key]['end_time'] = ($product->end_time == null) ? "" : $product->end_time;
                $products_list[$key]['distance'] = $product->distance;
                $products_list[$key]['start_lat'] = $product->start_lat;
                $products_list[$key]['end_lat'] = $product->end_lat;
                $products_list[$key]['start_long'] = $product->start_long;
                $products_list[$key]['end_long'] = $product->end_long;
                $products_list[$key]['signature'] = $product->signature;
                $products_list[$key]['image'] = $product->image;
                $products_list[$key]['status'] = $product->status;
                $products_list[$key]['reason_id'] = $product->reason_id;
                $products_list[$key]['remarks'] = $product->remarks;
                $products_list[$key]['reason'] = $product->reason;
                $products_list[$key]['reconcile'] = $product->reconcile;
                $products_list[$key]['merchant_name'] = $product->merchant_name;
            }

            break;

        case 'delivery':
            
            // Products
            $products = DB::table('order_product')->select(
                            'dt.id',
                            'dt.start_time',
                            'dt.end_time',
                            'dt.distance',
                            'dt.start_lat',
                            'dt.end_lat',
                            'dt.start_long',
                            'dt.end_long',
                            'dt.signature',
                            'dt.image',
                            'dt.status',
                            'dt.reason_id',
                            'dt.remarks',
                            'dt.amount',
                            'dt.reconcile',
                            'dt.quantity AS task_quantity',
                            'r.reason',
                            'so.unique_suborder_id',
                            'o.merchant_order_id',
                            'pc.name AS product_category',
                            'm.name AS merchant_name',
                            'order_product.product_title AS title',
                            'order_product.quantity',
                            'order_product.unit_price AS unit_product_price',
                            'order_product.unit_deivery_charge',
                            'order_product.sub_total AS total_product_price',
                            'order_product.total_delivery_charge AS total_delivery_charge',
                            'order_product.payable_product_price',
                            'order_product.total_payable_amount',
                            'order_product.delivery_pay_by_cus'
                        )
                        ->leftJoin('product_categories AS pc','pc.id', '=', 'order_product.product_category_id')
                        ->leftJoin('sub_orders AS so','so.id', '=', 'order_product.sub_order_id')
                        ->leftJoin('orders AS o','o.id', '=', 'order_product.order_id')
                        ->leftJoin('delivery_task AS dt','dt.unique_suborder_id', '=', 'so.unique_suborder_id')
                        ->leftJoin('reasons AS r','r.id', '=', 'dt.reason_id')
                        ->leftJoin('stores AS s','s.id', '=', 'o.store_id')
                        ->leftJoin('merchants AS m','m.id', '=', 's.merchant_id')
                        ->where('dt.consignment_id', $consignment_id)
                        ->where('o.id', $task_group_id)
                        ->get();

            $products_list = [];
            foreach($products as $key => $product){
                $products_list[$key]['task_id'] = $product->id;
                $products_list[$key]['task_type'] = 'delivery';
                $products_list[$key]['unique_suborder_id'] = $product->unique_suborder_id;
                $products_list[$key]['merchant_order_id'] = $product->merchant_order_id;
                $products_list[$key]['title'] = $product->title;
                $products_list[$key]['category'] = $product->product_category;

                $products_list[$key]['quantity'] = ($product->quantity == '') ? '0' : $product->quantity;
                $products_list[$key]['unit_product_price'] = ($product->unit_product_price == '') ? '0' : $product->unit_product_price;
                $products_list[$key]['unit_deivery_charge'] = ($product->unit_deivery_charge == '') ? '0' : $product->unit_deivery_charge;
                $products_list[$key]['total_product_price'] = ($product->total_product_price == '') ? '0' : $product->total_product_price;
                $products_list[$key]['total_delivery_charge'] = ($product->total_delivery_charge == '') ? '0' : $product->total_delivery_charge;
                $products_list[$key]['payable_product_price'] = ($product->payable_product_price == '') ? '0' : $product->payable_product_price;
                $products_list[$key]['total_payable_amount'] = ($product->total_payable_amount == '') ? '0' : $product->total_payable_amount;
                $products_list[$key]['paid_amount'] = ($product->amount == '') ? '0' : $product->amount;
                $products_list[$key]['task_quantity'] = ($product->task_quantity == '') ? '0' : $product->task_quantity;

                $products_list[$key]['delivery_pay_by_cus'] = $product->delivery_pay_by_cus;
                $products_list[$key]['start_time'] = ($product->start_time == null) ? "" : $product->start_time;
                $products_list[$key]['end_time'] = ($product->end_time == null) ? "" : $product->end_time;
                $products_list[$key]['distance'] = $product->distance;
                $products_list[$key]['start_lat'] = $product->start_lat;
                $products_list[$key]['end_lat'] = $product->end_lat;
                $products_list[$key]['start_long'] = $product->start_long;
                $products_list[$key]['end_long'] = $product->end_long;
                $products_list[$key]['signature'] = $product->signature;
                $products_list[$key]['image'] = $product->image;
                $products_list[$key]['status'] = $product->status;
                $products_list[$key]['reason_id'] = $product->reason_id;
                $products_list[$key]['remarks'] = $product->remarks;
                $products_list[$key]['reason'] = $product->reason;
                $products_list[$key]['reconcile'] = $product->reconcile;
                $products_list[$key]['merchant_name'] = $product->merchant_name;
            }

            break;

    }

    return $products_list;

}

function getTaskImage($consignment_id, $consignment_type, $task_group_id){

    $images = array('signature' => '', 'photo' => '');

    switch (strtolower($consignment_type)) {

        case 'picking':
            
            // Tasks
            $signature = DB::table('picking_task')
                        ->select('picking_task.signature')
                        ->leftJoin('order_product AS op','op.product_unique_id', '=', 'picking_task.product_unique_id')
                        ->leftJoin('pickup_locations AS pl','pl.id', '=', 'op.pickup_location_id')
                        ->whereNotNull('picking_task.signature')
                        ->where('picking_task.consignment_id', $consignment_id)
                        ->where('pl.id', $task_group_id)
                        ->first();

            $photo = DB::table('picking_task')
                        ->select('picking_task.image')
                        ->leftJoin('order_product AS op','op.product_unique_id', '=', 'picking_task.product_unique_id')
                        ->leftJoin('pickup_locations AS pl','pl.id', '=', 'op.pickup_location_id')
                        ->whereNotNull('picking_task.image')
                        ->where('picking_task.consignment_id', $consignment_id)
                        ->where('pl.id', $task_group_id)
                        ->first();

            break;

        case 'delivery':
            
            // Tasks
            $signature = DB::table('delivery_task')
                        ->select('delivery_task.signature')
                        ->leftJoin('sub_orders AS so','so.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
                        ->leftJoin('orders AS o','o.id', '=', 'so.order_id')
                        ->whereNotNull('delivery_task.signature')
                        ->where('delivery_task.consignment_id', $consignment_id)
                        ->where('o.id', $task_group_id)
                        ->first();

            $photo = DB::table('delivery_task')
                        ->select('delivery_task.image')
                        ->leftJoin('sub_orders AS so','so.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
                        ->leftJoin('orders AS o','o.id', '=', 'so.order_id')
                        ->whereNotNull('delivery_task.image')
                        ->where('delivery_task.consignment_id', $consignment_id)
                        ->where('o.id', $task_group_id)
                        ->first();

            break;

    }

    if(count($signature) > 0){
        $images['signature'] = $signature->signature;
    }else{
        $images['signature'] = '';
    }

    if(count($photo) > 0){
        $images['photo'] = $photo->image;
    }else{
        $images['photo'] = '';
    }

    return $images;

}
