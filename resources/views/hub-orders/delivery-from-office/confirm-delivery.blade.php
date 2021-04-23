@extends('layouts.appinside')

@section('content')
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL::to('office-delivery-list') }}">Delivery From Office</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Delivery Confirmation</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->

    @if($deliveryProduct)

        <div class="col-md-12">

            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">

                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-dark bold uppercase">Detail</span>
                    </div>
                </div>
                @include('errors.validation_error')
                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                    <div class="panel-group accordion" id="accordion1">

                        <ul class="list-group">


                            <div class="panel panel-default task_panel task_panel_1">

                                <div id="collapse_1" class="panel-collapse in">

                                    <li id="scroll_1" class="list-group-item"
                                        style="overflow: hidden;">

                                        <div class="panel-body col-md-4">
                                            <div class="portlet light tasks-widget bordered">

                                                <?php $status = "Success"; ?>

                                                <div class="portlet-title">
                                                    <div class="caption" style="text-align: center;">

                                                        <span class="caption-subject font-dark bold uppercase">{{ $deliveryProduct->unique_suborder_id }}</span>
                                                    </div>
                                                </div>

                                                <div class="portlet-body util-btn-margin-bottom-5"
                                                     style="overflow: hidden;">

                                                    <b>Status</b>
                                                    <p>{{ $deliveryProduct->status }}</p>

                                                    <b>Merchant Name</b>
                                                    <p>{{ $deliveryProduct->store_name }}</p>

                                                    <b>Merchant Order ID</b>
                                                    <p>{{ $deliveryProduct->merchant_order_id }}</p>

                                                    <b>Product</b>
                                                    <p>{{ $deliveryProduct->product_title }}</p>

                                                    <b>Category</b>
                                                    <p>{{ $deliveryProduct->product_category }}</p>

                                                    <b>Amount</b>
                                                    <p>{{ $deliveryProduct->total_payable_amount }}</p>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="panel-body col-md-4">
                                            <div class="portlet light tasks-widget bordered">

                                                <div class="portlet-title">
                                                    <div class="caption">

                                                        <span class="caption-subject font-dark bold uppercase">Product</span>
                                                    </div>
                                                </div>

                                                <div class="portlet-body util-btn-margin-bottom-5"
                                                     style="overflow: hidden;">

                                                    <label>Requested</label>
                                                    {!! Form::text('required_quantity',$deliveryProduct->quantity,['class' => 'form-control','id' => 'required_quantity_task_id','placeholder' => 'Requested Quantity','required' => '','readonly' => 'readonly']) !!}

                                                    <label>Success</label>

                                                    <?php
                                                    $filled_quantity = $deliveryProduct->quantity;
                                                    ?>

                                                    {!! Form::open(['url' => url('confirm-office-delivery')."/$deliveryProduct->suborder_id", 'method' => 'post','files' => false,'class' => '','id' => '']) !!}

                                                    {!! Form::text('filled_quantity',$filled_quantity,['class' => 'form-control','id' => 'filled_quantity_task_id','placeholder' => 'Success Quantity','required' => '','readonly' => 'readonly']) !!}

                                                    <label>Paid Amount</label>

                                                    {!! Form::text('paid_amount',$deliveryProduct->total_payable_amount,['class' => 'form-control','id' => 'paid_amount_task_id','placeholder' => 'Paid Amount','required' => 'required']) !!}

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
                                                    $due_quantity = $deliveryProduct->quantity - $filled_quantity;
                                                    ?>

                                                    <label>Failed Quantity</label>
                                                    {!! Form::number('due_quantity',$due_quantity,['class' => 'form-control due_quantity','id' => 'due_quantity_task_id','placeholder' => 'Reconciliation Quantity', 'required' => 'required', 'required_quantity' => $filled_quantity, 'task_id' => "task_id"]) !!}

                                                    <div id="failed_reason">
                                                        <label>Select Action</label>
                                                        {!! Form::select('sub_order_status', array(''=>'Do Nothing')+$sub_order_status_list, null, ['class' => 'form-control js-example-basic-single', 'id' => 'sub_order_status']) !!}

                                                        <label>Reason</label>

                                                        {!! Form::select('reason_id', array(''=>'Nothing')+$reasons, null, ['class' => 'form-control js-example-basic-single', 'id' => 'reason_id']) !!}

                                                        <label>Remark</label>

                                                        {!! Form::textarea('remarks',null,['class' => 'form-control','id' => 'remarks','placeholder' => 'Remarks','rows' => '2']) !!}
                                                    </div>
                                                    <br><br>

                                                    <button type="submit"
                                                            class="btn btn-primary filter-btn pull-right"
                                                            style="width: 100%;"><i
                                                                class="fa fa-check"></i>Reconcile
                                                    </button>

                                                    {!! Form::close() !!}

                                                </div>

                                            </div>
                                        </div>

                                    </li>

                                </div>
                            </div>

                        </ul>

                    </div>

                </div>
            </div>
        </div>

    @endIf

    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/pages/scripts/components-date-time-pickers.min.js') }}"
            type="text/javascript"></script>

    <script src="{{ URL::asset('custom/js/date-time.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('delivery-from-office', 'delivery');
            $("#failed_reason").hide();
        });

        $(".due_quantity").change(function () {
            var due_quantity = $(this).val();
            var task_id = $(this).attr('task_id');
            var required_quantity = $(this).attr('required_quantity');

            if (due_quantity > required_quantity) {
                due_quantity = required_quantity;
                $("#due_quantity_" + task_id).val(due_quantity);
            }
            if (due_quantity < 0) {
                due_quantity = 0;
                $("#due_quantity_" + task_id).val(due_quantity);
            }

            if (due_quantity > 0) {
                $("#failed_reason").show();
                $("#sub_order_status").prop('required', true);
                $("#reason_id").prop('required', true);
                $("#remarks").prop('required', true);
            } else {
                $("#failed_reason").hide();
                $("#sub_order_status").prop('required', false);
                $("#reason_id").prop('required', false);
                $("#remarks").prop('required', false);
            }

            var filled_quantity = required_quantity - due_quantity;
            $("#filled_quantity_" + task_id).val(filled_quantity);
        });

    </script>


@endsection
