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
            <span>Orders</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Orders
    <small> view</small>
</h1>

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
                    <!-- <label class="control-label">Sub-Order ID</label> -->
                    <!-- <div class="row"> -->
                     <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control focus_it" name="sub_order_id" id="sub_order_id" placeholder="Sub-Order ID">
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['order_id'])){$_GET['order_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Order ID</label> -->
                    <!-- <div class="row"> -->
                     <input type="text" value="{{$_GET['order_id']}}" class="form-control" name="order_id" id="order_id" placeholder="Order ID">
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['merchant_order_id'])){$_GET['merchant_order_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Merchant Order ID</label> -->
                    <!-- <div class="row"> -->
                     <input type="text" value="{{$_GET['merchant_order_id']}}" class="form-control" name="merchant_order_id" id="merchant_order_id" placeholder="Merchant Order ID">
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['sub_order_status'])){$_GET['sub_order_status'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Sub-Order Status</label> -->
                    <!-- <div class="row"> -->
                     {!! Form::select('sub_order_status', $sub_order_status, $_GET['sub_order_status'], ['class' => 'form-control js-example-basic-single', 'id' => 'order_status', 'placeholder' => 'All Sub-Order Status']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Merchants</label> -->
                    <!-- <div class="row"> -->
                     {!! Form::select('merchant_id', $merchants,$_GET['merchant_id'], ['class' => 'form-control js-example-basic-single','id' => 'merchant_id', 'placeholder' => 'All Merchants']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Stores</label> -->
                    <!-- <div class="row"> -->
                     {!! Form::select('store_id', $stores,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id', 'placeholder' => 'All Stores']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['customer_mobile_no'])){$_GET['customer_mobile_no'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Customer mobile NO.</label> -->
                    <!-- <div class="row"> -->
                     <input type="text" value="{{$_GET['customer_mobile_no']}}" class="form-control" name="customer_mobile_no" id="customer_mobile_no" placeholder="Customer mobile NO.">
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['pickup_man_id'])){$_GET['pickup_man_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Pickup Man</label> -->
                    <!-- <div class="row"> -->
                     {!! Form::select('pickup_man_id', $pickupman,$_GET['pickup_man_id'], ['class' => 'form-control js-example-basic-single','id' => 'pickup_man_id', 'placeholder' => 'All Pickup Man']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['delivary_man_id'])){$_GET['delivary_man_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Delivery Man</label> -->
                    <!-- <div class="row"> -->
                     {!! Form::select('delivary_man_id', $pickupman,$_GET['delivary_man_id'], ['class' => 'form-control js-example-basic-single','id' => 'delivary_man_id', 'placeholder' => 'All Delivery Man']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['pickup_zone_id'])){$_GET['pickup_zone_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Pickup Zones</label> -->
                    <!-- <div class="row"> -->
                     {!! Form::select('pickup_zone_id', $zones,$_GET['pickup_zone_id'], ['class' => 'form-control js-example-basic-single','id' => 'pickup_zone_id', 'placeholder' => 'All Pickup Zones']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['delivery_zone_id'])){$_GET['delivery_zone_id'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <div class="row"> -->
                    <!-- <label class="control-label">Delivery Zones</label> -->
                     {!! Form::select('delivery_zone_id', $zones,$_GET['delivery_zone_id'], ['class' => 'form-control js-example-basic-single','id' => 'delivery_zone_id', 'placeholder' => 'All Delivery Zones']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['start_date'])){$_GET['start_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <label class="control-label">Order from</label> -->
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
                    <!-- <label class="control-label">Order to</label> -->
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
                    <button type="button" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                </div>
                <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

@if(count($sub_orders) > 0)


<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Orders</span>
            </div>
            <div class="tools">
                <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" id="example0">
                <thead>
                    <th>Order Id</th>
                    <th>Sub-Order Id</th>
                    <th>Type</th>
                    <th>Merchant Order Id</th>
                    <th>Current Status</th>
                    {{-- <th>Merchant</th> --}}
                    <th>Store</th>
                    <th>Order created</th>
                    <th>Product</th>
                    {{-- <th>Category</th> --}}
                    <th>Quantity</th>
                    {{-- <th>Weight</th> --}}
                    <th>Verified Weight</th>
                    <th>Picking Attempt</th>
                    {{-- <th>Delivery Attempt</th> --}}
                    {{-- <th>Picking Name</th> --}}
                    {{-- <th>Picking Email</th> --}}
                    {{-- <th>Picking Mobile</th> --}}
                    {{-- <th>Picking Address</th> --}}
                    {{-- <th>Picking hub</th> --}}
                    <th>Delivery Name</th>
                    <th>Delivery Email</th>
                    <th>Delivery Mobile</th>
                    {{-- <th>Delivery Address</th> --}}
                    {{-- <th>Delivery hub</th> --}}
                    {{-- <th>Last Delivery Attempts</th> --}}
                    <th>Amount to be Collected</th>
                    <th>Amount Collected</th>

                    <th>TAT</th>
                    <th>Pickup aging</th>
                    <th>Delivery aging</th>
                    <th>Delivery attempt aging</th>                    
                    <th>Latest picking attempt</th>
                    <th>Latest picking reason</th>
                    <th>Latest delivery attempt</th>
                    <th>Latest delivery reason</th>

                </thead>
                <tbody>
                    @foreach($sub_orders as $sub_order)
                    <tr>
                        <td>
                            <!-- <b>Order:</b> -->
                            <a class="label label-success" href="{{ URL::to('order').'/'.$sub_order->order_id }}">
                                {{ $sub_order->unique_order_id }}
                            </a>
                        </td>
                        <td>{{ $sub_order->unique_suborder_id }}</td>
                        <td>
                            @if($sub_order->return == 1)
                                Return
                            @else
                                Delivery
                            @endIf
                        </td>
                        <td>{{ $sub_order->merchant_order_id }}</td>
                        <td>
                            @if($sub_order->sub_order_last_status === NULL)
                                {{ hubGetStatus($sub_order->sub_order_status) }}
                            @else
                                {{ hubGetStatus($sub_order->sub_order_last_status) }}
                            @endIf
                        </td>
                        
                        <td>{{ $sub_order->store_name }}</td>
                        <td>{{ $sub_order->created_at }}</td>
                        <td>{{ $sub_order->product_title }}</td>                        
                                             
                        <td>{{ $sub_order->quantity }}</td>                        
                                                
                        <td>{{ $sub_order->weight }}</td>                        
                        <td>{{ $sub_order->picking_attempts }}</td>
                        
                        <td>{{ $sub_order->delivery_name }}</td>
                        <td>{{ $sub_order->delivery_email }}</td>
                        <td>{{ $sub_order->delivery_msisdn }}, {{ $sub_order->delivery_alt_msisdn }}</td>
                        
                        
                        
                        <td>{{ $sub_order->sub_total }}</td>
                        <td>{{ $sub_order->delivery_paid_amount }}</td>

                        <?php
                            $sub_order_note = json_decode($sub_order->sub_order_note,true);
                        ?>
                        <td>{{ $sub_order_note['tat'] }}</td>
                        <td>{{ $sub_order_note['pickup_aging'] }}</td>
                        <td>{{ $sub_order_note['delivery_aging'] }}</td>
                        <td>{{ $sub_order_note['delivery_attempt_aging'] }}</td>
                        <td>@if (isset($sub_order_note['latest_picking_attempt'])) {{ $sub_order_note['latest_picking_attempt'] }} @endIf</td>
                        <td>@if (isset($sub_order_note['latest_picking_reason'])) {{ $sub_order_note['latest_picking_reason'] }} @endIf</td>
                        <td>@if (isset($sub_order_note['latest_delivery_attempt'])) {{ $sub_order_note['latest_delivery_attempt'] }} @endIf</td>
                        <td>@if (isset($sub_order_note['latest_delivery_reason'])) {{ $sub_order_note['latest_delivery_reason'] }} @endIf</td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination pull-right">
                {!! $sub_orders->appends($_REQUEST)->render() !!}
            </div>
        </div>
    </div>
</div>

@endIf

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('orders', 'orders');
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example0').dataTable( {
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });
    });

    $(".filter-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ URL::to('order') }}").submit();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ URL::to('orderexport/xls') }}").submit();
    });

</script>

@endsection
