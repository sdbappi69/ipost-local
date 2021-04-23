@extends('layouts.appinside')

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL::to('hub-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Assign Pickup</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Orders
        <small> assign pickup</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        
        @if(count($products) > 0)

            @foreach($products as $product)

                <div class="col-md-4 small">

                    <div class="mt-element-ribbon bg-grey-steel">

                        <a href="javascript:void(0)" class="ribbon ribbon-right ribbon-vertical-right ribbon-shadow ribbon-border-dash-vert ribbon-color-default uppercase print_modal" product_id = "{{ $product->product_unique_id }}">
                            <div class="ribbon-sub ribbon-bookmark"></div>
                            <i class="fa fa-print"></i>
                        </a>

                        <div class="mt-element-ribbon bg-grey-steel">
                            <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">
                                {{ $product->product_unique_id }}
                            </div>
                            <div class="ribbon-content">

                                <div class="print_body" id="view_{{ $product->product_unique_id }}">                                    

                                    <h4 class="uppercase">Picking Time</h4>
                                    {{ $product->picking_date }} ({{ $product->start_time }}-{{ $product->end_time }})

                                    <h4 class="uppercase">Customer</h4>
                                    Name: {{ $product->cus_name }}
                                    <br>
                                    Email: {{ $product->cus_email }}
                                    <br>
                                    Phone: {{ $product->cus_msisdn }}, {{ $product->cus_alt_msisdn }}

                                    <h4 class="uppercase">Picking Address</h4>
                                    Warehouse: <strong>{{ $product->title }}</strong>
                                    <br>
                                    Phone: {{ $product->msisdn }}, {{ $product->alt_msisdn }}
                                    <br>
                                    Address: {{ $product->address1 }}, {{ $product->zone_name }}, {{ $product->city_name }}, {{ $product->state_name }}
                                    
                                    <h4 class="uppercase">Products</h4>
                                    Title: <strong>{{ $product->product_title }}</strong>
                                    <br>
                                    Category: {{ $product->product_category }}
                                    <br>
                                    Quantity: {{ $product->quantity }}
                                    <br>
                                    <br>
                                </div>

                                <div class="row">
                                    <label class="control-label">Select Picker</label>
                                    {!! Form::open(array('url' => '/assign-pickup/'.$product->id, 'method' => 'put')) !!}
                                        {!! Form::hidden('status', '3', ['class' => 'form-control', 'required' => 'required']) !!}
                                        <div class="form-group">
                                            <select name="picker_id" class="form-control js-example-basic-single js-country" required="required">
                                                <option value="">Select one</option>
                                                @foreach($pickupman as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                            <i class="fa fa-check"></i>
                                            Assign
                                        </button>
                                    {!! Form::close() !!}
                                </div>

                                <div class="modal fade" id="invoice_{{ $product->product_unique_id }}" tabindex="-1" role="basic" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h4 class="modal-title">Invoice</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div style="padding: 15px;width: 2.5in; margin: 0 auto;" id="{{ $product->product_unique_id }}">
                                                  <div style='text-align: center;'>
                                                    <img style="width:2in" src="{{URL::asset('assets/pages/img/login/login-invert.png')}}">
                                                    <br>300/5/A Hatirpool, Dhaka
                                                    <br>Website: www.biddyut.com
                                                    <br>
                                                    <?php
                                                        // echo DNS1D::getBarcodeHTML('4', "C128B",1.5,33);
                                                        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->product_unique_id, "C128B",1,33) . '" alt="barcode"   /><br>';
                                                    ?>
                                                    {{ $product->product_unique_id }}
                                                  </div>
                                                  <p><b>Pick list printed on: {{ date('Y-m-d') }}</b></p>
                                                  
                                                  <table cellpadding="10"  border="1" style='width : 100%; font-size: 10px;' >
                                                   
                                                    <tr>
                                                      <td style='padding-left:10px'><b>Pickup req. Date</b></td>
                                                      <td style='padding-left:10px' >{{ $product->picking_date }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Package Tracking ID</b></td>
                                                      <td style='padding-left:10px' >{{ $product->product_unique_id }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Merchant Name</b></td>
                                                      <td style='padding-left:10px' >{{ $product->merchant_name }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Merchant Phone No</b></td>
                                                      <td style='padding-left:10px' >{{ $product->merchant_msisdn }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Store Name</b></td>
                                                      <td style='padding-left:10px' >{{ $product->title }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Store Phone No</b></td>
                                                      <td style='padding-left:10px' >{{ $product->msisdn }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Pickup location</b></td>
                                                      <td style='padding-left:10px' >{{ $product->address1 }}, {{ $product->zone_name }}</td>
                                                      
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>City</b></td>
                                                      <td style='padding-left:10px' >{{ $product->city_name }}</td>
                                                      
                                                    </tr>
                                                    <!-- <tr>
                                                      <td style='padding-left:10px' ><b>Postcode</b></td>
                                                      <td style='padding-left:10px' >1205</td>
                                                      
                                                    </tr> -->
                                                    <!-- <tr>
                                                      <td style='padding-left:10px' ><b>Customer Name</b></td>
                                                      <td style='padding-left:10px' >Asima Begum</td>                          
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Address</b></td>
                                                      <td style='padding-left:10px' >600/2, Moghbazar</td>
                                                      
                                                    </tr> -->
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Order Number(Mer)</b></td>
                                                      <td style='padding-left:10px' >20345678</td>
                                                    </tr>
                                                    
                                                  </table>

                                                  <br>
                                                  <table border="1" style="width : 100%; font-size: 10px;" >
                                                    
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Name</b></td>
                                                      <td style='padding-left:10px' >{{ $product->cus_name }}</td>
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Email</b></td>
                                                      <td style='padding-left:10px' >{{ $product->cus_email }}</td>
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Phone No</b></td>
                                                      <td style='padding-left:10px' >{{ $product->cus_msisdn }}, {{ $product->cus_alt_msisdn }}</td>
                                                    </tr>

                                                  </table>

                                                  <br>
                                                  <table border="1" style="width : 100%; font-size: 10px;" >
                                                    <tr>
                                                     <th style='padding-left:10px' >Product details</th>
                                                     <th style='padding-left:10px' >Product SKUs</th>
                                                     <th style='padding-left:10px' >Quantity</th>
                                                   </tr>
                                                   <tr>
                                                     <td style='padding-left:10px' >{{ $product->product_title }}</td>
                                                     <td style='padding-left:10px' >{{ $product->product_category }}</td>
                                                     <td style='padding-left:10px' >{{ $product->quantity }}</td>

                                                   </table>
                                                   
                                                   <br>
                                                   <table border="1" style="width : 100%; font-size: 10px;" >
                                                    <tr>
                                                     <th style='padding-left:10px' >Product Details</th>
                                                     <th style='padding-left:10px' >Total of Packages</th>
                                                     <th style='padding-left:10px' >Price</th>
                                                   </tr>
                                                   <tr>
                                                     <td style='padding-left:10px' >{{ $product->product_title }}</td>
                                                     <td style='padding-left:10px' ></td>
                                                     <td style='padding-left:10px' >{{ $product->sub_total }}</td>

                                                   </table>
                                                   <br><br>
                                                   <p><b>Merchant Signature</b></p>
                                                   <br><br><br>
                                                 </div>

                                                 <button type="button" class="btn dark btn-outline print_it" product_id = "{{ $product->product_unique_id }}">Print</button>

                                               </div>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->

                            </div>
                        </div>

                    </div>
                    
                </div>

            @endforeach

        @else

            <p>No task available here.</p>

        @endIf

    </div>

    <div class="pagination pull-right">
        {{ $products->render() }}
    </div>

    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('assign-pickup', 'tasks');
        });

        $(".print_it").click(function(){
            var product_id = $(this).attr("product_id");
            $("#"+product_id).print({
                globalStyles: true,
                mediaPrint: true,
                stylesheet: null,
                noPrintSelector: ".no-print",
                iframe: true,
                append: null,
                prepend: null,
                manuallyCopyFormValues: true,
                deferred: $.Deferred(),
                timeout: 750,
                title: null,
                doctype: '<!doctype html>'
            });
        });

        $(".print_modal").click(function(){
            var product_id = $(this).attr("product_id");
            $('#invoice_'+product_id).modal('show');
        });        
        
    </script>

@endsection
