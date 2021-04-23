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
                <span>Hubs</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Hubs
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="col-md-12">
      <div class="table-filtter">
         {!! Form::open(array('method' => 'get')) !!}
         <div class="col-md-2">
            <div class="row">
               {!! Form::select('id', array(''=>'Select Hub') + $hubs_lists, null, ['class' => 'form-control', 'id' => 'id']) !!}
            </div>
         </div>
         <!-- <div class="col-md-2">
            <div class="row">
               {!! Form::select('zone_genre_id', array(''=>'Select Region') + $zoneGenre, null, ['class' => 'form-control', 'id' => 'zone_genre_id']) !!}
            </div>
         </div> -->
         <div class="col-md-3">
            <div class="row">
               <input type="text" class="form-control" name="msisdn" id="msisdn" placeholder="Primary Contact">
            </div>
         </div>
         <div class="col-md-3">
            <div class="row">
               <input type="text" class="form-control" name="alt_msisdn" id="alt_msisdn" placeholder="Secondary Contact">
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

    @if(count($hubs) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <!-- <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Hubs</span>
                        <span class="caption-helper">list</span>
                    </div>
                </div> -->
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Title</th>
                            <th>Contact</th>
                            <!-- <th>Tier</th> -->
                            <th>Responsible</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach($hubs as $hub)
                                <tr>
                                    <td>{{ $hub->title }}</td>
                                    <td>{{ $hub->msisdn }}<br>{{ $hub->alt_msisdn }}</td>
                                    <td>{{ $hub->responsible_user->name or '' }} - {{ $hub->responsible_user->role->display_name or '' }}</td>
                                    <td>
                                      <a class="label label-success" href="hub/{{ $hub->id }}/edit">
                                        <i class="fa fa-pencil"></i> Update
                                      </a>
                                      &nbsp;
                                      <a class="label label-success" href="{{ url('hub/'. $hub->id .'') }}">
                                        <i class="fa fa-eye"></i> View
                                      </a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $hubs->appends($req)->render() }}
                    </div>
                </div>
            </div>
        </div>
   @else
    <div class="col-md-12">
      <div class="portlet light tasks-widget bordered">
         <p>
            No Data Found
         </p>
      </div>
    </div>
    @endIf

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('hub-manage', 'hubs');

            $('#example0').DataTable({
                "order": [],
            });

            {!! !empty($req['id'])              ? "document.getElementById('id').value = '".$req['id']."'" : "" !!}
            {!! !empty($req['zone_genre_id'])   ? "document.getElementById('zone_genre_id').value = '".$req['zone_genre_id']."'" : "" !!}
            {!! !empty($req['msisdn'])          ? "document.getElementById('msisdn').value = '".$req['msisdn']."'" : "" !!}
            {!! !empty($req['alt_msisdn'])      ? "document.getElementById('alt_msisdn').value = '".$req['alt_msisdn']."'" : "" !!}
        });
    </script>
    <style media="screen">
    .table-filtter .btn{ width: 100%;}
    .table-filtter {
      margin: 20px 0;
    }
    </style>

@endsection
