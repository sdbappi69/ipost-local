<!DOCTYPE html>
<html lang="en">
<head>
    <style type="text/css">
        @font-face {
                font-family: 'thesansarabic';
                src: url('{{public_path()}}/fonts/Bahij_TheSansArabic-Plain.ttf') format('truetype')
            }
        * {
            margin: 0;
            padding: 0;
            border: 0;
            outline: 0;
            vertical-align: baseline;
            background: transparent;
            font-family: 'thesansarabic';
        }

        table {
            border-collapse: collapse;
        }

        .large {
            font-weight: bold;
            font-size: 14px;
        }
        .medium {
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body style="font-size: 10px;">

<?php $i = 0; ?>

@foreach($consignment->task AS $task)

    <div style="padding: 10px; padding-top: 0; margin: 0 auto;">

        <div style='text-align: center;'>
            <img src="./assets/pages/img/login/login-invert.png" width="150px">
            
            <br>
            <?php
            // echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($task->suborder->unique_suborder_id, "C128B") . '" alt="barcode"   /><br>';
            ?>
            <img src="http://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{$task->suborder->unique_suborder_id}}">
            <p class="large">{{ $task->suborder->unique_suborder_id or '' }}</p>
        </div>

        <br>

        <h4>Pickup Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Pickup request Date</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    <?php $pickiup_created_at = orderApproveLog($task->suborder->order->id); ?>
                    {{ $pickiup_created_at }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->store->merchant->name or '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Phone Number</b></td>
                <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->store->merchant->msisdn or '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Store Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->product->pickup_location->title or '' }}
                </td>
            </tr>
        <!-- <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Phone Number</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $task->suborder->product->pickup_location->msisdn or '' }}
                </td>
              </tr> -->
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Pickup Address</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->product->pickup_location->address1 or '' }}
                    , {{ $task->suborder->product->pickup_location->zone->name or '' }}
                    , {{ $task->suborder->product->pickup_location->zone->city->name or '' }}
                    <br>
                    <b>Contact: {{ $task->suborder->product->pickup_location->msisdn or '' }}</b>
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Order ID</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->merchant_order_id or '' }}
                </td>
            </tr>
        </table>

        <br>

        <h4>Delivery Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Customer Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->delivery_name or '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Customer Phone Number</b></td>
                <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->delivery_msisdn or '' }}
                    @if($task->suborder->order->delivery_alt_msisdn)
                        , {{ $task->suborder->order->delivery_alt_msisdn or '' }}
                    @endIf
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Customer Address</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->delivery_address1 or '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Delivery Zone</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->delivery_zone->name or '' }}
                    , {{ $task->suborder->order->delivery_zone->city->name or '' }}
                </td>
            </tr>
        </table>

        <br>

        <h4>Product and Pricing Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Product Details</b></td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;"><b>Quantity</b></td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;"><b>Unit Price</b></td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;"><b>Total Price</b></td>
            </tr>
            @if($task->suborder->order->as_package == 1)
                @foreach($task->suborder->order->cart_products AS $cart_product)
                    <tr>
                        <td style="width: 40%; padding:5px; border:1px solid #000000;">
                            {{ $cart_product->product_title or '' }}<br>
                            Type: {{ $cart_product->product_category->name or '' }}
                        </td>
                        <td style="width: 20%; padding:5px; border:1px solid #000000;">
                            {{ $cart_product->quantity or '' }}
                        </td>
                        <td style="width: 20%; padding:5px; border:1px solid #000000;">
                            {{ $cart_product->unit_price or '' }}
                        </td>
                        <td style="width: 20%; padding:5px; border:1px solid #000000;">
                            {{ $cart_product->sub_total or '' }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->product->product_title or '' }}<br>
                        Type: {{ $task->suborder->product->product_category->name or '' }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->product->quantity or '' }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->product->unit_price or '' }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->product->sub_total or '' }}
                    </td>
                </tr>
            @endIf
        </table>

        <br>

        <br>

            <table style="width: 99%;">
                <tr>
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Delivery Charge</b></td>
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Total Collectable Amount</b></td>
                </tr>
                <tr>
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" >
                        @foreach($task->suborder->products as $row2)
                        
                        @if($row2->charge_details)
                        <?php
                        $charge = json_decode($row2->charge_details);
//                                                    dd($charge->trip_map);
                        ?>
                        <table>
                            <tr>
                                <td>Initial Charge</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $charge->initial_charge }}</td>
                            </tr>
                            @if($charge->discount_id)
                            <tr>
                                <td>Discount</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $charge->discount }}</td>
                            </tr>
                            @endif
                            <tr style="border-bottom:1px">
                                <td>Hub Transfer Charge</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $charge->hub_transit }} X {{ $charge->hub_transfer_charge }}</td>
                            </tr>
                            <tr>
                                <td>Total Delivery Charge</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $charge->final_delivery_charge }}</td>
                            </tr>
                        </table>
                        @endif
                        @endforeach
                    </td>
                    <td class="large" style="width: 50%; padding:5px; border:1px solid #000000;" >
                        <table class="medium">
                            <tr>
                                <td>Payment Method</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $task->suborder->order->paymentMethod->name or '' }}</td>
                            </tr>
                        </table>
                        {{ $task->suborder->product->total_payable_amount }}
                    </td>
                </tr>
            </table>

        @if(isset($task->suborder->order->order_remarks) && !empty($task->suborder->order->order_remarks))

            <br>

            <table style="width: 99%;">
                <tr>
                    <td style="padding:5px; border:1px solid #000000;"><b>Remarks/Special Notes</b></td>
                </tr>
                <tr>
                    <td style="padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->order->order_remarks or '' }}
                    </td>
                </tr>
            </table>

        @endIf

        <br>

        <table style="width: 99%;">
            <tr>
                <td style="width: 50%; padding:5px;"><b>Merchant Signature</b></td>
                <td style="width: 50%; padding:5px; text-align: right;"><b>Customer Signature</b></td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <br><br>
                    _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
                </td>
                <td style="width: 50%; text-align: right;">
                    <br><br>
                    _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
                </td>
            </tr>
        </table>

    </div>

    <?php $i++; ?>
    @if(sizeof($consignment->task) > 1 && $i != (sizeof($consignment->task)) )
        <div style="page-break-before: always;"></div>
    @endif

@endforeach

</body>
</html>