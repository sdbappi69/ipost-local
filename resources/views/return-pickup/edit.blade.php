@extends('layouts.appinside')

<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ secure_url('order') }}">Products</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Update</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Product
        <small>receive</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">

        {!! Form::model($product, array('url' => secure_url('') . '/receive-picked/'.$product->id, 'method' => 'put')) !!}

            <div class="row">

                @include('partials.errors')

                <div class="col-md-4">

                    <div class="form-group">
                        <label class="control-label">Product Title</label>
                        {!! Form::text('product_title', null, ['class' => 'form-control', 'placeholder' => 'Product name', 'required' => 'required']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Product URL</label>
                        {!! Form::text('url', null, ['class' => 'form-control', 'placeholder' => 'https://your-product-link']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Product Category</label>
                        {!! Form::select('product_category_id', array(''=>'Select Category')+$categories, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'product_category_id']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Unit Price (BDT)</label>
                        {!! Form::text('unit_price', null, ['class' => 'form-control', 'placeholder' => '00.00', 'required' => 'required']) !!}
                    </div>

                </div>

                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="control-label">Quantity</label>
                        {!! Form::text('quantity', null, ['class' => 'form-control input-group-lg quantity', 'required' => 'required', 'onkeydown' => 'return false;']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Width (CM)</label>
                        {!! Form::text('width', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Height (CM)</label>
                        {!! Form::text('height', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Length (CM)</label>
                        {!! Form::text('length', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                    </div>

                </div>

                <div class="col-md-4">

                    <div class="form-group">
                        <label class="control-label">Warehouse</label>
                        {!! Form::select('pickup_location_id', array(''=>'Select Warehouse')+$warehouse, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'pickup_location_id']) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Pick Date</label>

                        <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" data-date-start-date="+0d">
                            <span class="input-group-btn">
                                <button class="btn default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                            {!! Form::text('picking_date', null, ['class' => 'form-control picking_date', 'required' => 'required', 'readonly' => 'true', 'id' => $product->id]) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Pick Time</label>
                        {!! Form::select('picking_time_slot_id', array(''=>'Select Pick Time')+$picking_time_slot, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'picking_time_slot_id_'.$product->id]) !!}
                    </div>

                    <div class="form-group">
                        <label class="control-label">Remarks</label>
                        {!! Form::text('receive_remarks', null, ['class' => 'form-control']) !!}
                    </div>

                </div>

            </div>

            <br>

            @if($product->order->hub_id == auth()->user()->reference_id)

                <h4>Sub-Order</h4>

                <div class="row sub-order-list">

                    {!! Form::hidden('sub_order_id', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'sub_order_id']) !!}

                    <div class="col-md-3">
                        <a href="javascript:;" class="btn btn-lg green suborder_button_add">
                            <i class="fa fa-plus"></i> 
                            New Sub-order
                        </a>
                    </div>

                    @foreach($suborders AS $suborder)

                        <div class="col-md-3">
                            <a href="javascript:;" class="btn btn-lg default suborder_button suborder_button_{{ $suborder->id }} @if ($product->sub_order_id == $suborder->id) suborder_button_active @endIf" suborder_id = "{{ $suborder->id }}">
                                <i class="fa fa-cubes"></i> 
                                {{ $suborder->unique_suborder_id }}
                            </a>
                        </div>

                    @endforeach

                </div>

                <br>

            @endIf

            <h4>Assign</h4>

            <div class="row">

                <div class="form-group">
                    <label class="control-label">Outbound manager</label>
                    {!! Form::select('responsible_user_id', array(''=>'Select One')+$vehiclemanager, $product->sub_order->responsible_user_id, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                </div>

            </div>

            &nbsp;
            <div class="row padding-top-10">
                <a href="javascript:history.back()" class="btn default"> Cancel </a>
                {!! Form::submit('Done', ['class' => 'btn green pull-right']) !!}
            </div>

        {!! Form::close() !!}

    </div>

    <script src="{{ secure_asset('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/pages/scripts/components-bootstrap-touchspin.min.js') }}" type="text/javascript"></script>

    <script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

    <script src="{{ secure_asset('custom/js/date-time.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        // $("#quantity").TouchSpin();

        $('#quantity').TouchSpin({
                    min: 1,
                    max: 10000,
                });
        $('.quantity').TouchSpin({
                    min: 1,
                    max: 10000,
                });

        $('.number').TouchSpin({
                    min: 0,
                    max: 999999,
                    step: 0.1,
                    decimals: 2,
                    boostat: 5,
                    maxboostedstep: 10,
                });

        // Get Pick-up time On date Change
        $('#picking_date').on('change', function() {
            var date = $(this).val();
            var day = dayOfWeek(date);

            pick_up_slot(day);
        });

        // Get Pick-up time On date Change
        $('.picking_date').on('change', function() {
            var id = $(this).attr("id");
            var date = $(this).val();
            var day = dayOfWeek(date);

            pick_up_slot_by_id(id,day);
        });

        $('.suborder_button_add').on('click', function() {
            var unique_order_id = '{{ $product->order->unique_order_id }}';
            var url = site_path + 'addsuborder/'+unique_order_id;
            $.getJSON(url+'?callback=?',function(data){
                var html = '';
                // alert(data);
                $.each(data, function(key, item) { 
                    html = html+'<div class="col-md-3">'+
                                    '<a href="javascript:;" class="btn btn-lg default suborder_button suborder_button_'+item.id+'" suborder_id = "'+item.id+'">'+
                                        '<i class="fa fa-cubes"></i> '+item.unique_suborder_id+
                                    '</a>'+
                                '</div>';
                });
                $('.sub-order-list').append(html);
            });
        });

        $(document).on('click', '.suborder_button', function(){ 
        // $('.suborder_button').on('click', function() {
            var suborder_id = $(this).attr("suborder_id");
            var product_unique_id = '{{ $product->product_unique_id }}';
            var url = site_path + 'update_product_suborder/'+product_unique_id+'/'+suborder_id;
            $.getJSON(url+'?callback=?',function(data){
                if(suborder_id == data){
                    $('.suborder_button').removeClass('suborder_button_active');
                    $('.suborder_button_'+data).addClass('suborder_button_active');
                }
            });
        });


    </script>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('receive-picked', 'pickup');
        });

    </script>

@endsection
