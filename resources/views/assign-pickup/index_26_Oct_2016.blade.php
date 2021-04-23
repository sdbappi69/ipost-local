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
                <a href="{{ URL::to('hub-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Assign Pickup</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Orders
        <small> verified</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        
        @if(count($orders) > 0)

            @foreach($orders as $order)

                <div class="col-md-4 small">

                    <div class="mt-element-ribbon bg-grey-steel">

                        {{-- */ $view_url = URL::to('hub-order').'/'.$order->id; /* --}}
                        <a href="{{ $view_url }}" class="ribbon ribbon-right ribbon-vertical-right ribbon-shadow ribbon-border-dash-vert ribbon-color-default uppercase">
                            <div class="ribbon-sub ribbon-bookmark"></div>
                            <i class="fa fa-cube"></i>
                        </a>

                        <div class="mt-element-ribbon bg-grey-steel">
                            <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">{{ $order->unique_order_id }}</div>
                            <div class="ribbon-content">
                                COD: {{ $order->total_amount }} TK

                                <h4 class="uppercase">Shipping Address</h4>
                                Name: <strong>{{ $order->delivery_name }}</strong>
                                <br>
                                Phone: {{ $order->delivery_msisdn }}, {{ $order->delivery_alt_msisdn }}
                                <br>
                                Address: {{ $order->delivery_address1 }}, {{ $order->delivery_zone->name }}, {{ $order->delivery_zone->city->name }}, {{ $order->delivery_zone->city->state->name }}
                                <br>
                                Destination Hub: <strong>{{ $order->delivery_zone->hub->title }}</strong>
                                
                                <h4 class="uppercase">Products</h4>
                                <div class="product-summery-tbl">
                                    <table style="width:100%">
                                        <thead>
                                            <th>Product</th>
                                            <th style="border-left:1px solid #FFFFFF;border-right:1px solid #FFFFFF; padding: 0px 5px;">Qty</th>
                                            <th>Pickup</th>
                                        </thead>
                                        <tbody>
                                            {{--*/ $hubs = array() /*--}}
                                            @foreach($order->products as $product)
                                                <tr style="border-bottom:1px solid #FFFFFF;">
                                                    <td>
                                                        {{ $product->title }}
                                                        <br>
                                                        Cat: {{ $product->product_category->name }}
                                                    </td>
                                                    <td class="numeric" style="border-left:1px solid #FFFFFF;border-right:1px solid #FFFFFF; padding: 0px 5px;">{{ $product->quantity }}</td>
                                                    <td>
                                                        Name: {{ $product->pickup_location->title }}
                                                        <br>
                                                        Phone: {{ $product->pickup_location->msisdn }}, {{ $product->pickup_location->alt_msisdn }}
                                                        <br>
                                                        Address: {{ $product->pickup_location->address1 }}, {{ $product->pickup_location->zone->name }}, {{ $product->pickup_location->zone->city->name }}, {{ $product->pickup_location->zone->city->state->name }}
                                                    </td>
                                                </tr>

                                                {{--*/ $hubs[$product->pickup_location->zone->hub->id] = $product->pickup_location->zone->hub->title /*--}}

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div class="row">
                                    {!! Form::open(array('url' => '/assign-pickup/'.$order->id, 'method' => 'put')) !!}
                                        {!! Form::hidden('order_status', '3', ['class' => 'form-control', 'required' => 'required']) !!}
                                        <div class="form-group">
                                            <select name="picker_id" class="form-control js-example-basic-single js-country" required="required">
                                                <option value="">Select one</option>
                                                @foreach($pickupman as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                            <i class="fa fa-check"></i>
                                            Assign
                                        </button>
                                    {!! Form::close() !!}
                                </div>

                            </div>
                        </div>

                    </div>
                    
                </div>

            @endforeach

        @else

            <p>No task available here.</p>

        @endIf

    </div>

    <div class="pagination pull-right">
        {{ $orders->render() }}
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('assign-pickup', 'tasks');
        });
    </script>

@endsection
