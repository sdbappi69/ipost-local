@extends('layouts.appinside')
@section('content')
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span> Receive Hub Payment</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title">Receive Hub Payment
        <small>All</small>
    </h1>
    @include('partials.errors')
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
                {!! Form::open(array('method' => 'get')) !!}
                <?php if (!isset($_GET['batch_id'])) {
                    $_GET['batch_id'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Cash Transfer ID</label>
                    <input type="text" value="{{$_GET['batch_id']}}" class="form-control" name="batch_id"
                           id="batch_id" placeholder="Cash Transfer ID">
                </div>
                <?php if (!isset($_GET['status'])) {
                    $_GET['status'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Status</label>
                    {!! Form::select('status', ['1' => 'Pending','2'=>'Confirm'], null, ['placeholder'=>'Select All','class' => 'form-control js-example-basic-single', 'id' => 'product_category_id']) !!}

                </div>
                <?php if (!isset($_GET['start_date'])) {
                    $_GET['start_date'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order from</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd">
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                        {!! Form::text('start_date',$_GET['start_date'], ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>
                <?php if (!isset($_GET['end_date'])) {
                    $_GET['end_date'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order to</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd">
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                        {!! Form::text('end_date',$_GET['end_date'], ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="control-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control">Filter</button>
                </div>
                <div class="clearfix"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Receive Hub Payment Lists</span>
                </div>
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-bordered table-hover" id="example0">
                    <thead class="flip-content">
                    <th>Cash Transfer ID</th>
                    <th>Date</th>
                    <th>Total Qty</th>
                    <th>Collected</th>
                    <th>Delivery Amount</th>
                    <th>Status</th>
                    <th>Transaction ID</th>
                    <th>Remarks</th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                    @if(count($accumulateLists) > 0 )
                        @foreach($accumulateLists as $c)
                            <tr>

                                <td>{{$c->batch_id}}</td>
                                <td>{{$c->date}}</td>
                                <td>{{$c->total_quantity}}</td>
                                <td>{{$c->total_collected_amount}}</td>
                                <td>{{$c->total_delivery_charge}}</td>
                                <td> @if($c->status == 1)
                                        <span class="badge badge-info">Pending</span>
                                    @elseif($c->status == 2)
                                        <span class="badge badge-success" >Confirm</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$c->transaction_id}}</td>
                                <td>{{$c->remark}}</td>
                                <td>
                                    @if($c->status == 1)
                                    <button type="button" class="btn btn-primary confirm-accumulated"
                                            data-id="{{$c->id}}" data-toggle="modal" data-target="#confirm">
                                        <i class="fa fa-check-circle"></i>Confirm
                                    </button>
                                    @endif
                                    <a class="btn btn-info" target="_blank"  href="{{ url('collection-cash-details',$c->id) }}" ><i class="fa fa-eye"></i> &nbsp; View</a>
                                </td>
                            </tr>

                        @endforeach
                    @endif
                    </tbody>
                </table>
                @if(count($accumulateLists) > 0 )
                    <center>
                        {{$accumulateLists->appends($_REQUEST)->render()}}
                    </center>
                @endif
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Receive Hub Payment Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(array('url' => 'accumulated-collected-cash-confirm', 'method' => 'post')) !!}
                    <input type="hidden" name="accumulated_id" id="accumulated_id" value="">
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label">Transaction ID <span style="color: red;">(*)</span></label>--}}
{{--                        {!! Form::text('transaction_id', null, ['class' => 'form-control','palceholder'=>'Transaction ID']) !!}--}}
{{--                        --}}
{{--                    </div>--}}
                    <div class="form-group">
                        <label class="control-label">Remark</label>
                        {!! Form::textarea('remark', null, ['class' => 'form-control','palceholder'=>'Remark', 'rows' => 2, 'cols' => 10]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('receive_hub_payment', 'accounts_bills');
        });

        $(document).on("click", ".confirm-accumulated", function () {
            var accumulatedId = $(this).data('id');
            $(".modal-body #accumulated_id").val(accumulatedId);
        });
    </script>
@endsection
