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
                <a href="{{ URL::to('merchant-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Order
        <small> create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        <div class="row step-background-thin animated bounceInDown">
            <div class="col-md-6 bg-grey-steel mt-step-col active">
                <div class="mt-step-number">1</div>
                <div class="mt-step-title uppercase font-grey-cascade">Order</div>
                <div class="mt-step-content font-grey-cascade">Order information</div>
            </div>
            <div class="col-md-6 bg-grey-steel mt-step-col">
                <div class="mt-step-number">2</div>
                <div class="mt-step-title uppercase font-grey-cascade">Confirmation</div>
                <div class="mt-step-content font-grey-cascade">Order confirm</div>
            </div>
        </div>

        <div class="portlet light tasks-widget bordered">
            <div class="portlet-body util-btn-margin-bottom-5">

                {!! Form::open(array('url' => '/merchant-orderv2', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-4 animated bounceInLeft">

                        <div class="form-group">
                            <label class="control-label">Merchant Order ID*</label>
                            {!! Form::text('merchant_order_id', null, ['class' => 'form-control', 'placeholder' => 'Merchant Order ID', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Store*</label>
                            {!! Form::select('store_id', $stores, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'store_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Customer Name*</label>
                            {!! Form::text('delivery_name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-4 animated bounceInUp">

                        <div class="form-group">
                            <label class="control-label col-md-12 np-lr">Customer Mobile Number*</label>
                            {!! Form::text('delivery_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Customer Email</label>
                            {!! Form::email('delivery_email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Remarks</label>
                            {!! Form::text('order_remarks', null, ['class' => 'form-control', 'placeholder' => 'Remarks']) !!}
                        </div>

                    </div>

                    <div class="col-md-4 animated bounceInRight">
                        {!! Form::hidden('delivery_zone_id', null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
                        {!! Form::hidden('delivery_address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required', 'id' => 'address']) !!}
                        {!! Form::hidden('delivery_latitude', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'latitude']) !!}
                        {!! Form::hidden('delivery_longitude', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'longitude']) !!}

                        <button type="submit" class="btn blue btn-block btn-lg m-icon-big">
                            Create Order
                            <i class="m-icon-big-swapright m-icon-white"></i>
                        </button>

                        <a href="javascript:history.back()" class="btn default btn-block btn-lg m-icon-big">
                            Cancel Order
                            <i class="m-icon-big-swapleft m-icon-white"></i>
                        </a>

                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" id="map-search" style="margin-top: 10px; height: 25px; width: 400px;"
                               autocomplete="off">
                        <div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>

    </div>

    <script type="text/javascript">

        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('merchant-order-add', 'merchant-orders');
        });

    </script>
    <script src="http://maps.google.com/maps/api/js?libraries=places&region=uk&language=en&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>
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

            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
                this.setZoom(14);
                google.maps.event.removeListener(boundsListener);
            });

            marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: image
            });

            var geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function (event) {
                // $('#addll').text(event.latLng);
                let lat = event.latLng.lat();
                let lng = event.latLng.lng();
                $('#latitude').val(lat);
                $('#longitude').val(lng);

                set_zone_id(lat, lng);

                marker.setPosition(event.latLng);
                geocoder.geocode({
                    'latLng': event.latLng
                }, function (results, status) {
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
            map.addListener('bounds_changed', function () {
                searchBox.setBounds(map.getBounds());
            });

            var markers = [];
            searchBox.addListener('places_changed', function () {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach(function (marker) {
                    marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function (place) {
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
