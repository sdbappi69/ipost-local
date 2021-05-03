@extends('layouts.appinside')

@section('content')
<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
      rel="stylesheet" type="text/css"/>

<style type="text/css">
    .modal-backdrop.in {
        display: none;
    }

    .confirmationForm {
        display: none;
    }
</style>

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ secure_url('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Consignments</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Consignment
    <small>Reconciliation</small>
</h1>
@include('errors.validation_error')
<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered animated flipInX">

        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-dark bold uppercase">Consignment</span>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

            <?php
            $amount_to_collect = 0;
            $amount_collected = 0;
            $picking_quantity = 0;
            $picked_quantity = 0;
            $delivery_quantity = 0;
            $delivered_quantity = 0;
            $return_quantity = 0;
            $returned_quantity = 0;
            ?>


            @foreach($consignment->task as $task)
            <?php
            switch ($task->task_type_id) {
                case 1:
                    $picking_quantity += $task->quantity;
                    $picked_quantity += $task->collected_quantity;
                    break;
                case 2:
                    $delivery_quantity += $task->quantity;
                    $delivered_quantity += $task->collected_quantity;
                    $amount_to_collect += $task->amount;
                    $amount_collected += $task->collected;
                    break;
                case 4:
                    $return_quantity += $task->quantity;
                    $returned_quantity += $task->collected_quantity;
                    break;
            }
            ?>
            @endforeach

            <div class="col-md-6" style="margin-bottom:5px;">
                <table class="table table-hover">
                    <tr>
                        <td>Consignment id</td>
                        <td>:</td>
                        <td>{{ $consignment->consignment_unique_id }}</td>
                    </tr>
                    <tr>
                        <td>Picking Quantity</td>
                        <td>:</td>
                        <td>{{ $picking_quantity }}</td>
                    </tr>
                    <tr>
                        <td>Picked Quantity</td>
                        <td>:</td>
                        <td>{{ $picked_quantity }}</td>
                    </tr>
                    <tr>
                        <td>Delivery Quantity</td>
                        <td>:</td>
                        <td>{{ $delivery_quantity }}</td>
                    </tr>
                    <tr>
                        <td>Delivered Quantity</td>
                        <td>:</td>
                        <td>{{ $delivered_quantity }}</td>
                    </tr>
                    <tr>
                        <td>Return Quantity</td>
                        <td>:</td>
                        <td>{{ $return_quantity }}</td>
                    </tr>
                    <tr>
                        <td>Returned Quantity</td>
                        <td>:</td>
                        <td>{{ $returned_quantity }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6" style="margin-bottom:5px;">
                <table class="table table-hover">
                    <tr>
                        <td>Rider</td>
                        <td>:</td>
                        <td>{{ $consignment->rider->name }}</td>
                    </tr>
                    <tr>
                        <td>Contact</td>
                        <td>:</td>
                        <td>{{ $consignment->rider->msisdn }}</td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td>:</td>
                        <td>{{ $amount_to_collect }}</td>
                    </tr>
                    <tr>
                        <td>Available</td>
                        <td>:</td>
                        <td>{{ $amount_collected }}</td>
                    </tr>
                </table>
            </div>
            @if($dueCount == 0)
            <div class="pull-right">
                <a href="{{ secure_url('v2consignment/reconciliation/done/'.$consignment->id) }}"
                   class="btn btn-primary sign" style="margin-top: 68px;">Complete Reconciliation</a>
            </div>
            @endIf
        </div>

    </div>
</div>

@if(count($consignment->task) > 0)

<?php $i = 1; ?>

<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">

        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-dark bold uppercase">Detail</span>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

            <div class="panel-group accordion" id="accordion1">

                <ul class="list-group">

                    <?php $z = 0; ?>

                    @foreach($consignment->task AS $index => $task)

                    <div class="panel panel-default task_panel task_panel_{{ $z }}">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse"
                                   data-parent="#accordion1"
                                   href="#collapse_{{ $task->id }}"> {{ $task->suborder->product->pickup_location->title }} </a>
                            </h4>
                        </div>
                        <div id="collapse_{{ $task->id }}" class="panel-collapse in">

                            <div class="col-md-12" style="margin-bottom:5px; margin-top:5px;">
                                <table width="100%">
                                    <thead>
                                        <tr>
                                            <th>AWB</th>
                                            <th>Task Type</th>
                                            <th>Product</th>
                                            <th>Status</th>
                                            <th>Time</th>
                                            <th>Reconcile</th>
                                            <th>Map</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $task->suborder->unique_suborder_id or '' }}</td>
                                            <?php
                                            switch ($task->task_type_id) {
                                                case 1:
                                                    $type = 'Pick';
                                                    break;
                                                case 2:
                                                    $type = 'Delivery';
                                                    break;
                                                case 3:
                                                    $type = 'Pick & Deliver';
                                                    break;
                                                case 4:
                                                    $type = 'Return';
                                                    break;
                                                case 5:
                                                    $type = 'Return Pick';
                                                    break;
                                                case 6:
                                                    $type = 'Return to Buyer';
                                                    break;
                                                case 7:
                                                    $type = 'Delivery to Return';
                                                    break;
                                                default:
                                                    $type = '';
                                                    break;
                                            }
                                            ?>
                                            <td>{{ $type }}</td>
                                            <td>{{ $task->suborder->product->product_title or '' }}</td>
                                            <?php
                                            switch ($task->status) {
                                                case 1:
                                                    $status = 'Start';
                                                    break;
                                                case 2:
                                                    $status = 'Success';
                                                    break;
                                                case 3:
                                                    $status = 'Pertial';
                                                    break;
                                                case 4:
                                                    $status = 'Failed';
                                                    break;
                                                default:
                                                    $status = 'Processing';
                                                    break;
                                            }
                                            ?>
                                            <td>{{ $status }}</td>
                                            <td>{!! $task->start_time ? 'Start: ' .$task->start_time .'<br/>' : '' !!}
                                                {{ $task->end_time ? 'End: ' .$task->end_time : '' }}</td>
                                            @if($task->reconcile)
                                            <td><label class="badge badge-primary">Yes</label></td>
                                            @else
                                            <td><label class="badge badge-danger">No</label></td>
                                            @endif
                                            <td>
                                                <a target="_blank" class="btn default"
                                                   href="{{secure_url('maps/'.$task->start_lat.'/'.$task->start_long)}}">Start</a>
                                                <a target="_blank" class="btn default"
                                                   href="{{secure_url('maps/'.$task->end_lat.'/'.$task->end_long)}}">End</a>
                                            </td>
                                            @if($task->reconcile == 0)
                                            @if($task->status != 2)
                                            <td><a data-target="#mi-modal-{{ $index }}" data-toggle="modal" class="btn btn-primary" id="MainNavHelp" 
                                                   href="#mi-modal-{{ $index }}">Reconcile</a></td>
                                            @else
                                            <td>N/A</td>
                                            @endif
                                            @else
                                            <td>N/A</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <li id="scroll_{{ $i }}" class="list-group-item"
                                style="overflow: hidden;margin: -6px;">

                                <?php $i++; ?>

                            </li>


                        </div>
                    </div>

                    @endforeach

                </ul>

            </div>

        </div>
    </div>
