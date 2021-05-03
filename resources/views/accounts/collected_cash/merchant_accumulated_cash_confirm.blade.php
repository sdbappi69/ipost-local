@extends('layouts.appinside')

@section('content')

    <link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Confirm Checkout</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title">Confirm Checkout
        <small>All</small>
    </h1>
    @include('partials.errors')
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
                {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}
                <?php if (!isset($_GET['merchant_batch_id'])) {
                    $_GET['merchant_batch_id'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Merchant Checkout ID</label>
                    <input type="text" value="{{$_GET['merchant_batch_id']}}" class="form-control" name="merchant_batch_id"
                           id="merchant_batch_id" placeholder="Merchant Checkout ID">
                </div>
                <?php if (!isset($_GET['status'])) {
                    $_GET['status'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Status</label>
                    {!! Form::select('status', ['1' => 'Pending','2'=>'Confirm'], null, ['placeholder'=>'Select All','class' => 'form-control js-example-basic-single', 'id' => 'product_category_id']) !!}

                </div>
                <?php if(!isset($_GET['start_date'])){$_GET['start_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">From</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                        {!! Form::text('start_date',$_GET['start_date'], ['class' => 'form-control picking_date','placeholder' => 'Order from' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>

                <?php if(!isset($_GET['end_date'])){$_GET['end_date'] = null;} ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">To</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                        {!! Form::text('end_date',$_GET['end_date'], ['class' => 'form-control picking_date','placeholder' => 'Order to' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="control-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary filter-btn form-control">Filter</button>
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
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Merchant Checkout List</span>
                </div>
<!--                <div class="tools">
                    <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
                </div>-->
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-bordered table-hover" id="example0">
                    <thead class="flip-content">
                    <th>Merchant Checkout ID</th>
                    <th>Date</th>
                    <th>Total Qty</th>
                    <th>Collected</th>
                    <!-- <th>Delivery Amount</th> -->
                    <th>Status</th>
                    <th>Transaction ID</th>
                    <th>Remarks</th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                    @if(count($accumulateLists) > 0 )
                        @foreach($accumulateLists as $c)
                            <tr>
                                <td>{{$c->merchant_batch_id}}</td>
                                <td>{{$c->date}}</td>
                                <td>{{$c->total_quantity}}</td>
                                <td>{{ number_format($c->total_collected_amount) }}</td>
                                <!-- <td>{{$c->total_delivery_charge}}</td> -->
                                <td>
                                    @if($c->status == 1)
                                        <span class="badge badge-info" >Pending</span>
                                    @elseif($c->status == 2)
                                        <span class="badge badge-success">Confirm</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$c->merchant_transaction_id}}</td>
                                <td>{{$c->remark}}</td>
                                <td>
                                    @if($c->status == 1)
                                   <button type="button" class="btn btn-primary confirm-accumulated" data-id="{{$c->id}}" data-toggle="modal" data-target="#confirm"><i class="fa fa-check-circle"></i>&nbsp;Confirm</button>
                                    @endif
                                   <a target="_blank"  class="btn btn-info" href="{{ secure_url('collection-cash-details',$c->id) }}" ><i class="fa fa-eye"></i> &nbsp; View</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @if(count($accumulateLists) > 0 )
                    <center>
                        {{$accumulateLists->appends($_REQUEST)->render()}}
                    </center>
                @endif
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Merchant Checkout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(array('url' => secure_url('') . '/collected-cash-merchant-confirm', 'method' => 'post')) !!}
                    <input type="hidden" name="merchant_accumulated_id" id="merchant_accumulated_id" value="">
                    <div class="form-group">
                        <label class="control-label">Transaction ID </label>
                        {!! Form::text('transaction_id', null, ['class' => 'form-control','palceholder'=>'Transaction ID']) !!}

                    </div>
                    <div class="form-group">
                        <label class="control-label">Remark</label>
                        {!! Form::textarea('remark', null, ['class' => 'form-control','palceholder'=>'Remark', 'rows' => 2, 'cols' => 10]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('confirm_checkout', 'accounts_bills');
        });
        $(document).on("click", ".confirm-accumulated", function () {
            var  accumulatedId = $(this).data('id');
            $(".modal-body #merchant_accumulated_id").val( accumulatedId );
        });
        $(".filter-btn").click(function(e){
            e.preventDefault();
            $('#filter-form').attr('action', "{{ secure_url('collected-cash-merchant-confirm') }}").submit();
        });

        $(".export-btn").click(function(e){
            // alert(1);
            e.preventDefault();
            $('#filter-form').attr('action', "{{ secure_url('collected-cash-merchant-confirm-export/xls') }}").submit();
        });
    </script>
@endsection
