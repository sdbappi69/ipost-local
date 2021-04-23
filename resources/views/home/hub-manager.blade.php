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
        {{-- <li>
            <a href="{{ URL::to('home') }}">Home</a>
<i class="fa fa-circle"></i>
        </li> --}}
        <li>
            <span>Dashboard</span>
        </li>
    </ul>

    {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}

    <div class="form-group col-md-5 pull-right" style="padding-top: 5px; margin-bottom: 5px;"> 
        <div class="col-md-10">
            <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd">
                <input type="text" value="{{ $from_date }}" class="form-control from_date" name="from_date">
                <span class="input-group-addon"> to </span>
                <input type="text" value="{{ $to_date }}" class="form-control to_date" name="to_date"> </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="form-control btn btn-primary pull-right"><i class="fa fa-search"></i></button>
        </div>
    </div>

    {!! Form::close() !!}

</div>

<br>

<div class="row">

    <div class="col-md-6 col-sm-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-cursor font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">PickUp</span>
                    <span class="caption-helper">Total: {{ $total_pickup_req }}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_pickup_req != 0 && $panding_pickup_req != 0) {
                                $pending_pickup = ($panding_pickup_req / $total_pickup_req) * 100;
                            } else {
                                $pending_pickup = 0;
                            }
                            ?>

                            <div class="number pending" data-percent="{{ $pending_pickup }}">
                                <span>{{ sprintf('%0.2f', $pending_pickup) }}</span>% </div>
                            <a class="title" href="javascript:;"> Pending
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_pickup_req != 0 && $processing_pickup_req != 0) {
                                $processing_pickup = ($processing_pickup_req / $total_pickup_req) * 100;
                            } else {
                                $processing_pickup = 0;
                            }
                            ?>

                            <div class="number partial" data-percent="{{ $processing_pickup }}">

                                <span>{{ sprintf('%0.2f', $processing_pickup) }}</span>% </div>
                            <a class="title" href="javascript:;"> Processing
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_pickup_req != 0 && $success_pickup_req != 0) {
                                $success_pickup = ($success_pickup_req / $total_pickup_req) * 100;
                            } else {
                                $success_pickup = 0;
                            }
                            ?>

                            <div class="number success" data-percent="{{ $success_pickup }}">

                                <span>{{ sprintf('%0.2f', $success_pickup) }}</span>% </div>
                            <a class="title" href="javascript:;"> Success
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>                      
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_pickup_req != 0 && $failed_pickup_req != 0) {
                                $failed_pickup = ($failed_pickup_req / $total_pickup_req ) * 100;
                            } else {
                                $failed_pickup = 0;
                            }
                            ?>

                            <div class="number failed" data-percent="{{ $failed_pickup }}">

                                <span>{{ sprintf('%0.2f', $failed_pickup) }}</span>% </div>
                            <a class="title" href="javascript:;"> Failed
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
                    <span class="caption-helper">Total: {{ $total_delivery_req }}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">

                    @if(strtotime($from_date) > strtotime('2017-06-05'))

                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_delivery_req != 0 && $panding_delivery_req != 0) {
                                $pending_delivery = ($panding_delivery_req / $total_delivery_req) * 100;
                            } else {
                                $pending_delivery = 0;
                            }
                            ?>

                            <div class="number pending" data-percent="{{ $pending_delivery }}">
                                <span>{{ sprintf('%0.2f', $pending_delivery) }}</span>% </div>
                            <a class="title" href="javascript:;"> Pending
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_delivery_req != 0 && $processing_delivery_req != 0) {
                                $processing_delivery = ($partial_delivery_req / $processing_delivery_req) * 100;
                            } else {
                                $processing_delivery = 0;
                            }
                            ?>

                            <div class="number partial" data-percent="{{ $processing_delivery }}">

                                <span>{{ sprintf('%0.2f', $processing_delivery) }}</span>% </div>
                            <a class="title" href="javascript:;"> Processing
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_delivery_req != 0 && $success_delivery_req != 0) {
                                $success_delivery = ($success_delivery_req / $total_delivery_req) * 100;
                            } else {
                                $success_delivery = 0;
                            }
                            ?>

                            <div class="number success" data-percent="{{ $success_delivery }}">

                                <span>{{ sprintf('%0.2f', $success_delivery) }}</span>% </div>
                            <a class="title" href="javascript:;"> Success
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_delivery_req != 0 && $failed_delivery_req != 0) {
                                $failed_delivery = ($failed_delivery_req / $total_delivery_req ) * 100;
                            } else {
                                $failed_delivery = 0;
                            }
                            ?>

                            <div class="number failed" data-percent="{{ $failed_delivery }}">

                                <span>{{ sprintf('%0.2f', $failed_delivery) }}</span>% </div>
                            <a class="title" href="javascript:;"> Failed
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
                    <span class="caption-helper">Total: {{ $total_return_req }}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_return_req != 0 && $panding_return_req != 0) {
                                $pending_return = ($panding_return_req / $total_return_req) * 100;
                            } else {
                                $pending_return = 0;
                            }
                            ?>

                            <div class="number pending" data-percent="{{ $pending_return }}">
                                <span>{{ sprintf('%0.2f', $pending_return) }}</span>% </div>
                            <a class="title" href="javascript:;"> Pending
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>                    
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_return_req != 0 && $processing_return_req != 0) {
                                $processing_return = ($total_return_req / $processing_return_req) * 100;
                            } else {
                                $processing_return = 0;
                            }
                            ?>

                            <div class="number partial" data-percent="{{ $processing_return }}">

                                <span>{{ sprintf('%0.2f', $processing_return) }}</span>% </div>
                            <a class="title" href="javascript:;"> Processing
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_return_req != 0 && $success_return_req != 0) {
                                $success_return = ($success_return_req / $total_return_req) * 100;
                            } else {
                                $success_return = 0;
                            }
                            ?>

                            <div class="number success" data-percent="{{ $success_return }}">

                                <span>{{ sprintf('%0.2f', $success_return) }}</span>% </div>
                            <a class="title" href="javascript:;"> Success
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm"> </div>
                    <div class="col-md-3">
                        <div class="easy-pie-chart">

                            <?php
                            if ($total_return_req != 0 && $failed_return_req != 0) {
                                $failed_return = ($failed_return_req / $total_return_req ) * 100;
                            } else {
                                $failed_return = 0;
                            }
                            ?>

                            <div class="number failed" data-percent="{{ $failed_return }}">

                                <span>{{ sprintf('%0.2f', $failed_return) }}</span>% </div>
                            <a class="title" href="javascript:;"> Failed
                                {{-- <i class="icon-arrow-right"></i> --}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Verify & picked</h4>
            <div class="widget-thumb-wrap">
                {{-- <i class="widget-thumb-icon bg-green icon-bulb"></i> --}}
                <div class="widget-thumb-body">
                    {{-- <span class="widget-thumb-subtitle">Product</span> --}}
                    <span class="widget-thumb-body-stat" data-counter="counterup" id="receive_prodcut_widget" data-value="0">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('receive-prodcut') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Pending Consignment</h4>
            <div class="widget-thumb-wrap">
                {{-- <i class="widget-thumb-icon bg-green icon-bulb"></i> --}}
                <div class="widget-thumb-body">
                    {{-- <span class="widget-thumb-subtitle">Product</span> --}}
                    <span class="widget-thumb-body-stat" data-counter="counterup" id="consignment_widget" data-value="0">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('v2consignment') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">ready to make trip</h4>
            <div class="widget-thumb-wrap">
                {{-- <i class="widget-thumb-icon bg-green icon-bulb"></i> --}}
                <div class="widget-thumb-body">
                    {{-- <span class="widget-thumb-subtitle">Product</span> --}}
                    <span class="widget-thumb-body-stat" data-counter="counterup" id="trip_widget" data-value="0">0</span>
                </div>

                <br><br>

                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ URL::to('trip/create') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
            </div>
        </div>
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

    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('dashboard', 'dashboard');
    });

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
                                "value": {{ countStatus($key, $from_date, $to_date) }}
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
