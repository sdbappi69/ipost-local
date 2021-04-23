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
                <a href="{{ URL::to('hub') }}">Product Categories</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Edit</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Product Categories
        <small>update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

            {!! Form::model($detail, array('url' => '/product-category/'.$detail->id, 'method' => 'put')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Title</label>
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Parent Category</label>
                            {!! Form::select('parent_category_id', array(''=>'Select Parent')+$categories, null, ['class' => 'form-control']) !!}
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Category type</label>
                            {!! Form::select('category_type', array('parent'=>'Parent','child'=>'Child','individual'=>'Individual'), null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Vehicles</label>
                            {!! Form::select('vehicle_type_id[]', $vehicleTypes, $categoryVehicles, ['class' => 'form-control','id'=>'vehicle_type_id','multiple','Select Vehicle']) !!}
                        </div>

                    </div>

                </div>

                &nbsp;
                <div class="row padding-top-10 col-md-12">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('hub-product-category', 'product-category');
            $("#vehicle_type_id").select2({
                'placeholder': 'Select Vehicle'
            });
            // Get State list
            var country_id = $('#country_id').val();
            get_states(country_id);
        });

        // Get State list On Country Change
        $('#country_id').on('change', function() {
            get_states($(this).val());
        });

        // Get City list On State Change
        $('#state_id').on('change', function() {
            get_cities($(this).val());
        });

        // Get Zone list On City Change
        $('#city_id').on('change', function() {
            get_zones($(this).val());
        });

    </script>

@endsection
