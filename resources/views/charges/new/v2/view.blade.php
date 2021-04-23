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
            @if(isset($_GET['store_id'])&&$_GET['store_id'] != '')
            <a href="{{ URL::to('store/'.$_GET['store_id'].'/edit?step=2') }}">Category</a>
            @else
            <a href="{{ URL::to('product-category') }}">Category</a>
            @endIf
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Insert/Update</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Charges
    {{-- <small></small> --}}
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
@include('errors.validation_error')
<div class="row">

    <div class="col-md-12 animated flipInX">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">

            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Category Detail</span>
                </div>
            </div>

            <div class="portlet-body util-btn-margin-bottom-5">

                <table class="table table-striped table-bordered table-hover dt-responsive example0" style="width: 100%">
                    <tr>
                        <td width="50%"><b>Title</b></td>
                        <td>{{ $product_category->name }}</b></td>
                    </tr>
                    <tr>
                        <td width="50%"><b>Type</b></td>
                        <td>{{ $product_category->category_type }}</b></td>
                    </tr>
                    @if($product_category->category_type == 'child')
                    <tr>
                        <td width="50%"><b>Parent</b></td>
                        <td>{{ $product_category->parent_cat->name }}</b></td>
                    </tr>
                    @endIf
                    <tr>
                        <td width="50%"><b>Status</b></td>
                        <td>
                            @if($product_category->status == 0)
                            Inactive</b>
                            @else
                            Active</b>
                            @endIf
                        </td>
                    </tr>
                    <tr>
                        <td width="50%"><b>Charge Type</b></td>
                        <td>
                            {!! Form::select('charge_type', array(''=>'Select Charge Type')+$charge_types, $charges[0]->charge_type ?? null, ['class' => 'form-control js-example-basic-single', 'id' => 'charge_type', 'required' => 'required']) !!}
                        </td>
                    </tr>
                </table>

            </div>

        </div>

    </div>

</div>
<?php $i = 2; ?>
@if(count($charges) > 0)

@if($charges[0]->charge_type == 'Fixed')
@include('charges.new.v2.fixed_type_form')
@include('charges.new.v2.new_weight_based_form')
@else
@include('charges.new.v2.new_fixed_type_form')
@include('charges.new.v2.weight_based_form')

@endif

@else
@include('charges.new.v2.new_fixed_type_form')
@include('charges.new.v2.new_weight_based_form')

@endif

</div>

<script type="text/javascript">

    $(document).ready(function () {
        // Navigation Highlight
        @if (isset($_GET['store_id']) && $_GET['store_id'] != '')
        highlight_nav('store-manage', 'stores');
                @else
        highlight_nav('product-category-manage', 'product-category');
        @endIf

                var charge_type = $('#charge_type').val();
        set_charge_view(charge_type);
    });
    $('#charge_type').change(function () {
        var charge_type = $('#charge_type').val();
        set_charge_view(charge_type);
    });
    function set_charge_view(charge_type) {
        if (charge_type == 'Fixed') {
            $('#weight_based').hide(100);
            $('#fixed').show(100);
            $(".chargeType").val("Fixed");
        } else if (charge_type == 'Weight Based') {
            $('#fixed').hide(100);
            $('#weight_based').show(100);
            $(".chargeType").val("Weight Based");
        } else {
            $('#weight_based').hide(100);
            $('#fixed').hide(100);
            $(".chargeType").val("");
        }
    }


    var i = <?= $i+count($charges) ?>;
    console.log(i);
    var formContent = $("#form_1").html();
    $("#addNewRange").click(function () {
        $("#weightBasedForm").append('<div class="col-md-12 animated bounceIn" id="form_' + i + '">' + formContent + '</div>');  
        i++;      
    });

    $(document).on('click', '.removeRangeClass', function () {
        var divId = $(this).parents().eq(3).attr('id');
        console.log(divId,i);
        $('#' + divId).remove();
    });

</script>

@endsection
