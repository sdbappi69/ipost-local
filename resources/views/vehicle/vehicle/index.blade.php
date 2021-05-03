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
                <span>Vehicle</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Vehicle
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="col-md-12">
      <div class="row">
         <div class="table-filtter">
            {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}
            <div class="col-md-2">
               <div class="row">
                  <input type="text" class="form-control" name="name" id="name" placeholder="Name">
               </div>
            </div>
            <div class="col-md-2">
               <div class="row">
                  {!! Form::select('vehicle_type_id', ['' => 'SELECT ONE']+$vehicleTypes, null, ['class' => 'form-control', 'id' => 'vehicle_type_id']) !!}
               </div>
            </div>
            <div class="col-md-1">
               <div class="row">
                  <input type="text" class="form-control" name="license_no" id="license_no" placeholder="License No">
               </div>
            </div>
            <div class="col-md-2">
               <div class="row">
                  <input type="text" class="form-control" name="brand" id="brand" placeholder="Brand">
               </div>
            </div>
            <div class="col-md-2">
               <div class="row">
                  <input type="text" class="form-control" name="model" id="model" placeholder="Model">
               </div>
            </div>
            <div class="col-md-2">
               <div class="row">
                  {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'id' => 'status']) !!}
               </div>
            </div>
            <div class="col-md-1">
               <div class="row">
                  <button type="submit" class="btn btn-primary">Filter</button>
               </div>
            </div>
            <div class="clearfix"></div>
            {!! Form::close() !!}
         </div>
         <hr>
      </div>
    </div>

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
                            <th width="20%">Photo</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>License</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($vehicles) > 0)
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <img src="{!! secure_url('') . $vehicle->photo !!}" alt="Loading..." class="img-responsive">
                                    </td>
                                    <td>{!! $vehicle->name !!}</td>
                                    <td class="text-primary">{!! $vehicle->type->title !!}</td>
                                    <td>{!! $vehicle->license_no !!}</td>
                                    <td>{!! $vehicle->brand !!}</td>
                                    <td>{!! $vehicle->model !!}</td>
                                    <td>
                                        {!! ($vehicle->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                                    </td>
                                    <td>
                                        <a href="{!! secure_url('') . "/vehicle/$vehicle->id/edit" !!}" class="btn btn-sm btn-info">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pagination">
                        {!! $vehicles->appends($req)->render() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-plus"></i> Add New Vehicle
                </div>
                <div class="panel-body">
                    {!! Form::open(['url' => secure_url('') . '/vehicle', 'class' => 'form-horizontal', 'files' => true]) !!}

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
            highlight_nav('vehicle', 'vehicles');

            {!! !empty($req['name'])            ? "document.getElementById('name').value = '".$req['name']."'" : "" !!}
            {!! !empty($req['vehicle_type_id']) ? "document.getElementById('vehicle_type_id').value = '".$req['vehicle_type_id']."'" : "" !!}
            {!! !empty($req['license_no'])      ? "document.getElementById('license_no').value = '".$req['license_no']."'" : "" !!}
            {!! !empty($req['brand'])           ? "document.getElementById('brand').value = '".$req['brand']."'" : "" !!}
            {!! !empty($req['model'])           ? "document.getElementById('model').value = '".$req['model']."'" : "" !!}
            {!! !empty($req['status'])          ? "document.getElementById('status').value = '".$req['status']."'" : "" !!}
        });
    </script>
    <style media="screen">
    .table-filtter .btn{ width: 100%;}
    .table-filtter {
      margin: 20px 0;
    }
    </style>

@endsection
