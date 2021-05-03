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
    <small>verify</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="mt-element-step">

    {!! Form::model($product, array('url' => secure_url('') . '/receive-picked/'.$product->id, 'method' => 'put','id' => 'form_data')) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-4">

            <div class="form-group">
                <label class="control-label">Product Title</label>
                {!! Form::text('product_title', null, ['class' => 'form-control', 'placeholder' => 'Product name', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Product Category</label>
                {!! Form::select('product_category_id', array(''=>'Select Category')+$categories, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'product_category_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Unit Price (BDT)</label>
                {!! Form::text('unit_price', null, ['class' => 'form-control', 'placeholder' => '00.00', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Remarks</label>
                {!! Form::text('receive_remarks', null, ['class' => 'form-control']) !!}
            </div>

        </div>

        <div class="col-md-4">

            <div class="form-group">
                <label class="control-label">Weight (KG)</label>
                {!! Form::text('weight', null, ['class' => 'form-control input-group-lg weight', 'required' => 'required', 'min' => '0.1']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Width (CM)</label>
                {!! Form::text('width', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Height (CM)</label>
                {!! Form::text('height', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
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
                <label class="control-label">Length (CM)</label>
                {!! Form::text('length', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
            </div>

        </div>

    </div>

    <div class="row" style="display: none;">
        <div class="col-md-3">
            <a href="javascript:;" class="btn btn-lg green suborder_button_add">
                <i class="fa fa-plus"></i> 
                New Sub-order
            </a>
        </div>
    </div>

    <br>

    <h4 style="display: none;">Assign</h4>

    <div class="row" style="display: none;">

        <div class="form-group">
            <label class="control-label">Outbound manager</label>
            {!! Form::hidden('responsible_user_id', $responsible_user_id, ['required' => 'required']) !!}
        </div>

    </div>
    {!! Form::hidden('tm_delivery_status',0,['id'=>'tm_delivery_status']) !!}

    &nbsp;
    <div class="row">
        <div class="row padding-top-10">
            <a href="javascript:history.back()" class="btn default"> Cancel </a>
            @if($suborders->source_hub_id == $suborders->destination_hub_id)
            {!! Form::button('Done', ['class' => 'btn green pull-right','id'=>"btn-confirm"]) !!}
            @else
            {!! Form::submit('Done', ['class' => 'btn green pull-right']) !!}
            @endif
        </div>
    </div>

    {!! Form::close() !!}

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirmation</h4>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title" id="myModalLabel">Find the nearest rider by application itself.</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="modal-btn-si">Yes</button>
                    <button type="button" class="btn btn-default" id="modal-btn-no">No</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ secure_asset('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/components-bootstrap-touchspin.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('custom/js/date-time.js') }}" type="text/javascript"></script>

<script type="text/javascript">
var modalConfirm = function (callback) {

    $("#btn-confirm").on("click", function () {
        $("#mi-modal").modal('show');
    });

    $("#modal-btn-si").on("click", function () {
        callback(true);
        $("#mi-modal").modal('hide');
    });

    $("#modal-btn-no").on("click", function () {
        callback(false);
        $("#mi-modal").modal('hide');
    });
};

modalConfirm(function (confirm) {
    if (confirm) {
        // yes confirm
        $("#form_data").submit();
    } else {
        // no confirm
        $("#tm_delivery_status").val(1);
        $("#form_data").submit();
    }
});

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
$('#picking_date').on('change', function () {
    var date = $(this).val();
    var day = dayOfWeek(date);

    pick_up_slot(day);
});

// Get Pick-up time On date Change
$('.picking_date').on('change', function () {
    var id = $(this).attr("id");
    var date = $(this).val();
    var day = dayOfWeek(date);

    pick_up_slot_by_id(id, day);
});

$('.suborder_button_add').on('click', function () {
    var unique_order_id = '{{ $product->order->unique_order_id }}';
    var url = site_path + 'addsuborder/' + unique_order_id;
    $.getJSON(url + '?callback=?', function (data) {
        var html = '';
        // alert(data);
        $.each(data, function (key, item) {
            html = html + '<div class="col-md-3">' +
                    '<a href="javascript:;" class="btn btn-lg default suborder_button suborder_button_' + item.id + '" suborder_id = "' + item.id + '">' +
                    '<i class="fa fa-cubes"></i> ' + item.unique_suborder_id +
                    '</a>' +
                    '</div>';
        });
        $('.sub-order-list').append(html);
    });
});

$(document).on('click', '.suborder_button', function () {
    // $('.suborder_button').on('click', function() {
    var suborder_id = $(this).attr("suborder_id");
    var product_unique_id = '{{ $product->product_unique_id }}';
    var url = site_path + 'update_product_suborder/' + product_unique_id + '/' + suborder_id;
    $.getJSON(url + '?callback=?', function (data) {
        if (suborder_id == data) {
            $('.suborder_button').removeClass('suborder_button_active');
            $('.suborder_button_' + data).addClass('suborder_button_active');
        }
    });
});


</script>

<script type="text/javascript">

    $(document).ready(function () {
        // Navigation Highlight
        highlight_nav('receive-picked', 'pickup');
    });

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

    $('.weight').TouchSpin({
        min: 0.1,
        max: 999999,
        step: 0.1,
        decimals: 2,
        boostat: 5,
        maxboostedstep: 10,
    });

</script>

@endsection
