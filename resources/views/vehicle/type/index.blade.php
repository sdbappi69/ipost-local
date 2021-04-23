@extends('layouts.appinside')

@section('select2CSS')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" />
@endsection

@section('select2JS')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
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
                <span>Vehicle Types</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Vehicle Types
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-map-marker"></i> {!! $title !!}
                </div>
                <div class="panel-body">
                    @include('flash::message')

                    <table class="table table-bordered table-hover table-responsive table-striped">
                        <thead>
                        <tr>
                            <th width="20%">Title</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($vehicleTypes) > 0)
                            @foreach($vehicleTypes as $vehicleType)
                                <tr>
                                    <td>{!! $vehicleType->title !!}</td>
                                    <td>{!! $vehicleType->details !!}</td>
                                    <td>
                                        {!! ($vehicleType->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                                    </td>
                                    <td>
                                        <a href="{!! route('vehicle-type.edit', $vehicleType->id) !!}" class="btn btn-sm btn-info">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-plus"></i> Add New Vehicle Type
                </div>
                <div class="panel-body">
                    {!! Form::open(['url' => '/vehicle-type', 'class' => 'form-horizontal']) !!}

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
                            {!! Form::radio('status', 1, true) !!} &nbsp; Active &nbsp;
                            {!! Form::radio('status', 0, false) !!} &nbsp; De-active &nbsp;
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Now
                            </button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('vehicle-type', 'vehicles');
        });
    </script>
@endsection