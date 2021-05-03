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
      <span>Merchant Bill</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Merchant Bill
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
            {!! Form::model($_GET,array('method' => 'get', 'id' => 'filter-form')) !!}

            <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Merchants</label>
                {!! Form::select('merchant_id', array(''=>'Select Merchant')+$merchants,$_GET['merchant_id'], ['class' => 'form-control js-example-basic-single','id' => 'merchant_id']) !!}
            </div>
            <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
            <div class="col-md-4">
                <label class="control-label">Stores</label>
                {!! Form::select('store_id', array(''=>'Select Store','all' => 'All')+$stores,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id']) !!}
            </div>
            @endif


            <?php if(!isset($_GET['invoice_id'])){$_GET['invoice_id'] = null;} ?>
            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
            @if(isset($_GET['store_id']) and !empty($_GET['store_id']))

            <div class="col-md-4">
                <label class="control-label">Invoice</label>
                {!! Form::select('invoice_id', array('' => 'Select Invoice','new'=>'Add new')+$invoices,$_GET['invoice_id'], ['class' => 'form-control js-example-basic-single','id' => 'invoice_id']) !!}
            </div>
            @endif
            @endif
            
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
    {!! Form::open(array('url' => secure_url('') . '/merchant-bill-submit', 'method' => 'post')) !!}

    @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
    <input type="hidden" name="merchant_id" value="{{$_GET['merchant_id']}}">
    @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
    <input type="hidden" name="store_id" value="{{$_GET['store_id']}}">
    <input type="hidden" name="invoice" id="invoice_id_2">
    @endif
    @endif

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
        <div class="portlet-body util-btn-margin-bottom-5">


            <div class="table-scrollable">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
                            @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
                            <th scope="col">{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                            @endif
                            @endif
                            <th scope="col">AWB</th>
                            <th scope="col">Order ID</th>
                            <th scope="col">Merchant</th>
                            <th scope="col">Store</th>
                            <th scope="col">Delivery Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($merchant_bill) > 0 )
                        <?php $total_bill_amount = 0; ?>
                        @foreach($merchant_bill as $c)
                        <tr>
                            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
                            @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
                            <td>{!!Form::checkbox('cashCollectionId[]',$c->id, false) !!}</td>
                            @endif
                            @endif

                            <td>{{$c->sub_order->unique_suborder_id or 'N/A'}}</td>
                            <td>{{$c->order->unique_order_id or 'N/A'}}</td>
                            <td>{{$c->merchant->name or 'N/A'}}</td>
                            <td>{{$c->store->store_id or 'N/A'}}</td>
                            <td>{{$c->bill_amount}}</td>
                        </tr>
                        <?php $total_bill_amount += $c->bill_amount; ?>
                        @endforeach
                        <tr>
                            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
                            <td style="text-align: center;" colspan="5"><b>Total: </b></td>
                            @else
                            <td style="text-align: center;" colspan="4"><b>Total: </b></td>
                            @endif
                            <td><b>{{$total_bill_amount}}</b></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if(!isset($_GET['merchant_id']) or empty($_GET['merchant_id']) )
            <div class="pagination pull-right">
                {{ $merchant_bill->appends($_REQUEST)->render() }}
            </div>

            @endif
            @if(count($merchant_bill) > 0 and isset($_GET['merchant_id']) and !empty($_GET['merchant_id']) )
            @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
            <center>
                <div>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </center>
            @endif
            @endif

        </div>
    </div>
</div>
{!! Form::close() !!}

<script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('create_merchant_bill', 'accounts_bills');
    });
</script>


<script type="text/javascript">
    $("#select_all_chk").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
</script>
<script type="text/javascript">
    $("#invoice_id").change(function () {
        $("#invoice_id_2").val($("#invoice_id").val());
    });
</script>
@endsection