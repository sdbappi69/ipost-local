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
                <span>Vehicle</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Vehicle
        <small> update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        {!! Form::model($vehicle, ['route' => ['vehicle.update', $vehicle->id], 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true]) !!}
        <div class="col-md-8">
            @include('flash::message')

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-flag"></i> {!! $title !!}

                    <a href="{!! url('vehicle') !!}" class="pull-right text-danger">
                        <i class="fa fa-backward"></i> Back
                    </a>
                </div>
                <div class="panel-body">

                    <div class="form-group">
                        {!! Form::label('vehicle_type_id', 'Type', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('vehicle_type_id', ['' => 'SELECT ONE']+$vehicleTypes, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'Name', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('contact_email', 'Email', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::email('contact_email', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('contact_msisdn', 'Phone', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('contact_msisdn', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('license_no', 'License', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('license_no', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('brand', 'Brand', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('brand', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('model', 'Model', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('model', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('latitude', 'Latitude', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('latitude', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('longitude', 'Longitude', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('longitude', null, ['class' => 'form-control', 'required' => 'required']) !!}
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
                <img src="{!! $vehicle->photo !!}" alt="Loading" class="img-responsive">
            </p>
        </div>
    </div>
    {!! Form::close() !!}

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('vehicle', 'vehicles');
        });
    </script>
@endsection