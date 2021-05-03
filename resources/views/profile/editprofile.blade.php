@extends('layouts.appinside')

@section('content')

    <link href="{{ secure_asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Profile</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Profile
        <small> view & update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet ">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="{{ Auth::user()->photo }}" class="img-responsive" alt=""> </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ Auth::user()->name }} </div>
                        <div class="profile-usertitle-job"> {{ Auth::user()->email }} </div>
                        <div> {{ Auth::user()->address1 }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <!-- <div class="profile-userbuttons">
                        <button type="button" class="btn btn-circle green btn-sm">Follow</button>
                        <button type="button" class="btn btn-circle red btn-sm">Message</button>
                    </div> -->
                    <!-- END SIDEBAR BUTTONS -->
                    <!-- SIDEBAR MENU -->
                    <!-- <div class="profile-usermenu">
                        <ul class="nav">
                            <li>
                                <a href="page_user_profile_1.html">
                                    <i class="icon-home"></i> Overview </a>
                            </li>
                            <li class="active">
                                <a href="page_user_profile_1_account.html">
                                    <i class="icon-settings"></i> Account Settings </a>
                            </li>
                            <li>
                                <a href="page_user_profile_1_help.html">
                                    <i class="icon-info"></i> Help </a>
                            </li>
                        </ul>
                    </div> -->
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->
            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light ">
                            <div class="portlet-title tabbable-line">
                                <div class="caption caption-md">
                                    <i class="icon-globe theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">Profile Account</span>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="tab-btn info active">
                                        <a href="#info" data-toggle="tab">Personal Info</a>
                                    </li>
                                    <li class="tab-btn avatar">
                                        <a href="#avatar" data-toggle="tab">Change Avatar</a>
                                    </li>
                                    <li class="tab-btn password">
                                        <a href="#password" data-toggle="tab">Change Password</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="portlet-body">
                                <div class="tab-content">
                                    <!-- PERSONAL INFO TAB -->
                                    <div class="tab-pane active" id="info">
                                        {!! Form::model($user, array('url' => secure_url('') . '/profile/'.$user->id, 'method' => 'put')) !!}

                                            @include('partials.errors')

                                            <div class="form-group">
                                                <label class="control-label">Name</label>
                                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-md-12 np-lr">Mobile Number</label>
                                                <div class="col-md-2 np-lr">
                                                    {!! Form::select('msisdn_country', $countries, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                                </div>
                                                <div class="col-md-10 np-lr">
                                                    {!! Form::text('msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                                                </div>
                                            </div>

                                            <div class="form-group">

                                                <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
                                                <div class="col-md-2 np-lr">
                                                    {!! Form::select('alt_msisdn_country', $countries, null, ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="col-md-10 np-lr">
                                                    {!! Form::text('alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                                                </div>
                                                
                                            </div>

                                            &nbsp;

                                            <div class="padding-top-10">
                                                {!! Form::submit('Save Changes', ['class' => 'btn green']) !!}
                                                <a href="javascript:history.back()" class="btn default"> Cancel </a>
                                            </div>

                                        {!! Form::close() !!}
                                    </div>
                                    <!-- END PERSONAL INFO TAB -->
                                    <!-- CHANGE AVATAR TAB -->
                                    <div class="tab-pane" id="avatar">
                                        {!! Form::open(array('url' => secure_url('') . '/profile-photo/'.$user->id, 'method' => 'post', 'files' => true)) !!}

                                            @include('partials.errors')

                                            <div class="form-group">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                                                        <img src="{{ Auth::user()->photo }}" alt=""  id="img-thumb" />
                                                    </div>
                                                    <div>
                                                        <span class="btn default btn-file">
                                                            <span class="fileinput-new"> Select image </span>
                                                            <span class="fileinput-exists"> Change </span>
                                                            <input type="file" id="photo" name="photo" required = "required"> </span>
                                                        <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-10">
                                                    <span class="label label-danger">NOTE! </span>
                                                    <span>Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
                                                </div>
                                            </div>
                                            <div class="margin-top-10">
                                                {!! Form::submit('Upload', ['class' => 'btn green']) !!}
                                                <a href="javascript:history.back()" class="btn default"> Cancel </a>
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!-- END CHANGE AVATAR TAB -->
                                    <!-- CHANGE PASSWORD TAB -->
                                    <div class="tab-pane" id="password">
                                        {!! Form::open(array('url' => secure_url('') . '/change-password/'.$user->id, 'method' => 'post')) !!}

                                            @include('partials.errors')

                                            <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                                                <label class="control-label">Current Password</label>
                                                {!! Form::password('current_password', ['class' => 'form-control', 'placeholder' => 'Current Password', 'required' => 'required']) !!}
                                                @if ($errors->has('current_password'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('current_password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                           <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label class="control-label">New Password</label>
                                                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password', 'required' => 'required', 'id' => 'new_password']) !!}
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                <label class="control-label">Re-type New Password</label>
                                                {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Re-type New Password', 'required' => 'required', 'oninput' => 'check(this)']) !!}
                                                @if ($errors->has('password_confirmation'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="margin-top-10">
                                                {!! Form::submit('Change Password', ['class' => 'btn green']) !!}
                                                <a href="javascript:history.back()" class="btn default"> Cancel </a>
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!-- END CHANGE PASSWORD TAB -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Close Navigation
            close_nav();

            // Tab Active
            var url = $(location).attr('href');
            var hash = url.substring(url.indexOf('#'));
            var activePane = hash.substr(1);
            // alert(activePane);
            if(activePane != ''){
                $(".tab-btn").removeClass("active");
                $("."+activePane).addClass("active");

                $(".tab-pane").removeClass("active");
                $("#"+activePane).addClass("active");
            }

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
    </script>

@endsection
