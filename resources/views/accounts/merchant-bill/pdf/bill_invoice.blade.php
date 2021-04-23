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
                <img src="{{url('assets/pages/img/login/login-invert.png')}}" width="150px">
                <p>300/5/A Hatirpool, Dhaka</p>
            </td>
            <td style="border: none;">
                <h2>Bill / Invoice</h2>
            </td>
            <td width="35%" style="border: none;">
                <table width="100%" border="1">
                    <tr>
                        <td style="width: 20%;" class="bold_and_center_align">Date:</td>
                        <td style="width: 20%; text-align: center;">{{date('Y-m-d',strtotime($merchant_bill->created_at))}}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;"   class="bold_and_center_align">Merchant Name:</td>
                        <td style="width: 20%; text-align: center;">{{$merchant_bill->merchant->name or 'N/A'}}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;"  class="bold_and_center_align">Invoice Amount:</td>
                        <td style="width: 20%; text-align: center;">
                            {{round(($merchant_bill->amount + $store_total_vat + $merchant_bill->additional_charge) - $merchant_bill->discount_amount)}} BDT.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>

    <?php $total_cod = 0; ?>
    <?php 
    $total_amount_collected = 0;
    $total_delivery_charge = 0;
    $total_cod_charge = 0;
    $total_amount = 0;
    $total_bill_amount = 0;
    $vat_percent = 0;
    $vat_amount = 0;
    $total_vat = 0;
    ?>

    <table style="height: auto; border: none;" width="100%" border="0">
        <tr>
            <td style="border: none;">
                <table style="border: none;" width="100%" border="1">
                    <tr>
                        <th scope="col">SL#</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Product</th>
                        <th scope="col">Merchant Order ID</th>
                        <th scope="col">Biddyut Tracking Number</th>
                        <th scope="col">Verified Weight (gm)</th>
                        <th scope="col">Delivery date/Return date</th>
                        <th scope="col">Delivery Status</th>
                        <th scope="col">Delivery City/Zone</th>
                        <th scope="col">Shipment Provider (HUB)</th>
                        <th scope="col">Cash On Delivery (COD)</th>
                        <th scope="col">Amount Collected</th>
                        <th scope="col">A. Delivery Charge</th>
                        <th scope="col">B. Return Charge</th>
                        <th scope="col">C. COD Charge (%)</th>
                        <th scope="col">Total Receivable (A+B+C)</th>
                    </tr>
                    @if(count($cashCollectionInfo) > 0)
                    <?php $i = 0; ?>
                    @foreach($cashCollectionInfo as $val)
                    <?php $i++; ?>
                    <tr style="text-align: center;">
                        <td>{{$i}}</td>
                        <td>{{$val->customer_name or 'N/A'}}</td>
                        <td>{{$val->product or 'N/A'}}</td>
                        <td>{{$val->merchant_order_id or 'N/A'}}</td>
                        <td>{{$val->tracking_no or 'N/A'}}</td>
                        <td>{{($val->quantity) * ($val->weight)}}</td>
                        <td>{{ ($val->delivery_date) ? date('Y-m-d',strtotime($val->delivery_date)) : 'N/A'}}</td>
                        <td>{{($val->return == '1') ? 'Returned':'Delivered'}}</td>
                        <td>{{$val->delivery_zone or 'N/A'}}</td>
                        <td>{{$val->delivery_hub or 'N/A'}}</td>
                        <td>{{$val->cod_amount or 'N/A'}}</td>
                        <td>{{$val->collected_amount or 'N/A'}}</td>
                        <td>{{$val->delivery_charge or 'N/A'}}</td>
                        <td>&nbsp;</td>
                        <td>{{$val->cod_charge or 'N/A'}}</td>
                        <td>{{($val->delivery_charge + $val->cod_charge)}}</td>
                    </tr>
                    @if(isset($val->delivery_charge))
                    <?php 
                    $total_amount = ($val->delivery_charge + $val->cod_charge);
                    $total_bill_amount += $total_amount;
                    ?>
                    @endIf
                    <?php
                    if(isset($val->vat_include) && $vat->vat_include == 1){
                        $vat_percent = $val->vat_percentage;

                        $vat_amount = (($total_amount * $vat_percent) / 100);

                        $total_vat += $vat_amount;
                    }
                    ?>

                    @endforeach
                    @endif
                </table>
            </td>
        </tr>
        <tr>
            <td style="border: none;">
                <table style="border: none;" width="100%" border="0">
                    <tr>
                        <td style="text-align: right;">Total Bill Amount</td>
                        <td>{{ round($total_bill_amount) }}</td>
                    </tr>

                    <tr>
                        <td style="text-align: right;">Add: VAT on service {{ round($vat_percent) }}% (if applicable)</td>
                        <td>{{round($total_vat)}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><b>Gross Bill Amount</b></td>
                        <td><b>{{ round($total_bill_amount + $total_vat) }}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><b>Add: Additional Charge for {{$merchant_bill->additional_charge_remarks}}</b></td>
                        <td><b>{{$merchant_bill->additional_charge}}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><b>Less: Discount/Adjustment for {{$merchant_bill->discount_remarks}}</b></td>
                        <td><b>{{$merchant_bill->discount_amount}}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><b>Net Bill Amount</b></td>
                        <td><b>{{ round(($total_bill_amount + $total_vat + $merchant_bill->additional_charge) - $merchant_bill->discount_amount) }}</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="120" style="border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td style="border: none;">
                <table width="100%" style="border: none;">
                    <tr>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Prepared By</td>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Authorised By</td>
                        <td height="100" style="border-top: 1px solid #000; text-align: center; vertical-align: bottom;">Bill Received By</td>
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
