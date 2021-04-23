<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Auth;

use App\Merchant;
use App\PickingTask;
use App\DeliveryTask;

use Log;

class MailViewController extends Controller
{

    public function successpickup(){

        $html = '';

        $merchants = Merchant::whereStatus(true)->get();

        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'picking_task.quantity',
                                            'picking_task.status'
                                        )
                                // ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [2, 3])
                                ->where('picking_task.type', 'Picking')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                // return view('email.successpickup', compact('merchant', 'picking_data'));

                $html .= '<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Biddyut Limited</title>
                                    <style type="text/css">
                                        body{
                                            width: 100%; text-align: center; margin: 0; padding: 0;
                                        }
                                        .logo{
                                            margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;
                                        }
                                        .content{
                                            color: #666666;
                                            width: 80%;
                                            margin: 10px auto;
                                            font-family: arial;
                                            font-size: 12px;
                                            text-align: justify;
                                        }
                                        .date{
                                            /*float: right;*/
                                            font-weight: bold;
                                            display: inline-block;
                                        }
                                        table{
                                            width: 100%;
                                            border-collapse: collapse; 
                                        }
                                        thead{
                                            background: #D81A24;
                                            color: #FFFFFF;
                                        }
                                        tr{
                                            border-bottom: 1px solid #D81A24;
                                        }
                                        td{
                                            padding: 5px;
                                        }
                                        th{
                                            padding: 5px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="logo">
                                        <img src="http://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant->name.', </p>

                                        <p>Your request for picking up the product with below order numbers are successful. You will get a confirmation email once your product is delivered to the customer end.</p>

                                        <table>
                                            <thead>
                                                <th>Serial</th>
                                                <th>Product Id</th>
                                                <th>Merchant Order Id</th>
                                                <th>Product Information</th>
                                                <th>Quantity</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($picking_data as $data) {

                    if($data->status == 2){
                        $status = 'Full Picked';
                    }else if($data->status == 3){
                        $status = 'Partial Picked';
                    }

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data->unique_id .'</td>
                                                    <td>'. $data->merchant_order_id .'</td>
                                                    <td>'. $data->product_title .'</td>
                                                    <td>'. $data->quantity .'</td>
                                                    <td>'. $status .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </body>
                            </html>';

            }

        }

