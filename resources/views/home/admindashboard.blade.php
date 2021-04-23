@extends('layouts.appinside')

@section('content')

<style type="text/css">
.widget-thumb{
    text-align: center;
}
</style>

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <span>Dashboard</span>
        </li>
    </ul>
</div>

<br>

<div class="row">

    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Merchant</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_merchant }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('merchant') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>

            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>

    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Store</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_store }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('store') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
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
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_hub }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('hub') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
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
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_orders }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('order') }}" >
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

                <div class="col-md-6" style="margin-bottom:5px;">
                    <label class="control-label">Merchants</label>
                    {!! Form::select('merchant_id[]', $merchants,null, ['class' => 'form-control js-example-basic-single','id' => 'merchant_id', 'multiple' => '']) !!}
                </div>

                <?php if (!isset($_GET['hub_id'])) {
                    $_GET['hub_id'] = null;
                } ?>
                <div class="col-md-6" style="margin-bottom:5px;">
                    <label class="control-label">Hubs</label>
                    {!! Form::select('hub_id[]', $hubs,null, ['class' => 'form-control js-example-basic-single','id' => 'hub_id', 'multiple' => '']) !!}
                </div>

<?php if (!isset($_GET['from_date'])) {
    $_GET['from_date'] = null;
} ?>
                <div class="col-md-6" style="margin-bottom:5px;">
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

