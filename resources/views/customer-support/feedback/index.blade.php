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
            <span>Feedbacks</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Feedbacks
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
            {!! Form::open(array('method' => 'get', 'id' => 'filter-form','url' => 'feedback')) !!}
            <div class="col-md-12" style="margin-bottom:5px;">
                <label class="control-label">Search By Data</label>
                {{Form::text('search_by_data',null,['class' => 'form-control focus_it','placeholder' => 'Customer (Name, Mobile, Address) & Company'])}}
            </div>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Sub-Order ID</label>
                {{Form::text('sub_order_unique_id',null,['class' => 'form-control focus_it','placeholder' => 'Sub Order ID'])}}
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Rating</label>
                {!! Form::select('rating_s[]',$ratings, null, ['class' => 'form-control js-example-basic-single', 'id' => 'rating_s','data-placeholder' => 'Select Rating','multiple' => '']) !!}
            </div>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Unique Head</label>
                {!! Form::select('unique_head_s[]',$unique_heads, null, ['class' => 'form-control js-example-basic-single', 'id' => 'unique_head_s','data-placeholder' => 'Select Unique Head','multiple' => '']) !!}
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Reaction</label>
                {!! Form::select('reaction_s[]',$reactions, null, ['class' => 'form-control js-example-basic-single', 'id' => 'reaction_s','data-placeholder' => 'Select One','multiple' => '']) !!}
            </div>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Type</label>
                {!! Form::select('type_s[]',['Delivery' => 'Delivery','Return' => 'Return'], null, ['class' => 'form-control js-example-basic-single', 'id' => 'type_s','data-placeholder' => 'Select One','multiple' => '']) !!}
            </div>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Order Create from</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('order_date_from',null, ['class' => 'form-control picking_date','placeholder' => 'From' ,'readonly' => 'true', 'id' => 'order_date_from']) !!}
                </div>
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Order create to</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('order_date_to',null, ['class' => 'form-control picking_date','placeholder' => 'To' ,'readonly' => 'true', 'id' => 'order_date_to']) !!}
                </div>
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
                    {!! Form::text('date_to',null, ['class' => 'form-control picking_date','placeholder' => 'To' ,'readonly' => 'true', 'id' => 'date_to']) !!}
                </div>
            </div>
            @permission('head_of_customer_support')
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Agent</label>
                {!! Form::select('updated_by_s[]',$users,null, ['class' => 'form-control js-example-basic-single', 'id' => 'updated_by_s','data-placeholder' => 'Select Agent','multiple' => '']) !!}
            </div>
            @endpermission
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Status</label>
                {!! Form::select('status_s',$statues, null, ['class' => 'form-control js-example-basic-single', 'id' => 'status_s','data-placeholder' => 'Select Status']) !!}
            </div>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Rider</label>
                {!! Form::select('rider_s',$riders, null, ['class' => 'form-control js-example-basic-single', 'id' => 'rider_s','data-placeholder' => 'Select Rider','multiple' => '']) !!}
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

@if(count($feedbacks) > 0)


<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Feedbacks - {{$feedbacks->total()}}</span>
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
                    <th>Order <br> Date & Time</th>
                    <th>Delivered <br> Date & Time</th>
                    <th>Product</th>
                    <th>Amount To <br> Collect</th>
                    <th>Amount <br> Collected</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>Company</th>
                    <th>Hub</th>
                    <th>Rider</th>
                    <th>Mode</th>
                    <th>Unique Head</th>
                    <th>Reaction</th>
                    <th>Rating</th>
                    <th>Suggestion</th>
                    <th>Remarks</th>
                    <th>Call Date</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    @permission('head_of_customer_support')
                    <th>Agent</th>
                    @endpermission
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach($feedbacks as $feedback)
                    <tr>
                        <td></td>
                        <td>
                            <a class="btn btn-default btn-xs" href="{{url('order-cs?sub_order_id='.$feedback->sub_order->unique_suborder_id)}}">{{$feedback->sub_order->unique_suborder_id or ''}}</a>
                        </td>
                        <td>{{$feedback->order_created_at or ''}}</td>
                        <td>{{$feedback->delivered_date or ''}}</td>
                        <td>{{$feedback->product or ''}}</td>
                        <td>{{$feedback->amount_to_collect or ''}}</td>
                        <td>{{$feedback->amount_collected or ''}}</td>
                        <td>{{$feedback->type or ''}}</td>
                        <td>
                            <b>Name : </b>{{$feedback->customer_name}} <br>
                            <b>Number : </b>{{$feedback->customer_number}} <br>
                            <b>Address : </b>{{$feedback->customer_address}} <br>
                        </td>
                        <td>{{$feedback->company_name}}</td>
                        <td>{{$feedback->hub}}</td>
                        <td>{{$feedback->riderDetails->name or ''}}</td>
                        <td>{{$feedback->mode_selection}}</td>
                        <td>{{$feedback->uniqueHead->title or ''}}</td>
                        <td>{{$feedback->reactionDetails->title or ''}}</td>
                        <td>{{$feedback->rating or ''}}</td>
                        <td>{{$feedback->suggestion or ''}}</td>
                        <td>{{$feedback->remarks or ''}}</td>
                        <td>{{$feedback->call_date or ''}}</td>
                        <td>{{$feedback->created_at or ''}}</td>
                        <td>{{$feedback->status or ''}}</td>
                        @permission('head_of_customer_support')
                        <td>
                            Name : {{$feedback->updatedBy->name or ''}} <br>
                            E-mail : {{$feedback->updatedBy->email or ''}}
                        </td>
                        @endpermission
                        @if($feedback->status == 'Pending')
                        <td>
                            <button class="btn btn-info" data-toggle="modal" data-target="#edit_order_feedback_{{$feedback->id}}">
                                <i class="fa fa-pencil"></i> Take Feedback
                            </button>
                        </td>
                        @else
                        <td></td>
                        @endif
                        @include('customer-support.feedback.set_feedback_modal')
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination pull-right">
                {!! $feedbacks->appends($_REQUEST)->render() !!}
            </div>
        </div>
    </div>
</div>

@endIf

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        @if(isset($_REQUEST['status_s']) and ($_REQUEST['status_s']==1))
        highlight_nav('collected_feedback','outbound');
        @else{
            highlight_nav('get_feedback','outbound');
        }
        @endif
        $('#example0').dataTable( {
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false
        });
    });

    $(".filter-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ URL::to('feedback') }}").submit();
    });

    $(".export-btn").click(function(e){
        // alert(1);
        e.preventDefault();
        $('#filter-form').attr('action', "{{ URL::to('feedback/export-xls') }}").submit();
    });

</script>

@endsection
