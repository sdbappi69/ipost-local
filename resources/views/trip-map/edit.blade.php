@extends('layouts.appinside')

@section('content')

<style type="text/css">
    #add-panel{
        display: none;
    }
    form{
        display: inline;
    }
</style>

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
    <small> view/update</small>
</h1>

<div class="col-md-12">
    <div class="portlet light portlet-fit bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class=" icon-layers font-green"></i>
                <span class="caption-subject font-green bold uppercase">Trip-Map</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="mt-element-step">
                <div class="row step-default">

                    <div class="col-md-4"></div>
                    <div class="col-md-4 bg-grey mt-step-col active animated flipInX">
                        <div class="mt-step-number bg-white font-grey"><i class="icon-rocket"></i></div>
                        <div class="mt-step-title uppercase font-grey-cascade">{{ $start_hub->title }}</div>
                        <div class="mt-step-content font-grey-cascade">Picking hub</div>
                    </div>
                    <div class="col-md-4"></div>

                </div>

                @if(count($trip_maps) > 0)

                    <?php $i = 0; ?>

                    @foreach($trip_maps AS $trip_map)

                        <?php $i++; ?>

                        <div class="row step-default">
                    
                            <div class="col-md-4"></div>
                            <div class="col-md-4 bg-grey mt-step-col animated zoomIn">
                                <div class="mt-step-title uppercase font-grey-cascade">{{ $trip_map->hub->title }}</div>
                                <div class="mt-step-content font-grey-cascade">Transit hub</div>
                                <div class="mt-step-content font-grey-cascade">
                                    @if($i == 1)
                                        <a href="javascript:;" class="btn btn-icon-only">
                                            <i class="fa fa-angle-up"></i>
                                        </a>
                                    @else
                                        {!! Form::model($trip_map, array('url' => '/trip-map/'.$trip_map->id, 'method' => 'put')) !!}
                                            {!! Form::hidden('hub_priority', 'up', ['required' => 'required']) !!}
                                            <button type="submit" class="btn btn-icon-only blue">
                                                <i class="fa fa-angle-up"></i>
                                            </button>
                                        {{ Form::close() }}
                                    @endIf
                                    @if($i == count($trip_maps))
                                        <a href="javascript:;" class="btn btn-icon-only">
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                    @else
                                        {!! Form::model($trip_map, array('url' => '/trip-map/'.$trip_map->id, 'method' => 'put')) !!}
                                            {!! Form::hidden('hub_priority', 'down', ['required' => 'required']) !!}
                                            <button type="submit" class="btn btn-icon-only blue">
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                        {{ Form::close() }}
                                    @endIf

                                    {{ Form::open(array('url' => 'trip-map/'.$trip_map->id)) }}
                                        {{ Form::hidden('_method', 'DELETE') }}
                                        <button type="submit" class="btn btn-icon-only red" data-toggle="confirmation" data-original-title="Are you sure ?"><i class="fa fa-times"></i></button>
                                    {{ Form::close() }}
                                </div>
                            </div>
                            <div class="col-md-4"></div>

                        </div>

                    @endforeach

                @endIf

                <div class="row step-default">

                    <div class="col-md-4"></div>
                    <div class="col-md-4 bg-grey mt-step-col done animated zoomIn">
                        <div class="mt-step-content font-grey-cascade">
                            <a href="javascript:;" class="btn btn-icon-only green" id="add">
                                <i class="fa fa-plus"></i>
                            </a>
                            <div id="add-panel">
                                {!! Form::open(array('url' => '/trip-map', 'method' => 'post')) !!}
                                    <div class="col-md-12" style="margin-bottom:5px;">

                                        {!! Form::hidden('start_hub_id', $start_hub->id, ['required' => 'required']) !!}
                                        {!! Form::hidden('end_hub_id', $end_hub->id, ['required' => 'required']) !!}

                                        {!! Form::select('hub_id', array(''=>'Select Transit Hub')+$hubs, null, ['class' => 'form-control js-example-basic-single','id' => 'hub_id', 'required' => 'required']) !!}

                                    </div>

                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-default" style="float: left;" id="cancel">Cancel</button>
                                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4"></div>

                </div>

                <div class="row step-default">
                    
                    <div class="col-md-4"></div>
                    <div class="col-md-4 bg-grey mt-step-col active animated flipInX">
                        <div class="mt-step-number bg-white font-grey"><i class="fa fa-rocket"></i></div>
                        <div class="mt-step-title uppercase font-grey-cascade">{{ $end_hub->title }}</div>
                        <div class="mt-step-content font-grey-cascade">Delivery hub</div>
                    </div>
                    <div class="col-md-4"></div>

                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('trip-map-manage', 'trip-map');
    });

    $("#add").click(function() {
        $("#add").hide(400);
        $("#add-panel").show(800);
    });

    $("#cancel").click(function() {
        $("#add-panel").hide(400);
        $("#add").show(800);
    });
</script>

@endsection
