@extends('layouts.appinside')

@section('content')
<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="{{ secure_url('home') }}">Home</a>
      <i class="fa fa-circle"></i>
    </li>
    <li>
      <span>Consignments</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Consignment
  <small>view</small>
</h1>

<div class="row">
  
  <div class="col-md-12">
    <p>
      Consignment id: <b>{{ $consignment->consignment_unique_id }}</b>
      </br>
      Type: <b>{{ $consignment->type }}</b>
    </p>
    <p>
      Rider: <b>{{ $consignment->rider->name }}</b>
      </br>
      Contact: <b>{{ $consignment->rider->msisdn }}</b>
    </p>
  </div>

  <div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
      <div class="portlet-body util-btn-margin-bottom-5">
          <table class="table table-bordered table-hover" id="example0">
            <thead class="flip-content">

              <th>AWB</th>
              <th>Available Quantity</th>
              <th>Status</th>
              <th>Reason</th>
              <th>Start</th>
              <th>End</th>
              <th>Map</th>
              <th>Signature</th>
              <th>Proof</th>

            </thead>
            <tbody>
              @if(count($tasks) > 0)
                @foreach($tasks as $task)
                  <tr>
                    @if($consignment->type == 'picking')

                      <td>{{ $task->product->sub_order->unique_suborder_id }}</td>
                      <td>{{ $task->quantity }}</td>
                      <td>{{ $task->product->sub_order->suborder_status->title }}</td>
                      <td>{{ $task->reason->reason or null }}</td>
                      <td>{{ $task->start_time }}</td>
                      <td>{{ $task->end_time }}</td>
                      <td>
                        @if($task->start_lat != null && $task->start_long != null)
                          <a target="_blank" class="btn default" href="{{secure_url('maps/'.$task->start_lat.'/'.$task->start_long)}}">Start</a>
                        @endIf
                        @if($task->end_lat != null && $task->end_long != null)
                          <a target="_blank" class="btn default" href="{{secure_url('maps/'.$task->end_lat.'/'.$task->end_long)}}">End</a>
                        @endIf
                      </td>
                      <td>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#sign_{{ $task->id }}" class="btn green">Click Here</a>

                        <div class="modal fade" id="sign_{{ $task->id }}" tabindex="-1" role="basic" aria-hidden="true">
                          <div class="modal-dialog">
                              <div class="modal-content" style="padding: 15px;">
                                <img style="width: 100%;" src="{{ $task->signature }}">
                              </div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#proof_{{ $task->id }}" class="btn green">Click Here</a>

                        <div class="modal fade" id="proof_{{ $task->id }}" tabindex="-1" role="basic" aria-hidden="true">
                          <div class="modal-dialog">
                              <div class="modal-content" style="padding: 15px;">
                                <img style="width: 100%;" src="{{ $task->image }}">
                              </div>
                          </div>
                        </div>
                      </td>

                    @else

                      <td>{{ $task->suborder->unique_suborder_id }}</td>
                      <td>{{ $task->quantity }}</td>
                      <td>{{ $task->suborder->suborder_status->title }}</td>
                      <td>{{ $task->reason->reason or null }}</td>
                      <td>{{ $task->start_time }}</td>
                      <td>{{ $task->end_time }}</td>
                      <td>
                        @if($task->start_lat != null && $task->start_long != null)
                          <a target="_blank" class="btn default" href="{{secure_url('maps/'.$task->start_lat.'/'.$task->start_long)}}">Start</a>
                        @endIf
                        @if($task->end_lat != null && $task->end_long != null)
                          <a target="_blank" class="btn default" href="{{secure_url('maps/'.$task->end_lat.'/'.$task->end_long)}}">End</a>
                        @endIf
                      </td>
                      <td>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#sign_{{ $task->id }}" class="btn green">Click Here</a>

                        <div class="modal fade" id="sign_{{ $task->id }}" tabindex="-1" role="basic" aria-hidden="true">
                          <div class="modal-dialog">
                              <div class="modal-content" style="padding: 15px;">
                                <img style="width: 100%;" src="{{ $task->signature }}">
                              </div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#proof_{{ $task->id }}" class="btn green">Click Here</a>

                        <div class="modal fade" id="proof_{{ $task->id }}" tabindex="-1" role="basic" aria-hidden="true">
                          <div class="modal-dialog">
                              <div class="modal-content" style="padding: 15px;">
                                <img style="width: 100%;" src="{{ $task->image }}">
                              </div>
                          </div>
                        </div>
                      </td>

                    @endIf
                  </tr>
                @endforeach
              @endif
          </tbody>
        </table>

      </div>
    </div>
  </div>

</div>

<script type="text/javascript">
  $(document ).ready(function() {
    // Navigation Highlight
    highlight_nav('all_consignments', 'consignments');

    $('#example0').DataTable({
        "order": [],
    });
  });
</script>





        @endsection
