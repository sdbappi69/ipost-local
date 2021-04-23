<!DOCTYPE html>
<html lang="en">
<head>
<style type="text/css">
  * {
        margin: 0;
        padding: 0;
        border: 0;
        outline: 0;
        vertical-align: baseline;
        background: transparent;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    }
    table{
      border-collapse:collapse;
    }
</style>
</head>
<body style="font-size: 10px;">
  @if(count($products) > 0)
  <?php $i = 0; ?>
  @foreach($products as $product)

  <div style="padding: 10px; padding-top: 0; margin: 0 auto;">
    <div style='text-align: center;'>
      <img style="width:150px" src="./assets/pages/img/login/login-invert.png">
      <br>300/5/A Hatirpool, Dhaka
      <br>Website: www.biddyut.com
      <br>Contact: 09612433988
      <br>
      <?php
                                                        // echo DNS1D::getBarcodeHTML('4', "C128B",1.5,33);
      echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->unique_suborder_id, "C128B") . '" alt="barcode"   /><br>';
      ?>
      {{ $product->unique_suborder_id }}
    </div>
    <br>
    <p><b>@if($product->return == 0) Pickup Invoice printed on: @else Return Invoice printed on: @endIf {{ date('Y-m-d') }}</b></p>
    <br>

    @if($product->return == 0)

      <table style='width : 100%;' >

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Pickup req. Date</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->picking_date }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Package Tracking ID</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->product_unique_id }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Merchant Name</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->merchant_name }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Merchant Phone No</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->merchant_msisdn }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Store Name</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->store_name }}</td>

        </tr>
        <!-- <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Store Phone No</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->store_msisdn }}</td>
        </tr> -->
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Pickup location</b></td>
          <td style='padding:5px; border:1px solid #000000;'>
            {{ $product->title }}<br>
            {{ $product->msisdn }}<br>
            {{ $product->address1 }}, {{ $product->zone_name }}
          </td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>City</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->city_name }}</td>

        </tr>
        <!-- <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Postcode</b></td>
          <td style='padding:5px; border:1px solid #000000;'>1205</td>
          
        </tr> -->
        <!-- <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Name</b></td>
          <td style='padding:5px; border:1px solid #000000;'>Asima Begum</td>                          
        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Address</b></td>
          <td style='padding:5px; border:1px solid #000000;'>600/2, Moghbazar</td>
          
        </tr> -->
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Order Number(Mer)</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->merchant_order_id }}</td>
        </tr>

      </table>

      <br>
      <table style="width : 100%;" >

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Name</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->cus_name }}</td>
        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Email</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->cus_email }}</td>
        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Phone No</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->cus_msisdn }}, {{ $product->cus_alt_msisdn }}</td>
        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer address</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->delivery_address1 }}</td>

        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Zone</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->delivery_zone }}</td>

        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>City</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->delivery_city }}</td>

        </tr>

      </table>

      <br>
      <table style="width : 100%;" >
        <tr>
         <th style='padding:5px' >Product details</th>
         <th style='padding:5px' >Product SKUs</th>
         <th style='padding:5px' >Quantity</th>
       </tr>
       <tr>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->product_title }}</td>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->product_category }}</td>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->quantity }}</td>

       </table>

       <br>
       <table style="width : 100%;" >
        <tr>
         <th style='padding:5px' >Product Details</th>
         <th style='padding:5px' >Total of Packages</th>
         <th style='padding:5px' >Price</th>
       </tr>
       <tr>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->product_title }}</td>
         <td style='padding:5px; border:1px solid #000000;'></td>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->sub_total }}</td>

       </table>

       @if(isset($product->order_remarks))
        <br>
         <table style="width : 100%;" >
            <tr>
             <th style='padding:5px; border:1px solid #000000;'>Remarks</th>
            </tr>

            <tr>
             <td style='padding:5px; border:1px solid #000000;'>{{ $product->order_remarks or '' }}</td>
            </tr>
         </table>
       @endIf

        <br><br><br><br>
       <p><b>Merchant Signature</b></p>
       <br><br><br>
       <p><b>----------------------</b></p>
       <!-- <div style="page-break-before: always;"></div> -->
     </div>

    @else

      <table  style='width : 100%;' >

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Package Tracking ID</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->unique_suborder_id }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Picking Date</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->picking_date }}</td>
        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Return Date</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ date("Y-m-d", strtotime($product->created_at)) }}</td>
        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Merchant Name</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->merchant_name }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Merchant Phone No</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->merchant_msisdn }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Merchant Order Id</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->merchant_order_id }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Order Date</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->order_created_at }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Name</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->cus_name }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer Phone</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->cus_msisdn }}, {{ $product->cus_alt_msisdn }}</td>

        </tr>
        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Customer address</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->delivery_address1 }}</td>

        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Zone</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->delivery_zone }}</td>

        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>City</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product->delivery_city }}</td>

        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Shipping Charge</b></td>
          <?php 
          $delivery_charge = 0;
          $product_price = 0;
          $collectable_amount = 0;
          ?>
          <?php
            $delivery = $product->unit_deivery_charge * $product->quantity;
            $delivery_charge = $delivery_charge + $delivery;
            $price = $product->unit_price * $product->quantity;
            $product_price = $product_price + $price;

            $collectable_amount = $collectable_amount + $product->total_payable_amount;
          ?>
          <td style='padding:5px; border:1px solid #000000;'>{{ $delivery_charge }}</td>
          
        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Return Reason</b></td>

          <?php
            $sub_order_note = json_decode($product->sub_order_note,true);
          ?>
          <td style='padding:5px; border:1px solid #000000;'>
            @if (array_key_exists('return_reason', $sub_order_note) && $sub_order_note['return_reason'] != 'null')
                {{ $sub_order_note['return_reason'] }}
            @endIf
          </td>
          
        </tr>

        <tr>
          <td style='padding:5px; border:1px solid #000000;'><b>Product Price</b></td>
          <td style='padding:5px; border:1px solid #000000;'>{{ $product_price }}</td>
          
        </tr>
        
      </table>
      <br>
      <table style="width : 100%;" >
        <tr>
         <th style='padding:5px; border:1px solid #000000;'>Item Details</th>
         <th style='padding:5px; border:1px solid #000000;'>Price</th>
         <th style='padding:5px; border:1px solid #000000;'>Qty</th>
       </tr>

       <tr>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->product_title }}</td>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->unit_price }}</td>
         <td style='padding:5px; border:1px solid #000000;'>{{ $product->quantity }}</td>
       </tr>

     </table>

     <br>
     {{-- <table style="width : 100%;" >
      <tr>
       <th style='padding:5px; border:1px solid #000000;'>Product price</th>
       <th style='padding:5px; border:1px solid #000000;'>Shipping charge</th>
       <th style='padding:5px; border:1px solid #000000;'>Amount to be collected</th>
     </tr>
     <tr>
       <td style='padding:5px; border:1px solid #000000;'>{{ $product_price }}</td>
       <td style='padding:5px; border:1px solid #000000;'>{{ $delivery_charge }}</td>
       <td style='padding:5px; border:1px solid #000000;'>{{ $collectable_amount }}</td>

     </table> --}}

     @if(isset($product->order_remarks))
      <br>
       <table style="width : 100%;" >
          <tr>
           <th style='padding:5px; border:1px solid #000000;'>Remarks</th>
          </tr>

          <tr>
           <td style='padding:5px; border:1px solid #000000;'>{{ $product->order_remarks or '' }}</td>
          </tr>
       </table>
     @endIf

          <br><br><br><br>
                       <p><b>Merchant Signature</b></p>
                       <br><br><br>
                       <p><b>----------------------</b></p>
                       <!-- <div style="page-break-before: always;"></div> -->
   </div>

    @endIf

   <?php $i++; ?>
   @if(sizeof($products) > 1 && $i != (sizeof($products)) )
      <div style="page-break-before: always;"></div>
   @endif
   @endforeach
   @endif
 </body>
 </html>