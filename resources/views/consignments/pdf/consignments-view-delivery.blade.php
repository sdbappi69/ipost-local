<!DOCTYPE html>
<html lang="en">
<head>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            outline: 0;
            vertical-align: baseline;
            background: transparent;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 12px;
            /*float: left;*/
            /*display: inline;*/
        }

        table {
            border-collapse: collapse;
        }

        td {
            padding: 5px;
        }
    </style>
</head>
<body>

<div style="padding:5px; margin: 50px;">

    <table width="100%">
        <tr>
            <td>
                <img width="180px;" src="./assets/pages/img/login/login-invert.png">
            </td>
            <td>
                <h2 style="margin-top: 35px; font-size: 25px;">Delivery Run Sheet</h2>
                <p>Consignment ID : {{$picking->consignment_unique_id}}</p>
            </td>
        </tr>
    </table>

    <table width="100%" cellspacing="10" style="margin-top: 20px;">
        <tr>
            <td>
                <p><b>DATE OF DISPATCH:</b> {{date('d/m/Y h:i A')}}</p>
                <p><b>DELIVERY HUB:</b></p>
                <p><b>DRIVER NAME:</b> {{$picking->rider->name}}</p>
                <p><b>DRIVER PHONE:</b> {{$picking->rider->msisdn}}</p>
                <p><b>COMMENT:</b></p>
            </td>
            <td>
                <table width="100%">
                    <tr>
                        <td style="border: 1px solid #CCCCCC; padding:5px;">NAME & SIGNATURE</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" style="line-height: 20px;">
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">DISPATCHER</td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">SECURITY</td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">DRIVER</td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">FINANCE</td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table width="100%">
                    <tr>
                        <td style="border: 1px solid #CCCCCC; padding:5px;">NAME & SIGNATURE</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" style="line-height: 20px;">
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">AMOUNT REMITTED
                                        (DRIVER)
                                    </td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">AMOUNT RECEIVED
                                        (FINANCE)
                                    </td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">DIFFERENCE</td>
                                    <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellspacing="10" style="margin-top: 20px;">
        <tr>
            <td style="border: 1px solid #000000; padding:5px; width: 20px;"></td>
            <td>Point of Sales Working</td>
            <td style="border: 1px solid #000000; padding:5px; width: 20px;"></td>
            <td>Vehicle Working</td>
            <td style="border: 1px solid #000000; padding:5px; width: 20px;"></td>
            <td>Phone Charged and Working</td>
            <td style="border: 1px solid #000000; padding:5px; width: 20px;"></td>
            <td>Uniforms in good condition</td>
            <td style="border: 1px solid #000000; padding:5px; width: 20px;"></td>
            <td>Fuel check</td>
        </tr>

    </table>
    <br>
    <table width="100%" style="margin-top: 20px;">
        <tr>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">#</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold; width: 10%;">ADDRESS</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">PACKAGE</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">Product</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">ACT QTY</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">CONTACT</td>

            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">PAYMENT METHOD</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">AMOUNT EXPECTED</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">AMOUNT RECEIVED</td>
            {{-- <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">AMOUNT FINANCE</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">UPDATE</td> --}}
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">COMMENTS</td>
        </tr>
        <tbody>
        <?php $i = 1;?>
        <?php $total_amount_to_collect = 0;?>
        <?php $total_amount_paid = 0;?>
        @foreach($sub_orders as $sub_order)

            <tr>
                <td style="border: 1px solid #000000; padding:2px;">{{$i++}}</td>
                <td style="border: 1px solid #000000; padding:2px; width: 10%;">{{ $sub_order->order->delivery_address1 }}
                    <br>
                    Zone : {{$sub_order->order->delivery_zone->name}} <br>
                    City : {{$sub_order->order->delivery_zone->city->name}} <br>
                    State : {{$sub_order->order->delivery_zone->city->state->name}} <br>
                </td>
                <td style="border: 1px solid #000000; padding:2px; text-align: center; width: 160px;"><?php
                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B", 1, 33) . '" alt="barcode"   /><br>';
                    ?>
                    {{ $sub_order->unique_suborder_id }}</td>
                <td style="border: 1px solid #000000; padding:2px;">
                    <?php $amount_to_collect = 0; ?>
                    <?php $amount_paid = 0; ?>
                    @foreach($sub_order->products as $product)
                        <b>
                            Merchant : {{ $sub_order->order->store->merchant->name }}<br>
                            Order Id : {{ $sub_order->order->merchant_order_id }}<br>
                        </b>
                        Product : {{ $product->product_title }}<br>
                        Qty : {{ $product->quantity }}<br>

                        <?php $amount_to_collect += $product->total_payable_amount; ?>
                        <?php $amount_paid += $product->delivery_paid_amount; ?>
                    @endforeach
                    {{$sub_order->quantity}}
                </td>
                <td style="border: 1px solid #000000; padding:2px;"></td>
                <td style="border: 1px solid #000000; padding:2px;">
                    Name : {{ $sub_order->order->delivery_name  }}<br>

                    Mobile: {{ $sub_order->order->delivery_msisdn  }}, {{ $sub_order->order->delivery_alt_msisdn  }}
                </td>

                <td style="border: 1px solid #000000; padding:2px;"></td>
                <td style="border: 1px solid #000000; padding:2px;">{{ number_format($amount_to_collect) }}</td>
                <td style="border: 1px solid #000000; padding:2px;">{{ number_format($amount_paid) }}</td>
                {{-- <td style="border: 1px solid #000000; padding:2px;"></td>
                <td style="border: 1px solid #000000; padding:2px;"></td> --}}
                <td style="border: 1px solid #000000; padding:2px;"></td>

            </tr>
            <?php $total_amount_to_collect += $amount_to_collect;?>
            <?php $total_amount_paid += $amount_paid;?>
        @endforeach
        <tr>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;" colspan="7"> Total :</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">{{ number_format($total_amount_to_collect) }}</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">{{ number_format($total_amount_paid) }}</td>
            <td style="border: 1px solid #000000; padding:2px;" colspan="4"></td>
        </tr>
        </tbody>
    </table>

    <br><br><br><br><br>
    <p>Signature</p>

</div>

</body>
</html>
