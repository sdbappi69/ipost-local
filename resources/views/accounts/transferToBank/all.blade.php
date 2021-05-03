@extends('layouts.appinside')

@section('content')

<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="{{ secure_url('home') }}">Home</a>
      <i class="fa fa-circle"></i>
  </li>
  <li>
      <span>Transfer To Bank</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Transfer To Bank
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
            {!! Form::open(array('url' => secure_url('') . '/transfer-to-bank-submit', 'method' => 'post')) !!}
            <div class="col-md-4">
                <label class="control-label">Bank Account</label>
                {!! Form::select('hub_bank_account_id',['' => 'Select Bank Account']+$hub_bank_accounts->toArray(),old(' hub_bank_account_id'), ['class' => 'form-control js-example-basic-single', 'id' => ' hub_bank_account_id']) !!}
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
                <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                {{-- <th>Vault Title</th> --}}
                <th>Vault Name</th>
                <th>Amount</th>
                {{-- <th>COD Amount</th>
                    <th>COD Charge</th>
                    <th>Biil Amount Charge</th>
                    <th>Total Biil Amount</th>
                    <th>Paid Amount</th> --}}

                </thead>
                <tbody>
                    @if(count($transferToBank) > 0 )
                    <?php $total_amount = 0; ?>
                    @foreach($transferToBank as $t)

                    <tr>
                        <td>{!!Form::checkbox('vault_id[]',$t->id, false) !!}</td>
                        {{-- <td>{{$t->title or 'N/A'}}</td> --}}
                        <td>{{$t->vault->title or 'N/A'}}</td>
                        <td>{{$t->amount}}</td>
                        <input type="hidden" name="amount_{{$t->id}}" value="{{$t->amount}}">
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
                        {{-- <td></td> --}}
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b>{{$total_amount}}</b></td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @if(count($transferToBank) > 0 )
            <center><div >
                <button class="btn btn-success" type="submit">Submit</button>
            </div></center>
            @endif

        </div>
    </div>
</div>
{!! Form::close() !!}
<script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('transfer_to_bank', 'accounts_bills');

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
