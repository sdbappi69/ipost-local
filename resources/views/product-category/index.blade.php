@extends('layouts.appinside')

@section('content')

    <link href="{{ secure_asset('assets/global/plugins/typeahead/typeahead.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Product Categories</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Product Categories
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

                {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'style' => 'overflow: hidden;')) !!}

                    <?php if(!isset($_GET['name'])){$_GET['name'] = null;} ?>
                    <div class="col-md-6" style="margin-bottom:5px;">
                        <input type="text" class="form-control" name="name" id="typeahead_example_1" placeholder="Name" value="{{ $_GET['name'] }}">
                    </div>

                    <?php if(!isset($_GET['category_type'])){$_GET['category_type'] = null;} ?>
                    <div class="col-md-6" style="margin-bottom:5px;">
                        {!! Form::select('category_type', ['' => 'Type', 'parent' => 'Parent', 'child' => 'Child'], $_GET['category_type'], ['class' => 'form-control js-example-basic-single', 'id' => 'category_type', 'name' => 'category_type']) !!}
                    </div>

                    <?php if(!isset($_GET['parent_category_id'])){$_GET['parent_category_id'] = null;} ?>
                    <div class="col-md-6" style="margin-bottom:5px;">
                        {!! Form::select('parent_category_id', ['' => 'Select Parent'] + $parent_cat, $_GET['parent_category_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'parent_category_id', 'name' => 'parent_category_id']) !!}
                    </div>

                    <?php if(!isset($_GET['status'])){$_GET['status'] = null;} ?>
                    <div class="col-md-6" style="margin-bottom:5px;">
                        {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], $_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status', 'name' => 'status']) !!}
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
                    </div>
                    <div class="clearfix"></div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>



    @if(count($product_category) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <!-- <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Product Categories</span>
                        <span class="caption-helper">list</span>
                    </div>
                </div> -->
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Catgory Name</th>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Status</th>
                            @if(!Auth::user()->hasRole('salesteam'))
                            <th>Action</th>
                            <th>Charge</th>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($product_category as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->category_type }}</td>
                                    <td>{{ count((array)$category->parent_cat) ? $category->parent_cat->name : '' }}</td>
                                    <td>
                                        @if($category->status == 1)
                                            Active
                                        @else
                                            Inactive
                                        @endIf
                                    </td>
                                    @if(!Auth::user()->hasRole('salesteam'))
                                    <td>
                                        <a class="label label-success" href="product-category/{{ $category->id }}/edit">
                                            <i class="fa fa-pencil"></i> Update
                                        </a>
                                    </td>
                                    <td>
                                        <a class="label label-success" href="product-category-charge/v2/{{ $category->id }}">
                                            <i class="fa fa-money"></i> Add/Edit
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $product_category->appends($req)->render() }}
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

    <script src="{{ secure_asset('assets/global/plugins/typeahead/handlebars.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/global/plugins/typeahead/typeahead.bundle.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('product-category-manage', 'product-category');

            // $('#example0').DataTable({
            //     "order": [],
            // });

            {!! !empty($req['name'])               ? "document.getElementById('name').value = '".$req['name']."'" : "" !!}
            {!! !empty($req['category_type'])      ? "document.getElementById('category_type').value = '".$req['category_type']."'" : "" !!}
            {!! !empty($req['parent_category_id']) ? "document.getElementById('parent_category_id').value = '".$req['parent_category_id']."'" : "" !!}
            {!! !empty($req['status'])             ? "document.getElementById('status').value = '".$req['status']."'" : "" !!}

            ComponentsTypeahead.init();
        });

        var ComponentsTypeahead = function () {

          var handleTwitterTypeahead = function() {

              // Example #1
              // instantiate the bloodhound suggestion engine
              var numbers = new Bloodhound({
                datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.num); },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                local: [
                  @foreach($all_cat as $cat)
                    { num: '{{ $cat->name }}' },
                  @endforeach
                ]
              });
               
              // initialize the bloodhound suggestion engine
              numbers.initialize();
               
              // instantiate the typeahead UI
              if (App.isRTL()) {
                $('#typeahead_example_1').attr("dir", "rtl");  
              }
              $('#typeahead_example_1').typeahead(null, {
                displayKey: 'num',
                hint: (App.isRTL() ? false : true),
                source: numbers.ttAdapter()
              });

          }

          return {
              //main function to initiate the module
              init: function () {
                  handleTwitterTypeahead();
              }
          };

        }();

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
