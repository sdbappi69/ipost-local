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
                <span>Trips</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Trips
        <small> view</small>
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

                    <?php if(!isset($_GET['unique_trip_id'])){$_GET['unique_trip_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         <input type="text" value="{{$_GET['unique_trip_id']}}" class="form-control focus_it" name="unique_trip_id" id="unique_trip_id" placeholder="Sub-Order ID">
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['vehicle_id'])){$_GET['vehicle_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('vehicle_id',['' => 'All Vehicles']+$vehicles,$_GET['vehicle_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'vehicle_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['source_hub_id'])){$_GET['source_hub_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('source_hub_id', array(''=>'All Start Hub')+$hubs,$_GET['source_hub_id'], ['class' => 'form-control js-example-basic-single','id' => 'source_hub_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['destination_hub_id'])){$_GET['destination_hub_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('destination_hub_id', array(''=>'All End Hub')+$hubs,$_GET['destination_hub_id'], ['class' => 'form-control js-example-basic-single','id' => 'destination_hub_id']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['trip_status'])){$_GET['trip_status'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('trip_status', array(''=>'All Status')+$status,$_GET['trip_status'], ['class' => 'form-control js-example-basic-single','id' => 'trip_status']) !!}
                        <!-- </div> -->
                    </div>

                    <?php if(!isset($_GET['driver_id'])){$_GET['driver_id'] = null;} ?>
                    <div class="col-md-4" style="margin-bottom:5px;">
                        <!-- <div class="row"> -->
                         {!! Form::select('driver_id', array(''=>'All Drivers')+$drivers,$_GET['driver_id'], ['class' => 'form-control js-example-basic-single','id' => 'driver_id']) !!}
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

    @if(count($trips) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <!-- <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Trips</span>
                        <span class="caption-helper">list</span>
                    </div>
                </div> -->
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover">
                        <thead class="flip-content">
                            <th>Trip Id</th>
                            <th>Vehicle Type</th>
                            <th>Vehicle</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Responsible</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach($trips as $trip)
                                <tr>
                                    <td>{{ $trip->unique_trip_id }}</td>
                                    <td>{{ $trip->vehicle_type->title or '' }}</td>
                                    <td>{{ $trip->vehicle->name or '' }}</td>
                                    <td>{{ $trip->source_hub->title or '' }}</td>
                                    <td>{{ $trip->destination_hub->title or '' }}</td>
                                    <td>
                                        <!-- <a target="_blank" href="{{ URL::to('user') }}/{{ $trip->responsible_user->id }}"> -->
                                            {{ $trip->responsible_user->name or '' }}
                                        <!-- </a> -->
                                    </td>
                                    <td>
                                        {{ $trip->driver->name or 'Not defined' }}
                                    </td>
                                    <td>
                                        @if($trip->trip_status == 1)
                                            Waiting
                                        @elseIf($trip->trip_status == 2)
                                            In Transit
                                        @elseIf($trip->trip_status == 3)
                                            Reched
                                        @endIf
                                    </td>
                                    <td>{{ $trip->created_at }}</td>
                                    <td>
                                        <a target="_blank" class="btn btn-info btn-xs" target="_blank" href="{{url('triprunsheet/'.$trip->id)}}">Run Sheet</a>

                                        <a target="_blank" class="btn green btn-xs" href="{{ URL::to('trip') }}/{{ $trip->id }}">View</a>

                                        @if(auth()->user()->reference_id == $trip->source_hub_id && $trip->trip_status == 1)

                                            <a class="btn btn-primary btn-xs" href="{{ URL::to('trip') }}/{{ $trip->id }}/edit">Load</a>

                                            <a class="btn yellow btn-xs" href="{{ URL::to('trip_start') }}/{{ $trip->id }}">Start</a>

                                            <a class="btn red btn-xs" href="{{ URL::to('trip_cancel') }}/{{ $trip->id }}">Cancel</a>

                                        @endIf

                                        @if(auth()->user()->reference_id == $trip->destination_hub_id && $trip->trip_status == 2)
                                            <a class="btn red btn-xs" href="{{ URL::to('trip_end') }}/{{ $trip->id }}">End</a>
                                        @endIf

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $trips->render() }}
                    </div>
                </div>
            </div>
        </div>
    @endIf

    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('trip-manage', 'trips');

            $('#example0').DataTable({
                "order": [],
            });
        });
    </script>

@endsection
