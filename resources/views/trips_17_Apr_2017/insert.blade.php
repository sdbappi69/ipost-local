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
                <a href="{{ URL::to('trip') }}">Trips</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Trips
        <small>create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

            {!! Form::open(array('url' => '/trip', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Vehicle Type</label>
                            {!! Form::select('vehicle_type_id', array(''=>'Select Vehicle type')+$vehicletypes, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'vehicle_type_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Vehicle</label>
                            {!! Form::select('vehicle_id', array(''=>'Select Vehicle'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'vehicle_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Destination</label>
                            {!! Form::select('destination_hub_id', array(''=>'Select Hub')+$hubs, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'destination_hub_id']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Remarks</label>
                            {!! Form::textarea('remarks', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'remarks']) !!}
                        </div>

                    </div>

                </div>

                &nbsp;
                <div class="row padding-top-10">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}

    </div>

    <script src="{{ URL::asset('custom/js/vehicle-list.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('trip-add', 'trips');

            // Get State list
            var vehicle_type_id = $('#vehicle_type_id').val();
            get_vehicles(vehicle_type_id);
        });

        // Get State list On Country Change
        $('#vehicle_type_id').on('change', function() {
            get_vehicles($(this).val());
        });

    </script>

@endsection
