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
                <a href="{{ URL::to('charge') }}">Charges</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                @if($charge->id == 1)
                    <span>COD</span>
                @else
                    <span>View</span>
                @endif
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Charges
        @if($charge->id == 1)
            <small>for COD</small>
        @endif
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    {!! Form::model($charge, array('url' => '/charge/'.$charge->id, 'method' => 'put')) !!}

        <div class="row">

            @include('partials.errors')

            <div class="col-md-6">
                <div class="col-md-12 np-lr">
                    <div class="col-md-6 np-lr">
                        <div class="form-group">
                            <label class="control-label">Percentage Range Start</label>
                            {!! Form::text('percentage_range_start', null, ['class' => 'form-control', 'placeholder' => 'Start', 'required' => 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6 np-lr">
                        <div class="form-group">
                            <label class="control-label">Percentage Range End</label>
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

                <div class="form-group">
                    <label class="control-label">Select Status</label>
                    {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                </div>

            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Additional Slot</label>
                    {!! Form::text('additional_range_per_slot', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Additional Charge Type</label>
                    {!! Form::select('additional_charge_type', array('' => 'Select Type','1' => 'Flat','0' => 'Normal'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                </div>

                <div class="form-group">
                    <label class="control-label">Additional Charge Per Slot</label>
                    {!! Form::text('additional_charge_per_slot', null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>

            </div>

        </div>

        &nbsp;
        @if($charge->id == 1)
            <div class="row padding-top-10">
                <a href="javascript:history.back()" class="btn default"> Cancel </a>
                {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
            </div>
        @endif

    {!! Form::close() !!}

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('cod', 'cod');
        });

    </script>

@endsection
