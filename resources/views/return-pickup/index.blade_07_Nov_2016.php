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
                <span>Receive Product</span>
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
        
        @if(count($products) > 0)

            @foreach($products as $product)

                <div class="col-md-4 small">

                    <div class="mt-element-ribbon bg-grey-steel">

                        <!-- <a href="{{ URL::to('product').'/'.$product->id.'/edit' }}" class="ribbon ribbon-right ribbon-vertical-right ribbon-shadow ribbon-border-dash-vert ribbon-color-default uppercase" product_id = "{{ $product->product_unique_id }}">
                            <div class="ribbon-sub ribbon-bookmark"></div>
                            <i class="fa fa-pencil"></i>
                        </a> -->

                        <div class="mt-element-ribbon bg-grey-steel">
                            <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">
                                {{ $product->product_unique_id }}
                            </div>
                            <div class="ribbon-content">

                                <div id="{{ $product->product_unique_id }}">

                                    <?php
                                        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->product_unique_id, "C128B",1.5,33) . '" alt="barcode"   /><br>';
                                    ?>
                                    {{ $product->product_unique_id }}

                                    <h4 class="uppercase">Picking Time</h4>
                                    {{ $product->picking_date }} ({{ $product->start_time }}-{{ $product->end_time }})

                                    <h4 class="uppercase">Picking Address</h4>
                                    Warehouse: <strong>{{ $product->title }}</strong>
                                    <br>
                                    Phone: {{ $product->msisdn }}, {{ $product->alt_msisdn }}
                                    <br>
                                    Address: {{ $product->address1 }}, {{ $product->zone_name }}, {{ $product->city_name }}, {{ $product->state_name }}
                                    
                                    <h4 class="uppercase">Products</h4>
                                    Title: <strong>{{ $product->product_title }}</strong>
                                    <br>
                                    Category: {{ $product->product_category }}
                                    <br>
                                    Quantity: {{ $product->quantity }}
                                    <br>
                                    <br>
                                </div>

                                <a href="{{ URL::to('receive-picked').'/'.$product->id.'/edit' }}" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12">
                                    <i class="fa fa-check"></i>Receive
                                </a>

                                <!-- <label class="control-label">Select Outbound manager </label>
                                {!! Form::open(array('url' => '/assign-pickup/'.$product->id, 'method' => 'put')) !!}
                                    {!! Form::hidden('status', '3', ['class' => 'form-control', 'required' => 'required']) !!}
                                    <div class="form-group">
                                        <select name="picker_id" class="form-control js-example-basic-single js-country" required="required">
                                            <option value="">Select one</option>
                                            @foreach($vehiclemanager as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                        <i class="fa fa-check"></i>
                                        Assign
                                    </button>
                                {!! Form::close() !!} -->

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
        {{ $products->render() }}
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('receive-picked', 'tasks');
        });
    </script>

@endsection
