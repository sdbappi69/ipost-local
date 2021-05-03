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
            <span>Inquiry</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Inquiry
    <small> view</small>
</h1>

<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered animated flipInX">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Filter</span>
            </div>
            <div class="tools">
                <button data-toggle="modal" data-target="#submit_inquiry" type="button" class="btn btn-success"><i class="fa fa-plus"></i> Create New</button>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">
            {!! Form::open(array('method' => 'get', 'id' => 'filter-form','url' => secure_url('') . '/inquiry')) !!}
            <div class="col-md-12" style="margin-bottom:5px;">
                <label class="control-label">Search By Data</label>
                {{Form::text('search_by_data',null,['class' => 'form-control focus_it','placeholder' => 'Customer (Name, Mobile, E-mail, Address, Calling Number) & Company'])}}
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Query</label>
                {!! Form::select('query_id_s[]',$querys, null, ['class' => 'form-control js-example-basic-single', 'id' => 'query_id_s','data-placeholder' => 'Select Query','multiple' => '']) !!}
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Source of information</label>
                {!! Form::select('source_of_information_s[]',$source_of_informations, null, ['class' => 'form-control js-example-basic-single', 'id' => 'source_of_information_s','data-placeholder' => 'Select One','multiple' => '']) !!}
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Submitted from</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('date_from',null, ['class' => 'form-control picking_date','placeholder' => 'From' ,'readonly' => 'true', 'id' => 'date_from']) !!}
                </div>
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Submitted to</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('date_to',null, ['class' => 'form-control picking_date','placeholder' => 'From' ,'readonly' => 'true', 'id' => 'date_to']) !!}
                </div>
            </div>
            @permission('head_of_customer_support')
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Agent</label>
                {!! Form::select('created_by_s[]',$users, null, ['class' => 'form-control js-example-basic-single', 'id' => 'created_by_s','data-placeholder' => 'Select Agent','multiple' => '']) !!}
            </div>
            @endpermission
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Status</label>
                {!! Form::select('status_s[]',$statues, null, ['class' => 'form-control js-example-basic-single', 'id' => 'status_s','data-placeholder' => 'Select Status','multiple' => '']) !!}
            </div>

            <div class="col-md-12">
                <button type="button" class="btn btn-primary filter-btn pull-right">
                    <i class="fa fa-search"></i> Filter
                </button>
            </div>
            <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

@if(count($inquirys) > 0)


<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Inquiry - {{$inquirys->total()}}</span>
            </div>
            <div class="tools">
                <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
            </div>
        </div>

        @include('partials.errors')

        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" id="example0">
                <thead>
                    <th></th>
                    <th>Calling Number</th>
                    <th>Customer</th>
                    <th>Company</th>
                    <th>Mode</th>
                    <th>Source of <br> information</th>
                    <th>Query</th>
                    <th>Complain</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    @permission('head_of_customer_support')
                    <th>Agent</th>
                    <th>Action</th>
                    @endpermission
                </thead>
                <tbody>
                    @foreach($inquirys as $inquiry)
                    <tr>
                        <td></td>
                        <td>{{$inquiry->calling_number}}</td>
                        <td>
                            <b>Name : </b>{{$inquiry->customer_name}} <br>
                            <b>Number : </b>{{$inquiry->customer_number}} <br>
                            <b>Alt. Number : </b>{{$inquiry->customer_alt_number}} <br>
                            <b>E-mail : </b>{{$inquiry->customer_email}} <br>
                            <b>Address : </b>{{$inquiry->customer_address}} <br>
                        </td>
                        <td>{{$inquiry->company_name}}</td>
                        <td>{{$inquiry->mode_selection}}</td>
                        <td>{{$inquiry->sourceOfInformation->title or ''}}</td>
                        <td>{{$inquiry->queryDetails->title or ''}}</td>
                        <td>{{$inquiry->complain or ''}}</td>
                        <td>{{$inquiry->remarks or ''}}</td>
                        <td>{{$inquiry->inquiryStatus->title or ''}}</td>
                        <td>{{$inquiry->created_at or ''}}</td>
                        @permission('head_of_customer_support')
                        <td>
                            Name : {{$inquiry->createdBy->name or ''}} <br>
                            E-mail : {{$inquiry->createdBy->email or ''}}
                        </td>
                        <td>
                            <button class="btn btn-info" data-toggle="modal" data-target="#edit_inquiry_{{$inquiry->id}}">
                                <i class="fa fa-pencil"></i> Edit
                            </button>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#send_email_inquiry_{{$inquiry->id}}">
                                <i class="fa fa-envelope"></i> Send E-mail
                            </button>
                            @if($inquiry->status == 'In process')
                            <a class="btn btn-success" href="{{secure_url('inquiry/mark-as-solved/'.$inquiry->id)}}">
                                <i class="fa fa-check"></i> Solved
                            </a>
                            @endif
                        </td>
                        @include('customer-support.inquiry.edit_inquiry_modal')
                        @include('customer-support.inquiry.send_email_inquiry_modal')
                        @endpermission
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination pull-right">
                {!! $inquirys->appends($_REQUEST)->render() !!}
            </div>
        </div>
    </div>
</div>

@endIf
@include('customer-support.inquiry.submit_inquiry')
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        highlight_nav('inquiry','inbound');
        $('#example0').dataTable( {
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });
    });

    $(".filter-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('inquiry') }}").submit();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('inquiry/export-xls') }}").submit();
    });

</script>
<script type="text/javascript" src="{{secure_asset('custom/js/inquiry_caller_load_data_customer_support.js')}}"></script>
@endsection
