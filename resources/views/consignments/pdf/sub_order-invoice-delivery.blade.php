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
  <?php $i = 0; ?>

  
          <div style="padding: 10px; padding-top: 0; margin: 0 auto;">
            <div style='text-align: center;'>
              <img src="./assets/pages/img/login/login-invert.png" width="150px">
              <br>300/5/A Hatirpool, Dhaka
              <br>Website: www.biddyut.com
              <br>Contact: 09612433988
               <br>
      <?php
                                                        // echo DNS1D::getBarcodeHTML('4', "C128B",1.5,33);
      echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B") . '" alt="barcode"   /><br>';
      ?>
             
              {{ $sub_order->unique_suborder_id }}
            </div>

            <table  style='width : 100%;' >

              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Package Tracking ID</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->unique_suborder_id }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Order Date</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->created_at }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Merchant Name</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->store->merchant->name }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Merchant Phone No</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->store->merchant->msisdn }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Merchant Order Id</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->merchant_order_id }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Order Date</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->created_at }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Customer Name</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->delivery_name }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Customer Phone</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->delivery_msisdn }}</td>

              </tr>
              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Customer address</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->delivery_address1 }}</td>

              </tr>

              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>Zone</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->delivery_zone->name }}</td>

              </tr>

              <tr>
                <td style='padding:5px; border:1px solid #000000;'><b>City</b></td>
                <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->delivery_zone->city->name }}</td>

              </tr>

                                    <!-- <tr>
                                      <td style='padding:5px; border:1px solid #000000;'><b>Postcode</b></td>
                                      <td style='padding:5px; border:1px solid #000000;'>1217</td>
                                      
                                    </tr> -->
                                    <!-- <tr>
                                      <td style='padding:5px; border:1px solid #000000;'><b>Purchase order No :</b></td>
                                      <td style='padding:5px; border:1px solid #000000;'>20345678</td>
                                      
                                    </tr> -->
                                    <!-- <tr>
                                      <td style='padding:5px; border:1px solid #000000;'><b>Payment Type</b></td>
                                      <td style='padding:5px; border:1px solid #000000;'>Cash On Delivery</td>
                                      
                                    </tr> -->
                                    <tr>
                                      <td style='padding:5px; border:1px solid #000000;'><b>Shipping Charge</b></td>
                                      <?php 
                                      $delivery_charge = 0;
                                      $product_price = 0;
                                      $collectable_amount = 0;
                                      ?>
                                      @foreach($sub_order->products as $product)
                                      <?php
                                      $delivery = $product->unit_deivery_charge * $product->quantity;
                                      $delivery_charge = $delivery_charge + $delivery;
                                      $price = $product->unit_price * $product->quantity;
                                      $product_price = $product_price + $price;

                                      $collectable_amount = $collectable_amount + $product->payable_product_price;
                                      ?>
                                      @endforeach
                                      <td style='padding:5px; border:1px solid #000000;'>{{ $delivery_charge }}</td>
                                      
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

                                   @foreach($sub_order->products as $row)
                                   <tr>
                                     <td style='padding:5px; border:1px solid #000000;'>{{ $row->product_title }}</td>
                                     <td style='padding:5px; border:1px solid #000000;'>{{ $row->unit_price }}</td>
                                     <td style='padding:5px; border:1px solid #000000;'>{{ $row->quantity }}</td>
                                   </tr>
                                   @endforeach

                                 </table>

                                 <br>
                                 <table style="width : 100%;" >
                                  <tr>
                                   <th style='padding:5px; border:1px solid #000000;'>Product price</th>
                                   <th style='padding:5px; border:1px solid #000000;'>Shipping charge</th>
                                   <th style='padding:5px; border:1px solid #000000;'>Amount to be collected</th>
                                 </tr>
                                 <tr>
                                   <td style='padding:5px; border:1px solid #000000;'>{{ $product_price }}</td>
                                   <td style='padding:5px; border:1px solid #000000;'>{{ $delivery_charge }}</td>
                                   <td style='padding:5px; border:1px solid #000000;'>{{ $collectable_amount }}</td>

                                 </table>

                                 @if(isset($sub_order->order->order_remarks))
                                    <br>
                                   <table style="width : 100%;" >
                                      <tr>
                                       <th style='padding:5px; border:1px solid #000000;'>Remarks</th>
                                      </tr>

                                      <tr>
                                       <td style='padding:5px; border:1px solid #000000;'>{{ $sub_order->order->order_remarks or '' }}</td>
                                      </tr>
                                   </table>
                                 @endIf
                                 
                                      <br><br><br><br>
                                                   <p><b>Customer Signature</b></p>
                                                   <br><br><br>
                                                   <p><b>----------------------</b></p>
                                                   <!-- <div style="page-break-before: always;"></div> -->
                               </div>

                    <?php $i++; ?>
                                                                </body>
                     </html>