        return $html;

    }

    public function failpickup(){

        Log::info('Test:');

        $html = '';

        $merchants = Merchant::whereStatus(true)->get();

        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'r.reason'
                                        )
                                // ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('reasons AS r','r.id','=','picking_task.reason_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [4])
                                ->where('picking_task.type', 'Picking')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                $html .= '<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Biddyut Limited</title>
                                    <style type="text/css">
                                        body{
                                            width: 100%; text-align: center; margin: 0; padding: 0;
                                        }
                                        .logo{
                                            margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;
                                        }
                                        .content{
                                            color: #666666;
                                            width: 80%;
                                            margin: 10px auto;
                                            font-family: arial;
                                            font-size: 12px;
                                            text-align: justify;
                                        }
                                        .date{
                                            /*float: right;*/
                                            font-weight: bold;
                                            display: inline-block;
                                        }
                                        table{
                                            width: 100%;
                                            border-collapse: collapse; 
                                        }
                                        thead{
                                            background: #D81A24;
                                            color: #FFFFFF;
                                        }
                                        tr{
                                            border-bottom: 1px solid #D81A24;
                                        }
                                        td{
                                            padding: 5px;
                                        }
                                        th{
                                            padding: 5px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="logo">
                                        <img src="http://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant->name.', </p>

                                        <p>Your request for picking up the product with below order numbers are failed. Kindly call your Account Manager for another pickup request or call customer service for other query: 09612433988</p>

                                        <table>
                                            <thead>
                                                <th>Serial</th>
                                                <th>Product Id</th>
                                                <th>Merchant Order Id</th>
                                                <th>Product Information</th>
                                                <th>Quantity</th>
                                                <th>Reason</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($picking_data as $data) {

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data->unique_id .'</td>
                                                    <td>'. $data->merchant_order_id .'</td>
                                                    <td>'. $data->product_title .'</td>
                                                    <td>'. $data->quantity .'</td>
                                                    <td>'. $data->reason or "" .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </body>
                            </html>';

            }

        }

        return $html;

    }

    public function successreturn(){

        $html = '';

        $merchants = Merchant::whereStatus(true)->get();

        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'picking_task.quantity',
                                            'picking_task.status'
                                        )
                                ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [2, 3])
                                ->where('picking_task.type', 'Return')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                $html .= '<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Biddyut Limited</title>
                                    <style type="text/css">
                                        body{
                                            width: 100%; text-align: center; margin: 0; padding: 0;
                                        }
                                        .logo{
                                            margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;
                                        }
                                        .content{
                                            color: #666666;
                                            width: 80%;
                                            margin: 10px auto;
                                            font-family: arial;
                                            font-size: 12px;
                                            text-align: justify;
                                        }
                                        .date{
                                            /*float: right;*/
                                            font-weight: bold;
                                            display: inline-block;
                                        }
                                        table{
                                            width: 100%;
                                            border-collapse: collapse; 
                                        }
                                        thead{
                                            background: #D81A24;
                                            color: #FFFFFF;
                                        }
                                        tr{
                                            border-bottom: 1px solid #D81A24;
                                        }
                                        td{
                                            padding: 5px;
                                        }
                                        th{
                                            padding: 5px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="logo">
                                        <img src="http://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant->name.', </p>

                                        <p>Your product with below order numbers are returned to your shop / warehouse. You can again request for a delivery by calling your Account Manager or call customer service for other query: 09612433988</p>

                                        <table>
                                            <thead>
                                                <th>Serial</th>
                                                <th>Product Id</th>
                                                <th>Merchant Order Id</th>
                                                <th>Product Information</th>
                                                <th>Quantity</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($picking_data as $data) {

                    if($data->status == 2){
                        $status = 'Full Picked';
                    }else if($data->status == 3){
                        $status = 'Partial Picked';
                    }

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data->unique_id .'</td>
                                                    <td>'. $data->merchant_order_id .'</td>
                                                    <td>'. $data->product_title .'</td>
                                                    <td>'. $data->quantity .'</td>
                                                    <td>'. $status .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </body>
                            </html>';

            }

        }

        return $html;

    }

    public function failreturn(){

        $html = '';

        $merchants = Merchant::whereStatus(true)->get();

        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'r.reason'
                                        )
                                ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('reasons AS r','r.id','=','picking_task.reason_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [4])
                                ->where('picking_task.type', 'Return')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                $html .= '<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Biddyut Limited</title>
                                    <style type="text/css">
                                        body{
                                            width: 100%; text-align: center; margin: 0; padding: 0;
                                        }
                                        .logo{
                                            margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;
                                        }
                                        .content{
                                            color: #666666;
                                            width: 80%;
                                            margin: 10px auto;
                                            font-family: arial;
                                            font-size: 12px;
                                            text-align: justify;
                                        }
                                        .date{
                                            /*float: right;*/
                                            font-weight: bold;
                                            display: inline-block;
                                        }
                                        table{
                                            width: 100%;
                                            border-collapse: collapse; 
                                        }
                                        thead{
                                            background: #D81A24;
                                            color: #FFFFFF;
                                        }
                                        tr{
                                            border-bottom: 1px solid #D81A24;
                                        }
                                        td{
                                            padding: 5px;
                                        }
                                        th{
                                            padding: 5px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="logo">
                                        <img src="http://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant->name.', </p>

                                        <p>Your product with below order numbers are failed to returned to your shop / warehouse. You can again request for a delivery by calling your Account Manager or call customer service for other query: 09612433988</p>

                                        <table>
                                            <thead>
                                                <th>Serial</th>
                                                <th>Product Id</th>
                                                <th>Merchant Order Id</th>
                                                <th>Product Information</th>
                                                <th>Quantity</th>
                                                <th>Reason</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($picking_data as $data) {

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data->unique_id .'</td>
                                                    <td>'. $data->merchant_order_id .'</td>
                                                    <td>'. $data->product_title .'</td>
                                                    <td>'. $data->quantity .'</td>
                                                    <td>'. $data->reason or "" .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </body>
                            </html>';

            }

        }

        return $html;

    }

    public function successdelivery(){

        $html = '';

        $merchants = Merchant::whereStatus(true)->get();

        foreach ($merchants as $merchant) {

            $delivery_data = DeliveryTask::select(
                                            'delivery_task.unique_suborder_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'delivery_task.status'
                                        )
                                ->WhereBetween('delivery_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('delivery_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','delivery_task.unique_suborder_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('delivery_task.status', [2, 3])
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($delivery_data) > 0){

                $html .= '<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Biddyut Limited</title>
                                    <style type="text/css">
                                        body{
                                            width: 100%; text-align: center; margin: 0; padding: 0;
                                        }
                                        .logo{
                                            margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;
                                        }
                                        .content{
                                            color: #666666;
                                            width: 80%;
                                            margin: 10px auto;
                                            font-family: arial;
                                            font-size: 12px;
                                            text-align: justify;
                                        }
                                        .date{
                                            /*float: right;*/
                                            font-weight: bold;
                                            display: inline-block;
                                        }
                                        table{
                                            width: 100%;
                                            border-collapse: collapse; 
                                        }
                                        thead{
                                            background: #D81A24;
                                            color: #FFFFFF;
                                        }
                                        tr{
                                            border-bottom: 1px solid #D81A24;
                                        }
                                        td{
                                            padding: 5px;
                                        }
                                        th{
                                            padding: 5px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="logo">
                                        <img src="http://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant->name.', </p>

                                        <p>Below order numbers are sucessful and well received by the customer. Kindly call your Account Manager for another delivery request or call customer service for other query: 09612433988</p>

                                        <table>
                                            <thead>
                                                <th>Serial</th>
                                                <th>Product Id</th>
                                                <th>Merchant Order Id</th>
                                                <th>Product Information</th>
                                                <th>Quantity</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($delivery_data as $data) {

                    if($data->status == 2){
                        $status = 'Full Delivered';
                    }else if($data->status == 3){
                        $status = 'Partial Delivered';
                    }

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data->unique_id .'</td>
                                                    <td>'. $data->merchant_order_id .'</td>
                                                    <td>'. $data->product_title .'</td>
                                                    <td>'. $data->quantity .'</td>
                                                    <td>'. $status .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </body>
                            </html>';

            }

        }

        return $html;

    }

    public function faildelivery(){

        $html = '';

        $merchants = Merchant::whereStatus(true)->get();

        foreach ($merchants as $merchant) {

            $delivery_data = DeliveryTask::select(
                                            'delivery_task.unique_suborder_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'r.reason'
                                        )
                                ->WhereBetween('delivery_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('delivery_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','delivery_task.unique_suborder_id')
                                ->leftJoin('reasons AS r','r.id','=','delivery_task.reason_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('delivery_task.status', [4])
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($delivery_data) > 0){

                $html .= '<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Biddyut Limited</title>
                                    <style type="text/css">
                                        body{
                                            width: 100%; text-align: center; margin: 0; padding: 0;
                                        }
                                        .logo{
                                            margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;
                                        }
                                        .content{
                                            color: #666666;
                                            width: 80%;
                                            margin: 10px auto;
                                            font-family: arial;
                                            font-size: 12px;
                                            text-align: justify;
                                        }
                                        .date{
                                            /*float: right;*/
                                            font-weight: bold;
                                            display: inline-block;
                                        }
                                        table{
                                            width: 100%;
                                            border-collapse: collapse; 
                                        }
                                        thead{
                                            background: #D81A24;
                                            color: #FFFFFF;
                                        }
                                        tr{
                                            border-bottom: 1px solid #D81A24;
                                        }
                                        td{
                                            padding: 5px;
                                        }
                                        th{
                                            padding: 5px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="logo">
                                        <img src="http://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant->name.', </p>

                                        <p>Below order numbers are fail to delivered to the customers. Kindly call your Account Manager for another delivery request or call customer service for other query: 09612433988</p>

                                        <table>
                                            <thead>
                                                <th>Serial</th>
                                                <th>Product Id</th>
                                                <th>Merchant Order Id</th>
                                                <th>Product Information</th>
                                                <th>Reason</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($delivery_data as $data) {

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data->unique_id .'</td>
                                                    <td>'. $data->merchant_order_id .'</td>
                                                    <td>'. $data->product_title .'</td>
                                                    <td>'. $data->reason or "" .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </body>
                            </html>';

            }

        }

        return $html;

    }

}
