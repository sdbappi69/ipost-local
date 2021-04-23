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
                                <label class="control-label">Remarks</label>
                                {!! Form::text('order_remarks', null, ['class' => 'form-control', 'placeholder' => 'Remarks']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Customer Name*</label>
                                {!! Form::text('delivery_name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Customer Email</label>
                                {!! Form::email('delivery_email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-12 np-lr">Customer Mobile Number*</label>
                                {!! Form::text('delivery_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                            </div>

                        </div>

                        <div class="col-md-4 animated bounceInUp">

                            <div class="form-group">
                                <label class="control-label">Select Customer Zone*</label>
                                {!! Form::select('delivery_zone_id', array(''=>'Select Zone')+$zones, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Customer Address*</label>
                                {!! Form::text('delivery_address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required', 'id' => 'address']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">GEO Location</label>
                                <!-- BEGIN MARKERS PORTLET-->
                                <div class="portlet light portlet-fit bordered">
                                    <!-- <form class="form-inline margin-bottom-10" action="#"> -->
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="GEO Location">
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

                        </div>

                        <div class="col-md-4 animated bounceInRight">
                            
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

                {!! Form::close() !!}

            </div>
        </div>

    </div>

    <script src="
https://maps.google.com/maps/api/js?key=AIzaSyCBWhNYtf2cofZBppq9lfBqzGpJDjLBc4g&callback=initMap&sensor=false" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/gmaps/gmaps.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('custom/js/maps-google-geo.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-order-add', 'merchant-orders');
        });

        // Get Full Address On Address Change
        $('#zone_id').on('change', function() {

            if($('#zone_id').val() == ''){

                $("#gmap_geocoding_address").val('');
                $("#latitude").val('');
                $("#longitude").val('');

            }else{

                var zone = $("#zone_id option:selected").text();
                var full_address = zone;

                $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?address=' + full_address + '&sensor=false', null, function (data) {
                    var p = data.results[0].geometry.location;

                    if(p.lat == '' || p.lng == ''){
                        $("#gmap_geocoding_address").val('');
                        $("#latitude").val('');
                        $("#longitude").val('');
                    }else{
                        $("#gmap_geocoding_address").val(p.lat+', '+p.lng);
                        $("#latitude").val(p.lat);
                        $("#longitude").val(p.lng);
                    }

                });

                $("#gmap_geocoding_btn").trigger("click");

            }
        });

    </script>

@endsection
