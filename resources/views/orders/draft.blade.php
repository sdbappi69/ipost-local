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
                <span>Order</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Order
        <small> draft</small>
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

                {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

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

                    <?php if(!isset($_GET['customer_mobile_no'])){$_GET['customer_mobile_no'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['customer_mobile_no']}}" class="form-control" name="customer_mobile_no" id="customer_mobile_no" placeholder="Customer mobile NO.">
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('store_id', array(''=>'Select Store')+$stores,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['search_date'])){$_GET['search_date'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                            <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                                {!! Form::text('search_date',$_GET['search_date'], ['class' => 'form-control picking_date','placeholder' => 'Created date' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                            </div>
                        <!-- </div> -->
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary filter-btn pull-right" style="width: 100%;"><i class="fa fa-search"></i> Filter</button>
                    </div>
                    <div class="clearfix"></div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    @if(count($orders) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Order</span>
                        <span class="caption-helper">list</span>
                    </div>
                </div>
                <div class="portlet-body util-btn-margin-bottom-5">

                    <div class="well">
                        <span class="counter" style="font-weight: bold;">0</span> order selected
                    </div>

                    {!! Form::open(array('url' => secure_url('') . '/', 'method' => 'post', 'id' => 'selected_orders')) !!}
                        <table class="table table-bordered table-hover">
                            <thead class="flip-content">
                                <th>{!!Form::checkbox('mother_checkbox', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                                <th>Order Id</th>
                                <th>Merchant Order Id</th>
                                <th style="width:106px;">Status</th>
                                <th>Created</th>
                                <th>Store Id</th>
                                <th>Delivery Cost</th>
                                <th>Delivery Address</th>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        @if($order->order_status == ''||$order->order_status < 2)
                                            {{-- */ $url = secure_url('merchant-orderv2').'/'.$order->id.'/edit?step=3'; /* --}}
                                        @else
                                            {{-- */ $url = secure_url('merchant-orderv2').'/'.$order->id; /* --}}
                                        @endIf
                                        <td>
                                            {!!Form::checkbox('order_id[]',$order->id, false) !!}
                                        </td>
                                        <td>
                                            <a class="label label-success" href="{{ $url }}">
                                                {{ $order->unique_order_id }}
                                            </a>
                                        </td>
                                        <td>{{ $order->merchant_order_id }}</td>
                                        <td>
                                            @if($order->order_status >= 9)
                                            Delivered
                                            @elseIf($order->order_status >= 6)
                                            In Transit
                                            @elseIf($order->order_status >= 3)
                                            Picked
                                            @elseIf($order->order_status > 1)
                                            Verified
                                            @else
                                            Draft
                                            @endIf
                                        </td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>{{ $order->store->store_id }}</td>
                                        <td>{{ $order->delivery_payment_amount }}</td>
                                        <td>{{ $order->delivery_address1 }}, {{ $order->delivery_zone->name or '' }}, {{ $order->delivery_zone->city->name or '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="button" id="btn-remove" class="btn red btn-outline">Remove</button>

                        <button type="button" id="btn-approve" class="btn btn-success btn-outline pull-right">Approve</button>

                        <br><br>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endIf
    <script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('order-draft', 'orders');

            $('#example0').DataTable({
                "order": [],
            });
        });
    </script>

    <script type="text/javascript">
        $("#select_all_chk").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
            countCheckbox();
        });

        $(":checkbox").on("click", function(){
            countCheckbox();
        });

        $("#btn-approve").click(function(e){
            e.preventDefault();
            $('#selected_orders').attr('action', "{{ secure_url('merchant-order-draft-submit') }}").submit();
        });

        $("#btn-remove").click(function(e){
            e.preventDefault();
            $('#selected_orders').attr('action', "{{ secure_url('merchant-order-draft-remove') }}").submit();
        });

        function countCheckbox(){
            var check_count = $('input[type="checkbox"]:checked').length;
            if($('input[name="mother_checkbox"]').is(':checked')){
                var count = check_count - 1;
            }else{
                var count = check_count;
            }

            $('.counter').html(count);
        }

    </script>

@endsection
