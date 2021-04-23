@extends('layouts.appinside')

@section('content')

    <link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL::to('picking-time') }}">Picking Time</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Picking Time
        <small>create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

            {!! Form::open(array('url' => '/picking-time', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Day</label>
                            {!! Form::select('day', array('Sat' => 'Sat','Sun' => 'Sun','Mon' => 'Mon','Tue' => 'Tue','Wed' => 'Wed','Thu' => 'Thu','Fri' => 'Fri'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Start Time</label>
                            <div class="input-group">
                                {!! Form::text('start_time', null, ['class' => 'form-control timepicker timepicker-24', 'required' => 'required']) !!}
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-clock-o"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">End Time</label>
                            <div class="input-group">
                                {!! Form::text('end_time', null, ['class' => 'form-control timepicker timepicker-24', 'required' => 'required']) !!}
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-clock-o"></i>
                                    </button>
                                </span>
                            </div>
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

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('picking-times', 'picking-times-add');
        });

    </script>

@endsection
