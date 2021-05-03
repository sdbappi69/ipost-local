<?php

    $html = '<!DOCTYPE html>
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
                                        <img src="https://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="" class="content">
                                        <p class="date">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant["name"].', </p>

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

                    if($data["status"] == 2){
                        $status = 'Full Picked';
                    }else if($data["status"] == 3){
                        $status = 'Partial Picked';
                    }

                    $html .=                    '<tr>
                                                    <td>'. $i .'</td>
                                                    <td>'. $data["unique_id"] .'</td>
                                                    <td>'. $data["merchant_order_id"] .'</td>
                                                    <td>'. $data["product_title"] .'</td>
                                                    <td>'. $data["quantity"] .'</td>
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

echo $html;