</div>

@endIf


@if(count($consignment->task) > 0)
@foreach($consignment->task AS $index => $task)
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal-{{ $index }}">
    <div class="modal-dialog modal-sm" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">RECONCILIATION</h4>
            </div>
            <div class="modal-body">

                {!! Form::hidden('required_quantity',$task->quantity,['class' => 'form-control','id' => 'required_quantity_'.$task->id,'placeholder' => 'Requested Quantity','required' => '','readonly' => 'readonly']) !!}

                {!! Form::open(['url' => secure_url('v2consignment/reconcile'), 'method' => 'post','files' => true,'class' => '','id' => '']) !!}

                {!! Form::hidden('view',$i,['class' => 'form-control']) !!}

                {!! Form::hidden('filled_quantity',$task->collected_quantity,['class' => 'form-control','id' => 'filled_quantity_'.$task->id,'placeholder' => 'Success Quantity','required' => '','readonly' => 'readonly']) !!}

                {!! Form::hidden('paid_amount',$task->collected ?? 0,['class' => 'form-control','id' => 'paid_amount_'.$task['task_id'],'placeholder' => 'Paid Amount','required' => 'required']) !!}

                <div class="portlet-body util-btn-margin-bottom-5"
                     style="overflow: hidden;">

                    <?php
                    $due_quantity = $task->quantity - $task->collected_quantity;
                    ?>

                    {!! Form::hidden('task_id',$task->id,['class' => 'form-control','id' => 'task_id_'.$task->id, 'required' => 'required']) !!}

                    <label>Failed Quantity</label>
                    {!! Form::number('due_quantity',$due_quantity,['class' => 'form-control due_quantity','id' => 'due_quantity_'.$task->id,'placeholder' => 'Reconciliation Quantity', 'required' => 'required', 'required_quantity' => $task["quantity"], 'task_id' => $task->id]) !!}

                    <label>Select Action</label>
                    @if($task->task_type_id == 1)
                    {!! Form::select('sub_order_status', array(''=>'Select One')+$picking_sub_order_status, null, ['class' => 'form-control js-example-basic-single', 'id' => 'sub_order_status_'.$task->id, 'required']) !!}
                    @elseif($task->task_type_id == 2)
                    {!! Form::select('sub_order_status', array(''=>'Select One')+$delivery_sub_order_status, null, ['class' => 'form-control js-example-basic-single', 'id' => 'sub_order_status_'.$task->id, 'required']) !!}
                    @else
                    {!! Form::select('sub_order_status', array(''=>'Select One')+$return_sub_order_status, null, ['class' => 'form-control js-example-basic-single', 'id' => 'sub_order_status_'.$task->id, 'required']) !!}
                    @endif


                    <label>Reason</label>
                    {!! Form::select('reason_id', array(''=>'Nothing')+$reasons, $task->reason_id, ['class' => 'form-control js-example-basic-single', 'id' => 'reason_id_'.$task->id]) !!}

                    <label>Remark</label>

                    {!! Form::textarea('remarks',$task->remarks,['class' => 'form-control','id' => 'remarks_'.$task->id,'placeholder' => 'Remarks','rows' => '2']) !!}


                    @if($task['reconcile'] != 1)

                    <br><br>

                    @endIf



                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="modal-btn-si"><i
                        class="fa fa-check"></i>Reconcile</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endforeach
