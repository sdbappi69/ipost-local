<!DOCTYPE html>
<html lang="en">
<head>
  <style type="text/css">
            @font-face {
                font-family: 'thesansarabic';
                src: url('{{public_path()}}/fonts/Bahij_TheSansArabic-Plain.ttf') format('truetype')
            }
    *{
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
          <img width="180px;" src="./assets/pages/img/login/login-invert.png">
        </td>
        <td>
          <h2 style="margin-top: 35px; font-size: 25px;">Trip Run Sheet</h2>
          <p>Trip ID : {{$trip->unique_trip_id}}</p>
        </td>
      </tr>
    </table>

    <table width="100%" cellspacing="10" style="margin-top: 20px;">
      <tr>
        <td>
          <p><b>DATE OF DISPATCH:</b> {{date('d/m/Y h:i A')}}</p> 
          <p><b>DELIVERY HUB:</b></p>
          <p><b>DRIVER NAME:</b> </p>
          <p><b>DRIVER PHONE:</b> </p>
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
    <br><br><br>
    <br><br><br>
    <br><br><br>
    <br><br><br>
    <table width="100%" style="margin-top: 20px;">
      <tr>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">#</td>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">ADDRESS</td>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">PACKAGE</td>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">QTY</td>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">CONTACT</td>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">UPDATE</td>
        <td style="border: 1px solid #000000; padding:2px; font-weight: bold;">COMMENTS</td>
      </tr>
      <tbody>
        <?php $i = 1 ;?>
        @foreach($trip->suborders as $suborder)

        <tr>
          <td style="border: 1px solid #000000; padding:2px;">{{$i++}}</td>
          <td style="border: 1px solid #000000; padding:2px;">{{ $suborder->sub_order->order->delivery_address1 }} <br> 
            Zone  : {{$suborder->sub_order->order->delivery_zone->name}} <br> 
            City  : {{$suborder->sub_order->order->delivery_zone->city->name}} <br> 
            State : {{$suborder->sub_order->order->delivery_zone->city->state->name}} <br> 
          </td>
          <td style="border: 1px solid #000000; padding:2px; text-align: center">
            <br/><img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{$suborder->unique_suborder_id}}" style="padding-top:5px">
            <br/>{{ $suborder->sub_order->unique_suborder_id }}</td>
          <td style="border: 1px solid #000000; padding:2px;">
             Title : {{ $suborder->sub_order->product->product_category->name }}</b><br>
             Qty : {{ $suborder->sub_order->order->cart_products->sum('quantity') }}</b>
          </td>
          <td style="border: 1px solid #000000; padding:2px;">
            Name : {{ $suborder->sub_order->order->delivery_name  }}<br>
            
            Mobile: {{ $suborder->sub_order->order->delivery_msisdn  }}, {{ $suborder->sub_order->order->delivery_alt_msisdn  }}
          </td>
            <td style="border: 1px solid #000000; padding:2px;"></td>
            <td style="border: 1px solid #000000; padding:2px;"></td>

          </tr>
          @endforeach
        </tbody>
      </table>

      <br><br><br><br><br><p>Signature</p>

    </div>

  </body>
  </html>