<?php if (!isset($_GET['to_date'])) {
    $_GET['to_date'] = null;
} ?>
                <div class="col-md-6" style="margin-bottom:5px;">
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

    <div class="col-md-6 col-sm-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-cursor font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">PickUp</span>
                    <span class="caption-helper">
                        <?php
                        $getRequest = '';
                        if (isset($_GET['merchant_id'])) {
                            if ($getRequest == '')
                                $getRequest .= 'merchant_id=' . implode(',', $_GET['merchant_id']);
                            else
                                $getRequest .= '&merchant_id=' . implode(',', $_GET['merchant_id']);
                        }
                        if (isset($_GET['hub_id'])) {
                            if ($getRequest == '')
                                $getRequest .= 'hub_id=' . implode(',', $_GET['hub_id']);
                            else
                                $getRequest .= '&hub_id=' . implode(',', $_GET['hub_id']);
                        }
                        if (isset($_GET['from_date'])) {
                            if ($getRequest == '')
                                $getRequest .= 'from_date=' . $_GET['from_date'];
                            else
                                $getRequest .= '&from_date=' . $_GET['from_date'];
                        }
                        if (isset($_GET['to_date'])) {
                            if ($getRequest == '')
                                $getRequest .= 'to_date=' . $_GET['to_date'];
                            else
                                $getRequest .= '&to_date=' . $_GET['to_date'];
                        }
                        ?>
                        <a href="{{ URL::to('/dashboard-export/pickup_all?'.$getRequest) }}">
                            Total: {{ $home_info['total_pickup_req'] }}
                        </a>
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_pickup_req'] != 0 && $home_info['panding_pickup_req'] != 0) {
                                $pending_pickup = ($home_info['panding_pickup_req'] / $home_info['total_pickup_req']) * 100;
                            } else {
                                $pending_pickup = 0;
                            }
                            ?>

                            <div class="number pending" data-percent="{{ $pending_pickup }}">
                                <span>{{ sprintf('%0.2f', $pending_pickup) }}</span>% </div>
                            <a class="title" href="#"> Pending ({{$home_info['panding_pickup_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_pickup_req'] != 0 && $home_info['processing_pickup_req'] != 0) {
                                $processing_pickup = ($home_info['processing_pickup_req'] / $home_info['total_pickup_req']) * 100;
                            } else {
                                $processing_pickup = 0;
                            }
                            ?>

                            <div class="number partial" data-percent="{{ $processing_pickup }}">

                                <span>{{ sprintf('%0.2f', $processing_pickup) }}</span>% </div>
                            <a class="title" href="#"> Proccessing ({{$home_info['processing_pickup_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_pickup_req'] != 0 && $home_info['success_pickup_req'] != 0) {
                                $success_pickup = ($home_info['success_pickup_req'] / $home_info['total_pickup_req']) * 100;
                            } else {
                                $success_pickup = 0;
                            }
                            ?>

                            <div class="number success" data-percent="{{ $success_pickup }}">
                                <span>{{ sprintf('%0.2f', $success_pickup) }}</span>% 
                            </div>
                            <a class="title" href="#"> Success ({{$home_info['success_pickup_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_pickup_req'] != 0 && $home_info['failed_pickup_req'] != 0) {
                                $failed_pickup = ($home_info['failed_pickup_req'] / $home_info['total_pickup_req'] ) * 100;
                            } else {
                                $failed_pickup = 0;
                            }
                            ?>

                            <div class="number failed" data-percent="{{ $failed_pickup }}">

                                <span>{{ sprintf('%0.2f', $failed_pickup) }}</span>% </div>
                            <a class="title" href="#"> Failed ({{$home_info['failed_pickup_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-cursor font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Delivery</span>
                    <span class="caption-helper">
                        <a href="{{ URL::to('/dashboard-export/delivery_all?'.$getRequest) }}">
                            Total: {{ $home_info['total_delivery_req'] }}
                        </a>
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">

                    @if(strtotime($from_date) > strtotime('2017-06-05'))

                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_delivery_req'] != 0 && $home_info['panding_delivery_req'] != 0) {
                                $pending_delivery = ($home_info['panding_delivery_req'] / $home_info['total_delivery_req']) * 100;
                            } else {
                                $pending_delivery = 0;
                            }
                            ?>

                            <div class="number pending" data-percent="{{ $pending_delivery }}">
                                <span>{{ sprintf('%0.2f', $pending_delivery) }}</span>% </div>
                            <a class="title" href="#"> Pending ({{$home_info['panding_delivery_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_delivery_req'] != 0 && $home_info['processing_delivery_req'] != 0) {
                                $processing_delivery = ($home_info['processing_delivery_req'] / $home_info['total_delivery_req']) * 100;
                            } else {
                                $processing_delivery = 0;
                            }
                            ?>

                            <div class="number partial" data-percent="{{ $processing_delivery }}">

                                <span>{{ sprintf('%0.2f', $processing_delivery) }}</span>% </div>
                            <a class="title" href="#"> Processing ({{$home_info['processing_delivery_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_delivery_req'] != 0 && $home_info['success_delivery_req'] != 0) {
                                $success_delivery = ($home_info['success_delivery_req'] / $home_info['total_delivery_req']) * 100;
                            } else {
                                $success_delivery = 0;
                            }
                            ?>

                            <div class="number success" data-percent="{{ $success_delivery }}">

                                <span>{{ sprintf('%0.2f', $success_delivery) }}</span>% </div>
                            <a class="title" href="#"> Success ({{$home_info['success_delivery_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_delivery_req'] != 0 && $home_info['failed_delivery_req'] != 0) {
                                $failed_delivery = ($home_info['failed_delivery_req'] / $home_info['total_delivery_req'] ) * 100;
                            } else {
                                $failed_delivery = 0;
                            }
                            ?>

                            <div class="number failed" data-percent="{{ $failed_delivery }}">

                                <span>{{ sprintf('%0.2f', $failed_delivery) }}</span>% </div>
                            <a class="title" href="#"> Failed ({{$home_info['failed_delivery_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>

                    @else

                    <p style="padding: 20px;">This section will show data if the inputted date is after 05 Jun, 2017</p>

                    @endIf

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-6 col-sm-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-cursor font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Return</span>
                    <span class="caption-helper">
                        <a href="{{ URL::to('/dashboard-export/return_all?'.$getRequest) }}">
                            Total: {{ $home_info['total_return_req'] }}
                        </a>
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_return_req'] != 0 && $home_info['panding_return_req'] != 0) {
                                $pending_return = ($home_info['panding_return_req'] / $home_info['total_return_req']) * 100;
                            } else {
                                $pending_return = 0;
                            }
                            ?>

                            <div class="number pending" data-percent="{{ $pending_return }}">
                                <span>{{ sprintf('%0.2f', $pending_return) }}</span>% </div>
                            <a class="title" href="#"> Pending ({{$home_info['panding_return_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_return_req'] != 0 && $home_info['processing_return_req'] != 0) {
                                $processing_return = ($home_info['processing_return_req'] / $home_info['total_return_req'] ) * 100;
                            } else {
                                $processing_return = 0;
                            }
                            ?>

                            <div class="number processing" data-percent="{{ $processing_return }}">

                                <span>{{ sprintf('%0.2f', $processing_return) }}</span>% </div>
                            <a class="title" href=""> Processing ({{$home_info['processing_return_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_return_req'] != 0 && $home_info['success_return_req'] != 0) {
                                $success_return = ($home_info['success_return_req'] / $home_info['total_return_req']) * 100;
                            } else {
                                $success_return = 0;
                            }
                            ?>

                            <div class="number success" data-percent="{{ $success_return }}">

                                <span>{{ sprintf('%0.2f', $success_return) }}</span>% </div>
                            <a class="title" href="#"> Success ({{$home_info['success_return_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($home_info['total_return_req'] != 0 && $home_info['failed_return_req'] != 0) {
                                $failed_return = ($home_info['failed_return_req'] / $home_info['total_return_req'] ) * 100;
                            } else {
                                $failed_return = 0;
                            }
                            ?>

                            <div class="number failed" data-percent="{{ $failed_return }}">

                                <span>{{ sprintf('%0.2f', $failed_return) }}</span>% </div>
                            <a class="title" href="#"> Failed ({{$home_info['failed_return_req']}})
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">New Merchants</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $home_info['new_merchant_count'] }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('merchant') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>

    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">New Orders</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $home_info['new_order_count'] }}">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('order') }}" >
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
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption ">
                    <span class="caption-subject font-dark bold uppercase">Activities</span>
                    <span class="caption-helper">From {{ $from_date }} to {{ $to_date }}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_amchart_4" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">

    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('dashboard', 'dashboard');

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = array();} ?>
        $('#merchant_id').select2().val([{!! implode(",", $_GET['merchant_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['hub_id'])){$_GET['hub_id'] = array();} ?>
        $('#hub_id').select2().val([{!! implode(",", $_GET['hub_id']) !!}]).trigger("change");
    });
</script>

<script src="{{URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

<script src="{{URL::asset('assets/global/plugins/amcharts/amcharts/amcharts.js')}}" type="text/javascript"></script>
<script src="{{URL::asset('assets/global/plugins/amcharts/amcharts/pie.js')}}" type="text/javascript"></script>
<script src="{{URL::asset('assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js')}}" type="text/javascript"></script>

<script src="{{URL::asset('assets/global/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{URL::asset('assets/global/plugins/counterup/jquery.counterup.min.js')}}" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/pages/scripts/dashboard.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->


<script type="text/javascript">
    var Dashboard = function() {

        return {

            initAmChart4: function() {
                if (typeof(AmCharts) === 'undefined' || $('#dashboard_amchart_4').size() === 0) {
                    return;
                }

                <?php $hubAllStatus = hubAllStatus(); ?>

                var chart = AmCharts.makeChart("dashboard_amchart_4", {
                    "type": "pie",
                    "theme": "light",
                    "path": "assets/global/plugins/amcharts/ammap/images/",
                    "dataProvider": [
                    @foreach($hubAllStatus AS $key => $item)
                    {
                        "country": "{{ $item }}",
                        "value": {{ countStatusAll($key, $from_date, $to_date) }}
                    },
                    @endforeach
                    ],
                    "valueField": "value",
                    "titleField": "country",
                    "outlineAlpha": 0.4,
                    "depth3D": 15,
                    "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                    "angle": 30,
                    "export": {
                        "enabled": true
                    }
                });
                jQuery('.chart-input').off().on('input change', function() {
                    var property = jQuery(this).data('property');
                    var target = chart;
                    var value = Number(this.value);
                    chart.startDuration = 0;

                    if (property == 'innerRadius') {
                        value += "%";
                    }

                    target[property] = value;
                    chart.validateNow();
                });
            },

            initEasyPieCharts: function() {
                if (!jQuery().easyPieChart) {
                    return;
                }

                $('.easy-pie-chart .number.success').easyPieChart({
                    animate: 1000,
                    size: 75,
                    lineWidth: 3,
                    barColor: App.getBrandColor('green')
                });

                $('.easy-pie-chart .number.partial').easyPieChart({
                    animate: 1000,
                    size: 75,
                    lineWidth: 3,
                    barColor: App.getBrandColor('yellow')
                });

                $('.easy-pie-chart .number.failed').easyPieChart({
                    animate: 1000,
                    size: 75,
                    lineWidth: 3,
                    barColor: App.getBrandColor('red')
                });

                $('.easy-pie-chart .number.pending').easyPieChart({
                    animate: 1000,
                    size: 75,
                    lineWidth: 3,
                    barColor: App.getBrandColor('blue')
                });

            },

            init: function() {

                // this.initJQVMAP();
                // this.initCalendar();
                // this.initCharts();
                this.initEasyPieCharts();
                // this.initSparklineCharts();
                // this.initChat();
                // this.initDashboardDaterange();
                // this.initMorisCharts();

                // this.initAmChart1();
                // this.initAmChart2();
                // this.initAmChart3();
                this.initAmChart4();

                // this.initWorldMapStats();
            }

        };

    }();

</script>

@endsection
