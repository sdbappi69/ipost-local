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
                <span>Transfer Product</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Product
        <small> transfer</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        <div class="col-md-12">
            {!! Form::open(array('url' => '/transfer-product/', 'method' => 'post')) !!}

                <div class="form-group col-md-10">

                    {!! Form::text('product_unique_id', null, ['class' => 'form-control', 'id' => 'remarks']) !!}

                </div>

                <div class="form-group col-md-2">
                    
                    <span class="form-group-btn">
                        <button class="btn blue" type="submit">Find</button>
                    </span>

                </div>

            {!! Form::close() !!}

        </div>
        
        @if(count($products) > 0)

            @foreach($products as $product)

                <div class="col-md-4 small">

                    <div class="mt-element-ribbon bg-grey-steel">

                        <!-- <a href="javascript:void(0)" class="ribbon ribbon-right ribbon-vertical-right ribbon-shadow ribbon-border-dash-vert ribbon-color-default uppercase print_it" product_id = "{{ $product->product_unique_id }}">
                            <div class="ribbon-sub ribbon-bookmark"></div>
                            <i class="fa fa-print"></i>
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

                                    <h4 class="uppercase">Transfer Hub</h4>
                                    {{ $product->transfer_hub }}
                                    
                                    <h4 class="uppercase">Products</h4>
                                    Title: <strong>{{ $product->product_title }}</strong>
                                    <br>
                                    Category: {{ $product->product_category }}
                                    <br>
                                    Quantity: {{ $product->quantity }}
                                    <br>
                                    <br>
                                </div>

                                <div class="row">
                                    <label class="control-label">Select Trip</label>
                                    {!! Form::open(array('url' => '/transfer-product/'.$product->id, 'method' => 'put')) !!}
                                        {!! Form::hidden('receive_hub_id', $product->hub_id, ['class' => 'form-control']) !!}
                                        <div class="form-group">
                                            <select name="trip_id" class="form-control js-example-basic-single js-country" required="required">
                                                <option value="">Select one</option>
                                                @foreach($trips as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
                                        </div>
                                        <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                            <i class="fa fa-check"></i>
                                            Add
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
        {{ $products->render() }}
    </div>

    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('transfer-product', 'tasks');
        });

        $(".print_it").click(function(){
            var product_id = $(this).attr("product_id");
            $("#"+product_id).print({
            globalStyles: true,
            mediaPrint: true,
            stylesheet: null,
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: null,
            manuallyCopyFormValues: true,
            deferred: $.Deferred(),
            timeout: 750,
            title: null,
            doctype: '<!doctype html>'
    });
        });
        
    </script>

@endsection
