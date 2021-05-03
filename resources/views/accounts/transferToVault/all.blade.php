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
      <span>Transfer To Vault</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Transfer To Vault
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
            {!! Form::open(array('url' => secure_url('') . '/transfer-to-vault-submit', 'method' => 'post')) !!}
            <div class="col-md-4">
                <label class="control-label">Vault</label>
                {!! Form::select('hub_volt_account_id',['' => 'Select vault']+$hub_vault_accounts->toArray(),old('hub_volt_account_id'), ['class' => 'form-control js-example-basic-single', 'id' => 'hub_volt_account_id']) !!}
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
                <th>AWB</th>
                <th>Order ID</th>
                <th>Collected Amount</th>
                {{-- <th>COD Amount</th>
                    <th>COD Charge</th>
                    <th>Biil Amount Charge</th>
                    <th>Total Biil Amount</th>
                    <th>Paid Amount</th> --}}

                </thead>
                <tbody>
                    @if(count($transferToVault) > 0 )
                    <?php $total_collected_amount = 0;?>
                    @foreach($transferToVault as $t)

                    <tr>
                        <td>{!!Form::checkbox('cash_collection_id[]',$t->id, false) !!}</td>
                        <td>{{$t->sub_order->unique_suborder_id or null}}</td>
                        <td>{{$t->order->unique_order_id or null}}</td>
                        <td>{{$t->collected_amount}}</td>
                        <input type="hidden" name="collected_amount_{{$t->id}}" value="{{$t->collected_amount}}">
                        <input type="hidden" name="cash_collection_hub_id_{{$t->id}}" value="{{$t->hub_id}}">
                        {{--  <td>{{$t->cod_amount}}</td>
                        <td>{{$t->cod_charge}}</td>
                        <td>{{$t->bill_amount}}</td>
                        <td>{{$t->total_bill_amount}}</td>
                        <td>{{$t->paid_amount}}</td> --}}

                    </tr>
                    <?php $total_collected_amount += $t->collected_amount;?>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b>{{$total_collected_amount}}</b></td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @if(count($transferToVault) > 0 )
            <center>
                <div >
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
                {{$transferToVault->appends($_REQUEST)->render()}}
            </center>
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
        highlight_nav('transfer_to_vault', 'accounts_bills');

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
