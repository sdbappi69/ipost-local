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
            <a href="{{ secure_url('rider-user') }}">Rider</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Insert</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Rider Users
    <small>create new</small>
</h1>

{!! Form::open(array('url' => secure_url('') . '/rider-user', 'method' => 'post','files'=>true)) !!}

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
            {!! Form::text('msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
        </div>

        <div class="form-group">
            <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
            {!! Form::text('alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
        </div> 
        <div class="form-group rider-type">
            <label class="control-label">Select Rider Type</label>
            {!! Form::select('rider_type', ['Freelancer','Permanent'], null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'rider_type','placeholder'=>'Select One']) !!}
        </div>
        <div class="form-group transparent-mode">
            <label class="control-label">Select Transparent Mode</label>
            {!! Form::select('transparent_mode', $transparentModes, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'transparent_mode','placeholder'=>'Select One']) !!}
        </div>
        <div class="form-group">
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                    <img src="" alt=""  id="img-thumb" />
                </div>
                <div>
                    <span class="btn default btn-file">
                        <span class="fileinput-new"> Select image </span>
                        <span class="fileinput-exists"> Change </span>
                        <input type="file" id="photo" name="photo"> </span>
                    <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                </div>
            </div>
            <div class="clearfix margin-top-10">
                <span class="label label-danger">NOTE! </span>
                <span>Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
            </div>
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
        <div class="form-group">
            <label class="control-label">Password</label>
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password', 'id' => 'new_password']) !!}
        </div>

        <div class="form-group">
            <label class="control-label">Confirm Password</label>
            {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Re-type New Password', 'oninput' => 'check(this)']) !!}
        </div>

    </div>

</div>

&nbsp;
<div class="row padding-top-10">
    <a href="javascript:history.back()" class="btn default"> Cancel </a>
    {!! Form::submit('Submit', ['class' => 'btn green pull-right']) !!}
</div>

{!! Form::close() !!}


</div>

<script type="text/javascript">

    $(document).ready(function () {
        // Navigation Highlight
        highlight_nav('user-add', 'users');

        // Get State list
        var country_id = $('#country_id').val();
        get_states(country_id);
    });
    document.getElementById("photo").onchange = function () {
        var reader = new FileReader();

        reader.onload = function (e) {
            // get loaded data and render thumbnail.
            document.getElementById("img-thumb").src = e.target.result;
        };

        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    };
    function check(input) {
        if (input.value != document.getElementById('new_password').value) {
            input.setCustomValidity('Password Must be Matching.');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }
    // Get State list On Country Change
    $('#country_id').on('change', function () {
        get_states($(this).val());
    });

    // Get City list On State Change
    $('#state_id').on('change', function () {
        get_cities($(this).val());
    });

    // Get Zone list On City Change
    $('#city_id').on('change', function () {
        get_zones($(this).val());
    });

</script>

@endsection
