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
                <a href="{{ secure_url('user') }}">User</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Users
        <small>create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        <div class="row step-background-thin">
            <div class="col-md-3 bg-grey-steel mt-step-col active">
                <div class="mt-step-number">1</div>
                <div class="mt-step-title uppercase font-grey-cascade">Basic</div>
                <div class="mt-step-content font-grey-cascade">Personal information</div>
            </div>
            <div class="col-md-3 bg-grey-steel mt-step-col">
                <div class="mt-step-number">2</div>
                <div class="mt-step-title uppercase font-grey-cascade">Reference</div>
                <div class="mt-step-content font-grey-cascade">Choose user type</div>
            </div>
            <div class="col-md-3 bg-grey-steel mt-step-col">
                <div class="mt-step-number">3</div>
                <div class="mt-step-title uppercase font-grey-cascade">Photo</div>
                <div class="mt-step-content font-grey-cascade">Upload profile photo</div>
            </div>
            <div class="col-md-3 bg-grey-steel mt-step-col">
                <div class="mt-step-number">4</div>
                <div class="mt-step-title uppercase font-grey-cascade">Password</div>
                <div class="mt-step-content font-grey-cascade">Set a Password</div>
            </div>
        </div>

            {!! Form::open(array('url' => secure_url('') . '/user', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Name</label>
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Email</label>
                            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-12 np-lr">Mobile Number</label>
                            <!-- <div class="col-md-2 np-lr">
                                {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                            </div>
                            <div class="col-md-10 np-lr"> -->
                                {!! Form::text('msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                            <!-- </div> -->
                        </div>

                        <div class="form-group">

                            <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
                            <!-- <div class="col-md-2 np-lr">
                                {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-10 np-lr"> -->
                                {!! Form::text('alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                            <!-- </div> -->
                            
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">
                        
                        <div class="form-group">
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

                        <div class="form-group">
                            <label class="control-label">Select Zone</label>
                            {!! Form::select('zone_id', array(''=>'Select Zone'), null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Address</label>
                            {!! Form::text('address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
                        </div>

                    </div>

                </div>

                &nbsp;
                <div class="row padding-top-10">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('user-add', 'users');

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

    </script>

@endsection
