@extends('layouts.appinside')

@section('content')

    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Cash Collection</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title">Cash Collection
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
                {!! Form::open(array('method' => 'get')) !!}
                <?php if (!isset($_GET['sub_unique_id'])) {
                    $_GET['sub_unique_id'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Sub-Order ID</label>
                    <input type="text" value="{{$_GET['sub_unique_id']}}" class="form-control" name="sub_unique_id"
                           id="sub_unique_id" placeholder="Sub-Order ID">
                </div>

                <?php if (!isset($_GET['product_name'])) {
                    $_GET['product_name'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Product Name</label>
                    <input type="text" value="{{$_GET['product_name']}}" class="form-control" name="product_name"
                           id="product_name" placeholder="Product Name">
                </div>
                <?php if (!isset($_GET['product_category_id'])) {
                    $_GET['product_category_id'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Product Category</label>
                    {!! Form::select('product_category_id', ['' => 'All Categories'] + $product_categories, null, ['class' => 'form-control js-example-basic-single', 'id' => 'product_category_id']) !!}
                </div>
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

    <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Collecting Cash List</span>
                </div>
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-bordered table-hover" id="example0">
                    <thead class="flip-content">
                    <th>Sub-Order ID</th>
                    <th>Product Name</th>
                    <th>Product Category</th>
                    <th>Seller</th>
                    <th>Deliveryman</th>
                    <th>Delivery Time</th>
                    <th>Merchant Order Id</th>
                    <th>Pyment Type</th>
                    <th>Qty</th>
                    <th>Collected</th>
                    <th>Delivery Amount</th>
                    </thead>
                    <tbody>
                    @if(count($callectionCashDetails) > 0 )
                        <?php
                        $total_quantity = 0;
                        $total_collected_amount = 0;
                        $final_total_delivery_charge = 0;?>
                        @foreach($callectionCashDetails as $c)
                            <tr>
                                <td>
                                    @if(Auth::user()->hasRole('hubmanager'))
                                    <a target="_blank" href="{{url('hub-order',$c->order->id)}}" class="btn-primary"> {{$c->unique_suborder_id}}</a>
                                    @else
                                    <a target="_blank" href="{{url('order',$c->order->id)}}" class="btn-primary"> {{$c->unique_suborder_id}}</a>
                                    @endif
                                </td>
                                <td>{{$c->product->product_title}}</td>
                                <td>{{$c->product->product_category->name}}</td>
                                <td>{{$c->product->pickup_location->title}}</td>
                                <td>{{$c->rider_name}}</td>
                                <td>{{$c->delivery_time}}</td>
                                <td>{{$c->merchant_order_id}}</td>
                                <td>{{$c->payment_name}}</td>
                                <td>{{$c->product->quantity}}</td>
                                <td>{{$c->product->delivery_paid_amount}}</td>
                                <td>{{$c->product->total_delivery_charge}}</td>
                            </tr>
                            <?php
                            $total_quantity += $c->product->quantity;
                            $total_collected_amount += $c->product->delivery_paid_amount;
                            $final_total_delivery_charge += $c->product->total_delivery_charge; ?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>{{ $total_quantity }}</b></td>
                            <td><b>{{ $total_collected_amount }}</b></td>
                            <td><b>{{ $final_total_delivery_charge }} </b>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                @if(count($callectionCashDetails) > 0 )
                    <center>
                        {{$callectionCashDetails->appends($_REQUEST)->render()}}
                    </center>
                @endif

            </div>
        </div>
    </div>
    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script type="text/javascript">
    </script>
@endsection
