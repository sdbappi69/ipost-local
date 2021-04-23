@extends('layouts.appinside')

@section('content')

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Return Delivery</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Sub-Orders
        <small> return</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Order ID</th>
                            <th>Sub-Order ID</th>
                            <th>Shipping</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(count($sub_orders) > 0)
                              @foreach($sub_orders as $sub_order)
                                  <tr>
                                      <td>
                                          <a target="_blank" class="label label-success" href="hub-order/{{ $sub_order->order_id }}">
                                              {{ $sub_order->unique_order_id }}
                                          </a>
                                      </td>
                                      <td>{{ $sub_order->unique_suborder_id }}</td>
                                      <td>
                                        Name: {{ $sub_order->delivery_name }}<br>
                                        Email: {{ $sub_order->delivery_email }}<br>
                                        Mobile: {{ $sub_order->delivery_msisdn }}, {{ $sub_order->delivery_alt_msisdn }}<br>
                                        Address: <b>{{ $sub_order->delivery_address1 }}, {{ $sub_order->delivery_zone }}, {{ $sub_order->delivery_city }}, {{ $sub_order->delivery_state }}</b>
                                      </td>
                                      <td>
                                          {{ $sub_order->reason }}
                                      </td>
                                      <td>
                                          @if($sub_order->status == 4)
                                            @if($sub_order->unique_suborder_id[0] != 'R')

                                                {!! Form::open(array('url' => '/return-delivery/'.$sub_order->task_id, 'method' => 'put')) !!}
                                                    {!! Form::hidden('sub_order_id', $sub_order->id, ['class' => 'form-control', 'required' => 'required']) !!}
                                                    <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                                        <i class="fa fa-check"></i>
                                                        Re-Assign
                                                    </button>
                                                {!! Form::close() !!}

                                                <a href="{{ URL::to('return-delivery').'/'.$sub_order->task_id }}?type=full" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                                    <i class="fa fa-close"></i>
                                                    Return
                                                </a>
                                            @endIf
                                          @elseif($sub_order->status == 3)
                                            <a href="{{ URL::to('return-delivery').'/'.$sub_order->task_id }}?type=pertial" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                                <i class="fa fa-close"></i>
                                                Return rest
                                            </a>
                                          @endIf
                                      </td>
                                  </tr>
                                  <!-- /.modal-dialog -->
                          @endforeach
                        @endif
                    </tbody>
                </table>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

    <script src="{{ URL::asset('custom/js/date-time.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('return-delivery', 'pickup');
        });
    </script>

@endsection
