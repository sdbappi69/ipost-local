@extends('layouts.appinside')

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL::to('merchant-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>View</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Order
        <small> {{ $order->unique_order_id }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        <div class="row step-line">
            <div class="col-md-3 mt-step-col first @if($order->order_status > 1) done @elseIf($order->order_status == 1) active @endIf ">
                <div class="mt-step-number bg-white"><i class="fa fa-check"></i></div>
                <div class="mt-step-title uppercase font-grey-cascade">Verified</div>
                <div class="mt-step-content font-grey-cascade">Order verification</div>
            </div>
            <div class="col-md-3 mt-step-col @if($order->order_status >= 5) done @elseIf($order->order_status >= 2) active @endIf ">
                <div class="mt-step-number bg-white"><i class="fa fa-cart-plus"></i></div>
                <div class="mt-step-title uppercase font-grey-cascade">Picked</div>
                <div class="mt-step-content font-grey-cascade">Pickup the product</div>
            </div>
            <div class="col-md-3 mt-step-col @if($order->order_status >= 8) done @elseIf($order->order_status >= 5) active @endIf ">
                <div class="mt-step-number bg-white"><i class="fa fa-shopping-cart"></i></div>
                <div class="mt-step-title uppercase font-grey-cascade">In Transit</div>
                <div class="mt-step-content font-grey-cascade">Product on delivery</div>
            </div>
            <div class="col-md-3 mt-step-col last @if($order->order_status >= 9) done @elseIf($order->order_status == 8) active @endIf ">
                <div class="mt-step-number bg-white"><i class="fa fa-cart-arrow-down"></i></div>
                <div class="mt-step-title uppercase font-grey-cascade">Delivered</div>
                <div class="mt-step-content font-grey-cascade">Product delivered</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Shipping</span>
                        <span class="caption-helper">Information</span>
                    </div>
                </div>
                <div class="portlet-body util-btn-margin-bottom-5">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <td>:</td>
                                <td>{{ $order->delivery_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>:</td>
                                <td>{{ $order->delivery_email }}</td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td>:</td>
                                <td>{{ $order->delivery_msisdn }}</td>
                            </tr>
                            <tr>
                                <th>Alt. Mobile</th>
                                <td>:</td>
                                <td>{{ $order->delivery_alt_msisdn }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>:</td>
                                <td>{{ $order->delivery_zone->city->state->country->name }}</td>
                            </tr>
                            <tr>
                                <th>State</th>
                                <td>:</td>
                                <td>{{ $order->delivery_zone->city->state->name }}</td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td>:</td>
                                <td>{{ $order->delivery_zone->city->name }}</td>
                            </tr>
                            <tr>
                                <th>Zone</th>
                                <td>:</td>
                                <td>{{ $order->delivery_zone->name }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>:</td>
                                <td>{{ $order->delivery_address1 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Order</span>
                        <span class="caption-helper">Detail</span>
                    </div>
                </div>
                <div class="portlet-body util-btn-margin-bottom-5">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="flip-content">
                                <th>ID</th>
                                <th>Product</th>
                                <th class="numeric">Quantity</th>
                                <th>Picking Attempt</th>
                                <!-- <th>Status</th>
                                <th>Map</th> -->
                            </thead>
                            <tbody>
                                @foreach($order->products as $row)
                                    <tr>
                                        <td>{{ $row->product_unique_id }}</td>
                                        <td>{{ $row->product_title }}</td>
                                        <td class="numeric">{{ $row->quantity }}</td>
                                        <td>{{ $row->picking_attempts }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Payment</span>
                        <span class="caption-helper">Information</span>
                    </div>
                </div>
                <div class="portlet-body util-btn-margin-bottom-5">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="flip-content">
                                <th>Amount</th>
                                <th>Delivery Charge</th>
                                <th>COD</th>
                            </thead>
                            <tr>
                                <td>{{ $order->total_product_price }}</td>
                                <td>{{ $order->delivery_payment_amount }}</td>
                                <td>
                                    {{ $order->total_amount }}
                                    @if($order->delivery_pay_by_cus == 1)
                                        <p>Including delivery charge</p>
                                    @else
                                        <p>Excluding delivery charge</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-order-manage', 'merchant-orders');
        });
    </script>

@endsection
