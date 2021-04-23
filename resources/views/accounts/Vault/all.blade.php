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
      <span>Vault History</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Vault History
  <small>All</small>
</h1>
@include('partials.errors')
<div class="col-md-12">
   {!! Form::open(array('url' => 'vault-approval-submit', 'method' => 'post')) !!}

   <!-- BEGIN BUTTONS PORTLET-->
   <div class="portlet light tasks-widget bordered">
    <div class="portlet-body util-btn-margin-bottom-5">
        <div class="table-scrollable">
            <table class="table table-striped table-bordered table-hover">
                <thead class="flip-content">
                    <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                    {{-- <th>Vault Title</th> --}}
                    <th>Vault Name</th>
                    <th>Collected Amount</th>

                    {{-- <th>COD Amount</th>
                        <th>COD Charge</th>
                        <th>Biil Amount Charge</th>
                        <th>Total Biil Amount</th>
                        <th>Paid Amount</th> --}}

                    </thead>
                    <tbody>
                        @if(count($vault_list) > 0 )
                        <?php $total_amount = 0; ?>
                        @foreach($vault_list as $t)

                        <tr>
                            <td>{!!Form::checkbox('vault_id[]',$t->id, false) !!}</td>
                            {{-- <td>{{$t->title or 'N/A'}}</td> --}}
                            <td>{{$t->vault->title or 'N/A'}}</td>
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
                            {{-- <td></td> --}}
                            <td><b>Total</b></td>
                            <td><b>{{$total_amount}}</b></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @if(count($vault_list) > 0 )
                <center>
                    <div >
                        <button value="1" name="submit_btn" class="btn btn-success" type="submit">Approve</button>
                        <button value="0" name="submit_btn" class="btn btn-danger" type="submit">Decline</button>
                    </div>
                </center>
                @endif

            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

<script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('vault_list', 'accounts_bills');

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