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
                <a href="{{ secure_url('hub') }}">Shelfs</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Shelfs
        <small>create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

            {!! Form::open(array('url' => secure_url('') . '/shelf', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                       <div class="form-group">
                           <label class="control-label col-md-12 np-lr">Hub</label>
                           {!! Form::select('hub_id', array(''=>'Select Hub') + $hubs, null, ['class' => 'form-control']) !!}
                       </div>

                       <div class="form-group">
                           <label class="control-label col-md-12 np-lr">Assigned Hub</label>
                           {!! Form::select('assignd_hub_id', array(''=>'Select Hub') + $hubs, null, ['class' => 'form-control']) !!}
                       </div>

                       <div class="form-group">
                            <label class="control-label">Select Type</label>
                            {!! Form::select('shelf_type', array('delivery' => 'Delivery','pending' => 'Pending','return' => 'Return'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">

                       <div class="form-group">
                           <label class="control-label">Shelf Title</label>
                           {!! Form::text('shelf_title', null, ['class' => 'form-control', 'placeholder' => 'Shelf Title', 'required' => 'required']) !!}
                       </div>

                       <div class="form-group">
                            <label class="control-label">Width</label>
                            {!! Form::text('width', null, ['class' => 'form-control', 'placeholder' => 'Width', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                           <label class="control-label">Height</label>
                           {!! Form::text('height', null, ['class' => 'form-control', 'placeholder' => 'Height', 'required' => 'required']) !!}
                       </div>

                       <div class="form-group">
                           <label class="control-label">Length</label>
                           {!! Form::text('length', null, ['class' => 'form-control', 'placeholder' => 'Length', 'required' => 'required']) !!}
                       </div>

                    </div>

                </div>

                &nbsp;
                <div class="row padding-top-10">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('warehouse-manage', 'shelfs');

        });

    </script>

@endsection
