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
            <span>Trip-Map</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Trip-Map
    <small> create</small>
</h1>

<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered animated bounceInUp">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Create Map</span>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">

            {!! Form::open(array('url' => secure_url('') . '/trip-map', 'method' => 'post')) !!}

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Start hub</label>
                    <!-- <div class="row"> -->
                     {!! Form::select('start_hub_id', array(''=>'Select Start Hub')+$hubs, null, ['class' => 'form-control js-example-basic-single', 'id' => 'start_hub_id']) !!}
                    <!-- </div> -->
                </div>

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">End hub</label>
                    <!-- <div class="row"> -->
                     {!! Form::select('end_hub_id', array(''=>'Select End Hub')+$hubs, null, ['class' => 'form-control js-example-basic-single','id' => 'end_hub_id']) !!}
                    <!-- </div> -->
                </div>

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Transit hub</label>
                    <!-- <div class="row"> -->
                     {!! Form::select('hub_id', array(''=>'Select Transit Hub')+$hubs, null, ['class' => 'form-control js-example-basic-single','id' => 'hub_id']) !!}
                    <!-- </div> -->
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary filter-btn pull-right">Save</button>
                </div>
                <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('trip-map-add', 'trip-map');
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example0').dataTable( {
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });
    });

</script>

@endsection
