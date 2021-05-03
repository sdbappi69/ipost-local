@extends('layouts.appinside')

@section('content')
<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ secure_url('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ secure_url('merchant') }}">Merchants</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Insert</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Merchants
    <small>create new</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="mt-element-step">
    <div class="row step-background-thin">
        <div class="col-md-4 bg-grey-steel mt-step-col active">
            <div class="mt-step-number">1</div>
            <div class="mt-step-title uppercase font-grey-cascade">Merchant</div>
            <div class="mt-step-content font-grey-cascade">Basic information</div>
        </div>
        <div class="col-md-4 bg-grey-steel mt-step-col">
            <div class="mt-step-number">2</div>
            <div class="mt-step-title uppercase font-grey-cascade">User</div>
            <div class="mt-step-content font-grey-cascade">User Information</div>
        </div>
        <div class="col-md-4 bg-grey-steel mt-step-col">
            <div class="mt-step-number">3</div>
            <div class="mt-step-title uppercase font-grey-cascade">Store</div>
            <div class="mt-step-content font-grey-cascade">Store Information</div>
        </div>
    </div>

    {!! Form::open(array('url' => secure_url('') . '/merchant', 'method' => 'post' ,'enctype' => 'multipart/form-data')) !!}

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
                <label class="control-label">Website</label>
                {!! Form::text('website', null, ['class' => 'form-control', 'placeholder' => 'Website', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Status</label>
                {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
            </div>

        </div>

        <div class="col-md-6">

            <div class="form-group">
                <label class="control-label">Select Country</label>
                {!! Form::select('country_id', $countries,old('country_id'), ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'country_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select State</label>
                {!! Form::select('state_id', array(''=>'Select State'), old('state'), ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'state_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select City</label>
                {!! Form::select('city_id', array(''=>'Select City'),old('city_id'), ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'city_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Zone</label>
                {!! Form::select('zone_id', array(''=>'Select Zone'),old('zone_id'), ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Address</label>
                {!! Form::text('address1',old('address1'), ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
            </div>

        </div>


    </div>
    <div class="row">
     <div class="col-md-6 padding-top-10">

        <div class="form-group">
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                <img src="{{secure_url('/uploads/merchants/no_image.jpg')}}" alt=""  id="img-thumb" />
                </div>
                <div>
                    <span class="btn default btn-file">
                        <span class="fileinput-new"> Select Logo </span>
                        {{-- <span class="fileinput-exists"> Change </span> --}}
                        <input type="file" id="photo" name="photo"> </span>
                        <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                    </div>
                </div>
                <div class="clearfix margin-top-10">
                    <span class="label label-danger">NOTE! </span>
                    <span> Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
                </div>
            </div>

        </div>


        <div class="form-group reference-area"></div>

    </div>
</div>



&nbsp;
<div class="row padding-top-10">
    <a href="javascript:history.back()" class="btn default"> Cancel </a>
    {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
</div>

{!! Form::close() !!}


</div>

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-add', 'merchants');

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
    <script type="text/javascript">
        document.getElementById("photo").onchange = function () {
            var reader = new FileReader();

            reader.onload = function (e) {
            // get loaded data and render thumbnail.
            document.getElementById("img-thumb").src = e.target.result;
        };

        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    };
</script>

@endsection
