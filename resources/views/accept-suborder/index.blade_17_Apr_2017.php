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
                <a href="{{ secure_url('hub-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Receive Sub-Order</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Sub-Order
        <small> receive</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        
        <div class="col-md-12">
            {!! Form::open(array('url' => secure_url('') . '/accept-suborder/', 'method' => 'post')) !!}

                <div class="form-group col-md-10">

                    {!! Form::text('unique_id', null, ['class' => 'form-control', 'id' => 'remarks']) !!}

                </div>

                <div class="form-group col-md-2">
                    
                    <span class="form-group-btn">
                        <button class="btn blue" type="submit">Accept</button>
                    </span>

                </div>

            {!! Form::close() !!}

        </div>

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <!-- <th>Order ID</th> -->
                            <th>AWB</th>
                            <th>Came from</th>
                            <th>Product</th>
                        </thead>
                        <tbody>
                            @if(count($sub_orders) > 0)
                                @foreach($sub_orders as $sub_order)
                                  <tr>
                                      <!-- <td>
                                          <a target="_blank" class="label label-success" href="hub-order/{{ $sub_order->order->id }}">
                                              {{ $sub_order->order->unique_order_id }}
                                          </a>
                                      </td> -->
                                      <td>{{ $sub_order->unique_suborder_id }}</td>
                                      <td>{{ $sub_order->source_hub->title }}</td>
                                      <td>
                                          @foreach($sub_order->products as $product)
                                            <b>{{ $product->product_title }}</b>
                                            <br>
                                            ID: {{ $product->product_unique_id }}
                                            <br>
                                            Cat: {{ $product->product_category->name }}
                                            <br>
                                            Qty: {{ $product->quantity }}
                                          @endforeach
                                      </td>
                                  </tr>
                                @endforeach
                            @endif
                    </tbody>
                </table>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('accept-suborder', 'delivery');
        });
        
    </script>

@endsection
