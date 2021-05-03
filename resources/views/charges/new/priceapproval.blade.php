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
            <span>Price Approval</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Price
    <small> Approval</small>
</h1>

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

@if(count($charges) > 0)


<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Price Pending List</span>
            </div>
            <div class="tools">
                <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" id="example0">
                <thead>
                    <th>Store</th>
                    <th>Product Category</th>
                    <th>Charge Models</th>
                    <th>Fixed Charge</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach($charges as $charge)
                    <tr>
                        <td>{{ @$charge->store->store_id or '' }}</td>
                        <td>{{ $charge->product_category->name or '' }}</td>
                        <td>{{ $charge->charge_model->title or '' }}</td>
                        <td>{{ $charge->fixed_charge or '' }}</td>
                        <td>{{ ($charge->status == 0) ? 'Pending':(($charge->status == 1) ? 'Active':'Inactive') }}</td>
                        <td>{{ $charge->createdBy->name or '' }}</td>
                        <td><a href="approvePrice/{{ $charge->id }}" onclick="return confirm('Are you sure?');"><i class="fa fa-check"></i> Approve </a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endIf

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('orders', 'orders');

        <?php if(!isset($_GET['sub_order_status'])){$_GET['sub_order_status'] = array();} ?>
        $('#order_status').select2().val([{!! implode(",", $_GET['sub_order_status']) !!}]).trigger("change");

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = array();} ?>
        $('#merchant_id').select2().val([{!! implode(",", $_GET['merchant_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = array();} ?>
        $('#store_id').select2().val([{!! implode(",", $_GET['store_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['pickup_man_id'])){$_GET['pickup_man_id'] = array();} ?>
        $('#pickup_man_id').select2().val([{!! implode(",", $_GET['pickup_man_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['delivary_man_id'])){$_GET['delivary_man_id'] = array();} ?>
        $('#delivary_man_id').select2().val([{!! implode(",", $_GET['delivary_man_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['pickup_zone_id'])){$_GET['pickup_zone_id'] = array();} ?>
        $('#pickup_zone_id').select2().val([{!! implode(",", $_GET['pickup_zone_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['delivery_zone_id'])){$_GET['delivery_zone_id'] = array();} ?>
        $('#delivery_zone_id').select2().val([{!! implode(",", $_GET['delivery_zone_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['current_sub_order_status'])){$_GET['current_sub_order_status'] = array();} ?>
        $('#current_sub_order_status').select2().val([{!! implode(",", $_GET['current_sub_order_status']) !!}]).trigger("change");
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        highlight_nav('price-approval', 'price-approval');
        
        $('#example0').dataTable( {
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });
    });

    $(".filter-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('order') }}").submit();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('orderexport/xls') }}").submit();
    });

</script>

@endsection
