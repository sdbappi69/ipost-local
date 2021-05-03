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
                <span>Countries</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Countries
        <small> update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        {!! Form::model($country, array('url' => secure_url('') . '/country/'.$country->id, 'method' => 'put', 'class' => 'form-horizontal')) !!}
        <div class="col-md-8">
            @include('flash::message')

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-flag"></i> {!! $title !!}

                    <a href="{!! secure_url('country') !!}" class="pull-right text-danger">
                        <i class="fa fa-backward"></i> Back
                    </a>
                </div>
                <div class="panel-body small">
                    <div class="form-group">
                        {!! Form::label('name', 'Name', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('code', 'Code', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('code', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('prefix', 'Prefix', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('prefix', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-9">
                            {!! Form::radio('status', 1, false) !!} &nbsp; Active &nbsp;
                            {!! Form::radio('status', 0, false) !!} &nbsp; De-active &nbsp;
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fa fa-edit"></i> Update Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('country', 'locations');
        });
    </script>
@endsection