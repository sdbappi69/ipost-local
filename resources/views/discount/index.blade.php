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
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

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

              <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                  {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'style' => 'overflow: hidden;')) !!}

                      <?php if(!isset($_GET['discount_title'])){$_GET['discount_title'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          <input type="text" class="form-control" name="discount_title" id="typeahead_example_1" placeholder="Discount Title" value="{{ $_GET['discount_title'] }}">
                      </div>

                      <?php if(!isset($_GET['status'])){$_GET['status'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], $_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status', 'name' => 'status']) !!}
                      </div>

                      <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          {!! Form::select('store_id', ['' => 'All Stores'] + $stores, $_GET['store_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'store_id', 'name' => 'store_id']) !!}
                      </div>

                      <?php if(!isset($_GET['product_category_id'])){$_GET['product_category_id'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          {!! Form::select('product_category_id', ['' => 'All Categories'] + $product_categories, $_GET['product_category_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'product_category_id', 'name' => 'product_category_id']) !!}
                      </div>

                      <?php if(!isset($_GET['discount_type'])){$_GET['discount_type'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          {!! Form::select('discount_type', ['' => 'All Discount Types'] + $discount_types, $_GET['discount_type'], ['class' => 'form-control js-example-basic-single', 'id' => 'discount_type', 'name' => 'discount_type']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">

                          <?php if(!isset($_GET['min_discount'])){$_GET['min_discount'] = null;} ?>
                          <div class="col-md-6" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="min_discount" id="typeahead_example_1" placeholder="Min Discount" value="{{ $_GET['min_discount'] }}">
                          </div>

                          <?php if(!isset($_GET['max_discount'])){$_GET['max_discount'] = null;} ?>
                          <div class="col-md-6" style="padding-right: 0px;">
                            <input type="text" class="form-control" name="max_discount" id="typeahead_example_1" placeholder="Max Discount" value="{{ $_GET['max_discount'] }}">
                          </div>

                      </div>

                      <?php if(!isset($_GET['unit_type'])){$_GET['unit_type'] = null;} ?>
                      <div class="col-md-6" style="margin-bottom:5px;">
                          {!! Form::select('unit_type', ['' => 'All Unit Types'] + $unit_types, $_GET['unit_type'], ['class' => 'form-control js-example-basic-single', 'id' => 'unit_type', 'name' => 'unit_type']) !!}
                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">

                          <?php if(!isset($_GET['min_discount_range'])){$_GET['min_discount_range'] = null;} ?>
                          <div class="col-md-6" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="min_discount_range" id="typeahead_example_1" placeholder="Min Discount Range" value="{{ $_GET['min_discount_range'] }}">
                          </div>

                          <?php if(!isset($_GET['max_discount_range'])){$_GET['max_discount_range'] = null;} ?>
                          <div class="col-md-6" style="padding-right: 0px;">
                            <input type="text" class="form-control" name="max_discount_range" id="typeahead_example_1" placeholder="Max Discount Range" value="{{ $_GET['max_discount_range'] }}">
                          </div>

                      </div>

                      <div class="col-md-6" style="margin-bottom:5px;">
                          <div class="form-group">
                              <?php if(!isset($_GET['from_date'])){$_GET['from_date'] = null;} ?>
                              <div class="input-group date form_datetime input-full">
                                  <input type="text" size="16" readonly class="form-control" value="{{ $_GET['from_date'] }}" placeholder="Start Date" name="from_date">
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
                              <div class="input-group date form_datetime input-full">
                                  <input type="text" size="16" readonly class="form-control" value="{{ $_GET['to_date'] }}" placeholder="End Date" name="to_date">
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
                          <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                      </div>
                      <div class="clearfix"></div>

                  {!! Form::close() !!}

              </div>
          </div>
      </div>

      @if(count($discounts) > 0)

          <div class="col-md-12">
              <!-- BEGIN BUTTONS PORTLET-->
              <div class="portlet light tasks-widget bordered">
                  <!-- <div class="portlet-title">
                      <div class="caption">
                          <span class="caption-subject font-green-haze bold uppercase">Hubs</span>
                          <span class="caption-helper">list</span>
                      </div>
                  </div> -->
                  <div class="portlet-body util-btn-margin-bottom-5">
                      <table class="table table-bordered table-hover" id="example0">
                          <thead class="flip-content">
                              <th>Title</th>
                              <th>Store</th>
                              <th>Product Category</th>
                              <th>Type</th>
                              <th>Value</th>
                              <th>Unit</th>
                              <th>Start</th>
                              <th>End</th>
                              <th>From</th>
                              <th>To</th>
                              <th>Status</th>
                              <th>Action</th>
                          </thead>
                          <tbody>
                              @foreach($discounts as $discount)
                                  <tr>
                                      <td>{{ $discount->discount_title }}</td>
                                      <td>{{ $discount->store->store_id or 'All' }}</td>
                                      <td>{{ $discount->product_category->name or 'All' }}</td>
                                      <td>{{ ucfirst($discount->discount_type) }}</td>
                                      <td>
                                        {{ $discount->discount_value}}@if($discount->discount_type == 'percantage')%@endIf
                                      </td>
                                      <td>{{ strtoupper($discount->unit_type) }}</td>
                                      <td>{{ $discount->start_unit }} {{ strtoupper($discount->unit_type) }}</td>
                                      <td>{{ $discount->end_unit }} {{ strtoupper($discount->unit_type) }}</td>
                                      <td>{{ $discount->from_date }}</td>
                                      <td>{{ $discount->to_date }}</td>
                                      <td>
                                        @if($discount->status == 1)
                                          Active
                                        @else
                                          Inactive
                                        @endIf
                                      </td>
                                      <td>
                                        <a class="label label-success" href="discount/{{ $discount->id }}/edit">
                                            <i class="fa fa-pencil"></i> Update
                                        </a>
                                      </td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>

                      <div class="pagination pull-right">
                          {{ $discounts->appends($req)->render() }}
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

    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('discount-manage', 'discounts');
        });

    </script>

@endsection
