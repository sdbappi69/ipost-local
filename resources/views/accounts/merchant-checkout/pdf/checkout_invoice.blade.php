<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
    <style type="text/css">
    .bold_and_center_align {	font-weight: bold;
        text-align: center;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    table, th{
        padding: 5px;
    }
</style>

</head>

<body>
    <table width="100%" style="border: none;">
        <tr>
            <td style="border: none;">
                <img src="./assets/pages/img/login/login-invert.png" width="150px">
                <p>300/5/A Hatirpool, Dhaka</p>
            </td>
            <td style="border: none;">
                <h2>Payment Invoice</h2>
            </td>
            <td width="35%" style="border: none;">
                <table width="100%" border="1">
                    <tr>
                        <td style="width: 20%;" class="bold_and_center_align">Payment Date:</td>
                        <td style="width: 20%; text-align: center;">{{date('Y-m-d',strtotime($merchant_checkout->created_at))}}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;"   class="bold_and_center_align">Client Name:</td>
                        <td style="width: 20%; text-align: center;">{{$merchant_checkout->merchant->name or 'N/A'}}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;"  class="bold_and_center_align">Total Payment:</td>
                        <td style="width: 20%; text-align: center;">{{$merchant_checkout->total_amount}} BDT.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>

    <?php $total_cod = 0; ?>
    <?php $total_amount_collected = 0; ?>
    <?php $total_delivery_charge = 0; ?>
    <?php $total_cod_charge = 0; ?>
    <?php $total_vat = 0; ?>
    <?php $vat_percent = 0; ?>

    <table style=" width : 100%; height: auto;" border="0">
        <tr>
            <td style="border: none;">
                <table width="100%" border="1">
                <tr>
                    <th scope="col">SL#</th>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Product</th>
                    <th scope="col">Merchant Order ID</th>
                    <th scope="col">Biddyut Tracking Number</th>
                    <th scope="col">Verified weight (gm)</th>
                    <th scope="col">Delivery/Return date</th>
                    <th scope="col">Delivery Status</th>
                    <th scope="col">Delivery City/Zone</th>
                    <th scope="col">Shipment Provider (HUB)</th>
                    <th scope="col">Cash On Delivery (COD)</th>

                    <th scope="col">Amount Collected</th>
                    <th scope="col">Delivery Charge</th>
                    <th scope="col">Return Charge</th>
                    <th scope="col">COD Charge (%)</th>
                    <th scope="col">Net Payable/ (Receivable) to Seller</th>
                </tr>
                @if(count($cashCollectionData) > 0)
                <?php $i = 0; ?>
                @foreach($cashCollectionData as $val)
                <?php $i++; ?>
                <tr style="text-align: center;">
                    <td>{{$i}}</td>
                    <td>{{$val->order->delivery_name or 'N/A'}}</td>
                    <td>{{$val->productDetails->product_title or 'N/A'}}</td>
                    <td>{{$val->order->merchant_order_id or 'N/A'}}</td>
                    <td>{{$val->sub_order->unique_suborder_id or 'N/A'}}</td>
                    <td>{{($val->productDetails->quantity) * ($val->productDetails->weight)}}</td>
                    <td>{{ (count($val->sub_order->dTask)) ? date('Y-m-d', strtotime($val->sub_order->dTask->created_at)) : 'N/A'}}</td>
                    <td>{{($val->sub_order->return == 1) ? 'Returned':'Delivered'}}</td>
                    <td>{{$val->order->delivery_zone->name or 'N/A'}}</td>
                    <td>{{$val->sub_order->destination_hub->title or 'N/A'}}</td>
                    <td>{{$val->cod_amount or 'N/A'}}</td>
                    <td>{{$val->collected_amount or 'N/A'}}</td>
                    <td>{{$val->bill_amount or 'N/A'}}</td>
                    <td></td>
                    <td>{{$val->cod_charge or 'N/A'}}</td>
                    <td>{{round($val->collected_amount - $val->bill_amount - $val->cod_charge)}}</td>
                </tr>

                @if(isset($val->store->vat_include) && $vat->store->vat_include == 1){
                <?php $vat_percent = $val->vat_percentage; ?>
                @endIf

                @if(isset($val->cod_amount))
                <?php $total_cod += $val->cod_amount; ?>
                @endIf

                @if(isset($val->collected_amount))
                <?php $total_amount_collected += $val->collected_amount; ?>
                @endIf

                @if(isset($val->bill_amount))
                <?php $total_delivery_charge += $val->bill_amount; ?>
                @endIf

                @if(isset($val->cod_charge))
                <?php $total_cod_charge += $val->cod_charge; ?>
                @endIf

                @if(isset($val->vat_amount))
                <?php $total_vat += $val->vat_amount; ?>
                @endIf

                @endforeach
                @endif
            </table>
            </td>
        </tr>
        <tr>
            <?php $bill_amount = $total_delivery_charge + $total_cod_charge + $total_vat; ?>
            <?php $payable = $total_amount_collected - $bill_amount; ?>
            <td style="border: none;">
                <table width="100%" border="0">
                <tr>
                    <td width="208" style="text-align: right;"><b>Total Payalbe</b></td>
                    <td width="179"><b>{{ $total_amount_collected }}</b></td>
                </tr>
                <tr>
                    <td style="text-align: right;">Delivery Charge</td>
                    <td>{{ $total_delivery_charge }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">COD Charge</td>
                    <td>{{ $total_cod_charge }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Add: VAT on service {{$vat_percent}}% (if applicable)</td>
                    <td>{{ $total_vat }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><b>Gross Payment</b></td>
                    <td><b>{{ $bill_amount }}</b></td>
                </tr>
                <tr>
                    <td style="text-align: right;">Add: Discount/Adjustment for {{ $merchant_checkout->discount_remarks }}</td>
                    <td>{{ $merchant_checkout->discount_amount }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><b>Net Payment</b></td>
                    <td><b>{{ $payable }}</b></td>
                </tr>
            </table>
            </td>
        </tr>
        <tr>
            <td style="border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td style="border: none;">
                <table width="100%" style="border: none;">
                    <tr>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Prepared By</td>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Authorised By</td>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Paid By</td>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Received By</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: right; border: none;">
                <hr/>
                <h2>Please sign with Date</h2>
            </td>
        </tr>
    </table>
</body>
</html>
