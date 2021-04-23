@extends('layouts.appinside')

@section('content')

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ URL::to('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ URL::to('hub-order') }}">Orders</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Receive Pickup</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Product
    <small> receive</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

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

            <?php
            if (!isset($_GET['sub_order_id'])) {
                $_GET['sub_order_id'] = null;
            }
            if (!isset($_GET['rider_id'])) {
                $_GET['rider_id'] = null;
            }
            ?>
            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Sub-Order ID</label>
                <input type="text" value="{{$_GET['sub_order_id']}}" class="form-control focus_it" name="sub_order_id" id="sub_order_id" placeholder="Sub-Order ID">
            </div>

            <div class="col-md-4" style="margin-bottom:5px;">
                <label class="control-label">Pickup Man</label>
                {!! Form::select('rider_id', $riders, $_GET['rider_id'], ['class' => 'form-control js-example-basic-single','id' => 'rider_id','placeholder'=>'Select Rider']) !!}
            </div>

            <div class="col-md-12">
                <a href="{{ url('receive-prodcut') }}" class="btn btn-default"><i class="fa fa-times"></i> Clear</a>
                <button type="button" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
            </div>
            <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<div class="col-md-12">

    {!! Form::open(array('url' => '/received-varified-suborder/', 'id'=> 'receiveVerifyForm', 'method' => 'post')) !!}

    <div class="col-md-12" style="margin-bottom:5px; margin-top:10px;">
        {!! Form::text('unique_suborder_id', null, ['class' => 'form-control focus_it', 'id' => 'unique_suborder_id', 'placeholder' => 'Scan QR code or manually enter Sub-Order ID to accept']) !!}
    </div>

    <div class="form-group col-md-12">
        <button type="submit" id="btn-confirm" class="btn btn-primary col-md-12"><i class="fa fa-check"></i> Confirm Accept</button>
    </div>

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
        <div class="portlet-body util-btn-margin-bottom-5">

            <!--            <div class="well">
                            <span class="counter" style="font-weight: bold;">0</span> order selected
                        </div>-->

            <table class="table table-bordered table-hover">
                <thead class="flip-content">
                    <!-- <th>Order ID</th> -->
                <!--<th>{!!Form::checkbox('mother_checkbox', 'value', false,array('id'=>'select_all_chk')) !!}</th>-->
                <th>Unique SubOrder Id</th>
                <th>Rider Name</th>
                <th>Product Title</th>
                <th>Quantity</th>
                </thead>
                <tbody>
                    @if(count($receiveTasks) > 0)
                    @foreach($receiveTasks as $task)
                    <tr>
                        <!--<td>{!!Form::checkbox('unique_suborder_id[]',$task->unique_suborder_id, false) !!}</td>-->
                        <td>{{ $task->unique_suborder_id }}</td>
                        <td>{{ $task->rider_name }}</td>
                        <td>{{ $task->product_title }}</td>
                        <td>{{ $task->quantity }}</td>
                    </tr>

                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="pagination pull-right">
                {!! $receiveTasks->appends($_REQUEST)->render() !!}
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
    <div class="modal-dialog modal-sm" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Receive and Verify</h4>
            </div>
            <div class="modal-body">
                <!--<div id="qrcode" style="text-align: center;"></div>-->
                <h3>Please Confirm Your Acceptance or Rejection</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="modal-btn-no">Reject</button>
                <button type="button" class="btn btn-primary" id="modal-btn-si">Accept</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ URL::asset('custom/js/jquery.qrcode.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function () {
// Navigation Highlight
    highlight_nav('receive-prodcut', 'pickup');
    var modalConfirm = function (callback) {

        $("#btn-confirm").on("click", function (e) {
            e.preventDefault();
            setRejectButton($("#unique_suborder_id").val());
            $("#mi-modal").modal('show');
        });

        $("#modal-btn-si").on("click", function () {
            callback(true);
            $("#mi-modal").modal('hide');
        });

        $("#modal-btn-no").on("click", function () {
            callback(false);
            $("#mi-modal").modal('hide');
        });
    };

    modalConfirm(function (confirm) {
        if (confirm) {
            // yes confirm
            $('#receiveVerifyForm').attr('action', "{{ URL::to('received-varified-suborder') }}").submit();
        } else {
            // no confirm
            $('#receiveVerifyForm').attr('action', "{{ URL::to('receive-reject-suborder') }}").submit();
        }
    });

    function setRejectButton(unique_suborder_id) {
        if (unique_suborder_id.slice(0, 1) === 'D') {
            $("#modal-btn-no").hide();
        } else {
            $("#modal-btn-no").show();
        }
    }
// Datatable
    $('#example0').DataTable({
        "order": [],
    });

// Checkbox
    $("#select_all_chk").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
        countCheckbox();
    });
});

$("#select_all_chk").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
    countCheckbox();
});

$(":checkbox").on("click", function () {
    countCheckbox();
});
$(".filter-btn").click(function (e) {
    e.preventDefault();
    $('#filter-form').attr('action', "{{ URL::to('receive-prodcut') }}").submit();
});

function countCheckbox() {
    var check_count = $('input[type="checkbox"]:checked').length;
    if ($('input[name="mother_checkbox"]').is(':checked')) {
        var count = check_count - 1;
    } else {
        var count = check_count;
    }

    $('.counter').html(count);
}

</script>

@endsection