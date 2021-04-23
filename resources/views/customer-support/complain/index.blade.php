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
            <span>Complains</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Complains
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
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">
            {!! Form::open(array('method' => 'get', 'id' => 'filter-form','url' => 'complain')) !!}
            <div class="col-md-8" style="margin-bottom:5px;">
                <label class="control-label">Search By Data</label>
                {{Form::text('search_by_data',null,['class' => 'form-control focus_it','placeholder' => 'Customer (Name, Mobile, E-mail, Address) & Company'])}}
            </div>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Sub-Order ID</label>
                {{Form::text('sub_order_unique_id',null,['class' => 'form-control focus_it','placeholder' => 'Sub Order ID'])}}
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

@if(count($complains) > 0)


<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Complains - {{$complains->total()}}</span>
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
                    <th>Sub Order</th>
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
                    @foreach($complains as $complain)
                    <tr>
                        <td></td>
                        <td>
                            <a class="btn btn-default btn-xs" href="{{url('order-cs?sub_order_id='.$complain->sub_order->unique_suborder_id)}}">{{$complain->sub_order->unique_suborder_id or ''}}</a>
                        </td>
                        <td>
                            <b>Name : </b>{{$complain->customer_name}} <br>
                            <b>Number : </b>{{$complain->customer_number}} <br>
                            <b>Alt. Number : </b>{{$complain->customer_alt_number}} <br>
                            <b>E-mail : </b>{{$complain->customer_email}} <br>
                            <b>Address : </b>{{$complain->customer_address}} <br>
                        </td>
                        <td>{{$complain->company_name}}</td>
                        <td>{{$complain->mode_selection}}</td>
                        <td>{{$complain->sourceOfInformation->title or ''}}</td>
                        <td>{{$complain->queryDetails->title or ''}}</td>
                        <td>{{$complain->complain or ''}}</td>
                        <td>{{$complain->remarks or ''}}</td>
                        <td>{{$complain->status or ''}}</td>
                        <td>{{$complain->created_at or ''}}</td>
                        @permission('head_of_customer_support')
                        <td>
                            Name : {{$complain->createdBy->name or ''}} <br>
                            E-mail : {{$complain->createdBy->email or ''}}
                        </td>
                        <td>
                            <button class="btn btn-info" data-toggle="modal" data-target="#edit_order_complain_{{$complain->id}}">
                                <i class="fa fa-pencil"></i> Edit
                            </button>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#send_email_complain_{{$complain->id}}">
                                <i class="fa fa-envelope"></i> Send E-mail
                            </button>
                            @if($complain->status == 'In process')
                            <a class="btn btn-success" href="{{url('complain/mark-as-solved/'.$complain->id)}}">
                                <i class="fa fa-check"></i> Solved
                            </a>
                            @endif
                        </td>
                        @include('customer-support.complain.edit_order_complain_modal')
                        @include('customer-support.complain.send_email_complain_modal')
                        @endpermission
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination pull-right">
                {!! $complains->appends($_REQUEST)->render() !!}
            </div>
        </div>
    </div>
</div>

@endIf

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        highlight_nav('complain','inbound');
        $('#example0').dataTable( {
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });
    });

    $(".filter-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ URL::to('complain') }}").submit();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ URL::to('complain/export-xls') }}").submit();
    });

</script>

@endsection
