<!DOCTYPE html>
<html lang="en">
    <head>
        <style type="text/css">
            *{
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
            table{
                border-collapse:collapse;
            }
            td{
                padding: 5px;
            }
        </style>
    </head>
    <body>

        <div style="padding:5px; margin: 50px;">

            <table width="100%">
                <tr>
                    <td>
                      <!-- <img width="180px;" src="{{secure_asset('assets/pages/img/login/login-invert.jpg')}}"> -->
                        <img width="180px;" src="./assets/pages/img/login/login-invert.png">
                    </td>
                    <td>
                        <h2 style="margin-top: 35px; font-size: 25px;">Pickup Run Sheet</h2>
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
                                            <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">AMOUNT REMITTED (DRIVER)</td>
                                            <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #CCCCCC; padding:5px; width: 50%;">AMOUNT RECEIVED (FINANCE)</td>
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
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">ADDRESS</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">PACKAGE</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">QTY</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">ACT QTY</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">CONTACT</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">TYPE</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">PRODUCTS</td>
                    <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">COMMENTS</td>
                </tr>
                <tbody>
                    <?php $i = 1; ?>

                    @foreach($temp_arrays_return as $temp_array)
                    <tr>
                        <td style="border: 1px solid #000000; padding:2px;">{{$i++}}</td>
                        <td style="border: 1px solid #000000; padding:2px;">{{ $temp_array->return_product->address1 }} <br> 
                            Zone  : {{$temp_array->return_product->product->pickup_location->zone->name}} <br> 
                            City  : {{$temp_array->return_product->product->pickup_location->zone->city->name}} <br> 
                            State : {{$temp_array->return_product->product->pickup_location->zone->city->state->name}} <br> 
                        </td>
                        <td style="border: 1px solid #000000; padding:2px; text-align: center"><?php
                            echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($temp_array->return_product->product->sub_order->unique_suborder_id, "C128B", 1, 33) . '" alt="barcode"   /><br>';
                            ?>
                            {{ $temp_array->return_product->product->sub_order->unique_suborder_id }}</td>
                        <td style="border: 1px solid #000000; padding:2px;">{{$temp_array->return_product->quantity}}</td>
                        <td style="border: 1px solid #000000; padding:2px;"></td>
                        <td style="border: 1px solid #000000; padding:2px;">
                            Warehouse: <strong>{{ $temp_array->return_product->product->pickup_location->title }}</strong>
                            <br>
                            Phone: {{ $temp_array->return_product->product->pickup_location->msisdn }}, {{ $temp_array->return_product->product->pickup_location->alt_msisdn }}
                            <br>
                            Address: {{ $temp_array->return_product->product->pickup_location->address1 }}, {{ $temp_array->return_product->product->pickup_location->zone->name }}, {{ $temp_array->return_product->product->pickup_location->zone->city->name }}, {{ $temp_array->return_product->product->pickup_location->zone->city->state->name }}
                        </td>
                        <td style="border: 1px solid #000000; padding:2px;">Return</td>

<!--            <td style="border: 1px solid #000000; padding:2px;"></td>-->
                        <td style="border: 1px solid #000000; padding:2px;">{{$temp_array->product_title}}</td>
                        <td style="border: 1px solid #000000; padding:2px;"></td>

                    </tr>
                    @endforeach

                    @foreach($temp_arrays as $temp_array)
                    <tr>
                        <td style="border: 1px solid #000000; padding:2px;">{{$i++}}</td>
                        <td style="border: 1px solid #000000; padding:2px;">{{ $temp_array->address1 }} <br> 
                            Zone  : {{$temp_array->zone_name}} <br> 
                            City  : {{$temp_array->city_name}} <br> 
                            State : {{$temp_array->state_name}} <br> 
                        </td>
                        <td style="border: 1px solid #000000; padding:2px; text-align: center"><?php
                            echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($temp_array->unique_suborder_id, "C128B", 1, 33) . '" alt="barcode"   /><br>';
                            ?>
                            {{ $temp_array->unique_suborder_id }}</td>
                        <td style="border: 1px solid #000000; padding:2px;">{{$temp_array->quantity}}</td>
                        <td style="border: 1px solid #000000; padding:2px;"></td>
                        <td style="border: 1px solid #000000; padding:2px;">
                            Warehouse: <strong>{{ $temp_array->title }}</strong>
                            <br>
                            Phone: {{ $temp_array->msisdn }}, {{ $temp_array->alt_msisdn }}
                            <br>
                            Address: {{ $temp_array->address1 }}, {{ $temp_array->zone_name }}, {{ $temp_array->city_name }}, {{ $temp_array->state_name }}
                        </td>

                        <td style="border: 1px solid #000000; padding:2px;">Picking</td>

<!--            <td style="border: 1px solid #000000; padding:2px;"></td>-->
                        <td style="border: 1px solid #000000; padding:2px;">{{$temp_array->product_title}}</td>
                        <td style="border: 1px solid #000000; padding:2px;"></td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            <br><br><br><br><br><p>Signature</p>

        </div>

    </body>
</html>