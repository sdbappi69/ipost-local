@extends('layouts.appinside')

@section('content')

    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Queued Sub-Orders</span>
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

    <div class="col-md-12">

        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered animated flipInX">

            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Filter</span>
                </div>
            </div>

            <div class="portlet-body util-btn-margin-bottom-5">

                {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}

                    <?php if(!isset($_GET['sub_order_id'])){$_GET['sub_order_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control focus_it" name="sub_order_id" id="sub_order_id" placeholder="Sub-Order ID">
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['order_id'])){$_GET['order_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['order_id']}}" class="form-control" name="order_id" id="order_id" placeholder="Order ID">
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['merchant_order_id'])){$_GET['merchant_order_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['merchant_order_id']}}" class="form-control" name="merchant_order_id" id="merchant_order_id" placeholder="Merchant Order ID">
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('merchant_id', array(''=>'All Merchants')+$merchants,$_GET['merchant_id'], ['class' => 'form-control js-example-basic-single','id' => 'merchant_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('store_id', array(''=>'All Stores')+$stores,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['customer_mobile_no'])){$_GET['customer_mobile_no'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['customer_mobile_no']}}" class="form-control" name="customer_mobile_no" id="customer_mobile_no" placeholder="Customer mobile NO.">
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['pickup_zone_id'])){$_GET['pickup_zone_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('pickup_zone_id', array(''=>'All Pickup Zones')+$zones,$_GET['pickup_zone_id'], ['class' => 'form-control js-example-basic-single','id' => 'pickup_zone_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['delivery_zone_id'])){$_GET['delivery_zone_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('delivery_zone_id', array(''=>'All Delivery Zones')+$zones,$_GET['delivery_zone_id'], ['class' => 'form-control js-example-basic-single','id' => 'delivery_zone_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['start_date'])){$_GET['start_date'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                            <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                                {!! Form::text('start_date',$_GET['start_date'], ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                            </div>
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['end_date'])){$_GET['end_date'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                            <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                                {!! Form::text('end_date',$_GET['end_date'], ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                            </div>
                        <!-- </div> -->
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                    </div>
                    <div class="clearfix"></div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    <div class="col-md-12">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">
            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-bordered table-hover" id="example0">
                    <thead class="flip-content">
                        <th>Unique ID</th>
                        <th>Delivery to</th>
                        <th>Product</th>
                        <th>Invoice</th>
                    </thead>
                    <tbody>
                        @if(count($sub_orders) > 0)
                          @foreach($sub_orders as $sub_order)
                              <tr>
                                  <td>{{ $sub_order->unique_suborder_id }}</td>
                                  <td>{{ $sub_order->delivery_hub }}</td>
                                  <td>
                                    <b>{{ $sub_order->product_title }}</b>
                                    <br>
                                    Cat: {{ $sub_order->product_category }}
                                    <br>
                                    Qty: {{ $sub_order->quantity }}
                                  </td>
                                  <td>
                                      <a class="btn btn-info btn-xs" target="_blank" href="{{url('suborder-invoice/'.$sub_order->unique_suborder_id)}}">Invoice</a>
                                  </td>
                              </tr>
                      @endforeach
                    @endif
                </tbody>
            </table>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>

    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('queued-shipping', 'delivery');

            // Datatable
            $('#example0').DataTable({
                "order": [],
            });
        });

        $(".print_it").click(function(){
            var suborder_id = $(this).attr("suborder_id");
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

        $(".print_modal").click(function(){
            var suborder_id = $(this).attr("suborder_id");
            $('#invoice_'+suborder_id).modal('show');
        }); 
    </script>

@endsection
