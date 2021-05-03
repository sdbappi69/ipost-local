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
        table{
            border-collapse:collapse;
        }
        .large{
            font-weight: bold;
            font-size: 15px;
        }
        .medium{
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body style="font-size: 10px;">

<div style="padding: 10px; padding-top: 0; margin: 0 auto;">

    <div style='text-align: center;'>
        <img src="./assets/pages/img/login/login-invert.png" width="150px">

        <br>
        <?php
        // echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B") . '" alt="barcode"   /><br>';
        ?>
        <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{$sub_order->unique_suborder_id}}">
        <p class="large">{{ $sub_order->unique_suborder_id }}</p>
    </div>

    <br>

    @if($sub_order->return == 0)
        <h4>Pickup Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->store->merchant->name }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Number</b></td>
                <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->store->merchant->msisdn }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->pickup_location->title }}
                </td>
            </tr>
        <!-- <tr>
                      <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Number</b></td>
                      <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                          {{ $sub_order->product->pickup_location->msisdn }}
                </td>
              </tr> -->
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Address</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->pickup_location->address1 }}, {{ $sub_order->product->pickup_location->zone->name }}, {{ $sub_order->product->pickup_location->zone->city->name }}
                    <br>
                    <b>Contact: {{ $sub_order->product->pickup_location->msisdn or '' }}</b>
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Order ID</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->merchant_order_id }}
                </td>
            </tr>
        </table>
        <br>
        <h4>Delivery Information</h4>
        <table style="width: 99%;">
        <tr>
            <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Name</b></td>
            <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                {{ $sub_order->order->delivery_name }}
            </td>
        </tr>
        <tr>
            <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Number</b></td>
            <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                {{ $sub_order->order->delivery_msisdn }}
                @if($sub_order->order->delivery_alt_msisdn)
                    , {{ $sub_order->order->delivery_alt_msisdn }}
                @endIf
            </td>
        </tr>
        <tr>
            <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Address</b></td>
            <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                {{ $sub_order->order->delivery_address1 }}
            </td>
        </tr>
        <tr>
            <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Zone</b></td>
            <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}
            </td>
        </tr>
    </table>
    @else
        <h4>Pickup Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->delivery_name }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Number</b></td>
                <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->delivery_msisdn }}
                    @if($sub_order->order->delivery_alt_msisdn)
                        , {{ $sub_order->order->delivery_alt_msisdn }}
                    @endIf
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Address</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->delivery_address1 }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Zone</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}
                </td>
            </tr>
        </table>
        <br>
        <h4>Delivery Information</h4>
        <table style="width: 99%;">
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->store->merchant->name }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Number</b></td>
                <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->store->merchant->msisdn }}
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Name</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->pickup_location->title }}
                </td>
            </tr>
        <!-- <tr>
                  <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Number</b></td>
                  <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                      {{ $sub_order->product->pickup_location->msisdn }}
                </td>
              </tr> -->
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Address</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->pickup_location->address1 }}, {{ $sub_order->product->pickup_location->zone->name }}, {{ $sub_order->product->pickup_location->zone->city->name }}
                    <br>
                    <b>Contact: {{ $sub_order->product->pickup_location->msisdn or '' }}</b>
                </td>
            </tr>
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Order ID</b></td>
                <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->merchant_order_id }}
                </td>
            </tr>
        </table>
        @endif
    <br>

    <h4>Product and Pricing Information</h4>
    <table style="width: 99%;">
        <tr>
            <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Product Details</b></td>
            <td style="width: 20%; padding:5px; border:1px solid #000000;" ><b>Quantity</b></td>
            <td style="width: 20%; padding:5px; border:1px solid #000000;" ><b>Total Price(IQD)</b></td>
        </tr>
        @if($sub_order->order->as_package == 1)
{{--            @foreach($sub_order->order->cart_products AS $cart_product)--}}
{{--                <tr>--}}
{{--                    <td style="width: 40%; padding:5px; border:1px solid #000000;" >--}}
{{--                        {{ $cart_product->product_title }}<br>--}}
{{--                        Type: {{ $cart_product->product_category->name }}--}}
{{--                    </td>--}}
{{--                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >--}}
{{--                        {{ $cart_product->quantity }}--}}
{{--                    </td>--}}
{{--                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >--}}
{{--                        {{ number_format($cart_product->unit_price) }}--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->product_title }}<br>
                    Type: {{ $sub_order->product->product_category->name }}
                </td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->cart_products->sum('quantity') }}
                </td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                    @if($sub_order->post_delivery_return == 1)
                        {{ number_format($sub_order->product->sub_total) }}
                    @elseif($sub_order->return == 0)
                        {{ number_format($sub_order->product->payable_product_price) }}
                    @else
                        {{ $sub_order->parent_sub_order ? number_format($sub_order->parent_sub_order->product->payable_product_price) : number_format($sub_order->product->payable_product_price) }}
                    @endif
                </td>
            </tr>
        @else
{{--            <tr>--}}
{{--                <td style="width: 40%; padding:5px; border:1px solid #000000;" >--}}
{{--                    {{ $sub_order->product->product_title }}<br>--}}
{{--                    Type: {{ $sub_order->product->product_category->name }}--}}
{{--                </td>--}}
{{--                <td style="width: 20%; padding:5px; border:1px solid #000000;" >--}}
{{--                    {{ $sub_order->product->quantity }}--}}
{{--                </td>--}}
{{--                <td style="width: 20%; padding:5px; border:1px solid #000000;" >--}}
{{--                    {{ number_format($sub_order->product->unit_price) }}--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <td style="width: 40%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->product_title }}<br>
                    Type: {{ $sub_order->product->product_category->name }}
                </td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->product->quantity }}
                </td>
                <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                    @if($sub_order->post_delivery_return == 1)
                        {{ number_format($sub_order->product->sub_total) }}
                    @elseif($sub_order->return == 0)
                        {{ number_format($sub_order->product->payable_product_price) }}
                    @else
                        {{ $sub_order->parent_sub_order ? number_format($sub_order->parent_sub_order->product->payable_product_price) : number_format($sub_order->product->payable_product_price) }}
                    @endif
                </td>
            </tr>
        @endIf
    </table>

    <br>

    @if($sub_order->post_delivery_return == 0 )
        <table style="width: 99%;">
            <tr>
                @if($sub_order->order->delivery_pay_by_cus == 1 )
                    @if(in_array($sub_order->sub_order_status,[26,27,28,29,30,31,32,33,38,39]) && $sub_order->return == 0)
                        <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Delivery Charge(IQD)</b></td>
                    @endif
                @endif
                <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Total Collectable Amount(IQD)</b></td>
            </tr>
            <tr>
                @if($sub_order->order->delivery_pay_by_cus == 1 )
                    @if(in_array($sub_order->sub_order_status,[26,27,28,29,30,31,32,33,38,39]) && $sub_order->return == 0)
                        <td style="width: 50%; padding:5px; border:1px solid #000000;" >
                            @foreach($sub_order->products as $row2)

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
                    @if(in_array($sub_order->sub_order_status,[26,27,28,29,30,31,32,33,34,38,39]) && $sub_order->return == 0)
                        <table class="medium">
                            <tr>
                                <td>Payment Method</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td style="color: red">{{ $sub_order->order->paymentMethod->name or '' }}@if($sub_order->order->paymentMethod->id == 2)<img src="{{public_path()}}/fastpay.png" alt="fastpay" width="50" height="12">@endif</td></td>
                            </tr>
                        </table>
                        @if($sub_order->order->paymentMethod->id == 2)
                            0
                        @else
                            {{ number_format($sub_order->product->total_payable_amount) }}
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

    @if(isset($sub_order->order->order_remarks))

        <br>

        <table style="width: 99%;">
            <tr>
                <td style="padding:5px; border:1px solid #000000;" ><b>Remarks/Special Notes</b></td>
            </tr>
            <tr>
                <td style="padding:5px; border:1px solid #000000;" >
                    {{ $sub_order->order->order_remarks or '' }}
                </td>
            </tr>
        </table>

    @endIf

    <br>

    <table style="width: 99%;">
        <tr>
            <td style="width: 50%; padding:5px;" ><b>Merchant Signature</b></td>
            <td style="width: 50%; padding:5px; text-align: right;" ><b>Customer Signature</b></td>
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

</body>
</html>
