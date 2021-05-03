<link href="{{ secure_asset('assets/global/plugins/typeahead/typeahead.css') }}" rel="stylesheet" type="text/css" />

<div class="row">
    <br>
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

                    <input type="hidden" name="step" value="2">

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
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Product Categories</span>

                        @if(Auth::user()->hasRole('superadministrator')||Auth::user()->hasRole('systemadministrator'))
                            @if($cod->store_id == "")
                                <a target="_blank" href="{{ secure_url('chargecod/create') }}?clone={{ $cod->id }}&store={{ $id }}" class="btn btn-warning btn-xs" style="position: absolute; right: 35px;">
                                    <i class="fa fa-money"></i> Change COD Amount 
                                </a>
                            @else
                                <a target="_blank" href="{{ secure_url('chargecod') }}/{{ $cod->id }}/edit?overwrite={{ $cod->id }}&store_id={{ $id }}" class="btn btn-warning btn-xs" style="position: absolute; right: 35px;"> 
                                    <i class="fa fa-money"></i> Change COD Amount
                                </a>
                            @endif
                        @endIf

                    </div>
                </div>
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Catgory Name</th>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Status</th>
                            <th>Charge</th>
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
                                    <td>
                                        <a class="label label-success" href="{{ secure_url('product-category-charge/v2/'.$category->id.'?store_id='.$id) }}">
                                            <i class="fa fa-money"></i> Change
                                        </a>
                                    </td>
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

</div>

<script src="{{ secure_asset('assets/global/plugins/typeahead/handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/typeahead/typeahead.bundle.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {

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