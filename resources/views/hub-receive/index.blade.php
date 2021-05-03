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
                <span>Hub Receviced</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Sub-Orders
        <small> queued</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        
        @if(count($sub_orders) > 0)

            @foreach($sub_orders as $sub_order)

                <div class="col-md-4 small">

                    <div class="mt-element-ribbon bg-grey-steel" style="overflow: hidden;">
                        <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">{{ $sub_order->unique_suborder_id }}</div>
                        <br><br>
                        <h4 class="uppercase">Products</h4>
                        <div class="product-summery-tbl col-md-12">
                            <table style="width:100%">
                                <thead>
                                    <th>Product</th>
                                    <th style="bsub_order-left:1px solid #FFFFFF;bsub_order-right:1px solid #FFFFFF; padding: 0px 5px;">Qty</th>
                                </thead>
                                <tbody>
                                    {{--*/ $hubs = array() /*--}}
                                    @foreach($sub_order->products as $product)
                                        <tr style="bsub_order-bottom:1px solid #FFFFFF; border-bottom: 1px solid #666666; border-top: 1px solid #666666;">
                                            <td>
                                                <b>{{ $product->product_title }}</b>
                                                <br>
                                                Cat: {{ $product->product_category->name }}
                                            </td>
                                            <td class="numeric" style="bsub_order-left:1px solid #FFFFFF;bsub_order-right:1px solid #FFFFFF; padding: 0px 5px;">{{ $product->quantity }}</td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div class="col-md-12">
                            {!! Form::open(array('url' => secure_url('') . '/hub-receive/'.$sub_order->id, 'method' => 'put')) !!}
                                {!! Form::hidden('order_id', $sub_order->order->id, ['class' => 'form-control', 'required' => 'required']) !!}
                                <div class="form-group">
                                    <label class="control-label">Delivery man</label>
                                    <select name="deliveryman_id" class="form-control js-example-basic-single js-country" required="required">
                                        <option value="">Select one</option>
                                        @foreach($rider as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Remarks</label>
                                    {!! Form::text('remarks', null, ['class' => 'form-control', 'id' => 'remarks']) !!}
                                </div>
                                <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                    <i class="fa fa-check"></i>
                                    Received
                                </button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    
                </div>

            @endforeach

        @else

            <p>No task available here.</p>

        @endIf

    </div>

    <div class="pagination pull-right">
        {{ $sub_orders->appends($_REQUEST)->render() }}
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('hub-receive', 'tasks');
        });
    </script>

@endsection
