@extends('layouts.appinside')

@section('select2CSS')
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" /> -->
@endsection

@section('select2JS')
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script> -->
<script src="{!! asset('js/locations.dropdown.js') !!}"></script>
@endsection

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ URL::to('home') }}">Home</a>
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
    <small> update</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="row">
    {!! Form::model($zone, ['route' => ['zone.update', $zone->id], 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true]) !!}
    <div class="col-md-12">
        @include('flash::message')

        <div class="panel panel-danger">
            <div class="panel-heading">
                <i class="fa fa-flag"></i> {!! $title !!}

                <a href="{!! url('zone') !!}" class="pull-right text-danger">
                    <i class="fa fa-backward"></i> Back
                </a>
            </div>
            <div class="panel-body small">
                <div class="form-group">
                    {!! Form::label('country_id', 'Country*', ['class' => 'col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::select('country_id', ['' => 'SELECT ONE']+$countries, $zone->city->state->country->id, ['class' => 'form-control js-example-basic-single js-country', 'data-type' => 'Country', 'required' => 'required']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('state_id', 'State*', ['class' => 'col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::select('state_id', ['' => 'SELECT ONE']+$zone->city->state->lists('name', 'id')->toArray(), $zone->city->state->id, ['class' => 'form-control js-example-basic-single js-state', 'data-type' => 'State', 'required' => 'required']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('city_id', 'City*', ['class' => 'col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::select('city_id', ['' => 'SELECT ONE']+$zone->city->lists('name', 'id')->toArray(), $zone->city_id, ['class' => 'form-control js-example-basic-single js-city', 'data-type' => 'City', 'required' => 'required']) !!}
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

                <!-- <div class="form-group">
                    {!! Form::label('zone_genre_id', 'Tier', ['class' => 'col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::select('zone_genre_id', ['' => 'SELECT ONE']+$zone_genres, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                    </div>
                </div> -->

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
                        {!! Form::text('coordinates', isset($zone->map->coordinates) ? $zone->map->coordinates : '', ['class' => 'form-control', 'readonly'=>'readonly']) !!}
                    </div>
                </div>

                <div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>

                <hr>

                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fa fa-edit"></i> Update Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

<script type="text/javascript">
$(document).ready(function () {
    // Navigation Highlight
    highlight_nav('zone', 'locations');
});
</script>

@if($zone->map)
<script>
    $(function () {
        var coords = reWicket("{{ $zone->map->coordinates or '' }}");
//         alert(coords);
        var points = coords.split(',');
        var paths = [];
        for (var i in points) {
            if (i % 2 == 1)
                continue;
            paths.push({lat: parseFloat(points[i++]), lng: parseFloat(points[i])});
        }

        var map;
        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: {lat: 36.1911401, lng: 44.0090357},
            panControl: true,
            panControlOptions: {
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.TOP_left
            }
        },
                image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png',
                map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);


        var position = new google.maps.LatLng(paths[0]["lat"], paths[0]["lng"]);
        bounds.extend(position);

        var polygon = new google.maps.Polygon({
            paths: paths,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            editable: true
        });
        polygon.setMap(map);
        map.fitBounds(bounds);

        google.maps.event.addListener(polygon.getPath(), 'remove_at', function () {
            getCoordinates(polygon.getPath());
        });
        google.maps.event.addListener(polygon.getPath(), 'set_at', function () {
            getCoordinates(polygon.getPath());
        });
        google.maps.event.addListener(polygon.getPath(), 'insert_at', function () {
            getCoordinates(polygon.getPath());
        });

        var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
            this.setZoom(14);
            google.maps.event.removeListener(boundsListener);
        });

        function getCoordinates(vertices) {
            // var vertices = drawingManager.getPath();
            var coordinates = "";
            for (var i = 0; i < vertices.getLength(); i++) {
                var xy = vertices.getAt(i);
                if (i > 0)
                    coordinates += ",";
                coordinates += xy.lat() + "," + xy.lng();
            }
            document.getElementById('coordinates').value = wicket(coordinates);
        }

        function wicket(coordinates) {
            let coordinatesArr = coordinates.split(',');
            let ret = "";
            for (var i = 0; i < coordinatesArr.length; i += 2) {
                ret += coordinatesArr[i] + ' ' + coordinatesArr[i + 1] + ',';
            }
            ret += coordinatesArr[0] + ' ' + coordinatesArr[1];
            return ret;
        }

        function reWicket(coordinates) {
            let coordinatesArr = coordinates.split(',');
            let ret = "";
            for (var i = 0; i < coordinatesArr.length; i++) {
                let twoCoords = coordinatesArr[i].split(' ');
                ret += twoCoords[0] + ', ' + twoCoords[1];
                if (i + 1 < coordinatesArr.length)
                    ret += ',';
            }
            return ret;
        }
    });
</script>
@else
<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            center: {lat: 36.1911401, lng: 44.0090357},
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
        });

        var drawingManager = new google.maps.drawing.DrawingManager({
            polygonOptions: {
                fillOpacity: 0.2,
                strokeWeight: 3,
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
    }

    function getCoordinates(vertices) {
        // var vertices = drawingManager.overlay.getPath();
        var coordinates = "";
        for (var i = 0; i < vertices.getLength(); i++) {
            var xy = vertices.getAt(i);
            if (i > 0)
                coordinates += ",";
            coordinates += xy.lat() + "," + xy.lng();
        }
        document.getElementById('coordinates').value = wicket(coordinates);
    }

    function wicket(coordinates) {
        let coordinatesArr = coordinates.split(',');
        let ret = "";
        for (var i = 0; i < coordinatesArr.length; i += 2) {
            ret += coordinatesArr[i] + ' ' + coordinatesArr[i + 1] + ',';
        }
        ret += coordinatesArr[0] + ' ' + coordinatesArr[1];
        return ret;
    }
</script>
@endif

<script src="http://maps.google.com/maps/api/js?libraries=places&region=uk&libraries=drawing&callback=initMap&language=en&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>
@endsection