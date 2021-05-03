@extends('layouts.appinside')

@section('select2CSS')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" />
@endsection

@section('select2JS')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
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
                <span>Settings</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Settings
        <small> update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        {!! Form::model($settings, ['url' => secure_url('') . "/settings/$settings->id", 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true]) !!}
        <div class="col-md-8">
            @include('flash::message')

            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle"></i> {!! $title !!}

                    <a href="{!! secure_url('settings') !!}" class="pull-right text-danger">
                        <i class="fa fa-backward"></i> Back
                    </a>
                </div>
                <div class="panel-body small">
                    <div class="form-group">
                        {!! Form::label('title', 'Title', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('title', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('email', 'Email', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::email('email', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('website', 'Website', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('website', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('msisdn', 'MSISDN', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('msisdn', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('address1', 'Address Line 1', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('address1', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('address2', 'Address Line 2', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('address2', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('latitude', 'Latitude', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('latitude', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('longitude', 'Longitude', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('longitude', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('country_id', 'Country', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('country_id', $countries, null, ['class' => 'form-control js-example-basic-single js-country', 'data-type' => 'Country']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('state_id', 'State', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('state_id', $states, null, ['class' => 'form-control js-example-basic-single js-state', 'data-type' => 'State']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('city_id', 'City', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('city_id', $cities, null, ['class' => 'form-control js-example-basic-single js-city', 'data-type' => 'City']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('zone_id', 'Tier', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('zone_id', $zones, null, ['class' => 'form-control js-example-basic-single js-zone', 'data-type' => 'Tier']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('logo', 'Logo', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::file('logo') !!}
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
                <img src="{!! $settings->logo !!}" alt="Loading..." class="img-responsive">
            </p>
        </div>
    </div>
    {!! Form::close() !!}

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('settings', 'settings');
        });
    </script>
@endsection