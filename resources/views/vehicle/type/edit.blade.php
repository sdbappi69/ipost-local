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
                <span>Vehicle Types</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Vehicle Types
        <small> update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        {!! Form::model($vehicleType, ['route' => ['vehicle-type.update', $vehicleType->id], 'class' => 'form-horizontal', 'method' => 'PUT']) !!}
        <div class="col-md-8">
            @include('flash::message')

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-flag"></i> {!! $title !!}

                    <a href="{!! url('vehicle-type') !!}" class="pull-right text-danger">
                        <i class="fa fa-backward"></i> Back
                    </a>
                </div>
                <div class="panel-body small">
                    <div class="form-group">
                        {!! Form::label('title', 'Title', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('details', 'Details', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::textarea('details', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 2]) !!}
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
    </div>
    {!! Form::close() !!}

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('vehicle-type', 'vehicles');
        });
    </script>
@endsection