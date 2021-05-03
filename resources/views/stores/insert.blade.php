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
                <a href="{{ secure_url('store') }}">Stores</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Stores
        <small>create new</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        <div class="row step-background-thin">
            <div class="col-md-6 bg-grey-steel mt-step-col active">
                <div class="mt-step-number">1</div>
                <div class="mt-step-title uppercase font-grey-cascade">Basic</div>
                <div class="mt-step-content font-grey-cascade">Store information</div>
            </div>
            <div class="col-md-6 bg-grey-steel mt-step-col">
                <div class="mt-step-number">2</div>
                <div class="mt-step-title uppercase font-grey-cascade">Charge</div>
                <div class="mt-step-content font-grey-cascade">Set charge</div>
            </div>
        </div>

            {!! Form::open(array('url' => secure_url('') . '/store', 'method' => 'post')) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Store Id</label>
                            {!! Form::text('store_id', null, ['class' => 'form-control', 'placeholder' => 'Store id', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Store Password</label>
                            {!! Form::text('store_password', null, ['class' => 'form-control', 'placeholder' => 'Store password', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Status</label>
                            {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Store Url</label>
                            {!! Form::text('store_url', null, ['class' => 'form-control', 'placeholder' => 'Store Url', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Select Merchant</label>
                            {!! Form::select('merchant_id', array(''=>'Select Merchant')+$merchants, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label">Store Type</label>
                            {!! Form::select('store_type_id', $storetypes, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                        </div>

                    </div>

                </div>

                &nbsp;
                <div class="row padding-top-10">
                    <a href="javascript:history.back()" class="btn default"> Cancel </a>
                    {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
                </div>

            {!! Form::close() !!}


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('store-add', 'stores');
        });

    </script>

@endsection
