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
                                {!! Form::select('charge_type', array(''=>'Select Charge Type')+$charge_types, $charge_type, ['class' => 'form-control js-example-basic-single', 'id' => 'charge_type', 'required' => 'required']) !!}
                            </td>
                        </tr>
                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-md-12 fixed animated bounceInDown" style="display: none;">

            <div id="accordion3" class="panel-group">

                <?php $i = 1; ?>

                @foreach($charge_models as $charge_model)

                    @if($charge_model->unit == 'none')

                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#{{ $charge_model->id }}">
                                        <i class="fa fa-plus"></i> {{ $charge_model->title }}
                                    </a>
                                </h4>
                            </div>
                            <div id="{{ $charge_model->id }}" class="panel-collapse collapse @if($i == 1) in @endIf">
                                <div class="panel-body">

                                    @foreach($zone_genres as $zone_genre)

                                        <?php
                                            $fixed_charge = 0;
                                            $charge_added = 0;
                                            $charge_id = 0;

                                            if(isset($_GET['store_id'])&&$_GET['store_id'] != ''){

                                                foreach ($charges as $charge) {
                                                    if($charge->product_category_id == $product_category->id && $charge->charge_model_id == $charge_model->id && $charge->zone_genre_id == $zone_genre->id && $charge->store_id == $_GET['store_id']){
                                                        $fixed_charge = $charge->fixed_charge;
                                                        $charge_added = 1;
                                                        $charge_id = $charge->id;
                                                        break;
                                                    }
                                                }

                                                if($charge_added == 0){
                                                    foreach ($charges as $charge) {
                                                        if($charge->product_category_id == $product_category->id && $charge->zone_genre_id == $zone_genre->id && $charge->store_id == $_GET['store_id'] && $charge->charge_model_id != $charge_model->id){
                                                            break;
                                                        }else if($charge->product_category_id == $product_category->id && $charge->charge_model_id == $charge_model->id && $charge->zone_genre_id == $zone_genre->id){
                                                            $fixed_charge = $charge->fixed_charge;
                                                            $charge_added = 1;
                                                            break;
                                                        }
                                                    }
                                                }

                                            }else{

                                                foreach ($charges as $charge) {
                                                    if($charge->product_category_id == $product_category->id && $charge->charge_model_id == $charge_model->id && $charge->zone_genre_id == $zone_genre->id){
                                                        $fixed_charge = $charge->fixed_charge;
                                                        $charge_added = 1;
                                                        $charge_id = $charge->id;
                                                        break;
                                                    }
                                                }

                                            }
                                        ?>

                                        <div class="col-md-3 animated bounceIn">
                                            <!-- BEGIN BUTTONS PORTLET-->
                                            <div class="portlet light tasks-widget bordered">

                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        @if($charge_added == 0)
                                                            <i class="fa fa-circle" style="color:red"></i>
                                                        @else
                                                            <i class="fa fa-check-circle" style="color:green"></i>
                                                        @endIf
                                                        <span class="caption-subject font-dark bold uppercase">{{ $zone_genre->title }}</span>
                                                        <span class="caption-helper">{{ $zone_genre->description }}</span>
                                                    </div>
                                                </div>

                                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                                    {!! Form::open(array('url' => '/product-category-charge-submit', 'method' => 'post')) !!}

                                                        <input type="hidden" name="product_category_id" value="{{ $product_category->id }}">

                                                        <input type="hidden" name="charge_model_id" value="{{ $charge_model->id }}">

                                                        <input type="hidden" name="zone_genre_id" value="{{ $zone_genre->id }}">

                                                        @if(isset($_GET['store_id'])&&$_GET['store_id'] != '')
                                                            <input type="hidden" name="store_id" value="{{ $_GET['store_id'] }}">
                                                        @endIf

                                                        <div class="form-group">
                                                            <label class="control-label">Charge (BDT)</label>
                                                            {!! Form::text('fixed_charge', $fixed_charge, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                                        </div>

                                                        @if($charge_id != 0)
                                                            {!! Form::submit('Save', ['class' => 'btn green col-md-6']) !!}
                                                            
                                                            <a href="{{ URL::to('charge-remove/'.$charge_id) }}" class="btn red col-md-6" data-toggle="confirmation" data-original-title="Are you sure ?" title="">Remove</a>

                                                        @else
                                                            {!! Form::submit('Save', ['class' => 'btn green col-md-12']) !!}
                                                        @endIf

                                                    {!! Form::close() !!}                                                 

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <?php $i++; ?>
                    
                    @endIf

                @endforeach

            </div>

        </div>

        <div class="col-md-12 weight_based animated bounceInDown" style="display: none;">

            <div id="accordion3" class="panel-group">

                <?php $j = 1; ?>

                @foreach($charge_models as $charge_model)

                    @if($charge_model->unit == 'Kg' || $charge_model->unit == 'kg')

                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#{{ $charge_model->id }}">
                                        <i class="fa fa-plus"></i> {{ $charge_model->title }}
                                    </a>
                                </h4>
                            </div>
                            <div id="{{ $charge_model->id }}" class="panel-collapse collapse @if($j == 1) in @endIf">
                                <div class="panel-body">

                                    @foreach($zone_genres as $zone_genre)

                                        <?php
                                            $fixed_charge = 0;
                                            $additional_range_per_slot = 0;
                                            $additional_charge_per_slot = 0;
                                            $charge_added = 0;
                                            $charge_id = 0;

                                            if(isset($_GET['store_id'])&&$_GET['store_id'] != ''){

                                                foreach ($charges as $charge) {
                                                    if($charge->product_category_id == $product_category->id && $charge->charge_model_id == $charge_model->id && $charge->zone_genre_id == $zone_genre->id && $charge->store_id == $_GET['store_id']){
                                                        $fixed_charge = $charge->fixed_charge;
                                                        $additional_range_per_slot = $charge->additional_range_per_slot;
                                                        $additional_charge_per_slot = $charge->additional_charge_per_slot;
                                                        $charge_added = 1;
                                                        $charge_id = $charge->id;
                                                        break;
                                                    }
                                                }

                                                if($charge_added == 0){
                                                    foreach ($charges as $charge) {
                                                        if($charge->product_category_id == $product_category->id && $charge->zone_genre_id == $zone_genre->id && $charge->store_id == $_GET['store_id'] && $charge->charge_model_id != $charge_model->id){
                                                            // die();
                                                            break;
                                                        }else if($charge->product_category_id == $product_category->id && $charge->charge_model_id == $charge_model->id && $charge->zone_genre_id == $zone_genre->id){
                                                            $fixed_charge = $charge->fixed_charge;
                                                            $additional_range_per_slot = $charge->additional_range_per_slot;
                                                            $additional_charge_per_slot = $charge->additional_charge_per_slot;
                                                            $charge_added = 1;
                                                            break;
                                                        }
                                                    }
                                                }

                                            }else{

                                                foreach ($charges as $charge) {
                                                    if($charge->product_category_id == $product_category->id && $charge->charge_model_id == $charge_model->id && $charge->zone_genre_id == $zone_genre->id){
                                                        $fixed_charge = $charge->fixed_charge;
                                                        $additional_range_per_slot = $charge->additional_range_per_slot;
                                                        $additional_charge_per_slot = $charge->additional_charge_per_slot;
                                                        $charge_added = 1;
                                                        $charge_id = $charge->id;
                                                        break;
                                                    }
                                                }

                                            }
                                        ?>

                                        <div class="col-md-3 animated bounceIn">
                                            <!-- BEGIN BUTTONS PORTLET-->
                                            <div class="portlet light tasks-widget bordered">

                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        @if($charge_added == 0)
                                                            <i class="fa fa-circle" style="color:red"></i>
                                                        @else
                                                            <i class="fa fa-check-circle" style="color:green"></i>
                                                        @endIf
                                                        <span class="caption-subject font-dark bold uppercase">{{ $zone_genre->title }}</span>
                                                        <span class="caption-helper">{{ $zone_genre->description }}</span>
                                                    </div>
                                                </div>

                                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                                    {!! Form::open(array('url' => '/product-category-charge-submit', 'method' => 'post')) !!}

                                                        <input type="hidden" name="product_category_id" value="{{ $product_category->id }}">

                                                        <input type="hidden" name="charge_model_id" value="{{ $charge_model->id }}">

                                                        <input type="hidden" name="zone_genre_id" value="{{ $zone_genre->id }}">

                                                        <input type="hidden" name="additional_charge_type" value="1">

                                                        @if(isset($_GET['store_id'])&&$_GET['store_id'] != '')
                                                            <input type="hidden" name="store_id" value="{{ $_GET['store_id'] }}">
                                                        @endIf

                                                        <div class="form-group">
                                                            <label class="control-label">Initial (BDT)</label>
                                                            {!! Form::text('fixed_charge', $fixed_charge, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label">Additional Slot (Kg)</label>
                                                            {!! Form::text('additional_range_per_slot', $additional_range_per_slot, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label">Charge per slot (BDT)</label>
                                                            {!! Form::text('additional_charge_per_slot', $additional_charge_per_slot, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                                        </div>

                                                        @if($charge_id != 0)
                                                            {!! Form::submit('Save', ['class' => 'btn green col-md-6']) !!}

                                                            <a href="{{ URL::to('charge-remove/'.$charge_id) }}" class="btn red col-md-6" data-toggle="confirmation" data-original-title="Are you sure ?" title="">Remove</a>
                                                        @else
                                                            {!! Form::submit('Save', ['class' => 'btn green col-md-12']) !!}
                                                        @endIf

                                                    {!! Form::close() !!}                                                 

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <?php $j++; ?>
                    
                    @endIf

                @endforeach

            </div>

        </div>

    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            @if(isset($_GET['store_id'])&&$_GET['store_id'] != '')
                highlight_nav('store-manage', 'stores');
            @else
                highlight_nav('product-category-manage', 'product-category');
            @endIf

            var charge_type = $('#charge_type').val();
            set_charge_view(charge_type);

        });

        $('#charge_type').change(function() {
            var charge_type = $('#charge_type').val();
            set_charge_view(charge_type);
        });

        function set_charge_view(charge_type){
            if(charge_type == 'fixed'){
                $('.weight_based').hide(100);
                $('.fixed').show(100);
            }else if(charge_type == 'weight_based'){
                $('.fixed').hide(100);
                $('.weight_based').show(100);
            }else{
                $('.weight_based').hide(100);
                $('.fixed').hide(100);
            }
        }

    </script>

@endsection
