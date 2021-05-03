@extends('layouts.appinside')

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Hub Receviced</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Sub-Orders
        <small> queued</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        <div class="col-md-12">
            {!! Form::open(array('url' => secure_url('') . '/assign-delivery/', 'method' => 'post')) !!}

                <div class="form-group col-md-10">

                    {!! Form::text('unique_id', null, ['class' => 'form-control', 'id' => 'remarks']) !!}

                </div>

                <div class="form-group col-md-2">
                    
                    <span class="form-group-btn">
                        <button class="btn blue" type="submit">Find</button>
                    </span>

                </div>

            {!! Form::close() !!}

        </div>

        @if(count($sub_orders) > 0)

            @foreach($sub_orders as $sub_order)

                <div class="col-md-4 small">

                    <div class="mt-element-ribbon bg-grey-steel" style="overflow: hidden;">

                    <a href="javascript:void(0)" class="ribbon ribbon-right ribbon-vertical-right ribbon-shadow ribbon-border-dash-vert ribbon-color-default uppercase print_modal" suborder_id = "{{ $sub_order->unique_suborder_id }}">
                                <div class="ribbon-sub ribbon-bookmark"></div>
                                <i class="fa fa-print"></i>
                            </a>

                        <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">{{ $sub_order->unique_suborder_id }}</div>
                        <div class="ribbon-content" style="overflow:hidden;">

                            <div>
                                <?php
                                    echo '<img style="width:100%;" src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B",1,33) . '" alt="barcode"   /><br>';
                                ?>
                                {{ $sub_order->unique_suborder_id }}

                                <h4 class="uppercase">Shipping</h4>
                                Name: {{ $sub_order->order->delivery_name }}<br>
                                Email: {{ $sub_order->order->delivery_email }}<br>
                                Mobile: {{ $sub_order->order->delivery_msisdn }}, {{ $sub_order->order->delivery_alt_msisdn }}<br>
                                Address: <b>{{ $sub_order->order->delivery_address1 }}, {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}, {{ $sub_order->order->delivery_zone->city->state->name }}</b>
                                <h4 class="uppercase">Products</h4>
                                <div class="product-summery-tbl">
                                    <table style="width:100%">
                                        <thead>
                                            <th>Product</th>
                                            <th style="padding-left:1px solid #FFFFFF;padding-right:1px solid #FFFFFF;">Qty</th>
                                        </thead>
                                        <tbody>
                                            @foreach($sub_order->products as $product)
                                                <tr style="padding-bottom:1px solid #FFFFFF; border-bottom: 1px solid #666666; border-top: 1px solid #666666;">
                                                    <td>
                                                        <b>{{ $product->product_title }}</b>
                                                        <br>
                                                        ID: {{ $product->product_unique_id }}
                                                        <br>
                                                        Cat: {{ $product->product_category->name }}
                                                    </td>
                                                    <td class="numeric" style="padding-left:1px solid #FFFFFF;padding-right:1px solid #FFFFFF;">{{ $product->quantity }}</td>
                                                </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                            </div>

                            {!! Form::open(array('url' => secure_url('') . '/assign-delivery/'.$sub_order->id, 'method' => 'put')) !!}
                                {!! Form::hidden('sub_order_id', $sub_order->id, ['class' => 'form-control', 'required' => 'required']) !!}
                                {!! Form::hidden('order_id', $sub_order->order->id, ['class' => 'form-control', 'required' => 'required']) !!}
                                <div class="form-group">
                                    <select name="deliveryman_id" class="form-control js-example-basic-single js-country" required="required">
                                        <option value="">Select one</option>
                                        @foreach($deliveryman as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Remarks</label>
                                    {!! Form::text('remarks', null, ['class' => 'form-control', 'id' => 'remarks']) !!}
                                </div>
                                <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                    <i class="fa fa-check"></i>
                                    Go
                                </button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    
                </div>

                <div class="modal fade" id="invoice_{{ $sub_order->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Invoice</h4>
                            </div>
                            <div class="modal-body">
                                <div style="padding: 15px;width: 2.5in; margin: 0 auto;" id="{{ $sub_order->unique_suborder_id }}">
                                  <div style='text-align: center;'>
                                    <img src="{{secure_asset('assets/pages/img/login/login-invert.png')}}">
                                    <br>300/5/A Hatirpool, Dhaka
                                    <br>Website: www.biddyut.com
                                    <br><br>
                                    <?php
                                        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B",1,33) . '" alt="barcode"   /><br>';
                                    ?>
                                    {{ $sub_order->unique_suborder_id }}
                                  </div>
                                  
                                  <br>
                                  <table cellpadding="10"  border="1" style='width : 100%; font-size: 10px;' >
           
                                    <tr>
                                      <td style='padding-left:10px'><b>Package Tracking ID</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->unique_suborder_id }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Order Date</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->created_at }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Merchant Name</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->store->merchant->name }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Merchant Phone No</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->store->merchant->msisdn }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Merchant Order Id</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->merchant_order_id }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Order Date</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->created_at }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Customer Name</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->delivery_name }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Customer Phone</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->delivery_msisdn }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Customer address</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->delivery_address1 }}</td>
                                      
                                    </tr>

                                    <tr>
                                      <td style='padding-left:10px' ><b>Zone</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->delivery_zone->name }}</td>
                                      
                                    </tr>

                                    <tr>
                                      <td style='padding-left:10px' ><b>City</b></td>
                                      <td style='padding-left:10px' >{{ $sub_order->order->delivery_zone->city->name }}</td>
                                      
                                    </tr>
                                    
                                    <!-- <tr>
                                      <td style='padding-left:10px' ><b>Postcode</b></td>
                                      <td style='padding-left:10px' >1217</td>
                                      
                                    </tr> -->
                                    <!-- <tr>
                                      <td style='padding-left:10px' ><b>Purchase order No :</b></td>
                                      <td style='padding-left:10px' >20345678</td>
                                      
                                    </tr> -->
                                    <!-- <tr>
                                      <td style='padding-left:10px' ><b>Payment Type</b></td>
                                      <td style='padding-left:10px' >Cash On Delivery</td>
                                      
                                    </tr> -->
                                    <tr>
                                      <td style='padding-left:10px' ><b>Shipping Charge</b></td>
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
                                      <td style='padding-left:10px' >{{ $delivery_charge }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Product Price</b></td>
                                      <td style='padding-left:10px' >{{ $product_price }}</td>
                                      
                                    </tr>
                                    
                                  </table>
                                  <br>
                                  <table border="1" style="width : 100%; font-size: 10px;" >
                                    <tr>
                                     <th style='padding-left:10px' >Item Details</th>
                                     <th style='padding-left:10px' >Price</th>
                                     <th style='padding-left:10px' >Qty</th>
                                   </tr>

                                    @foreach($sub_order->products as $row)
                                    <tr>
                                     <td style='padding-left:10px' >{{ $row->product_title }}</td>
                                     <td style='padding-left:10px' >{{ $row->unit_price }}</td>
                                     <td style='padding-left:10px' >{{ $row->quantity }}</td>
                                    </tr>
                                    @endforeach

                                   </table>
                                   
                                   <br>
                                   <table border="1" style="width : 100%; font-size: 10px;" >
                                    <tr>
                                     <th style='padding-left:10px' >Product price</th>
                                     <th style='padding-left:10px' >Shipping charge</th>
                                     <th style='padding-left:10px' >Amount to be collected</th>
                                   </tr>
                                   <tr>
                                     <td style='padding-left:10px' >{{ $product_price }}</td>
                                     <td style='padding-left:10px' >{{ $delivery_charge }}</td>
                                     <td style='padding-left:10px' >{{ $collectable_amount }}</td>

                                   </table>
                                   <br><br>
                                   <p><b>Customer Signature</b></p>
                                   <br><br><br>
                                 </div>

                                 <button type="button" class="btn dark btn-outline print_it" suborder_id = "{{ $sub_order->unique_suborder_id }}">Print</button>

                               </div>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->

            @endforeach

        @endIf

    </div>

    <div class="pagination pull-right">
        {{ $sub_orders->appends($_REQUEST)->render() }}
    </div>

    <script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('assign-delivery', 'tasks');
        });

        $(".print_modal").click(function(){
            var suborder_id = $(this).attr("suborder_id");
            $('#invoice_'+suborder_id).modal('show');
        });

        $(".print_it").click(function(){
            var suborder_id = $(this).attr("suborder_id");
            // alert(suborder_id);
            $("#"+suborder_id).print({
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
    </script>

@endsection
