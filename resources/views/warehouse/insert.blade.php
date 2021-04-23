@extends('layouts.appinside')

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL::to('warehouse') }}">Pick-up location</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Pick-up location
        <small>create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">

            {!! Form::open(array('url' => '/warehouse', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        {!! Form::hidden('merchant_id', Auth::user()->reference_id, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}

                        <div class="form-group">
                            <label class="control-label">Title</label>
                            {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Email</label>
                            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">
                        
{{--                         <div class="form-group">
                            <label class="control-label">Select Country</label>
                            {!! Form::select('country_id', $countries, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'country_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select State</label>
                            {!! Form::select('state_id', array(''=>'Select State'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'state_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select City</label>
                            {!! Form::select('city_id', array(''=>'Select City'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'city_id']) !!}
                        </div>
 --}}
                        <div class="form-group">
                            <label class="control-label">Select Zone</label>
                            {!! Form::select('zone_id', array(''=>'Select Zone'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-12 np-lr">Mobile Number</label>
                            <div class="col-md-2 np-lr">
                                {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                            </div>
                            <div class="col-md-10 np-lr">
                                {!! Form::text('msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
                            <div class="col-md-2 np-lr">
                                {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-10 np-lr">
                                {!! Form::text('alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                            </div>    
                        </div>

                        <div class="form-group">
                            <label class="control-label">Address</label>
                            {!! Form::text('address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required', 'id' => 'address']) !!}
                        </div>
                    </div>

                    {!! Form::hidden('latitude', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'latitude']) !!}
                    {!! Form::hidden('longitude', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'longitude']) !!}

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <input type="text" id="map-search" style="margin-top: 10px; height: 25px; width: 400px;" autocomplete="off">
                        <div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>                        
                    </div>
                </div>

                &nbsp;
                <div class="row padding-top-10">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}


    </div>


{{--     <script src="
https://maps.google.com/maps/api/js?key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM&callback=initMap&sensor=false" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/gmaps/gmaps.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('custom/js/maps-google-geo.js') }}" type="text/javascript"></script>
 --}}
    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('warehouse-add', 'warehouses');

            // Get State list
            var country_id = $('#country_id').val();
            get_states(country_id);
        });

        // Get State list On Country Change
        $('#country_id').on('change', function() {
            get_states($(this).val());
        });

        // Get City list On State Change
        $('#state_id').on('change', function() {
            get_cities($(this).val());
        });

        // Get Zone list On City Change
        $('#city_id').on('change', function() {
            get_zones($(this).val());
        });

        // // Get Full Address On Address Change
        // $('#address').on('change', function() {
        //     var country = $("#country_id option:selected").text();
        //     var state = $("#state_id option:selected").text();
        //     var city = $("#city_id option:selected").text();
        //     var zone = $("#zone_id option:selected").text();
        //     var address = $("#address").val();
        //     var full_address = address+', '+zone+', '+city+', '+state+', '+country;
            
        //     $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?address=' + full_address + '&sensor=false', null, function (data) {
        //         var p = data.results[0].geometry.location
        //         // var latlng = new google.maps.LatLng(p.lat, p.lng);
        //         $("#gmap_geocoding_address").val(p.lat+', '+p.lng);
        //         $("#latitude").val(p.lat);
        //         $("#longitude").val(p.lng);
        //         // alert(latlng);
        //     });

        //     $("#gmap_geocoding_btn").trigger("click");
        //     // alert(full_address);
        // });

    </script>
<script src="http://maps.google.com/maps/api/js?libraries=places&region=uk&language=en&sensor=true&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>
<script>
    $(function () {
      var image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png',
          bounds = new google.maps.LatLngBounds();
          mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
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
          position = new google.maps.LatLng(36.1901046, 44.0080293);
          map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

      bounds.extend(position),
      map.fitBounds(bounds);

      var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
            this.setZoom(14);
            google.maps.event.removeListener(boundsListener);
          });

      marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: image
      });

      var geocoder = new google.maps.Geocoder();
      google.maps.event.addListener(map, 'click', function(event) {
        // $('#addll').text(event.latLng);
        let lat = event.latLng.lat();
        let lng = event.latLng.lng();
        $('#latitude').val(lat);
        $('#longitude').val(lng);

        get_zone_bound(lat, lng);

        marker.setPosition(event.latLng);
        geocoder.geocode({
          'latLng': event.latLng
        }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                $("#address").val(results[0].formatted_address);
            }
          }
        });
      });


      /*PLACE SEARCH*/
      var input = document.getElementById('map-search');
      var searchBox = new google.maps.places.SearchBox(input);
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      // Bias the SearchBox results towards current map's viewport.
      map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
      });

      var markers = [];
      searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
          return;
        }

        // Clear out the old markers.
        markers.forEach(function(marker) {
          marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
          if (!place.geometry) {
            console.log("Returned place contains no geometry");
            return;
          }
          var icon = {
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(25, 25)
          };

          // Create a marker for each place.
          markers.push(new google.maps.Marker({
            map: map,
            icon: icon,
            title: place.name,
            position: place.geometry.location
          }));

          if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
          } else {
            bounds.extend(place.geometry.location);
          }
        });
        map.fitBounds(bounds);
      });
    });
</script>
@endsection
