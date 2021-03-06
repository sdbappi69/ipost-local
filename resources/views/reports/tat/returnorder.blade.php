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
            <span>TAT</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Return
    <small> Completed</small>
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

            <?php if(!isset($_GET['sub_order_id'])){$_GET['sub_order_id'] = null;} ?>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">AWB</label>
                <!-- <div class="row"> -->
                   <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control focus_it" name="sub_order_id" id="sub_order_id" placeholder="AWB">
                   <!-- </div> -->
               </div>

               <?php if(!isset($_GET['order_id'])){$_GET['order_id'] = null;} ?>
               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Order ID</label>
                <!-- <div class="row"> -->
                   <input type="text" value="{{$_GET['order_id']}}" class="form-control" name="order_id" id="order_id" placeholder="Order ID">
                   <!-- </div> -->
               </div>

               <?php if(!isset($_GET['merchant_order_id'])){$_GET['merchant_order_id'] = null;} ?>
               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Merchant Order ID</label>
                <!-- <div class="row"> -->
                   <input type="text" value="{{$_GET['merchant_order_id']}}" class="form-control" name="merchant_order_id" id="merchant_order_id" placeholder="Merchant Order ID">
                   <!-- </div> -->
               </div>

               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Merchants</label>
                <!-- <div class="row"> -->
                   {!! Form::select('merchant_id[]', $merchants, null, ['class' => 'form-control js-example-basic-single','id' => 'merchant_id', 'multiple' => '']) !!}
                   <!-- </div> -->
               </div>

               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Stores</label>
                <!-- <div class="row"> -->
                   {!! Form::select('store_id[]', $stores, null, ['class' => 'form-control js-example-basic-single','id' => 'store_id', 'multiple' => '']) !!}
                   <!-- </div> -->
               </div>

               <?php if(!isset($_GET['customer_mobile_no'])){$_GET['customer_mobile_no'] = null;} ?>
               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Customer mobile NO.</label>
                <!-- <div class="row"> -->
                   <input type="text" value="{{$_GET['customer_mobile_no']}}" class="form-control" name="customer_mobile_no" id="customer_mobile_no" placeholder="Customer mobile NO.">
                   <!-- </div> -->
               </div>

               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Pickup Man</label>
                <!-- <div class="row"> -->
                   {!! Form::select('pickup_man_id[]', $pickupman, null, ['class' => 'form-control js-example-basic-single','id' => 'pickup_man_id', 'multiple' => '']) !!}
                   <!-- </div> -->
               </div>

               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Delivery Man</label>
                <!-- <div class="row"> -->
                   {!! Form::select('delivary_man_id[]', $pickupman, null, ['class' => 'form-control js-example-basic-single','id' => 'delivary_man_id', 'multiple' => '']) !!}
                   <!-- </div> -->
               </div>

               <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Pickup Hubs</label>
                <!-- <div class="row"> -->
                   {!! Form::select('pickup_hub_id[]', $hubs, null, ['class' => 'form-control js-example-basic-single','id' => 'pickup_hub_id', 'multiple' => '']) !!}
                   <!-- </div> -->
               </div>

               <div class="col-md-4" style="margin-bottom:5px;">
                <!-- <div class="row"> -->
                    <label class="control-label">Delivery Hubs</label>
                    {!! Form::select('delivery_hub_id[]', $hubs, null, ['class' => 'form-control js-example-basic-single','id' => 'delivery_hub_id', 'multiple' => '']) !!}
                    <!-- </div> -->
                </div>

                <?php if(!isset($_GET['start_date'])){$_GET['start_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Delivered from</label>
                    <!-- <div class="row"> -->
                        <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                            <span class="input-group-btn">
                                <button class="btn default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                            {!! Form::text('start_date',$_GET['start_date'], ['class' => 'form-control picking_date','placeholder' => 'Delivered from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                        </div>
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['end_date'])){$_GET['end_date'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <label class="control-label">Delivered to</label>
                        <!-- <div class="row"> -->
                            <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                                {!! Form::text('end_date',$_GET['end_date'], ['class' => 'form-control picking_date','placeholder' => 'Delivered to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
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


            @if(count($order_logs) > 0)


            <div class="col-md-12">
                <!-- BEGIN BUTTONS PORTLET-->
                <div class="portlet light tasks-widget bordered">

                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-edit font-dark"></i>
                            <span class="caption-subject font-dark bold uppercase">Sub-Orders</span>
                        </div>
                        <div class="tools">
                            <button type="button" class="btn btn-primary export-btn">
                                <i class="fa fa-file-excel-o"></i>
                                Total: {{ $order_logs->toArray()['total'] }} data
                            </button>
                        </div>
                    </div>

                    <div class="portlet-body util-btn-margin-bottom-5">
                        <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" id="example0">
                            <thead>
                                <th>Order Id</th>
                                <th>AWB</th>
                                <th>Merchant Order Id</th>
                                <th>Merchant</th>
                                <th>Store</th>

                                <th>TAT</th>

                                <th>Order created</th>

                                <th>Picked at</th>
                                <th>Returned at</th>

                                <th>Picking Attempt</th>
                                <th>Return Attempt</th>

                                <th>Product</th>
                                <th>Quantity</th>
                                
                                <th>Current Status</th>
                            </thead>
                            <tbody>
                                @foreach($order_logs as $order_log)
                                <tr>
                                    <td>
                                        <a class="label label-success" href="{{ secure_url('order').'/'.$order_log->orderId }}">
                                            {{ $order_log->order_id }}
                                        </a>
                                    </td>
                                    <td>{{ $order_log->sub_order_id or '' }}</td>
                                    <td>{{ $order_log->merchant_order_id or '' }}</td>
                                    <td>{{ $order_log->merchant or '' }}</td>
                                    <td>{{ $order_log->store or '' }}</td>

                                    <td>{{ $order_log->tat or '' }}</td>
                                    <td>{{ $order_log->order_created or '' }}</td>
                                    <td>{{ $order_log->picked_at or '' }}</td>
                                    <td>{{ $order_log->returned_at or '' }}</td>
                                    <td>{{ $order_log->picking_attempt or '' }}</td>
                                    <td>{{ $order_log->return_attempt or '' }}</td>
                                    <td>{{ $order_log->product or '' }}</td>
                                    <td>{{ $order_log->quantity or '' }}</td>
                                    <td>{{ $order_log->current_status or '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="pagination pull-right">
                            {!! $order_logs->appends($_REQUEST)->render() !!}
                        </div>
                    </div>
                </div>
            </div>

            @endIf

            <script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
            <script type="text/javascript">
                $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('tat-return', 'tat');

        <?php if(!isset($_GET['sub_order_status'])){$_GET['sub_order_status'] = array();} ?>
        $('#order_status').select2().val([{!! implode(",", $_GET['sub_order_status']) !!}]).trigger("change");

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = array();} ?>
        $('#merchant_id').select2().val([{!! implode(",", $_GET['merchant_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = array();} ?>
        $('#store_id').select2().val([{!! implode(",", $_GET['store_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['pickup_man_id'])){$_GET['pickup_man_id'] = array();} ?>
        $('#pickup_man_id').select2().val([{!! implode(",", $_GET['pickup_man_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['delivary_man_id'])){$_GET['delivary_man_id'] = array();} ?>
        $('#delivary_man_id').select2().val([{!! implode(",", $_GET['delivary_man_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['pickup_hub_id'])){$_GET['pickup_hub_id'] = array();} ?>
        $('#pickup_hub_id').select2().val([{!! implode(",", $_GET['pickup_hub_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['delivery_hub_id'])){$_GET['delivery_hub_id'] = array();} ?>
        $('#delivery_hub_id').select2().val([{!! implode(",", $_GET['delivery_hub_id']) !!}]).trigger("change");
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
        $('#filter-form').attr('action', "{{ secure_url('tat/return') }}").submit();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('tat/returnexport/xls') }}").submit();
    });

</script>

@endsection
