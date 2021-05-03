@extends('layouts.appinside')

@section('content')

    <link href="{{ secure_asset('assets/global/plugins/typeahead/typeahead.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Discounts</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Discounts
        <small> add</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
      
      <div class="col-md-12">

          <!-- BEGIN BUTTONS PORTLET-->
          <div class="portlet light tasks-widget bordered animated zoomIn">

              <div class="portlet-title">
                  <div class="caption">
                      <i class="icon-edit font-dark"></i>
                      <span class="caption-subject font-dark bold uppercase">Add New Discount</span>
                  </div>
              </div>

              <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                  {!! Form::open(array('method' => 'post', 'url' => secure_url('') . '/discount', 'style' => 'overflow: hidden;')) !!}

                      <?php if(!isset($_GET['discount_title'])){$_GET['discount_title'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Discount Title</label>
                          <input type="text" class="form-control" name="discount_title" id="typeahead_example_1" placeholder="Discount Title" required="required" value="{{ $_GET['discount_title'] }}">
                      </div>

                      <?php if(!isset($_GET['status'])){$_GET['status'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Status</label>
                          {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], $_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status', 'name' => 'status', 'required' => 'required']) !!}
                      </div>

                      <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Store</label>
                          {!! Form::select('store_id', ['' => 'All Stores'] + $stores, $_GET['store_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'store_id', 'name' => 'store_id', 'required' => 'required']) !!}
                      </div>

                      <?php if(!isset($_GET['product_category_id'])){$_GET['product_category_id'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Product Category</label>
                          {!! Form::select('product_category_id', ['' => 'All Categories'] + $product_categories, $_GET['product_category_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'product_category_id', 'name' => 'product_category_id']) !!}
                      </div>

                      <?php if(!isset($_GET['discount_type'])){$_GET['discount_type'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Discount Type</label>
                          {!! Form::select('discount_type', ['' => 'Select Discount Type'] + $discount_types, $_GET['discount_type'], ['class' => 'form-control js-example-basic-single', 'id' => 'discount_type', 'name' => 'discount_type']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Discount Value</label>
                          <input type="text" class="form-control" name="discount_value" placeholder="Discount Value" required="required">
                      </div>

                      <?php if(!isset($_GET['unit_type'])){$_GET['unit_type'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Unit Type</label>
                          {!! Form::select('unit_type', ['' => 'Select Unit Type'] + $unit_types, $_GET['unit_type'], ['class' => 'form-control js-example-basic-single', 'id' => 'unit_type', 'name' => 'unit_type', 'required' => 'required']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">

                          <?php if(!isset($_GET['min_discount_range'])){$_GET['min_discount_range'] = null;} ?>
                          <div class="col-md-6" style="padding-left: 0px;">
                            <label class="control-label">Discount Range</label>
                            <input type="text" class="form-control" name="start_unit" id="typeahead_example_1" placeholder="Minimum" value="{{ $_GET['min_discount_range'] }}" required="required">
                          </div>

                          <?php if(!isset($_GET['max_discount_range'])){$_GET['max_discount_range'] = null;} ?>
                          <div class="col-md-6" style="padding-right: 0px;">
                            <label class="control-label">Discount Range</label>
                            <input type="text" class="form-control" name="end_unit" id="typeahead_example_1" placeholder="Maximum" value="{{ $_GET['max_discount_range'] }}" required="required">
                          </div>

                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <div class="form-group">
                              <?php if(!isset($_GET['from_date'])){$_GET['from_date'] = null;} ?>
                              <label class="control-label">Start Date</label>
                              <div class="input-group date form_datetime input-full">
                                  <input type="text" size="16" readonly class="form-control" value="{{ $_GET['from_date'] }}" placeholder="Start Date" name="from_date" required="required">
                                  <span class="input-group-btn">
                                      <button class="btn default date-set" type="button">
                                          <i class="fa fa-calendar"></i>
                                      </button>
                                  </span>
                              </div>
                              <!-- /input-group -->
                          </div>
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <div class="form-group">
                              <?php if(!isset($_GET['to_date'])){$_GET['to_date'] = null;} ?>
                              <label class="control-label">End Date</label>
                              <div class="input-group date form_datetime input-full">
                                  <input type="text" size="16" readonly class="form-control" value="{{ $_GET['to_date'] }}" placeholder="End Date" name="to_date" required="required">
                                  <span class="input-group-btn">
                                      <button class="btn default date-set" type="button">
                                          <i class="fa fa-calendar"></i>
                                      </button>
                                  </span>
                              </div>
                              <!-- /input-group -->
                          </div>
                      </div>

                      <div class="col-md-12">
                          <button type="submit" class="btn btn-primary filter-btn pull-right">Save</button>
                      </div>
                      <div class="clearfix"></div>

                  {!! Form::close() !!}

              </div>
          </div>
      </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('discount-add', 'discounts');
        });

    </script>

@endsection
