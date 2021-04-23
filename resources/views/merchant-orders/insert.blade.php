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
        <div class="row step-background-thin">
            <div class="col-md-4 bg-grey-steel mt-step-col active">
                <div class="mt-step-number">1</div>
                <div class="mt-step-title uppercase font-grey-cascade">Basic</div>
                <div class="mt-step-content font-grey-cascade">Shipping information</div>
            </div>
            <div class="col-md-4 bg-grey-steel mt-step-col">
                <div class="mt-step-number">2</div>
                <div class="mt-step-title uppercase font-grey-cascade">Product</div>
                <div class="mt-step-content font-grey-cascade">products information</div>
            </div>
            <div class="col-md-4 bg-grey-steel mt-step-col">
                <div class="mt-step-number">3</div>
                <div class="mt-step-title uppercase font-grey-cascade">Charge</div>
                <div class="mt-step-content font-grey-cascade">Order confirm</div>
            </div>
        </div>

        {!! Form::open(array('url' => '/merchant-order', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Merchant Order ID</label>
                            {!! Form::text('merchant_order_id', null, ['class' => 'form-control', 'placeholder' => 'Merchant Order ID', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Store</label>
                            {!! Form::select('store_id', array(''=>'Select Store')+$stores, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'store_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Customer Name</label>
                            {!! Form::text('delivery_name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Customer Email</label>
                            {!! Form::email('delivery_email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-12 np-lr">Customer Mobile Number</label>
                            <div class="col-md-2 np-lr">
                                {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                            </div>
                            <div class="col-md-10 np-lr">
                                {!! Form::text('delivery_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="control-label col-md-12 np-lr">Customer Alt. Mobile Number</label>
                            <div class="col-md-2 np-lr">
                                {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-10 np-lr">
                                {!! Form::text('delivery_alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                            </div>
                            
                        </div>

                        <div class="form-group">
                            <label class="control-label">Remarks</label>
                            {!! Form::text('order_remarks', null, ['class' => 'form-control', 'placeholder' => 'Remarks']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Order Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label class="control-label">Select Customer Country</label>
                            {!! Form::select('delivery_country_id', $countries, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'country_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Customer State</label>
                            {!! Form::select('delivery_state_id', array(''=>'Select State'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'state_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Customer City</label>
                            {!! Form::select('delivery_city_id', array(''=>'Select City'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'city_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Customer Zone</label>
                            {!! Form::select('delivery_zone_id', array(''=>'Select Zone'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Customer Address</label>
                            {!! Form::text('delivery_address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required', 'id' => 'address']) !!}
                        </div>

                        <!-- BEGIN MARKERS PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <!-- <form class="form-inline margin-bottom-10" action="#"> -->
                                <div class="input-group">
                                    <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="address...">
                                    <span class="input-group-btn">
                                        <button class="btn blue" id="gmap_geocoding_btn">
                                            <i class="fa fa-search"></i>
                                        <!-- </a> -->
                                    </span>
                                </div>
                            <!-- </form> -->
                            <div id="gmap_geocoding" class="gmaps"> </div>
                        </div>
                        <!-- END MARKERS PORTLET-->

                    </div>

                    {!! Form::hidden('delivery_latitude', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'latitude']) !!}
                    {!! Form::hidden('delivery_longitude', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'longitude']) !!}

                </div>

                &nbsp;
                <div class="row padding-top-10">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}

    </div>

    <script src="
https://maps.google.com/maps/api/js?key=AIzaSyCBWhNYtf2cofZBppq9lfBqzGpJDjLBc4g&callback=initMap&sensor=false" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/gmaps/gmaps.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('custom/js/maps-google-geo.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-order-add', 'merchant-orders');

            // Get State list
            var country_id = $('#country_id').val();
            // alert(country_id);
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

        // Get Full Address On Address Change
        $('#address').on('change', function() {
            var country = $("#country_id option:selected").text();
            var state = $("#state_id option:selected").text();
            var city = $("#city_id option:selected").text();
            var zone = $("#zone_id option:selected").text();
            var address = $("#address").val();
            // var full_address = address+', '+zone+', '+city+', '+state+', '+country;
            var full_address = zone+', '+city+', '+state+', '+country;
            
            // console.log('https://maps.googleapis.com/maps/api/geocode/json?address=' + full_address + '&sensor=false');

            $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?address=' + full_address + '&sensor=false', null, function (data) {
                var p = data.results[0].geometry.location
                // var latlng = new google.maps.LatLng(p.lat, p.lng);
                $("#gmap_geocoding_address").val(p.lat+', '+p.lng);
                $("#latitude").val(p.lat);
                $("#longitude").val(p.lng);
                // alert(latlng);
            });

            $("#gmap_geocoding_btn").trigger("click");
            // alert(full_address);
        });

    </script>

@endsection
