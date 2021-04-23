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
            <span>Bank History</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->

<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Bank History
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
            {!! Form::open(array('url' => 'bank-approval-submit', 'method' => 'post')) !!}

            <div class="col-md-4">
                <label class="control-label">Depositor</label>
                {!! Form::select('depositor_id',['' => 'Select Depositor']+$depositor->toArray(),old(' hub_bank_account_id'), ['class' => 'form-control js-example-basic-single', 'id' => ' hub_bank_account_id']) !!}
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<!-- BEGIN BUTTONS PORTLET-->
<div class="portlet light tasks-widget bordered">
    <div class="portlet-body util-btn-margin-bottom-5">
        <table class="table table-bordered table-hover" id="example0">
            <thead class="flip-content">
                <tr>
                    <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                    <th>Bank Name</th>
                    <th>Account NO</th>
                    <th>Amount</th>

                    {{-- <th>COD Amount</th>
                        <th>COD Charge</th>
                        <th>Biil Amount Charge</th>
                        <th>Total Biil Amount</th>
                        <th>Paid Amount</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @if(count($bank_list) > 0 )
                    <?php $total_amount = 0; ?>
                    @foreach($bank_list as $t)

                    <tr>
                        <td>{!!Form::checkbox('bank_id[]',$t->id, false) !!}</td>
                        <td>{{$t->hub_ban_account->account->name or 'N/A'}}</td>
                        <td>{{$t->hub_ban_account->account->account_no or 'N/A'}}</td>
                        <td>{{$t->amount}}</td>

                        <input type="hidden" name="hub_id_{{$t->id}}" value="{{$t->hub_id}}">
                        {{--  <td>{{$t->cod_amount}}</td>
                        <td>{{$t->cod_charge}}</td>
                        <td>{{$t->bill_amount}}</td>
                        <td>{{$t->total_bill_amount}}</td>
                        <td>{{$t->paid_amount}}</td> --}}

                    </tr>
                    <?php $total_amount += $t->amount;?>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b>{{$total_amount}}</b></td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @if(count($bank_list) > 0 )
            <center><div >
                <button value="1" name="submit_btn" class="btn btn-success" type="submit">Approve</button>
                {{-- <button value="0" name="submit_btn" class="btn btn-danger" type="submit">Decline</button> --}}
            </div></center>
            @endif

        </div>
    </div>
</div>
{!! Form::close() !!}
<script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('bank_list', 'accounts_bills');

            // $('#example0').DataTable({
            //     "order": [],
            // });
        });
    </script>


    <script type="text/javascript">
      $("#select_all_chk").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
</script>



@endsection
