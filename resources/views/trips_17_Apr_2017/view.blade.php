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
                <a href="{{ secure_url('trip') }}">Trip</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>view detail</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Trip
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        @if($adding == 1)
        
            <div class="col-md-12">

                {!! Form::open(array('url' => secure_url('') . '/trip_load/'.$id, 'method' => 'post')) !!}

                    <div class="form-group col-md-10">

                        {!! Form::text('unique_id', null, ['class' => 'form-control', 'id' => 'unique_id']) !!}

                    </div>

                    <div class="form-group col-md-2">
                        
                        <span class="form-group-btn">
                            <button class="btn blue" type="submit">Add</button>
                        </span>

                        @if($start == 1)
                            <a href="{{ secure_url('trip_start') }}/{{ $id }}" type="button" class="btn blue">Start trip</a>
                        @endif

                    </div>

                {!! Form::close() !!}

            </div>
        @elseIf($end == 1)
            <div class="col-md-12"><a href="{{ secure_url('trip_end') }}/{{ $id }}" type="button" class="btn blue pull-right">End trip</a></div>
        @endif

        <!-- <div class="col-md-12">
            
            @if(count($trip->products) > 0)
                @foreach($trip->products as $row)

                    @if($row->status == 1)

                        <div class="col-md-4 small">

                            <div class="mt-element-ribbon bg-grey-steel">

                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">
                                        {{ $row->product->product_unique_id }}
                                    </div>
                                    <div class="ribbon-content">

                                        <div id="{{ $row->product->product_unique_id }}">

                                            <?php
                                                echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($row->product->product_unique_id, "C128B",1.5,33) . '" alt="barcode"   /><br>';
                                            ?>
                                            {{ $row->product->product_unique_id }}

                                            <h4 class="uppercase">Transfer Hub</h4>
                                            {{ $row->product->order->hub->title }}
                                            
                                            <h4 class="uppercase">Products</h4>
                                            Title: <strong>{{ $row->product->product_title }}</strong>
                                            <br>
                                            Category: {{ $row->product->product_category->name }}
                                            <br>
                                            Quantity: {{ $row->product->quantity }}
                                            <br>
                                            <br>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            
                        </div>

                    @endIf

                @endforeach
            @endIf

        </div> -->

        <div class="col-md-12">
            
            @if(count($trip->suborders) > 0)
                @foreach($trip->suborders as $row)

                    @if($row->status == 1)

                        <div class="col-md-4 small">

                            <div class="mt-element-ribbon bg-grey-steel">

                                <div class="mt-element-ribbon bg-grey-steel">
                                    <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">{{ $row->sub_order->unique_suborder_id }}</div>
                                        <div class="ribbon-content" style="overflow:hidden;">

                                            <div id="{{ $row->sub_order->unique_suborder_id }}">
                                                <?php
                                                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($row->sub_order->unique_suborder_id, "C128B",1.5,33) . '" alt="barcode"   /><br>';
                                                ?>
                                                {{ $row->sub_order->unique_suborder_id }}

                                                <h4 class="uppercase">Destination</h4>
                                                Destination Hub: <b>{{ $row->sub_order->destination_hub->title }}</b>
                                                <h4 class="uppercase">Products</h4>
                                                <div class="product-summery-tbl">
                                                    <table style="width:100%">
                                                        <thead>
                                                            <th>Product</th>
                                                            <th style="padding-left:1px solid #FFFFFF;padding-right:1px solid #FFFFFF;">Qty</th>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($row->sub_order->products as $product)
                                                                <tr style="padding-bottom:1px solid #FFFFFF; border-bottom: 1px solid #666666; border-top: 1px solid #666666;">
                                                                    <td>
                                                                        <b>{{ $product->product_title }}</b>
                                                                        <br>
                                                                        ID: {{ $product->product_unique_id }}
                                                                        <br>
                                                                        Cat: {{ $product->product_category->name }}
                                                                    </td>
                                                                    <td class="numeric" style="padding-left:1px solid #FFFFFF;padding-right:1px solid #FFFFFF;">{{ $product->quantity }}</td>
                                                                </tr>

                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <br>
                                            </div>
                                            
                                        </div>
                                </div>

                            </div>
                            
                        </div>

                    @endIf

                @endforeach
            @endIf

        </div>

    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('trip-manage', 'trips');
        });
        
    </script>

@endsection
