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
                <span>Merchant Checkout</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title">Merchant Checkout
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
                {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}
                <?php if (!isset($_GET['batch_id'])) {
                    $_GET['batch_id'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Cash Transfer ID</label>
                    <input type="text" value="{{$_GET['batch_id']}}" class="form-control" name="batch_id"
                           id="batch_id" placeholder="Cash Transfer ID">
                </div>

                <?php if (!isset($_GET['status'])) {
                    $_GET['status'] = null;
                } ?>
                <div class="col-md-4">
                    <label class="control-label">Status</label>
                    {!! Form::select('status', ['1' => 'Pending','2'=>'Confirm'], null, ['placeholder'=>'Select All','class' => 'form-control js-example-basic-single', 'id' => 'product_category_id']) !!}

                </div>

                <?php if (!isset($_GET['start_date'])) {
                    $_GET['start_date'] = null;
                } ?>
                <div class="col-md-4" style="margin-bottom:5px;">
                    <label class="control-label">Order from</label>
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
                    <label class="control-label">Order to</label>
                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd">
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
    {!! Form::open(array('url' => 'collected-cash-merchant', 'method' => 'post','id'=>'merchant-checkout')) !!}

    <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">

            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Received Hub Payment Lists</span>
                </div>
<!--                <div class="tools">
                    <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
                </div>-->
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                <table class="table table-bordered table-hover" id="example0">
                    <thead class="flip-content">
                    <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                    <th>Cash Transfer ID</th>
                    <th>Date</th>
                    <th>Total Qty</th>
                    <th>Collected</th>
                    <th>Delivery Amount</th>
                    <th>Status</th>
                    <th>Transaction ID</th>
                    <th>Remarks</th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                    @if(count($accumulateLists) > 0 )
                        @foreach($accumulateLists as $c)
                            <tr>
                                <td>{!!Form::checkbox('collected_cash_accumulated_id[]',$c->id, false) !!}</td>
                                <td>{{$c->batch_id}}</td>
                                <td>{{$c->date}}</td>
                                <td>{{$c->total_quantity}}</td>
                                <td>{{$c->total_collected_amount}}</td>
                                <td>{{$c->total_delivery_charge}}</td>
                                <td>
                                    @if($c->status == 1)
                                        <span class="badge badge-info" >Pending</span>
                                    @elseif($c->status == 2)
                                        <span class="badge badge-success">Confirm</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$c->transaction_id}}</td>
                                <td>{{$c->remark}}</td>
                                <td><a class="btn btn-info" target="_blank"  href="{{ url('collection-cash-details',$c->id) }}" ><i class="fa fa-eye"></i> &nbsp; View</a></td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @if(count($accumulateLists) > 0 )
                    <center>
                        <div>
                            <button class="btn btn-success" type="button"  data-toggle="modal" data-target="#merchant_checkouts" >Merchant Checkout</button>
                        </div>
                        {{$accumulateLists->appends($_REQUEST)->render()}}
                    </center>
                @endif

            </div>
        </div>
    </div>
    <div class="modal fade" id="merchant_checkouts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Received Hub Payment Process</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3>Please Click to Confirm Merchant Checkout</h3>
                 {{--   <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Transaction ID:</label>
                        <input type="text" class="form-control" name="transaction_id" id="payer-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Remark:</label>
                        <textarea class="form-control" name="remark" id="comment"></textarea>
                    </div>--}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary process-now"   onClick='submitDetailsForm()' >Confirm Process</button>
                </div>

            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script type="text/javascript">
        function submitDetailsForm() {
            $('#merchant-checkout').submit();
        }

        $(document).ready(function () {
            // Navigation Highlight
            highlight_nav('merchant_checkout', 'accounts_bills');
        });
        $("#select_all_chk").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });
        //confirm form submit merchant checkout
        // $("#merchant-checkout").submit(function (event) {
        //     var x = confirm("Are you sure you want to submit merchant checkout?");
        //     if (x) {
        //         return true;
        //     } else {
        //         event.preventDefault();
        //         return false;
        //     }
        // });
        $(".filter-btn").click(function(e){
            e.preventDefault();
            $('#filter-form').attr('action', "{{ URL::to('collected-cash-merchant') }}").submit();
        });

        $(".export-btn").click(function(e){
            // alert(1);
            e.preventDefault();
            $('#filter-form').attr('action', "{{ URL::to('collected-cash-merchant-export/xls') }}").submit();
        });
    </script>
@endsection
