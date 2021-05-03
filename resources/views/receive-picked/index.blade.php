@extends('layouts.appinside')

@section('content')

    <link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ secure_url('hub-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Product Verify</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Orders
        <small> verify</small>
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

                {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

                    <?php if(!isset($_GET['sub_order_id'])){$_GET['sub_order_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control focus_it" name="sub_order_id" id="sub_order_id" placeholder="AWB">
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

                    <?php if(!isset($_GET['pickup_man_id'])){$_GET['pickup_man_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('pickup_man_id', array(''=>'All Pickup Man')+$pickupman,$_GET['pickup_man_id'], ['class' => 'form-control js-example-basic-single','id' => 'pickup_man_id']) !!}
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

                    <?php if(!isset($_GET['consignment_id'])){$_GET['consignment_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['consignment_id']}}" class="form-control" name="consignment_id" id="consignment_id" placeholder="Consignment Id">
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
            {!! Form::open(array('url' => secure_url('') . '/receive-picked-bulk', 'method' => 'post', 'id' => 'selected_orders')) !!}
                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <!-- <th>Order ID</th> -->
                            <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                            <th>AWB</th>
                            <th>Picking Time</th>
                            <th>Picking Address</th>
                            <th>Product</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(count($products) > 0)
                                @foreach($products as $p)
                                <tr>
                                    <!-- <td>
                                        <a target="_blank" class="label label-success" href="hub-order/{{ $p->order_id }}">
                                            {{ $p->unique_suborder_id }}
                                        </a>
                                    </td> -->
                                    <td>
                                        {!!Form::checkbox('product_id[]',$p->id, false) !!}
                                    </td>
                                    <td>{{ $p->unique_suborder_id }}</td>
                                    <td>{{ $p->picking_date }} ({{ $p->start_time }}-{{ $p->end_time }})</td>
                                    <td> Warehouse: <strong>{{ $p->title }}</strong>
                                        <br>
                                        Phone: {{ $p->msisdn }}, {{ $p->alt_msisdn }}
                                        <br>
                                        Address: {{ $p->address1 }}, {{ $p->zone_name }}, {{ $p->city_name }}, {{ $p->state_name }}
                                    </td>
                                    <td>
                                        Title: <strong>{{ $p->product_title }}</strong>
                                        <br>
                                        Category: {{ $p->product_category }}
                                        <br>
                                        Quantity: {{ $p->quantity }}
                                    </td>
                                    <td>
                                        <a href="{{ secure_url('receive-picked').'/'.$p->id.'/edit' }}" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12">
                                            <i class="fa fa-check"></i> Verify
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- /.modal-dialog -->
                            
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <button type="submit" id="btn-approve" class="btn blue pull-right">Verify Selected</button>

                </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="pagination pull-right">
        {{ $products->appends($_REQUEST)->render() }}
    </div>

    <script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('receive-picked', 'pickup');
        });

        $("#select_all_chk").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

    </script>

@endsection
