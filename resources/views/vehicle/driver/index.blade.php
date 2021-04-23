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
                <span>Drivers</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Drivers
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="col-md-12">
      <div class="row">
         <div class="table-filtter">
            {!! Form::open(array('method' => 'get')) !!}
            <div class="col-md-2">
               <div class="row">
                  <input type="text" class="form-control" name="name" id="name" placeholder="Name">
               </div>
            </div>
            <div class="col-md-2">
               <div class="row">
                  <input type="text" class="form-control" name="contact_msisdn" id="contact_msisdn" placeholder="Phone">
               </div>
            </div>
            <div class="col-md-3">
               <div class="row">
                  {!! Form::select('job_type', ['' => 'Select Job Type', 'Permanent' => 'Permanent', 'Contractual' => 'Contractual', 'Freelancer' => 'Freelancer'], null, ['class' => 'form-control', 'id' => 'job_type']) !!}
               </div>
            </div>
            <div class="col-md-3">
               <div class="row">
                  {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'id' => 'status']) !!}
               </div>
            </div>

            <div class="col-md-2">
               <div class="row">
                  <button type="submit" class="btn btn-primary">Filter</button>
               </div>
            </div>
            <div class="clearfix"></div>
            {!! Form::close() !!}
         </div>
      </div>
    </div>

    <div class="row">
        <div class="col-md-7">
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
                            <th>Phone</th>
                            <th>Job Type</th>
                            <th>Status</th>
                            <th colspan="2">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($drivers) > 0)
                            @foreach($drivers as $driver)
                                <tr>
                                    <td>
                                        <img src="{!! $driver->photo !!}" alt="Loading..." class="img-responsive">
                                    </td>
                                    <td>{!! $driver->name !!}</td>
                                    <td>{!! $driver->contact_msisdn !!}</td>
                                    <td>{!! $driver->job_type !!}</td>
                                    <td>
                                        {!! ($driver->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                                    </td>
                                    <td>
                                        <a href="{!! route('driver.edit', $driver->id) !!}" class="btn btn-sm btn-warning">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('driver.show', $driver->id) !!}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-search"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pagination">
                        {!! $drivers->appends($req)->render() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-plus"></i> Add New Driver
                </div>
                <div class="panel-body">
                    {!! Form::open(['url' => '/driver', 'class' => 'form-horizontal', 'files' => true]) !!}

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
            highlight_nav('drivers', 'drivers');

            {!! !empty($req['name'])            ? "document.getElementById('name').value = '".$req['name']."'" : "" !!}
            {!! !empty($req['contact_msisdn'])  ? "document.getElementById('contact_msisdn').value = '".$req['contact_msisdn']."'" : "" !!}
            {!! !empty($req['job_type'])        ? "document.getElementById('job_type').value = '".$req['job_type']."'" : "" !!}
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
