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
            <a href="{{ secure_url('trip') }}">Trips</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Update</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Trips
    <small>update</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

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

      {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

        <?php if(!isset($_GET['sub_order_id'])){$_GET['sub_order_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control" name="sub_order_id" id="sub_order_id" placeholder="AWB">
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

        <?php if(!isset($_GET['delivery_hub_id'])){$_GET['delivery_hub_id'] = null;} ?>
        <div class="col-md-6" style="margin-bottom:5px;">
            <!-- <div class="row"> -->
             {!! Form::select('delivery_hub_id', array(''=>'All Destination Hubs')+$hubs,$_GET['delivery_hub_id'], ['class' => 'form-control js-example-basic-single','id' => 'delivery_hub_id']) !!}
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


      {!! Form::open(array('url' => secure_url('') . '/add-bulk-trip-cart', 'method' => 'post')) !!}

        <div class="col-md-12">
            <button type="submit" class="btn btn-primary add-to-cart pull-right"><i class="fa fa-shopping-cart"></i> Add to cart</button>
        </div>

        <table class="table table-bordered table-hover" id="example0">
          <thead class="flip-content">
            <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
            <th>AWB</th>
            <th>Destination</th>
            <th>Products</th>
            <th>Invoice</th>
          </thead>
          <tbody>
            @if(count($sub_orders) > 0)
            @foreach($sub_orders as $sub_order)
            <tr>
              <td>{!!Form::checkbox('unique_suborder_ids[]',$sub_order->unique_suborder_id, false) !!}</td>
              <td> {{ $sub_order->unique_suborder_id }}</td>

                <td> {{ "Delivery Hub: " .$sub_order->delivery_hub }}<br/>
                    {{ "Next Hub: " .$sub_order->next_hub_title }}</td>

              <td><h4 class="uppercase">Product</h4>
                Title: <b>{{ $sub_order->product_title }}</b><br>
                Quantity: <b>{{ $sub_order->quantity }}</b><br>
                Product Category: <b>{{ $sub_order->product_category }}</b>
              </td>
              <td>
                <a class="btn btn-info btn-xs" target="_blank" href="{{secure_url('suborder-invoice/'.$sub_order->unique_suborder_id)}}">Invoice</a>
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
            <span class="caption-subject font-dark bold uppercase">{{ $trip->unique_trip_id }}</span>
        </div>
    </div>

    <div class="portlet-body util-btn-margin-bottom-5">

      {!! Form::open(array('url' => secure_url('') . '/add-trip-cart', 'method' => 'post')) !!}

        <div class="col-md-8" style="margin-bottom:5px;">
            <input type="text" value="" class="form-control focus_it" name="unique_suborder_id" placeholder="AWB" required="required">
        </div>

        <div class="col-md-4" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-primary add-to-cart pull-right"><i class="fa fa-shopping-cart"></i> Add to cart</button>
        </div>

      {!! Form::close() !!}

      @if(Session::has('trip_cart'))

        <div class="portlet light tasks-widget bordered">

            <div class="portlet-body util-btn-margin-bottom-5">

                @foreach(Session::get('trip_cart') AS $cart)

                  <a href="{{ secure_url('remove-trip-cart/'.$cart) }}" class="btn btn-xs green">
                    <i class="fa fa-times"></i> {{ $cart }} 
                  </a>

                @endforeach

            </div>

        </div>

      @endIf

      <div class="tripconsignment update-tripconsignment">
        {!! Form::open(array('url' => secure_url('') . '/update-tripconsignments-trip-submit', 'method' => 'post')) !!}

          <input type="hidden" value="{{ $trip->id }}" class="form-control" name="tripconsignment_id" id="tripconsignment_id">

          <div class="col-md-12" style="margin-bottom:5px;">
               <button type="submit" class="btn btn-primary col-md-12"><i class="fa fa-check"></i> Add to Trip</button>
          </div>

        {!! Form::close() !!}

        <div class="portlet light tasks-widget bordered">

          <div class="portlet-body util-btn-margin-bottom-5">

            <table class="table table-bordered table-hover" id="example0">
              <thead class="flip-content">
                <th>AWB</th>
                <th>Destination</th>
                <th>Products</th>
                <th>Action</th>
              </thead>
              <tbody class="loaded-suborder">

              </div>
            </tbody>
          </table>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>

<script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('trip-manage', 'trips');

        $('.loaded-suborder').html('');
        var url = site_path + 'on_trip/'+{{ $trip->id }};

        var html_tbl = '';
        $.getJSON(url+'?callback=?',function(data){

          $('.loaded-suborder').html('');
          $.each(data, function(key, item) { 

              html_tbl = html_tbl+'<tr>';
              html_tbl = html_tbl+'<td>'+item.unique_suborder_id+'</td>';
              html_tbl = html_tbl+'<td>Delivery Hub: ' + item.delivery_hub + '<br/>Next Hub: ' + item.next_hub_title + '</td>';
              html_tbl = html_tbl+'<td><h4 class="uppercase">Product</h4>Title: <b>'+item.product_title+'</b><br>Quantity: <b>'+item.quantity+'</b><br>Product Category: <b>'+item.product_category+'</b></td>';
              html_tbl = html_tbl+'<td><a href="'+site_path+'remove-from-trip/'+item.trip_id+'/'+item.suborder_id+'" class="btn btn-xs red"><i class="fa fa-times"></i> Remove</a></td>';
              html_tbl = html_tbl+'</tr>';

          });

          $('.loaded-suborder').html(html_tbl);

        });

    });

    $("#select_all_chk").change(function () {
      $("input:checkbox").prop('checked', $(this).prop("checked"));
    });

</script>

@endsection
