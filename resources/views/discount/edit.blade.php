@extends('layouts.appinside')

@section('content')

    <link href="{{ URL::asset('assets/global/plugins/typeahead/typeahead.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
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
        <small> edit</small>
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
                      <span class="caption-subject font-dark bold uppercase">Edit Discount</span>
                  </div>
              </div>

              <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                  {!! Form::model($discount, array('url' => '/discount/'.$discount->id, 'method' => 'put', 'style' => 'overflow: hidden;')) !!}

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Discount Title</label>
                          {!! Form::text('discount_title', null, ['class' => 'form-control', 'placeholder' => 'Discount Title', 'required' => 'required']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Status</label>
                          {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control js-example-basic-single', 'id' => 'status', 'name' => 'status', 'required' => 'required']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Store</label>
                          {!! Form::select('store_id', ['' => 'All Stores'] + $stores, null, ['class' => 'form-control js-example-basic-single', 'id' => 'store_id', 'name' => 'store_id', 'required' => 'required']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Product Category</label>
                          {!! Form::select('product_category_id', ['' => 'All Categories'] + $product_categories, null, ['class' => 'form-control js-example-basic-single', 'id' => 'product_category_id', 'name' => 'product_category_id']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Discount Type</label>
                          {!! Form::select('discount_type', ['' => 'Select Discount Type'] + $discount_types, null, ['class' => 'form-control js-example-basic-single', 'id' => 'discount_type', 'name' => 'discount_type']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Discount Value</label>
                          {!! Form::text('discount_value', null, ['class' => 'form-control', 'placeholder' => 'Discount Value', 'required' => 'required']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <label class="control-label">Unit Type</label>
                          {!! Form::select('unit_type', ['' => 'Select Unit Type'] + $unit_types, null, ['class' => 'form-control js-example-basic-single', 'id' => 'unit_type', 'name' => 'unit_type', 'required' => 'required']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">

                          <div class="col-md-6" style="padding-left: 0px;">
                            <label class="control-label">Discount Range</label>
                            {!! Form::text('start_unit', null, ['class' => 'form-control', 'placeholder' => 'Minimum', 'required' => 'required']) !!}
                          </div>

                          <div class="col-md-6" style="padding-right: 0px;">
                            <label class="control-label">Discount Range</label>
                            {!! Form::text('end_unit', null, ['class' => 'form-control', 'placeholder' => 'Maximum', 'required' => 'required']) !!}
                          </div>

                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <div class="form-group">
                              <label class="control-label">Start Date</label>
                              <div class="input-group date form_datetime input-full">
                                  {!! Form::text('from_date', null, ['class' => 'form-control', 'placeholder' => 'Start Date', 'required' => 'required']) !!}
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
                              <label class="control-label">End Date</label>
                              <div class="input-group date form_datetime input-full">
                                  {!! Form::text('to_date', null, ['class' => 'form-control', 'placeholder' => 'End Date', 'required' => 'required']) !!}
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
                          <button type="submit" class="btn btn-primary filter-btn pull-right">Update</button>
                      </div>
                      <div class="clearfix"></div>

                  {!! Form::close() !!}

              </div>
          </div>
      </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('discount-manage', 'discounts');
        });

    </script>

@endsection
