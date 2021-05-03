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
                <a href="{{ secure_url('rider-profile-update-request') }}">Rider Request</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Update</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Rider
        <small>update user</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">

        {!! Form::model($update, array('url' => secure_url('') . '/rider/'.$user->id, 'method' => 'put')) !!}

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

                <div class="form-group rider-type">
                    <label class="control-label">Select Rider Type</label>
                    {!! Form::select('rider_type', ['Freelancer','Permanent'], null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'rider_type','placeholder'=>'Select One']) !!}
                </div>
                <div class="form-group transparent-mode">
                    <label class="control-label">Select Transparent Mode</label>
                    {!! Form::select('transparent_mode', $transparentModes, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'transparent_mode','placeholder'=>'Select One']) !!}
                </div>

                <div class="form-group rider-reference-area">
                    <label class="control-label">Select Reference</label>
                    {!! Form::select('rider_reference_id[]', $reference_list, json_decode($update->rider_reference_id), ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'rider_reference_id','multiple']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Select Status</label>
                    {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">
                    <label class="control-label">Select Country</label>
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'country_id']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Select State</label>
                    {!! Form::select('state_id', $states, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'state_id']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Select City</label>
                    {!! Form::select('city_id', $cities, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'city_id']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Select Zone</label>
                    {!! Form::select('zone_id', $zones, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'zone_id']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Address</label>
                    {!! Form::text('address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                            <img src="{{ secure_url('/').'/'.$update->photo }}" alt=""  id="image" />
                        </div>
                        <div>
                        <span class="btn default btn-file">
                            <span class="fileinput-new"> Change Image </span>
                            <input type="file" id="photo" name="photo"> </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        &nbsp;
        <div class="row padding-top-10">
            <a href="javascript:history.back()" class="btn default"> Cancel </a>
            {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
        </div>

        {!! Form::close() !!}


    </div>

    <script type="text/javascript">

        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('user-manage', 'users');
            $('#country_id').on('change', function () {
                if ($(this).val() != {{ $user->country_id }}) {
                    get_states($(this).val());
                }
            });

            // Get City list On State Change
            $('#state_id').on('change', function () {
                if ($(this).val() != {{ $user->state_id }}) {
                    get_cities($(this).val());
                }
            });

            // Get Zone list On City Change
            $('#city_id').on('change', function () {
                if ($(this).val() != {{ $user->city_id }}) {
                    get_zones($(this).val());
                }
            });

        });
        document.getElementById("photo").onchange = function () {
            var reader = new FileReader();

            reader.onload = function (e) {
                // get loaded data and render thumbnail.
                document.getElementById("image").src = e.target.result;
            };

            // read the image file as a data URL.
            reader.readAsDataURL(this.files[0]);
        };
    </script>
@endsection
