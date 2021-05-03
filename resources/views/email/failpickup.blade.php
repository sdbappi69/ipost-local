<?php

    $html = '<div style="width: 100%; text-align: center; margin: 0; padding: 0;">
                    <div class="logo" style="margin-top: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 5px solid #FCD404;">
                                    
                                        <img src="https://biddyut.com/img/x1487095966.png.pagespeed.ic.ts3RNsRdoS.png" alt="Biddyut Limited">
                                    </div>
                                    <div style="color: #666666; width: 80%; margin: 10px auto; font-family: arial; font-size: 12px; text-align: justify;" class="content">
                                        <p class="date" style="font-weight: bold; display: inline-block;">'.date('Y-m-d').'</p>
                                        <p>Dear '.$merchant["name"].', </p>

                                        <p>Your request for picking up the product with below order numbers are failed. Kindly call your Account Manager for another pickup request or call customer service for other query: 09612433988</p>

                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead style="background: #D81A24; color: #FFFFFF;">
                                                <th style="padding: 5px;">Serial</th>
                                                <th style="padding: 5px;">Product Id</th>
                                                <th style="padding: 5px;">Merchant Order Id</th>
                                                <th style="padding: 5px;">Product Information</th>
                                                <th style="padding: 5px;">Quantity</th>
                                                <th style="padding: 5px;">Reason</th>
                                            </thead>
                                            <tbody>';

                $i = 1;
                foreach ($picking_data as $data) {

                    $html .=                    '<tr style="border-bottom: 1px solid #D81A24;">
                                                    <td style="padding: 5px;">'. $i .'</td>
                                                    <td style="padding: 5px;">'. $data["unique_id"] .'</td>
                                                    <td style="padding: 5px;">'. $data["merchant_order_id"] .'</td>
                                                    <td style="padding: 5px;">'. $data["product_title"] .'</td>
                                                    <td style="padding: 5px;">'. $data["quantity"] .'</td>
                                                    <td style="padding: 5px;">'. $data["reason"] .'</td>
                                                </tr>';

                    $i++;
                }

                $html .=                    '</tbody>
                                        </table>

                                        <p>For any concern, kindly call Biddyut Customer Service: 09612433988</You>

                                        <p>Thank you,</br>Biddyut Team</p>
                                    </div>
                                </div>';

echo $html;