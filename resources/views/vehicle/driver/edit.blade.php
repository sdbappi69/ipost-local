@extends('layouts.appinside')

@section('select2CSS')
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" /> -->
@endsection

@section('select2JS')
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script> -->
    <script src="{!! secure_asset('js/locations.dropdown.js') !!}"></script>
@endsection

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Drivers</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Drivers
        <small> update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        {!! Form::model($driver, ['url' => secure_url('') . "/driver/$driver->id", 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true]) !!}
        <div class="col-md-8">
            @include('flash::message')

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-flag"></i> {!! $title !!}

                    <a href="{!! secure_url('driver') !!}" class="pull-right text-danger">
                        <i class="fa fa-backward"></i> Back
                    </a>
                </div>
                <div class="panel-body">

                    <div class="form-group">
                        {!! Form::label('job_type', 'Job Type', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('job_type', ['' => 'Select Job Type', 'Permanent' => 'Permanent', 'Contractual' => 'Contractual', 'Freelancer' => 'Freelancer'], null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'Name', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('contact_msisdn', 'Phone', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('contact_msisdn', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 2]) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('driving_license_no', 'License', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('driving_license_no', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        {!! Form::label('date_of_birth', 'DOB', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::date('date_of_birth', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div> -->

                    <div class="form-group">
                        {!! Form::label('reference_name', 'Ref. Name', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('reference_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('reference_msisdn', 'Ref. Phone', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('reference_msisdn', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('photo', 'Photo', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::file('photo') !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::radio('status', 1, true) !!} &nbsp; Active &nbsp;
                            {!! Form::radio('status', 0, false) !!} &nbsp; De-active &nbsp;
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fa fa-edit"></i> Update Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <p>
                <img src="{!! $driver->photo !!}" alt="Loading" class="img-responsive">
            </p>
        </div>
    </div>
    {!! Form::close() !!}

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('drivers', 'drivers');
        });
    </script>
@endsection