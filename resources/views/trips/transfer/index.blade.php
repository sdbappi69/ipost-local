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
      <span>Transfer</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Transfer
  <small>Delivery</small>
</h1>

<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">

    <div class="portlet-title">
        <div class="caption">
            <i class="icon-edit font-dark"></i>
            <span class="caption-subject font-dark bold uppercase">Racked Products</span>
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
             {!! Form::select('merchant_id', array(''=>'All Merchants')+$merchants,$_GET['merchant_id'], ['class' => 'form-control','id' => 'merchant_id']) !!}
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('store_id', array(''=>'All Stores')+$stores,$_GET['store_id'], ['class' => 'form-control','id' => 'store_id']) !!}
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
             {!! Form::select('pickup_zone_id', array(''=>'All Pickup Zones')+$zones,$_GET['pickup_zone_id'], ['class' => 'form-control','id' => 'pickup_zone_id']) !!}
            <!-- </div> -->
        </div>

        <?php if(!isset($_GET['delivery_zone_id'])){$_GET['delivery_zone_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('delivery_zone_id', array(''=>'All Delivery Zones')+$zones,$_GET['delivery_zone_id'], ['class' => 'form-control','id' => 'delivery_zone_id']) !!}
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

    </div>

  </div>

</div>

<div class="col-md-12">
  
  <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

      <div class="portlet-title">
          <div class="caption">
              <i class="icon-edit font-dark"></i>
              <span class="caption-subject font-dark bold uppercase">Racked Products</span>
          </div>
      </div>

        <table class="table table-bordered table-hover" style="width: 100%;">
          <thead class="flip-content">
            <th>Unique ID</th>
            <th>Address</th>
            <th>Edit</th>
          </thead>
          <tbody>
            @if(count($sub_orders) > 0)
            @foreach($sub_orders as $sub_order)
            <tr>
              <td> {{ $sub_order->unique_suborder_id }}</td>

              <td><h4 class="uppercase">Shipping</h4>
                Address: <b>{{ $sub_order->delivery_address }}, {{ $sub_order->delivery_zone }}, {{ $sub_order->delivery_city }}, {{ $sub_order->delivery_state }}
              </td>
              <td>
                <a class="btn btn-info btn-xs" data-toggle="modal" data-target="#address_{{ $sub_order->unique_suborder_id }}" href="javascript:void(0)">Edit</a>
              </td>

              <div class="modal fade" id="address_{{ $sub_order->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
                  <div class="modal-dialog">
                      <div class="modal-content" style="padding: 15px; overflow: hidden;">

                        {!! Form::model(null, array('url' => '/transfer/'.$sub_order->suborder_id, 'method' => 'put')) !!}

                          <div class="form-group">
                              <label class="control-label">Customer Name</label>
                              {!! Form::text('delivery_name', $sub_order->delivery_name, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Customer Email</label>
                              {!! Form::text('delivery_email', $sub_order->delivery_email, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Customer Mobile</label>
                              {!! Form::text('delivery_msisdn', $sub_order->delivery_msisdn, ['class' => 'form-control', 'placeholder' => 'Mobile', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Customer At. Mobile</label>
                              {!! Form::text('delivery_alt_msisdn', $sub_order->delivery_alt_msisdn, ['class' => 'form-control', 'placeholder' => 'At. Mobile']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Select Customer Country</label>
                              {!! Form::select('delivery_country_id', $countries, $sub_order->delivery_country_id, ['class' => 'form-control country_id', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Select Customer State</label>
                              {!! Form::select('delivery_state_id', $states, $sub_order->delivery_state_id, ['class' => 'form-control state_id', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Select Customer City</label>
                              {!! Form::select('delivery_city_id', $cities, $sub_order->delivery_city_id, ['class' => 'form-control city_id', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Select Customer Zone</label>
                              {!! Form::select('delivery_zone_id', $zones, $sub_order->delivery_zone_id, ['class' => 'form-control zone_id', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              <label class="control-label">Customer Address</label>
                              {!! Form::text('delivery_address1', $sub_order->delivery_address, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
                          </div>

                          <div class="form-group">
                              {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
                          </div>

                        {!! Form::close() !!}

                      </div>
                  </div>
              </div>

            </tr>

          @endforeach
          @endif
        </tbody>
      </table>

      <div class="pagination pull-right">
          {!! $sub_orders->appends($_REQUEST)->render() !!}
      </div>

    </div>

</div>

 <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
 <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
 <script type="text/javascript">
  $(document ).ready(function() {
    // Navigation Highlight
    highlight_nav('transfer', 'delivery');
  });
</script>

<script type="text/javascript">

    // Get State list On Country Change
    $('.country_id').on('change', function(e) {
        e.preventDefault();
        get_states($(this).val());
    });

    // Get City list On State Change
    $('.state_id').on('change', function(e) {
        e.preventDefault();
        get_cities($(this).val());
    });

    // Get Zone list On City Change
    $('.city_id').on('change', function(e) {
        e.preventDefault();
        get_zones($(this).val());
    });
</script>

@endsection
