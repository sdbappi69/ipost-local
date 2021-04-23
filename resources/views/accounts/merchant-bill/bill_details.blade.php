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
      <span>Invoice Bill Details</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Invoice Bill Details</h1>

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
            {!! Form::model($_GET,array('method' => 'get', 'id' => 'filter-form')) !!}

            <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Stores</label>
                {!! Form::select('store_id', array(''=>'Select Store')+$stores,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id']) !!}
            </div>

            <div class="col-md-3">
                <label class="control-label">Sub-Order ID</label>
                {!! Form::text('sub_order_id', null, ['class' => 'form-control','id' => 'sub_order_id']) !!}
            </div>

            <div class="col-md-3">
                <label class="control-label">Order ID</label>
                {!! Form::text('order_id', null, ['class' => 'form-control','id' => 'order_id']) !!}
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
        <div class="portlet-body util-btn-margin-bottom-5">
            <div class="table-scrollable">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Invoice No.</th>
                            <th scope="col">Sub-Order ID</th>
                            <th scope="col">Order ID</th>
                            <th scope="col">Merchant Order ID</th>
                            <th scope="col">Store</th>
                            <th scope="col">Delivery Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($invoice_bills) > 0 )
                        <?php $total_bill_amount = 0; ?>
                        @foreach($invoice_bills as $c)
                        <tr>
                            <td>{{$c->invoice_no or 'N/A'}}</td>
                            <td>{{$c->unique_suborder_id or 'N/A'}}</td>
                            <td>{{$c->unique_order_id or 'N/A'}}</td>
                            <td>{{$c->merchant_order_id or 'N/A'}}</td>
                            <td>{{$c->store_id or 'N/A'}}</td>
                            <td>{{$c->bill_amount}}</td>
                        </tr>
                        <?php $total_bill_amount += $c->bill_amount; ?>
                        @endforeach
                        <tr>
                            <td style="text-align: center;" colspan="5"><b>Total: </b></td>
                            
                            <td><b>{{$total_bill_amount}}</b></td>
                        </tr>
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
        highlight_nav('create_merchant_bill', 'accounts_bills');
    });
</script>
@endsection
