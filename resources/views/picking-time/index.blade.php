@extends('layouts.appinside')

@section('content')

   <link href="{{ secure_asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
   <link href="{{ secure_asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Picking Times</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Picking Times
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="col-md-12">
       <div class="table-filtter">
          {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}
          <div class="col-md-4">
             <div class="row">
                {!! Form::select('day', array(' ' => 'Select Day', 'Sat' => 'Sat','Sun' => 'Sun','Mon' => 'Mon','Tue' => 'Tue','Wed' => 'Wed','Thu' => 'Thu','Fri' => 'Fri'), null, ['class' => 'form-control', 'id' => 'day']) !!}
             </div>
          </div>
          <div class="col-md-3">
             <div class="row">
                <input type="text" class="form-control timepicker timepicker-24" name="start_time" id="start_time">
             </div>
          </div>
          <div class="col-md-3">
             <div class="row">
                <input type="text" class="form-control timepicker timepicker-24" name="end_time" id="end_time">
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

    @if(count($picking_times) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover">
                        <thead class="flip-content">
                            <th>Day</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach($picking_times as $picking_time)
                                <tr>
                                    {{-- */ $update_url = secure_url('picking-time').'/'.$picking_time->id.'/edit?step=3'; /* --}}
                                    <td>{{ $picking_time->day }}</td>
                                    <td>{{ $picking_time->start_time }} - {{ $picking_time->end_time }}</td>
                                    <td>{!! ($picking_time->status) ? 'Active' : 'Inactive' !!}</td>
                                    <td>
                                        <a class="label label-success" href="{{ $update_url }}">
                                            <i class="fa fa-pencil"></i> Update
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $picking_times->appends($req)->render() }}
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
            highlight_nav('picking-times', 'picking-times-manage');

            $('#example0').DataTable({
                "order": [],
            });
            //
            // $('#start_time').timepicker({
            //    'setTime' : "12:12:1 AM"
            // });

            {!! !empty($req['day'])         ? "document.getElementById('day').value = '".$req['day']."'" : "" !!}
            {!! !empty($req['start_time'])  ? "document.getElementById('start_time').value = '".$req['start_time']."'" : "" !!}
            {!! !empty($req['end_time'])    ? "document.getElementById('end_time').value = '".$req['end_time']."'" : "" !!}

        });
    </script>

    <style media="screen">
    .table-filtter .btn{ width: 100%;}
    .table-filtter {
      margin: 20px 0;
    }
    </style>

@endsection
