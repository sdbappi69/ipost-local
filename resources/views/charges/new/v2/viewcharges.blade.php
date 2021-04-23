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
            <span>View Charges</span>
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
                            {{$charges[0]->charge_type ?? ''}}
                        </td>
                    </tr>
                </table>

            </div>

        </div>

    </div>

</div>

@if(count($charges) > 0)

@if($charges[0]->charge_type == 'Fixed')
<div class="row" id="fixed">

    <div class="col-md-12 fixed animated bounceInDown">

        <div id="accordion3" class="panel-group">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#charge_model_1">
                            <i class="fa fa-plus"></i> Fixed Charges
                        </a>
                    </h4>
                </div>
                <div id="charge_model_1" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class="col-md-12 animated bounceIn">
                            <!-- BEGIN BUTTONS PORTLET-->
                            <div class="portlet light tasks-widget bordered">

                                <div class="portlet-title">
                                    <div class="caption">
                                        @if($charges[0]->approved == 0)
                                        <i class="fa fa-circle" style="color:red"></i>
                                        @else
                                        <i class="fa fa-check-circle" style="color:green"></i>
                                        @endIf

                                        <span class="caption-subject font-dark bold uppercase">Charge</span>
                                    </div>
                                </div>

                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Initial Charge</label>
                                        {!! Form::text('initial_charge', $charges[0]->initial_charge, ['class' => 'form-control input-group-lg number', 'readonly' => 'readonly']) !!}
                                    </div>                               

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Hub Transfer Charge</label>
                                        {!! Form::text('hub_transfer_charge', $charges[0]->hub_transfer_charge, ['class' => 'form-control input-group-lg number', 'readonly' => 'readonly']) !!}
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Return Charge</label>
                                        {!! Form::text('return_charge', $charges[0]->return_charge, ['class' => 'form-control input-group-lg number', 'readonly' => 'readonly']) !!}
                                    </div>

                                </div>


                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@else

<div class="row" id="weight_based">

    <div class="col-md-12 fixed animated bounceInDown">

        <div id="accordion3" class="panel-group">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#charge_model_2">
                            <i class="fa fa-plus"></i> Weight Based Charges
                        </a>
                    </h4>
                </div>
                <div id="charge_model_2" class="panel-collapse collapse in">
                    <div class="panel-body">

                        @foreach($charges as $key => $charge)
                        <div class="col-md-12 animated bounceIn" id="{{$key}}">
                            <div class="portlet light tasks-widget bordered" id="formContent">

                                <div class="portlet-title" id="13">
                                    <div class="caption">
                                        @if($charge->approved == 0)
                                        <i class="fa fa-circle" style="color:red"></i>
                                        @else
                                        <i class="fa fa-check-circle" style="color:green"></i>
                                        @endIf

                                        <span class="caption-subject font-dark bold uppercase">Charges</span>
                                    </div>
                                </div>

                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Minimum Weight</label>
                                            <input type="text" name="min_weight[]" value="{{$charge->min_weight}}" class="form-control input-group-lg number" readonly="">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Maximum Weight</label>
                                            <input type="text" name="max_weight[]" value="{{$charge->max_weight}}" class="form-control input-group-lg number" readonly="">
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">     

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Initial Charge</label>
                                            <input type="text" name="initial_charge[]" value="{{$charge->initial_charge}}" class="form-control input-group-lg number" readonly="">
                                        </div>     

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Hub Transfer Charge</label>
                                            <input type="text" name="hub_transfer_charge[]" value="{{$charge->hub_transfer_charge}}" class="form-control input-group-lg number" readonly="">
                                        </div> 

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Return Charge</label>
                                            <input type="text" name="return_charge[]" value="{{$charge->return_charge}}" class="form-control input-group-lg number" readonly="">
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endif

@endif


<script type="text/javascript">

    $(document).ready(function () {
        // Navigation Highlight
        @if (isset($_GET['store_id']) && $_GET['store_id'] != '')
        highlight_nav('store-manage', 'stores');
                @ else
        highlight_nav('product-category-manage', 'product-category');
        @endIf
    });

</script>

@endsection
