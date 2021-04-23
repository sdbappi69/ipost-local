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
                <img src="./assets/pages/img/login/login-invert.png" style="width:80px">

                <br>
                <?php
                // echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B") . '" alt="barcode"   /><br>';
                ?>
                <img src="http://chart.googleapis.com/chart?chs=120x120&cht=qr&chl={{$sub_order->unique_suborder_id}}">
                <p class="large">{{ $sub_order->unique_suborder_id }}</p>
            </div>

            <br>

            <h4>Merchant Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Pickup request Date</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        <?php $pickiup_created_at = orderApproveLog($sub_order->order->id); ?>
                        {{ $pickiup_created_at }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->order->store->merchant->name }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Merchant Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->order->store->merchant->msisdn }}
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

            <h4>Product and Pricing Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Product Details</b></td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" ><b>Quantity</b></td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" ><b>Unit Price</b></td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" ><b>Total Price</b></td>
                </tr>
                @if($sub_order->order->as_package == 1)
                @foreach($sub_order->order->cart_products AS $cart_product)
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" >
                        {{ $cart_product->product_title }}<br>
                        Type: {{ $cart_product->product_category->name }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $cart_product->quantity }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $cart_product->unit_price }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $cart_product->sub_total }}
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->product_title }}<br>
                        Type: {{ $sub_order->product->product_category->name }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->quantity }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->unit_price }}
                    </td>
                    <td style="width: 20%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->sub_total }}
                    </td>
                </tr>
                @endIf
            </table>

            <br>
            <!--picking status-->
            @if(in_array($sub_order->sub_order_status,[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17]))
            @if($sub_order->post_delivery_return == 0)
            <h4>Pickup Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >

                        {{ $sub_order->product->pickup_location->title or '' }}

                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >

                        {{ $sub_order->product->pickup_location->msisdn or '' }}

                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >

                        {{ $sub_order->product->pickup_location->address1 or '' }}

                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >

                        {{ $sub_order->product->pickup_location->zone->name or '' }}, {{ $sub_order->product->pickup_location->zone->city->name or '' }}

                    </td>
                </tr>
            </table>

            <br>
            <h4>Delivery Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->address1 }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->zone->name }}, {{ $sub_order->source_hub->zone->city->name }}
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
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Phone Number</b></td>
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
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Delivery Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}
                    </td>
                </tr>
            </table>

            <br>
            <h4>Delivery Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->address1 }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->zone->name }}, {{ $sub_order->destination_hub->zone->city->name }}
                    </td>
                </tr>
            </table>
            @endif
            <!--trip status-->
            @elseif(in_array($sub_order->sub_order_status,[18,19,20,21,22,23,24,25]))
            <h4>Pickup Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Hub</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->current_hub->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Hub Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->current_hub->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Hub Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->current_hub->address1 }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Hub Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->current_hub->zone->name }}, {{ $sub_order->current_hub->zone->city->name }}
                    </td>
                </tr>
            </table>

            <br>
            <h4>Delivery Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Delivery Hub</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->next_hub->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Delivery Hub Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->next_hub->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Delivery Hub Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->next_hub->address1 }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Delivery Hub Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->next_hub->zone->name }}, {{ $sub_order->next_hub->zone->city->name }}
                    </td>
                </tr>
            </table>
            <!--delivery status-->
            @elseif(in_array($sub_order->sub_order_status,[26,27,28,29,30,31,32,33,38,39]))

            <h4>Pickup Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->address1 }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->destination_hub->zone->name }}, {{ $sub_order->destination_hub->zone->city->name }}
                    </td>
                </tr>
            </table>

            <br>
            <h4>Delivery Information</h4>
            @if($sub_order->return == 0)
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->order->delivery_name }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Customer Phone Number</b></td>
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
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Delivery Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}
                    </td>
                </tr>
            </table>
            @else
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->address1 or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->zone->name or '' }}, {{ $sub_order->product->pickup_location->zone->city->name or '' }}
                    </td>
                </tr>
            </table>
            @endif
            <!--return status-->
            @elseif(in_array($sub_order->sub_order_status,[35,36,37]))

            <h4>Pickup Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->address1 }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Hub Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->source_hub->zone->name }}, {{ $sub_order->source_hub->zone->city->name }}
                    </td>
                </tr>
            </table>

            <br>
            <h4>Delivery Information</h4>
            <table style="width: 99%;">
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Store Name</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->title or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Phone Number</b></td>
                    <td class="large" style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->msisdn or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Address</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->address1 or '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%; padding:5px; border:1px solid #000000;" ><b>Picking Zone</b></td>
                    <td style="width: 60%; padding:5px; border:1px solid #000000;" >
                        {{ $sub_order->product->pickup_location->zone->name or '' }}, {{ $sub_order->product->pickup_location->zone->city->name or '' }}
                    </td>
                </tr>
            </table>
            @endif
            <br>
            @if($sub_order->post_delivery_return == 0 )
            <table style="width: 99%;">
                <tr>
                    @if($sub_order->order->delivery_pay_by_cus == 1 )
                    @if(in_array($sub_order->sub_order_status,[26,27,28,29,30,31,32,33,38,39]) && $sub_order->return == 0)
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Delivery Charge</b></td>
                    @endif
                    @endif
                    <td style="width: 50%; padding:5px; border:1px solid #000000;" ><b>Total Collectable Amount</b></td>
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
                        @if(in_array($sub_order->sub_order_status,[26,27,28,29,30,31,32,33,38,39]) && $sub_order->return == 0)
                        <table class="medium">
                            <tr>
                                <td>Payment Method</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td>{{ $sub_order->order->paymentMethod->name or '' }}</td>
                            </tr>
                        </table>
                        {{ $sub_order->product->total_payable_amount }}
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