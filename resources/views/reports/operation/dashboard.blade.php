@extends('layouts.appinside')

@section('content')

<style type="text/css">
.widget-thumb{
	text-align: center;
}
</style>

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<span>Quantitative Dashboard</span>
		</li>
	</ul>
</div>

<br>
<div class="row">
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

                {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Merchants</label>
                    {!! Form::select('merchant_id[]', $merchants,null, ['class' => 'form-control js-example-basic-single','id' => 'merchant_id', 'multiple' => '']) !!}
                </div>

                <?php if(!isset($_GET['from_date'])){$_GET['from_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order from</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                        {!! Form::text('from_date',$from_date, ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <?php if(!isset($_GET['to_date'])){$_GET['to_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order to</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                        {!! Form::text('to_date',$to_date, ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                </div>
                <div class="clearfix"></div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

	<div class="col-md-3">
		<!-- BEGIN WIDGET THUMB -->
		<div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
			<h4 class="widget-thumb-heading">Total Pickup</h4>
			<div class="widget-thumb-wrap">
				<div class="widget-thumb-body">
					<span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $total_pickup }}">0</span>
				</div>

				<br><br>

				<div class="col-md-12" style="padding-left: 0; padding-right: 0;">
					<a class="form-control btn btn-primary pull-right" href="javascript:;" onclick="exportData('pickup')">
						Merchantwise Breakdown
						<i class="fa fa-arrow-circle-o-right"></i>
					</a>
				</div>

			</div>
		</div>
		<!-- END WIDGET THUMB -->
	</div>

	<div class="col-md-3">
		<!-- BEGIN WIDGET THUMB -->
		<div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
			<h4 class="widget-thumb-heading">Total Delivered Order</h4>
			<div class="widget-thumb-wrap">
				<div class="widget-thumb-body">
					<span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $total_delivered }}">0</span>
				</div>

				<br><br>

				<div class="col-md-12" style="padding-left: 0; padding-right: 0;">
					<a class="form-control btn btn-primary pull-right" href="javascript:;" onclick="exportData('delivered')">
						Merchantwise Breakdown
						<i class="fa fa-arrow-circle-o-right"></i>
					</a>
				</div>

			</div>
		</div>
		<!-- END WIDGET THUMB -->
	</div>

	<div class="col-md-3">
		<!-- BEGIN WIDGET THUMB -->
		<div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
			<h4 class="widget-thumb-heading">Total Failed Delivery Order</h4>
			<div class="widget-thumb-wrap">
				<div class="widget-thumb-body">
					<span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $total_delivery_failed }}">0</span>
				</div>

				<br><br>

				<div class="col-md-12" style="padding-left: 0; padding-right: 0;">
					<a class="form-control btn btn-primary pull-right" href="javascript:;" onclick="exportData('delivery_failed')">
						Merchantwise Breakdown
						<i class="fa fa-arrow-circle-o-right"></i>
					</a>
				</div>

			</div>
		</div>
		<!-- END WIDGET THUMB -->
	</div>

	<div class="col-md-3">
		<!-- BEGIN WIDGET THUMB -->
		<div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
			<h4 class="widget-thumb-heading">Total Return Completed Order</h4>
			<div class="widget-thumb-wrap">
				<div class="widget-thumb-body">
					<span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $total_returned }}">0</span>
				</div>

				<br><br>

				<div class="col-md-12" style="padding-left: 0; padding-right: 0;">
					<a class="form-control btn btn-primary pull-right" href="javascript:;" onclick="exportData('returned')">
						Merchantwise Breakdown
						<i class="fa fa-arrow-circle-o-right"></i>
					</a>
				</div>

			</div>
		</div>
		<!-- END WIDGET THUMB -->
	</div>
</div>


<script src="{{secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/typeahead/handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/typeahead/typeahead.bundle.min.js') }}" type="text/javascript"></script>

<script src="{{secure_asset('assets/global/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{secure_asset('assets/global/plugins/counterup/jquery.counterup.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('operation-quantitative', 'operation');

        <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = array();} ?>
        $('#merchant_id').select2().val([{!! implode(",", $_GET['merchant_id']) !!}]).trigger("change");

        <?php if(!isset($_GET['hub_id'])){$_GET['hub_id'] = array();} ?>
        $('#hub_id').select2().val([{!! implode(",", $_GET['hub_id']) !!}]).trigger("change");

        // ComponentsTypeahead.init();
    });

    function exportData(type){
        // alert(type);
        // e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('operation/quantitativeexport/') }}"+"/"+type+"").submit();
    }

    $(".filter-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('operation/quantitative') }}").submit();
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
