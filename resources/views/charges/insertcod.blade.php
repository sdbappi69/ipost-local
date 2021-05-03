@extends('layouts.appinside')

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ secure_url('charge') }}">COD</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> COD
        <small>insert</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    {!! Form::model($charge, array('url' => secure_url('') . '/charge?clone=cod', 'method' => 'post')) !!}

        <div class="row">

            @include('partials.errors')

            <div class="col-md-6">

                {!! Form::hidden('store_id', $store_id, ['required' => 'required']) !!}

                <div class="form-group">
                    <label class="control-label">Charge Model</label>
                    {!! Form::select('charge_model_id', [''=>'Select Charge Model']+$charge_models, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'charge_model_id']) !!}
                </div>

                <label class="control-label">Percentage Range:</label>
                <div class="col-md-12 np-lr">
                    <div class="col-md-6 np-lr">
                        <div class="form-group">
                            <label class="control-label">Start</label>
                            {!! Form::text('percentage_range_start', null, ['class' => 'form-control', 'placeholder' => 'Start', 'required' => 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6 np-lr">
                        <div class="form-group">
                            <label class="control-label">End</label>
                            {!! Form::text('percentage_range_end', null, ['class' => 'form-control', 'placeholder' => 'End', 'required' => 'required']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Percentage Value</label>
                    {!! Form::text('percentage_value', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Fixed Charge</label>
                    {!! Form::text('fixed_charge', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">
                    <label class="control-label">Additional Slot</label>
                    {!! Form::text('additional_range_per_slot', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Additional Charge Per Slot</label>
                    {!! Form::text('additional_charge_per_slot', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Additional Charge Type</label>
                    {!! Form::select('additional_charge_type', array('' => 'Select Type','1' => 'Flat','0' => 'Normal'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Select Status</label>
                    {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                </div>

            </div>

        </div>

        &nbsp;
        <div class="row padding-top-10">
            <a href="javascript:history.back()" class="btn default"> Cancel </a>
            {!! Form::submit('Create', ['class' => 'btn green pull-right']) !!}
        </div>

    {!! Form::close() !!}

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('cod', 'cod');
        });

    </script>

@endsection
