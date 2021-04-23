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
            <span>Trip-Map</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Trip-Map
    <small> view</small>
</h1>

<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered animated flipInX">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Filter</span>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">

            {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Start hub</label>
                    <!-- <div class="row"> -->
                     {!! Form::select('start_hub_id', $hubs, null, ['class' => 'form-control js-example-basic-single', 'id' => 'start_hub_id']) !!}
                    <!-- </div> -->
                </div>

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">End hub</label>
                    <!-- <div class="row"> -->
                     {!! Form::select('end_hub_id', $hubs, null, ['class' => 'form-control js-example-basic-single','id' => 'end_hub_id']) !!}
                    <!-- </div> -->
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                </div>
                <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

@if(count($trip_maps) > 0)

    <div class="col-md-12">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">

            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Trip Maps</span>
                </div>
            </div>

            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" id="example0">
                    <thead>
                        <th>Action</th>
                        <th>Start Hub</th>
                        <th>End Hub</th>

                    </thead>
                    <tbody>
                        @foreach($trip_maps as $trip_map)
                        <tr>
                            <td>
                                <a class="label label-success" href="{{ URL::to('trip-map').'/'.$trip_map->start_hub_id.'/'.$trip_map->end_hub_id }}">
                                    <i class="fa fa-pencil"></i> View/Update
                                </a>
                            </td>
                            <td>{{ $trip_map->start_hub->title or '' }}</td> 
                            <td>{{ $trip_map->end_hub->title or '' }}</td>                        
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination pull-right">
                    {!! $trip_maps->appends($_REQUEST)->render() !!}
                </div>
            </div>
        </div>
    </div>

@endIf

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('trip-map-manage', 'trip-map');
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
