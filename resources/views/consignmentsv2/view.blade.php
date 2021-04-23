@extends('layouts.appinside')

@section('content')
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
          rel="stylesheet" type="text/css"/>

    <style type="text/css">
        .modal-backdrop.in {
            display: none;
        }
    </style>

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
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
        <small>view</small>
    </h1>

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
                $pickingQuantity = 0;
                $deliveryQuantity = 0;
                $returnQuantity = 0;

                foreach ($consignment->task as $task) {
                    $amount_to_collect += $task->amount;
                    $amount_collected += $task->collected;
                    switch ($task->task_type_id) {
                        case 1:
                            $pickingQuantity += $task->quantity;
                            break;
                        case 2:
                            $deliveryQuantity += $task->quantity;
                            break;
                        case 4:
                            $returnQuantity += $task->quantity;
                            break;

                    }
                }
                ?>

                <div class="col-md-6" style="margin-bottom:5px;">
                    <table class="table table-hover">
                        <tr>
                            <td>Consignment id</td>
                            <td>:</td>
                            <td>{{ $consignment->consignment_unique_id or '' }}</td>
                        </tr>
                        <tr>
                            <td>Picking Quantity</td>
                            <td>:</td>
                            <td>{{ $pickingQuantity }}</td>
                        </tr>
                        <tr>
                            <td>Delivery Quantity</td>
                            <td>:</td>
                            <td>{{ $deliveryQuantity }}</td>
                        </tr>
                        <tr>
                            <td>Return Quantity</td>
                            <td>:</td>
                            <td>{{ $returnQuantity }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6" style="margin-bottom:5px;">
                    <table class="table table-hover">
                        <tr>
                            <td>Rider</td>
                            <td>:</td>
                            <td>{{ $consignment->rider->name or '' }}</td>
                        </tr>
                        <tr>
                            <td>Contact</td>
                            <td>:</td>
                            <td>{{ $consignment->rider->msisdn or '' }}</td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>:</td>
                            <td>{{ $amount_to_collect }}</td>
                        </tr>
                        <tr>
                            <td>Collected Amount</td>
                            <td>:</td>
                            <td>{{ $amount_collected }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>

    @if(count($consignment->task) > 0)

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

                            @foreach($consignment->task AS $task)

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle collapsed" data-toggle="collapse"
                                               data-parent="#accordion1"
                                               href="#collapse_{{ $task->id }}"> {{ $task->suborder->order->delivery_name or '' }} </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_{{ $task->id }}" class="panel-collapse collapse">

                                        <div class="col-md-12" style="margin-bottom:5px; margin-top:5px;">
                                            <table width="100%">
                                                <thead>
                                                <th>Time</th>
                                                <th>Map</th>
                                                <th>Signature</th>
                                                <th>Photo</th>
                                                </thead>
                                                <tr>

                                                    <td>{{ $task->start_time ? $task->start_time . ' to ' : '' }}
                                                        {{ $task->end_time }}</td>

                                                    <td>
                                                        <a target="_blank" class="btn default"
                                                           href="{{url('maps/'.$task->start_lat.'/'.$task->start_long)}}">Start</a>
                                                        <a target="_blank" class="btn default"
                                                           href="{{url('maps/'.$task->end_lat.'/'.$task->end_long)}}">End</a>
                                                    </td>

                                                    <td>
                                                        <a href="javascript:void(0)" data-toggle="modal"
                                                           task_id="{{ $task->id }}"
                                                           data-target="#sign_{{ $task->id }}"
                                                           class="btn green sign">Click Here</a>
                                                        <div class="modal fade" id="sign_{{ $task->id }}"
                                                             tabindex="-1" role="basic" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content" style="padding: 15px;">
                                                                    <img style="width: 100%;"
                                                                         src="{{ $task->signature or "" }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <a href="javascript:void(0)" data-toggle="modal"
                                                           task_id="{{ $task->id }}"
                                                           data-target="#proof_{{ $task->id }}"
                                                           class="btn green proof">Click Here</a>
                                                        <div class="modal fade" id="proof_{{ $task->id }}"
                                                             tabindex="-1" role="basic" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content" style="padding: 15px;">
                                                                    <img style="width: 100%;"
                                                                         src="{{ $task->image }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                </tr>
                                            </table>
                                        </div>

                                        <li class="list-group-item" style="overflow: hidden;">

                                            <div class="panel-body col-md-4">
                                                <div class="portlet light tasks-widget bordered">

                                                    @if($task->status == '0')
                                                        <?php $status = "Processing"; ?>
                                                    @elseIf($task->status == '1')
                                                        <?php $status = "Start"; ?>
                                                    @elseIf($task->status == '3')
                                                        <?php $status = "Finished"; ?>
                                                    @else
                                                        <?php $status = "Pending"; ?>
                                                    @endIf

                                                    <div class="portlet-title">
                                                        <div class="caption" style="text-align: center;">

                                                            <span class="caption-subject font-dark bold uppercase">{{ $task->suborder->unique_suborder_id or '' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="portlet-body util-btn-margin-bottom-5"
                                                         style="overflow: hidden;">

                                                        <?php switch ($task->task_type_id) {
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
                                                            default:
                                                                $type = '';
                                                                break;
                                                        }?>
                                                        <b>Type</b>
                                                        <p>{{ $type }}</p>

                                                        <b>Status</b>
                                                        <p>{{ $status }}</p>

                                                        <b>Merchant Order ID</b>
                                                        <p>{{ $task->suborder->order->merchant_order_id or '' }}</p>

                                                        <b>Product</b>
                                                        <p>{{ $task->suborder->product->product_title or '' }}</p>

                                                        <b>Category</b>
                                                        <p>{{ $task->suborder->product->product_category->name or '' }}</p>

                                                        <b>Amount</b>
                                                        <p>{{ $task->amount }}</p>

                                                    </div>

                                                </div>
                                            </div>

                                            <div class="panel-body col-md-4">
                                                <div class="portlet light tasks-widget bordered">

                                                    <div class="portlet-title">
                                                        <div class="caption">

                                                            <span class="caption-subject font-dark bold uppercase">Task</span>
                                                        </div>
                                                    </div>

                                                    <div class="portlet-body util-btn-margin-bottom-5"
                                                         style="overflow: hidden;">

                                                        <label>Requested</label>
                                                        {!! Form::text('required_quantity',$task->quantity,['class' => 'form-control','id' => 'required_quantity_'.$task->id,'placeholder' => 'Requested Quantity','required' => '','readonly' => 'readonly']) !!}

                                                        <label>Success</label>

                                                        {!! Form::text('filled_quantity',$task->collected_quantity,['class' => 'form-control','id' => 'filled_quantity_'.$task->id,'placeholder' => 'Success Quantity','required' => '','readonly' => 'readonly']) !!}

                                                        <label>Paid Amount</label>

                                                        {!! Form::text('paid_amount',$task->collected,['class' => 'form-control','id' => 'paid_amount_'.$task->id,'placeholder' => 'Paid Amount','required' => '','readonly' => 'readonly']) !!}
                                                        <br>
                                                        <b>Reason</b>
                                                        <p>{{ $task->reason->reason or '' }}</p>

                                                        <b>Remark</b>
                                                        <p>{{ $task->remarks }}</p>

                                                    </div>

                                                </div>
                                            </div>

                                            <div class="panel-body col-md-4">
                                                <div class="portlet light tasks-widget bordered">

                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <span class="caption-subject font-dark bold uppercase">Reconciliation</span>
                                                        </div>
                                                    </div>

                                                    <div class="portlet-body util-btn-margin-bottom-5"
                                                         style="overflow: hidden;">

                                                        <?php
                                                        $due_quantity = $task->quantity - $task->collected_quantity;
                                                        ?>

                                                        {!! Form::text('due_quantity',$due_quantity,['class' => 'form-control','id' => 'due_quantity_'.$task->id,'placeholder' => 'Reconciliation Quantity','required' => '','readonly' => 'readonly']) !!}

                                                    </div>

                                                </div>
                                            </div>

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

    <script type="text/javascript">
        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('consignmentsv2', 'consignments');

            $('#example0').DataTable({
                "order": [],
            });
        });

        $(".sign").click(function () {
            var id = $(this).attr('task_id');
            $("#sign_" + id).animate({scrollTop: $("#sign_" + id).offset().top});
        });

        $(".proof").click(function () {
            var id = $(this).attr('task_id');
            $("#proof_" + id).animate({scrollTop: $("#proof_" + id).offset().top});
        });
    </script>





@endsection
