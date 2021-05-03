@extends('layouts.appinside')

@section('content')

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
            <span>View</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Trips
    <small>view</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">

    <div class="portlet-title">
        <div class="caption">
            <i class="icon-edit font-dark"></i>
            <span class="caption-subject font-dark bold uppercase">Sub-Orders</span>
        </div>
    </div>

    <table class="table table-bordered table-hover" id="example0">
      <thead class="flip-content">
        <th>AWB</th>
        <th>Destination</th>
        <th>Products</th>
        <th>Status</th>
      </thead>
      <tbody>
        @if(count($sub_orders) > 0)
          @foreach($sub_orders as $sub_order)
          <tr>
            <td> {{ $sub_order->unique_suborder_id }}</td>

            <td> {{ $sub_order->delivery_hub }}</td>

            <td><h4 class="uppercase">Product</h4>
              Title: <b>{{ $sub_order->product_title }}</b><br>
              Quantity: <b>{{ $sub_order->quantity }}</b><br>
              Product Category: <b>{{ $sub_order->product_category }}</b>
            </td>
            <td>
              @if($sub_order->sub_order_trip_status == 1)
                On Trip
{{--              @elseif($sub_order->sub_order_trip_status == 2)--}}
              @else
                Reached
              @endIf
            </td>

          </tr>

        @endforeach
      @endif
    </tbody>
  </table>

  </div>
</div>

<script type="text/javascript">

    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('trip-manage', 'trips');
    });

</script>

@endsection
