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
            <span>Checkout History</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Checkout History
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
            <?php if (!isset($_GET['merchant_batch_id'])) {
                $_GET['merchant_batch_id'] = null;
            } ?>
            <div class="col-md-4">
                <label class="control-label">Merchant Batch ID</label>
                <input type="text" value="{{$_GET['merchant_batch_id']}}" class="form-control"
                       name="merchant_batch_id"
                       id="merchant_batch_id" placeholder="Merchant Batch ID">
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
                <span class="caption-subject font-dark bold uppercase">Checkout History List</span>
            </div>
        </div>
        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-bordered table-hover" id="example0">
                <thead class="flip-content">
                <th>Merchant Batch ID</th>
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
                @if(count($finalMerchantAccumulatedCash) > 0 )
                    @foreach($finalMerchantAccumulatedCash as $c)
                        <tr>
                            <td>{{$c->merchant_batch_id}}</td>
                            <td>{{$c->date}}</td>
                            <td>{{$c->total_quantity}}</td>
                            <td>{{$c->total_collected_amount}}</td>
                            <td>{{$c->total_delivery_charge}}</td>
                            <td>{{ ($c->status == 1)? "Pending": ($c->status == 2)? "Confirm":"Inactive" }}</td>
                            <td>{{$c->merchant_transaction_id}}</td>
                            <td>{{$c->remark}}</td>
                            <td><a target="_blank"  class="btn btn-info" href="{{ url('collection-cash-details',$c->id) }}" ><i class="fa fa-eye"></i> &nbsp; View</a></td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            @if(count($finalMerchantAccumulatedCash) > 0 )
                <center>
                    {{$finalMerchantAccumulatedCash->appends($_REQUEST)->render()}}
                </center>
            @endif
        </div>
    </div>
</div>
<script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
        type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // Navigation Highlight
        highlight_nav('checkout_history', 'accounts_bills');
    });
</script>
@endsection
