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
            outline: 0;
            vertical-align: baseline;
            background: transparent;
            font-family: 'thesansarabic';
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
                <p>Consignment ID : {{$consignment->consignment_unique_id}}</p>
            </td>
        </tr>
    </table>

    <table width="100%" cellspacing="10" style="margin-top: 20px;">
        <tr>
            <td>
                <p><b>DATE OF DISPATCH:</b> {{date('d/m/Y h:i A')}}</p>
                <p><b>DELIVERY HUB:</b></p>
                <p><b>DRIVER NAME:</b> {{$consignment->rider->name}}</p>
                <p><b>DRIVER PHONE:</b> {{$consignment->rider->msisdn}}</p>
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
    <br><br><br>
    <br><br><br>
    <br><br><br>
    <table width="100%" style="margin-top: 20px;">
        <tr>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">#</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold; width: 10%;">ADDRESS</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">PACKAGE</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">Product</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">Type</td>
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
        @foreach($consignment->task as $task)

            <tr>
                <td style="border: 1px solid #000000; padding:2px;">{{$i++}}</td>
                <td style="border: 1px solid #000000; padding:2px; width: 10%;">{{ $task->suborder->order->delivery_address1 or '' }}
                    <br>
                    Zone : {{ $task->suborder->order->delivery_zone->name or '' }} <br>
                    City : {{ $task->suborder->order->delivery_zone->city->name or '' }} <br>
                    State : {{ $task->suborder->order->delivery_zone->city->state->name or '' }} <br>
                </td>
                <td style="border: 1px solid #000000; padding:2px; text-align: center; width: 160px;"><?php
//                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($task->suborder->unique_suborder_id, "C128B", 1, 33) . '" alt="barcode"   /><br>';
                    ?>
                    <br/><br/><img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{$task->suborder->unique_suborder_id}}" style="padding-top:5px">
                    {{ $task->suborder->unique_suborder_id }}</td>
                <td style="border: 1px solid #000000; padding:2px;">
                    <?php $amount_to_collect = 0; ?>
                    <?php $amount_paid = 0; ?>
                    @if($task->task_type_id != 2)
                    @foreach($task->suborder->products as $product)
                        <b>
                            Merchant : {{ $task->suborder->order->store->merchant->name }}<br>
                            Order Id : {{ $task->suborder->order->merchant_order_id }}<br>
                        </b>
                        Product : {{ $product->product_title }}<br>
                        Qty : {{ $product->quantity }}<br>
                    @endforeach
                    @else
                    @foreach($task->suborder->products as $product)
                        <b>
                            Merchant : {{ $task->suborder->order->store->merchant->name }}<br>
                            Order Id : {{ $task->suborder->order->merchant_order_id }}<br>
                        </b>
                        Product : {{ $product->product_title }}<br>
                        Qty : {{ $product->quantity }}<br>

                        <?php
                            if($task->suborder->order->paymentMethod->id == 1){
                                $amount_to_collect += $product->total_payable_amount;
                                $amount_paid += $product->delivery_paid_amount;
                            }
                        ?>
                    @endforeach
                    @endif
                </td>
                <?php switch ($task->task_type_id) {
                    case 1:
                        $type = 'Pick';
                        break;
                    case 2:
                        $type = 'Delivery';
                        break;
                    case 3:
                        $type = 'Picked & Delivered';
                        break;
                    case 4:
                        $type = 'Return';
                        break;
                    case 5:
                        $type = 'Post Return Pick';
                        break;
                    case 6:
                        $type = 'Return to Buyer';
                        break;
                    default:
                        $type = '';
                        break;
                }?>
                <td style="border: 1px solid #000000; padding:2px;">{{ $type }}</td>
                <td style="border: 1px solid #000000; padding:2px;"></td>
                <td style="border: 1px solid #000000; padding:2px;">
                    Name : {{ $task->suborder->order->delivery_name  }}<br>

                    Mobile: {{ $task->suborder->order->delivery_msisdn  }}
                    , {{ $task->suborder->order->delivery_alt_msisdn  }}
                </td>
                <td style="border: 1px solid #000000; padding:2px;">{{ $task->suborder->order->paymentMethod->name or '' }}@if($task->suborder->order->paymentMethod->id == 2)<img src="{{public_path()}}/fastpay.png" alt="fastpay" width="50" height="12">@endif</td>
                <td style="border: 1px solid #000000; padding:2px;">{{number_format($amount_to_collect)}}</td>
                <td style="border: 1px solid #000000; padding:2px;">{{number_format($amount_paid)}}</td>
                {{-- <td style="border: 1px solid #000000; padding:2px;"></td>
                <td style="border: 1px solid #000000; padding:2px;"></td> --}}
                <td style="border: 1px solid #000000; padding:2px;"></td>

            </tr>
            <?php $total_amount_to_collect += $amount_to_collect;?>
            <?php $total_amount_paid += $amount_paid;?>
        @endforeach
        <tr>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;" colspan="8"> Total :</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">{{number_format($total_amount_to_collect)}}</td>
            <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">{{number_format($total_amount_paid)}}</td>
            <td style="border: 1px solid #000000; padding:2px;" colspan=""></td>
        </tr>
        </tbody>
    </table>

    <br><br><br><br>
    <p>Signature</p>

</div>

</body>
</html>