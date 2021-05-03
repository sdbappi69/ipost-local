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
                <span>Cities</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Cities
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

                    {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

                        <?php if(!isset($_GET['filter_name'])){$_GET['filter_name'] = null;} ?>
                        <div class="col-md-6" style="margin-bottom:5px;">
                             <input type="text" value="{{$_GET['filter_name']}}" class="form-control focus_it" name="filter_name" id="filter_name" placeholder="Name">
                        </div>

                        <?php if(!isset($_GET['filter_state_id'])){$_GET['filter_state_id'] = null;} ?>
                        <div class="col-md-6" style="margin-bottom:5px;">
                             {!! Form::select('filter_state_id', ['' => 'All States']+$states, $_GET['filter_state_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'filter_state_id']) !!}
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary filter-btn pull-right" style="width: 100%; margin-bottom:5px;"><i class="fa fa-search"></i> Filter</button>
                        </div>
                        <div class="clearfix"></div>

                    {!! Form::close() !!}

                    @include('flash::message')

                    <table class="table table-bordered table-hover table-responsive table-striped">
                        <thead>
                        <tr>
                            <th>City / District</th>
                            <th>State</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($cities) > 0)
                            @foreach($cities as $city)
                                <tr>
                                    <td>{!! $city->name !!}</td>
                                    <td class="text-primary">{!! $city->state->name !!}</td>
                                    <td class="text-danger">{!! $city->state->country->name !!}</td>
                                    <td>
                                        {!! ($city->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                                    </td>
                                    <td>
                                        <a href="{!! secure_url('') . "/city/$city->id/edit" !!}" class="btn btn-sm btn-info">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pagination">
                        {!! $cities->appends($_REQUEST)->render() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-plus"></i> Add New City
                </div>
                <div class="panel-body">
                    {!! Form::open(['url' => secure_url('') . '/city', 'class' => 'form-horizontal']) !!}

                    <div class="form-group">
                        {!! Form::label('country_id', 'Country', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('country_id', ['' => 'SELECT ONE']+$countries, null, ['class' => 'form-control js-example-basic-single js-country', 'data-type' => 'Country', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('state_id', 'State', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::select('state_id', ['' => 'SELECT ONE'], null, ['class' => 'form-control js-example-basic-single js-state', 'data-type' => 'State', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', 'Name', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
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
            highlight_nav('city', 'locations');
        });
    </script>
@endsection