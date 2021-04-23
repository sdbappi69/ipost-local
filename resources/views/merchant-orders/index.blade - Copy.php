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
                <span>Order</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Order
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    @if(count($orders) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Order</span>
                        <span class="caption-helper">list</span>
                    </div>
                </div>
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Order Id</th>
                            <th style="width:106px;">Status</th>
                            <th>Created</th>
                            <th>Store Id</th>
                            <th>Delivery Cost</th>
                            <th>Delivery Address</th>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    @if($order->order_status == ''||$order->order_status == '0')
                                        {{-- */ $url = URL::to('merchant-order').'/'.$order->id.'/edit?step=1'; /* --}}
                                    @else
                                        {{-- */ $url = URL::to('merchant-order').'/'.$order->id; /* --}}
                                    @endIf
                                    <td>
                                        <a class="label label-success" href="{{ $url }}">
                                            {{ $order->unique_order_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ $url }}" class="btn @if($order->order_status > 1) green-meadow @elseIf($order->order_status == 1) blue-madison @else default @endIf" title="Verified"></a>
                                        <a href="{{ $url }}" class="btn @if($order->order_status >= 5) green-meadow @elseIf($order->order_status >= 2) blue-madison @else default @endIf" title="Picked"></a>
                                        <a href="{{ $url }}" class="btn @if($order->order_status >= 9) green-meadow @elseIf($order->order_status >= 5) blue-madison @else default @endIf" title="Shipping"></a>
                                        <a href="{{ $url }}" class="btn @if($order->order_status == 10) green-meadow @elseIf($order->order_status == 9) blue-madison @else default @endIf" title="Delivered"></a>
                                    </td>
                                    <td>{{ $order->created_at }}</td>
                                    <td>{{ $order->store->store_id }}</td>
                                    <td>{{ $order->delivery_payment_amount }}</td>
                                    <td>{{ $order->delivery_address1 }}, {{ $order->delivery_zone->name }}, {{ $order->delivery_zone->city->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $orders->render() }}
                    </div>
                </div>
            </div>
        </div>
    @endIf

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-order-manage', 'merchant-orders');

            $('#example0').DataTable({
                "order": [],
            });
        });
    </script>

@endsection
