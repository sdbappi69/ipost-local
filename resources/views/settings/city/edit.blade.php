@extends('layouts.appinside')

@section('select2CSS')
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" /> -->
@endsection

@section('select2JS')
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script> -->
    <script src="{!! asset('js/locations.dropdown.js') !!}"></script>
@endsection

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Cities</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Cities
        <small> update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        {!! Form::model($city, ['route' => ['city.update', $city->id], 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true]) !!}
        <div class="col-md-8">
            @include('flash::message')

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-flag"></i> {!! $title !!}

                    <a href="{!! url('city') !!}" class="pull-right text-danger">
                        <i class="fa fa-backward"></i> Back
                    </a>
                </div>
                <div class="panel-body small">
                    <div class="form-group">
                        {!! Form::label('country_id', 'Country', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('country_id', ['' => 'SELECT ONE']+$countries, $city->state->country->id, ['class' => 'form-control js-example-basic-single js-country', 'data-type' => 'Country', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('state_id', 'State', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('state_id', ['' => 'SELECT ONE']+$city->state->lists('name', 'id')->toArray(), $city->state->id, ['class' => 'form-control js-example-basic-single js-state', 'data-type' => 'State', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'City', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::radio('status', 1, false) !!} &nbsp; Active &nbsp;
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
        {!! Form::close() !!}
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('city', 'locations');
        });
    </script>
@endsection