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
            <span>Inactive Merchants</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Inactive Merchants
    <small> view</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered animated flipInX">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Filter</span>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

            {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Merchants</label>
                {!! Form::select('merchant_id[]', $merchants,null, ['class' => 'form-control js-example-basic-single','id' => 'merchant_id', 'multiple' => '']) !!}
            </div>

            <?php if(!isset($_GET['from_date'])){$_GET['from_date'] = null;} ?>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">From</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('from_date',$from_date, ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date', 'required' => '']) !!}
                </div>
            </div>

            <?php if(!isset($_GET['to_date'])){$_GET['to_date'] = null;} ?>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">To</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('to_date',$to_date, ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date', 'required' => '']) !!}
                </div>
            </div>

            <div class="col-md-12">
                <button class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
            </div>
            <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>



@if(count($merchants_orders) > 0)

<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Inactive Merchants</span>
            </div>
            <div class="tools">
                <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-bordered table-hover" id="example0">
                <thead class="flip-content">
                    <tr>
                        <th>Merchant Name</th>
                        <th>Last Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($merchants_orders as $orders)
                    @if((date('Y-m-d', strtotime($orders['last_order_date'])) < $_GET['from_date']) && ($_GET['to_date'] > date('Y-m-d', strtotime($orders['last_order_date']))))
                    <tr>
                        <td>{{ $orders['merchant_name'] }}</td>
                        <td>{{ $orders['last_order_date'] }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>

            <div class="pagination pull-right">
                {{ $merchants_orders->appends($inputs)->render() }}
            </div>
        </div>
    </div>
</div>
@else
<div class="col-md-12">
  <div class="portlet light tasks-widget bordered">
     <p>
        No Data Found
    </p>
</div>
</div>
@endIf

<script src="{{secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/typeahead/handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/typeahead/typeahead.bundle.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('inactive-merchants', 'merchant-report');

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = array();} ?>
        $('#merchant_id').select2().val([{!! implode(",", $_GET['merchant_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['hub_id'])){$_GET['hub_id'] = array();} ?>
        $('#hub_id').select2().val([{!! implode(",", $_GET['hub_id']) !!}]).trigger("change");

        // ComponentsTypeahead.init();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('inactive-merchants-export/xls') }}").submit();
    });
    $(".filter-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('inactive-merchants') }}").submit();
    });


</script>

<style media="screen">
.table-filtter .btn{ width: 100%;}
.table-filtter {
    margin: 20px 0;
}
.tt-menu{
    max-height: 75px;
}
</style>

@endsection
