@extends('layouts.appinside')

@section('select2CSS')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" />
@endsection

@section('select2JS')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
    <script src="{!! secure_asset('js/locations.dropdown.js') !!}"></script>
@endsection

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Zones</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Zones
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-danger">
                <div class="panel-heading" data-toggle="collapse" data-target="#addZone">
                    <i class="fa fa-plus"></i> Add New Zone
                </div>
                <div class="panel-body collapse" id="addZone">
                    {!! Form::open(['url' => secure_url('') . '/zone', 'class' => 'form-horizontal']) !!}

                    <div class="form-group">
                        {!! Form::label('country_id', 'Country*', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('country_id', ['' => 'SELECT ONE']+$countries, null, ['class' => 'form-control js-example-basic-single js-country', 'data-type' => 'Country', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('state_id', 'State*', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('state_id', ['' => 'SELECT ONE'], null, ['class' => 'form-control js-example-basic-single js-state', 'data-type' => 'State', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('city_id', 'City*', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('city_id', ['' => 'SELECT ONE'], null, ['class' => 'form-control js-example-basic-single js-city', 'data-type' => 'City', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('hub_id', 'Hub', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('hub_id', ['' => 'SELECT ONE']+$hubs, null, ['class' => 'form-control js-example-basic-single']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'Zone*', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('zip_code', 'Zip Code', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('zip_code', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status*', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::radio('status', 1, true) !!} &nbsp; Active &nbsp;
                            {!! Form::radio('status', 0, false) !!} &nbsp; De-active &nbsp;
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('coordinates', 'Coordinates', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('coordinates', null, ['class' => 'form-control', 'readonly'=>'readonly']) !!}
                        </div>
                    </div>

                    <script src="https://maps.googleapis.com/maps/api/js"></script>
                    {{-- <input type="text" id="map-search" style="margin-top: 10px; height: 25px; width: 400px;"> --}}
                    <div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>

                    <hr>

                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Now
                            </button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-map-marker"></i> {!! $title !!}
                </div>
                <div class="panel-body">

                    {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

                        <?php if(!isset($_GET['filter_name'])){$_GET['filter_name'] = null;} ?>
                        <div class="col-md-4" style="margin-bottom:5px;">
                             <input type="text" value="{{$_GET['filter_name']}}" class="form-control focus_it" name="filter_name" id="filter_name" placeholder="Name">
                        </div>

                        <?php if(!isset($_GET['filter_city_id'])){$_GET['filter_city_id'] = null;} ?>
                        <div class="col-md-4" style="margin-bottom:5px;">
                             {!! Form::select('filter_city_id', ['' => 'All Cities']+$cities, $_GET['filter_city_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'filter_city_id']) !!}
                        </div>

                        <?php if(!isset($_GET['filter_hub_id'])){$_GET['filter_hub_id'] = null;} ?>
                        <div class="col-md-4" style="margin-bottom:5px;">
                             {!! Form::select('filter_hub_id', array(''=>'All Hub')+$hubs,$_GET['filter_hub_id'], ['class' => 'form-control js-example-basic-single','id' => 'filter_hub_id']) !!}
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary filter-btn pull-right" style="width: 100%; margin-bottom:5px;"><i class="fa fa-search"></i> Filter</button>
                        </div>
                        <div class="clearfix"></div>

                    {!! Form::close() !!}

                    @include('flash::message')

                    <table class="table table-bordered table-hover table-responsive table-striped">
                        <thead>
                        <tr>
                            <th>Zone</th>
                            <!-- <th>Hub</th> -->
                            <th>City / District</th>
                            <th>State</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($zones) > 0)
                            @foreach($zones as $zone)
                                <tr>
                                    <td>{!! $zone->name !!}</td>
                                    <td class="text-warning">{!! $zone->city->name ?? '' !!}</td>
                                    <td class="text-primary">{!! $zone->city->state->name ?? '' !!}</td>
                                    <td class="text-danger">{!! $zone->city->state->country->name ?? '' !!}</td>
                                    <td>
                                        {!! ($zone->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                                    </td>
                                    <td>
                                        <a href="{!! secure_url('') . "/zone/$zone->id/edit" !!}" class="btn btn-sm btn-info">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <a href="{!! secure_url('') . "/zone/$zone->id" !!}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> view
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $zones->appends($_REQUEST)->render() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('zone', 'locations');
        });
    </script>

    <script>
        var bounds = new google.maps.LatLngBounds();

        var geocoder;
        var map;
        var polygons = [];

        function initialize() {
            map = new google.maps.Map(document.getElementById('map_canvas'), {
                center: {lat: 36.1911401, lng: 44.0090357},
                zoom: 8,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            var drawingManager = new google.maps.drawing.DrawingManager({
                polygonOptions: {
                    fillOpacity: 0.2,
                    strokeWeight: 3,
                    strokeColor: '#FF0000',
                    editable: true,
                    draggable: true,
                    zIndex: 1
                },
                map: map,
                drawingControl: false,
            });
            drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
            google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
                // When draw mode is set to null you can edit the polygon you just drawed
                getCoordinates(event.overlay.getPath());
                drawingManager.setDrawingMode(null);
                google.maps.event.addListener(event.overlay.getPath(), 'remove_at', function () {
                    getCoordinates(event.overlay.getPath());
                });
                google.maps.event.addListener(event.overlay.getPath(), 'set_at', function () {
                    getCoordinates(event.overlay.getPath());
                });
                google.maps.event.addListener(event.overlay.getPath(), 'insert_at', function () {
                    getCoordinates(event.overlay.getPath());
                });
            });

            // start multi polygon
                    @if(count($activeZones))
                    @foreach($activeZones as $index => $activeZone)
                <?php
                $coordinates = explode(",",$activeZone->map->coordinates);
                ?>
            var active_zone_{{$index}} = [
                        @foreach($coordinates as $coid => $coordinate)
                        new google.maps.LatLng({{str_replace(" ",", ",$coordinate)}}) <?php if($coid < (count($coordinates) - 1)){echo ',';} ?>
                        @endforeach
                ];
            polygons.push(new google.maps.Polygon({
                path: active_zone_{{$index}},
                geodesic: false,
                strokeColor: '#2F4F4F',
                strokeOpacity: 1.0,
                strokeWeight: 1,
                map: map
            }));
            @endforeach
            @endif
            // end multi polygon
            for (var j = 0; j < polygons.length; j++) {
                for (var i = 0; i < polygons[j].getPath().getLength(); i++) {
                    bounds.extend(polygons[j].getPath().getAt(i));
                }
            }

            map.fitBounds(bounds);
        }
        google.maps.event.addDomListener(window, "load", initialize);

        function getCoordinates(vertices){
          // var vertices = drawingManager.overlay.getPath();
          var coordinates = "";
          for (var i =0; i < vertices.getLength(); i++) {
            var xy = vertices.getAt(i);
            if(i > 0)
              coordinates += ",";
            coordinates += xy.lat()+","+xy.lng();
          }
          document.getElementById('coordinates').value = wicket(coordinates);
        }

        function wicket(coordinates){
            let coordinatesArr = coordinates.split(',');
            let ret = "";
            for (var i = 0; i < coordinatesArr.length; i += 2) {
                ret += coordinatesArr[i] + ' ' + coordinatesArr[i+1] + ',';
            }
            ret  += coordinatesArr[0] + ' ' + coordinatesArr[1];
            return ret;
        }
    </script>

    <script src="https://maps.google.com/maps/api/js?libraries=places&region=uk&libraries=drawing&callback=initMap&language=en&sensor=true&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>

@endsection
