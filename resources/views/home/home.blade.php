@extends('layouts.appinside')

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        {{-- <li>
            <a href="{{ secure_url('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li> --}}
        <li>
            <span>Dashboard</span>
        </li>
    </ul>
    <!-- <div class="page-toolbar">
        <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
            <i class="icon-calendar"></i>&nbsp;
            <span class="thin uppercase hidden-xs"></span>&nbsp;
            <i class="fa fa-angle-down"></i>
        </div>
    </div> -->
</div>
<div class="row widget-row">
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Merchant</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-green icon-users"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{$count_merchant}}">{{$count_merchant}}</span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Hub</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-red icon-drawer"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{$count_hub}}">{{$count_hub}}</span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Stores</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-purple icon-screen-desktop"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{$count_store}}">{{$count_store}}</span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Orders</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-blue icon-bar-chart"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{$count_orders}}">{{$count_orders}}</span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
</div>

<div class="row">
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

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Merchants</label>
                    {!! Form::select('merchant_id[]', $merchants,null, ['class' => 'form-control js-example-basic-single','id' => 'merchant_id', 'multiple' => '']) !!}
                </div>

                <?php if(!isset($_GET['from_date'])){$_GET['from_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order from</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                        {!! Form::text('from_date',$from_date, ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <?php if(!isset($_GET['to_date'])){$_GET['to_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order to</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                        {!! Form::text('to_date',$to_date, ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                </div>
                <div class="clearfix"></div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered text-center">
            <h4 class="widget-thumb-heading">New Orders</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $home_info['new_order_count'] }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('order') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
</div>


<div class="row">
    <div class="col-md-12 col-sm-12">
        <h1 class="page-title"> Recent Orders
            <small></small>
        </h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Order Id</th>
                    <th style="width:143px;">Status</th>
                    <th>Created</th>
                    <th>Store Id</th>
                    <th>Delivery Cost</th>
                    <th>Delivery Address</th>
                </tr>
            </thead>
            <tbody>
                
             @foreach($recent_orders as $order)
             <tr>
                {{-- */ $update_url = secure_url('order').'/'.$order->id.'/edit?step=3'; /* --}}
                {{-- */ $view_url = secure_url('order').'/'.$order->id; /* --}}
                <td>
                    {{-- <a class="label label-success" href="{{ $view_url }}"> --}}
                        {{ $order->unique_order_id }}
                    {{-- </a> --}}
                </td>
                <td>

                    @if($order->order_status == 1)
                    Order
                    @elseIf($order->order_status == 2)
                    Verify
                    @elseIf($order->order_status == 3)
                    Assign Picker
                    @elseIf($order->order_status == 4)
                    Picked
                    @elseIf($order->order_status == 5)
                    Received
                    @elseIf($order->order_status == 6)
                    In Transit
                    @elseIf($order->order_status == 7)
                    Destination
                    @elseIf($order->order_status == 8)
                    Assign Delivery
                    @elseIf($order->order_status == 9)
                    Delivery
                    @elseIf($order->order_status == 10)
                    Documents
                    @endIf
                </td>
                <td>{{ @$order->created_at }}</td>
                <td>{{ @$order->store->store_id }}</td>
                <td>{{ @$order->delivery_payment_amount }}</td>
                <td>{{ @$order->delivery_address1 }}, {{ @$order->delivery_zone->name }}, {{ @$order->delivery_zone->city->name }}</td>
                    <!-- <td>
                        <a class="label label-success" href="{{ $update_url }}">
                            <i class="fa fa-pencil"></i> Update
                        </a>
                    </td> -->
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="{{secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
<script src="{{secure_asset('assets/global/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{secure_asset('assets/global/plugins/counterup/jquery.counterup.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('dashboard', 'dashboard');

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = array();} ?>
        $('#merchant_id').select2().val([{!! implode(",", $_GET['merchant_id']) !!}]).trigger("change");
    });
</script>

@endsection