@endIf

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}"
type="text/javascript"></script>

<script src="{{ secure_asset('custom/js/date-time.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function () {
// Navigation Highlight
    highlight_nav('consignmentsv2', 'consignments');

    $("#btn-confirm").on("click", function () {
        $("#mi-modal").modal('show');
    });

    $("#modal-btn-si").on("click", function () {
        $("#mi-modal").modal('hide');
    });

    actionForm($('#action_id').val());

    $(".search_box").on("keyup", function () {
        var search = $(this).val();
        var search = search.toLowerCase();

        if (search != '') {

            $('.task_panel').hide(10);

            var task_panel = $('.task_panel').toArray();

            $.each(task_panel, function (index, value) {
                var html = $('.task_panel_' + index).html();
                var html = html.toLowerCase();
                if (html.match(search)) {
                    $('.task_panel_' + index).show(10);
                }
            });

        } else {

            $('.task_panel').show(10);

        }

    });

});

function actionForm(action_id) {
    $('.confirmationForm').hide(10);
    if (action_id == 'confirm') {
        $('.confirmForm').show(10);
    } else {
        $('.notConfirmForm').show(10);
    }
}

$(".sign").click(function () {
    var id = $(this).attr('task_id');
    $("#sign_" + id).animate({scrollTop: $("#sign_" + id).offset().top});
});

$(".proof").click(function () {
    var id = $(this).attr('task_id');
    $("#proof_" + id).animate({scrollTop: $("#proof_" + id).offset().top});
});

$(".due_quantity").change(function () {
    var due_quantity = $(this).val();
    var task_id = $(this).attr('task_id');
    var required_quantity = $(this).attr('required_quantity');

    if (due_quantity > required_quantity) {
        due_quantity = required_quantity;
        $("#due_quantity_" + task_id).val(due_quantity);
    }
    if (due_quantity < 1) {
        due_quantity = 0;
        $("#due_quantity_" + task_id).val(due_quantity);
        $("#sub_order_status_" + task_id).removeAttr("required");
    }

    var filled_quantity = required_quantity - due_quantity;
    $("#filled_quantity_" + task_id).val(filled_quantity);
});

$("#action_id").change(function () {
    actionForm($('#action_id').val());
});

// Get Pick-up time On date Change
$('.picking_date').on('change', function () {
    var id = $(this).attr("id");
    var date = $(this).val();
    var day = dayOfWeek(date);

    pick_up_slot_by_id(id, day);
});

</script>





@endsection
