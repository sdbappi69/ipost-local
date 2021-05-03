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
            <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{$task->suborder->unique_suborder_id}}">
            <p class="large">{{ $task->suborder->unique_suborder_id or '' }}</p>
        </div>

        <br>
        @if($task->suborder->return == 0)
            <h4>Pickup Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->order->store->merchant->name or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Number</b></td>
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
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Number</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $task->suborder->product->pickup_location->msisdn or '' }}
                    </td>
                  </tr> -->
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Address</b></td>
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
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Customer Number</b></td>
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
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Zone</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;">
                    {{ $task->suborder->order->delivery_zone->name or '' }}
                    , {{ $task->suborder->order->delivery_zone->city->name or '' }}
                </td>
            </tr>
        </table>
        @else
            <h4>Pickup Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Customer Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->order->delivery_name or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Customer Number</b></td>
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
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->order->delivery_zone->name or '' }}
                        , {{ $task->suborder->order->delivery_zone->city->name or '' }}
                    </td>
                </tr>
            </table>
            <br>
            <h4>Delivery Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;">
                        {{ $task->suborder->order->store->merchant->name or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Merchant Number</b></td>
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
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Number</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $task->suborder->product->pickup_location->msisdn or '' }}
                    </td>
                  </tr> -->
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Address</b></td>
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
        @endif
        <br>

        <h4>Product and Pricing Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;"><b>Product Details</b></td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;"><b>Quantity</b></td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;"><b>Total Price(IQD)</b></td>
            </tr>
            @if($task->suborder->order->as_package == 1)
{{--                @foreach($task->suborder->order->cart_products AS $cart_product)--}}
{{--                    <tr>--}}
{{--                        <td style="width: 40%; padding:5px; border:1px solid #000000;">--}}
{{--                            {{ $cart_product->product_title or '' }}<br>--}}
{{--                            Type: {{ $cart_product->product_category->name or '' }}--}}
{{--                        </td>--}}
{{--                        <td style="width: 20%; padding:5px; border:1px solid #000000;">--}}
{{--                            {{ $cart_product->quantity or '' }}--}}
{{--                        </td>--}}
{{--                        <td style="width: 20%; padding:5px; border:1px solid #000000;">--}}
{{--                            {{ number_format($cart_product->unit_price) }}--}}
{{--                        </td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" >
                        {{ $task->suborder->product->product_title }}<br>
                        Type: {{ $task->suborder->product->product_category->name }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $task->suborder->order->cart_products->sum('quantity') }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        @if($task->suborder->post_delivery_return == 1)
                            {{ number_format($task->suborder->product->sub_total) }}
                        @elseif($task->suborder->return == 0)
                            {{ number_format($task->suborder->product->payable_product_price) }}
                        @else
                            {{ $task->suborder->parent_sub_order ? number_format($task->suborder->parent_sub_order->product->payable_product_price) : number_format($task->suborder->product->payable_product_price) }}
                        @endif
                    </td>
                </tr>
            @else
{{--                <tr>--}}
{{--                    <td style="width: 40%; padding:5px; border:1px solid #000000;">--}}
{{--                        {{ $task->suborder->product->product_title or '' }}<br>--}}
{{--                        Type: {{ $task->suborder->product->product_category->name or '' }}--}}
{{--                    </td>--}}
{{--                    <td style="width: 20%; padding:5px; border:1px solid #000000;">--}}
{{--                        {{ $task->suborder->product->quantity or '' }}--}}
{{--                    </td>--}}
{{--                    <td style="width: 20%; padding:5px; border:1px solid #000000;">--}}
{{--                        {{ number_format($task->suborder->product->unit_price) }}--}}
{{--                    </td>--}}
{{--                </tr>--}}
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" >
                        {{ $task->suborder->product->product_title }}<br>
                        Type: {{ $task->suborder->product->product_category->name }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $task->suborder->product->quantity }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        @if($task->suborder->post_delivery_return == 1)
                            {{ number_format($task->suborder->product->sub_total) }}
                        @elseif($task->suborder->return == 0)
                            {{ number_format($task->suborder->product->payable_product_price) }}
                        @else
                            {{ $task->suborder->parent_sub_order ? number_format($task->suborder->parent_sub_order->product->payable_product_price) : number_format($task->suborder->product->payable_product_price) }}
                        @endif
                    </td>
                </tr>
            @endIf
        </table>

        <br>

        <br>

        @if($task->suborder->post_delivery_return == 0 )
            <table style="width: 99%;">
                <tr>
                    @if($task->suborder->order->delivery_pay_by_cus == 1 )
                        @if(in_array($task->suborder->sub_order_status,[26,27,28,29,30,31,32,33,38,39]) && $task->task_type_id != 1 && $task->suborder->return == 0)
                            <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Delivery Charge(IQD)</b></td>
                        @endif
                    @endif
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Total Collectable Amount(IQD)</b></td>
                </tr>
                <tr>
                    @if($task->suborder->order->delivery_pay_by_cus == 1 )
                        @if(in_array($task->suborder->sub_order_status,[26,27,28,29,30,31,32,33,38,39]) && $task->task_type_id != 1 && $task->suborder->return == 0)
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
                                                <td>{{ $charge->hub_transit or 0 }} X {{ $charge->hub_transfer_charge }}</td>
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
                        @endif
                    @endif
                    <td class="large" style="width: 50%; padding:5px; border:1px solid #000000;" >
                        @if(in_array($task->suborder->sub_order_status,[26,27,28,29,30,31,32,33,34,38,39]) && $task->task_type_id != 1 && $task->suborder->return == 0)
                            <table class="medium">
                                <tr>
                                    <td>Payment Method</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td style="color: red">{{ $task->suborder->order->paymentMethod->name or '' }}@if($task->suborder->order->paymentMethod->id == 2)<img src="{{public_path()}}/fastpay.png" alt="fastpay" width="50" height="12">@endif</td>
                                </tr>
                            </table>
                            @if($task->suborder->order->paymentMethod->id == 2)
                                0
                            @else
                                {{ number_format($task->suborder->product->total_payable_amount) }}
                            @endif
                        @else
                            0
                        @endif
                    </td>
                </tr>
            </table>
        @else
            <table style="width: 99%;">
                <tr>
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Total Collectable Amount</b></td>
                </tr>
                <tr>
                    <td class="large" style="width: 50%; padding:5px; border:1px solid #000000;" >
                        0
                    </td>
                </tr>
            </table>
        @endif

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
