@extends('layouts.appinside')

@section('select2CSS')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" />
@endsection

@section('select2JS')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
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
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-plus"></i> {!! $zone->name or '' !!}
                    <a href="{!! route('zone.edit', $zone->id) !!}" class="btn btn-sm btn-info pull-right">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Name</td>
                            <td class="text-warning">{!! $zone->name !!}</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td class="text-warning">{!! $zone->city->name !!}</td>
                        </tr>
                        <tr>
                            <td>State</td>
                            <td class="text-primary">{!! $zone->city->state->name !!}</td>
                        </tr>
                        <tr>
                            <td>Country</td>
                            <td class="text-danger">{!! $zone->city->state->country->name !!}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                {!! ($zone->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                            </td>
                        </tr>
                        </tr>
                    </table>
                    {{-- <input type="text" id="map-search" style="margin-top: 10px; height: 25px; width: 400px;"> --}}
                    <div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>
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

{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM&libraries=drawing&callback=initMap&language=en&sensor=true" --}}


    <script>
            $(function () {
                var coords = "{{ isset($zone->map->coordinates) ? str_replace(' ', ',', $zone->map->coordinates ) : '' }}";
                var points = coords.split(',');
                var paths = [];
                for (var i in points) {
                    if(i % 2 == 1)
                        continue;
                    paths.push({lat: parseFloat(points[i++]), lng: parseFloat(points[i])});
                }

                var map;
                var bounds = new google.maps.LatLngBounds();
                var mapOptions = {
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        panControl: true,
                        center: {lat: 36.1911401, lng: 44.0090357},
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

                if(coords !== ""){
                    var position = new google.maps.LatLng(paths[0]["lat"], paths[0]["lng"]);
                    bounds.extend(position);

                    var polygon = new google.maps.Polygon({
                          paths: paths,
                          strokeColor: '#FF0000',
                          strokeOpacity: 0.8,
                          strokeWeight: 2,
                          fillColor: '#FF0000',
                          fillOpacity: 0.35
                        });
                    polygon.setMap(map);
                    map.fitBounds(bounds);                    
                }

                var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
                        this.setZoom(14);
                        google.maps.event.removeListener(boundsListener);
                    });
            });
    </script>

    <script src="http://maps.google.com/maps/api/js?libraries=places&region=uk&language=en&sensor=true&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>

@endsection