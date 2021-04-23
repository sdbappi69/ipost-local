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
      <span>Consignments</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Consignments
  <small>Pick-Up</small>
</h1>

<div class="col-md-6">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">

    <div class="portlet-title">
        <div class="caption">
            <i class="icon-edit font-dark"></i>
            <span class="caption-subject font-dark bold uppercase">Pending</span>
        </div>
    </div>

    <div class="portlet-body util-btn-margin-bottom-5">

      {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}

        <?php if(!isset($_GET['sub_order_id'])){$_GET['sub_order_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control" name="sub_order_id" id="sub_order_id" placeholder="Sub-Order ID">
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['order_id'])){$_GET['order_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             <input type="text" value="{{$_GET['order_id']}}" class="form-control" name="order_id" id="order_id" placeholder="Order ID">
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['merchant_order_id'])){$_GET['merchant_order_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             <input type="text" value="{{$_GET['merchant_order_id']}}" class="form-control" name="merchant_order_id" id="merchant_order_id" placeholder="Merchant Order ID">
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('merchant_id', array(''=>'All Merchants')+$merchants,$_GET['merchant_id'], ['class' => 'form-control js-example-basic-single','id' => 'merchant_id']) !!}
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('store_id', array(''=>'All Stores')+$stores,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id']) !!}
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['customer_mobile_no'])){$_GET['customer_mobile_no'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             <input type="text" value="{{$_GET['customer_mobile_no']}}" class="form-control" name="customer_mobile_no" id="customer_mobile_no" placeholder="Customer mobile NO.">
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['pickup_zone_id'])){$_GET['pickup_zone_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('pickup_zone_id', array(''=>'All Pickup Zones')+$zones,$_GET['pickup_zone_id'], ['class' => 'form-control js-example-basic-single','id' => 'pickup_zone_id']) !!}
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['delivery_zone_id'])){$_GET['delivery_zone_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('delivery_zone_id', array(''=>'All Delivery Zones')+$zones,$_GET['delivery_zone_id'], ['class' => 'form-control js-example-basic-single','id' => 'delivery_zone_id']) !!}
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['start_date'])){$_GET['start_date'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('start_date',$_GET['start_date'], ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                </div>
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['end_date'])){$_GET['end_date'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('end_date',$_GET['end_date'], ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                </div>
            <!-- </div> -->
        </div>

        <div class="col-md-12">
            <button type="submit" class="btn btn-primary filter-btn"><i class="fa fa-search"></i> Filter</button>
        </div>
        <div class="clearfix"></div>

      {!! Form::close() !!}


      {!! Form::open(array('url' => '/add-bulk-pickup-cart', 'method' => 'post')) !!}

        <div class="col-md-12">
            <button type="submit" class="btn btn-primary add-to-cart pull-right"><i class="fa fa-shopping-cart"></i> Add to cart</button>
        </div>

        <table class="table table-bordered table-hover" id="example0">
          <thead class="flip-content">
            <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
            <th>Unique ID</th>
            <th>Address</th>
            <th>Documents</th>
          </thead>
          <tbody>
            @if(count($sub_orders) > 0)
            @foreach($sub_orders as $sub_order)
            <tr>
              <td>{!!Form::checkbox('unique_suborder_ids[]',$sub_order->unique_suborder_id, false) !!}</td>
              <td> {{ $sub_order->unique_suborder_id }}</td>

              <td><h4 class="uppercase">Picking Address</h4>

                @if($sub_order->return == 1)

                  <b>{{ $sub_order->delivery_name }}</b>
                  <br>
                  Address: <b>{{ $sub_order->delivery_address }}, {{ $sub_order->delivery_zone }}, {{ $sub_order->delivery_city }}, {{ $sub_order->delivery_state }}
                      @if(lastDeniedUser($sub_order->suborder_id))
                        <br/>Last Denied By : <b>{{lastDeniedUser($sub_order->suborder_id)}}</b>
                      @endif

                @else

                  <b>{{ $sub_order->pickup_title }}</b>
                  <br>
                  Address: <b>{{ $sub_order->pickup_address }}, {{ $sub_order->pickup_zone }}, {{ $sub_order->pickup_city }}, {{ $sub_order->pickup_state }}
                      @if(lastDeniedUser($sub_order->suborder_id))
                      <br/>Last Denied By : <b>{{lastDeniedUser($sub_order->suborder_id)}}</b>
                      @endif

                @endif

              </td>
              <td>
                <a class="btn btn-info btn-xs" target="_blank" href="{{url('common-awb-single/'.$sub_order->suborder_id)}}">AWB</a>
                <a class="btn btn-success btn-xs" target="_blank" href="{{url('common-invoice-single/'.$sub_order->suborder_id)}}">Invoice</a>
              </td>

              <!-- <td><button type="button" value="{{ $sub_order->unique_suborder_id }}" class="print_modal"><i class="fa fa-folder-open-o" aria-hidden="true"></i></button></td> -->


            </tr>

            <!-- /.modal-dialog -->
          </div>
          @endforeach
          @endif
        </tbody>
      </table>
      {!! Form::close() !!}
  </div>
</div>
</div>

<div class="col-md-6 animated flipInX">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered" style="overflow: hidden;">

    <div class="portlet-title">
        <div class="caption">
            <i class="icon-edit font-dark"></i>
            <span class="caption-subject font-dark bold uppercase">Create Consignment</span>
        </div>
    </div>

    <div class="portlet-body util-btn-margin-bottom-5">

      {!! Form::open(array('url' => 'add-pickup-cart', 'method' => 'post')) !!}

        <div class="col-md-8" style="margin-bottom:5px;">
            <input type="text" value="" class="form-control focus_it" name="unique_suborder_id" placeholder="Sub-Order ID" required="required">
        </div>

        <div class="col-md-4" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-primary add-to-cart pull-right"><i class="fa fa-shopping-cart"></i> Add to cart</button>
        </div>

      {!! Form::close() !!}

      @if(Session::has('pickup_cart'))

        <div class="portlet light tasks-widget bordered">

            <div class="portlet-body util-btn-margin-bottom-5">

                @foreach(Session::get('pickup_cart') AS $cart)

                  <a href="remove-pickup-cart/{{ $cart }} " class="btn btn-xs green">
                    <i class="fa fa-times"></i> {{ $cart }} 
                  </a>

                @endforeach

            </div>

            <small>
              <cite title="Source Title">
               <b>{{ count(Session::get('pickup_cart')) }}</b> Orders added to the cart
              </cite>
            </small>

        </div>

      @endIf

      {!! Form::open(array('url' => 'consignments-pick-up-submit', 'method' => 'post')) !!}

        <div class="col-md-8" style="margin-bottom:5px;">
             {!! Form::select('picker_id',['' => 'Select Pickup Man']+$pickupman,old('picker_id'), ['class' => 'form-control js-example-basic-single', 'id' => 'picker_id', 'required' => 'required']) !!}
        </div>

        <div class="col-md-4" style="margin-bottom:5px;">
             <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-check"></i> Create</button>
        </div>

      {!! Form::close() !!}

    </div>

  </div>

</div>

<script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document ).ready(function() {
  // Navigation Highlight
  highlight_nav('pick_up_consignments', 'consignments');

  // $('#example0').DataTable({
  //     "order": [],
  // });
});
</script>

<script type="text/javascript">
 $(".print_it").click(function(){
  var product_id = $(this).attr("product_id");
    //alert(product_id);
    $("#"+product_id).print({
      globalStyles: true,
      mediaPrint: true,
      stylesheet: null,
      noPrintSelector: ".no-print",
      iframe: true,
      append: null,
      prepend: null,
      manuallyCopyFormValues: true,
      deferred: $.Deferred(),
      timeout: 750,
      title: null,
      doctype: '<!doctype html>'
    });
  });
</script>

<script type="text/javascript">
  $(".print_modal").click(function(){
    var product_id = $(this).val();

    $('#invoice_'+product_id).modal('show');
  }); 
</script>

<script type="text/javascript">
  $("#select_all_chk").change(function () {
    // $("input.selected_pick_up").prop('checked', $(this).prop("checked"));
    $("input:checkbox").prop('checked', $(this).prop("checked"));
  });
  // $("#select_all_return").change(function () {
  //   $("input.selected_return").prop('checked', $(this).prop("checked"));
  // });
</script>

@endsection
