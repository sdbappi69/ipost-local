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
                <span>Consignments</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Consignments
        <small>All Consignment</small>
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

                {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}

                <?php if (!isset($_GET['c_unique_id'])) {
                    $_GET['c_unique_id'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <div class="row"> -->
                    <input type="text" value="{{$_GET['c_unique_id']}}" class="form-control" name="c_unique_id"
                           id="c_unique_id" placeholder="Consignment Unique ID">
                    <!-- </div> -->
                </div>

                <?php if (!isset($_GET['type'])) {
                    $_GET['type'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <div class="row"> -->
                {!! Form::select('type',['' => 'Select Type','picking' => 'Picking','delivery' => 'Delivery'],$_GET['type'], ['class' => 'form-control js-example-basic-single', 'id' => 'type']) !!}
                <!-- </div> -->
                </div>

                <?php if (!isset($_GET['status'])) {
                    $_GET['status'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <div class="row"> -->
                {!! Form::select('status',['' => 'Select Status','0' => 'Cancel','1' => 'Ready','2' => 'On The Way','3' => 'Submited','4' => 'Completed'],$_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status']) !!}
                <!-- </div> -->
                </div>

                <?php if (!isset($_GET['rider_id'])) {
                    $_GET['rider_id'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <!-- <div class="row"> -->
                {!! Form::select('rider_id', array(''=>'Select Rider')+$rider,$_GET['rider_id'], ['class' => 'form-control js-example-basic-single','id' => 'rider_id']) !!}
                <!-- </div> -->
                </div>

                <?php if (!isset($_GET['start_date'])) {
                    $_GET['start_date'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd">
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                        {!! Form::text('start_date',$_GET['start_date'], ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <?php if (!isset($_GET['end_date'])) {
                    $_GET['end_date'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd">
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                        {!! Form::text('end_date',$_GET['end_date'], ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="button" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i>
                        Filter
                    </button>
                </div>
                <div class="clearfix"></div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    <div class="col-md-12">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">

            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">Consignments</span>
                </div>
                <div class="tools">
                    <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
                </div>
            </div>

            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-bordered table-hover" id="example0">
                    <thead class="flip-content">

                    <th>Consignment Unique ID</th>
                    <th>Rider</th>
                    <th>Amount To Collect</th>
                    <th>Amount Collected</th>
                    <th>Picking Quantity</th>
                    <th>Delivery Quantity</th>
                    <th>Return Quantity</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Action</th>

                    </thead>
                    <tbody>
                    @if(count($consignments) > 0)
                        @foreach($consignments as $consignment)

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
                                    case 5:
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

                            <tr>
                                <td>{{ $consignment->consignment_unique_id or '' }}</td>
                                <td>{{ $consignment->rider->name or '' }}</td>
                                <td>{{ $amount_to_collect }}</td>
                                <td>{{ $amount_collected }}</td>
                                <td>{{ $pickingQuantity }}</td>
                                <td>{{ $deliveryQuantity }}</td>
                                <td>{{ $returnQuantity }}</td>
                                <td>{{ $consignment->created_at }}</td>
                                <td>
                                    @if($consignment->status == 0)
                                        Cancel
                                    @elseif ($consignment->status == 1)
                                        Ready
                                    @elseif ($consignment->status == 2)
                                        On The Way
                                    @elseif ($consignment->status == 3)
                                        Submitted
                                    @elseif ($consignment->status == 4)
                                        Complete
                                    @endif

                                </td>

                                <td>
                                    <a class="btn btn-primary btn-xs" target="_blank"
                                       href="{{url('consignments/'.$consignment->id)}}">View</a>

                                    <?php $count = count($consignment->task); ?>

                                    @if($count <= 15)
                                        <a class="btn btn-info btn-xs" target="_blank"
                                           href="{{url('common-awb-multi/'.$consignment->id)}}">All AWB</a>
                                        <a class="btn btn-success btn-xs" target="_blank"
                                           href="{{url('common-invoice-multi/'.$consignment->id)}}">All Invoice</a>
                                    @else

                                        <div class="btn-group">
                                            <button id="btnGroupVerticalDrop2" type="button"
                                                    class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown">
                                                All AWB
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu"
                                                aria-labelledby="btnGroupVerticalDrop2">

                                                <?php $pages = ceil($count / 15); ?>

                                                @for($i = 1; $i <= $pages; $i++)
                                                    <li><a target="_blank"
                                                           href="{{url('common-awb-multi/'.$consignment->id.'?page='.$i)}}">
                                                            Page {{ $i }} </a></li>
                                                @endfor

                                            </ul>
                                        </div>
                                        <div class="btn-group">
                                            <button id="btnGroupVerticalDrop1" type="button"
                                                    class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown">
                                                All Invoice
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu"
                                                aria-labelledby="btnGroupVerticalDrop1">

                                                <?php $pages = ceil($count / 15); ?>

                                                @for($i = 1; $i <= $pages; $i++)
                                                    <li><a target="_blank"
                                                           href="{{url('common-invoice-multi/'.$consignment->id.'?page='.$i)}}">
                                                            Page {{ $i }} </a></li>
                                                @endfor

                                            </ul>
                                        </div>

                                    @endIf


                                    <a class="btn btn-warning btn-xs" target="_blank"
                                       href="{{url('v2consignment/'.$consignment->id)}}">Follow Up</a>
                                    @if ($consignment->status < 2)
                                        {{-- <a class="btn btn-default btn-xs"  href="{{url('consignments-edit/'.$c->id)}}">Edit</a> --}}
                                        <a class="btn btn-success btn-xs" href="{{url('consignments-start/'.$consignment->id)}}">Start</a>
                                        <a class="btn btn-danger btn-xs"
                                           href="{{url('consignments-cancel/'.$consignment->id)}}">Cancel</a>
                                    @endif
                                    @if ($consignment->status == 3)
                                        <a class="btn btn-danger btn-xs"
                                           href="{{url('v2consignment/reconciliation/'.$consignment->id)}}">Reconciliation</a>
                                    @endif
                                </td>

                            </tr>


                        @endforeach
                    @endif
                    </tbody>
                </table>        <!-- /.modal-dialog -->
            </div>
            <div class="pagination pull-right">
                {!! $consignments->appends($_REQUEST)->render() !!}
            </div>

        </div>
    </div>
    </div>

    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('consignmentsv2', 'consignments');

            // $('#example0').DataTable({
            //     "order": [],
            // });
        });

        $(".filter-btn").click(function (e) {
            e.preventDefault();
            $('#filter-form').attr('action', "{{ URL::to('v2consignment') }}").submit();
        });

        $(".export-btn").click(function (e) {
            e.preventDefault();
            $('#filter-form').attr('action', "{{ URL::to('v2consignment/export/xls') }}").submit();
        });

    </script>

@endsection
