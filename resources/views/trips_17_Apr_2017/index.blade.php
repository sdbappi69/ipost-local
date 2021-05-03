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
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Trip Id</th>
                            <th>Vehicle Type</th>
                            <th>Vehicle</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Responsible</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach($trips as $trip)
                                <tr>
                                    <td>{{ $trip->unique_trip_id }}</td>
                                    <td>{{ $trip->vehicle_type->title }}</td>
                                    <td>{{ $trip->vehicle->name }}</td>
                                    <td>{{ $trip->source_hub->title }}</td>
                                    <td>{{ $trip->destination_hub->title }}</td>
                                    <td>
                                        <!-- <a target="_blank" href="{{ secure_url('user') }}/{{ $trip->responsible_user->id }}"> -->
                                            {{ $trip->responsible_user->name }}
                                        <!-- </a> -->
                                    </td>
                                    <td>
                                        @if($trip->trip_status == 1)
                                            <a href="{{ secure_url('trip') }}/{{ $trip->id }}">Load</a>
                                        @elseIf($trip->trip_status == 2)
                                            <a href="{{ secure_url('trip') }}/{{ $trip->id }}">In Transit</a>
                                        @elseIf($trip->trip_status == 3)
                                            <a href="{{ secure_url('trip') }}/{{ $trip->id }}">Reched</a>
                                        @endIf
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $trips->appends($_REQUEST)->render() }}
                    </div>
                </div>
            </div>
        </div>
    @endIf

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
