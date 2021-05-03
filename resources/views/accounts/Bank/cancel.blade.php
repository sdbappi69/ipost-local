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
            <span>Bank Transfer Cancel List</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->

<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Bank Transfer Cancel List
  <small>All</small>
</h1>

@include('partials.errors')

{!! Form::open(array('url' => secure_url('') . '/bank-cancel-submit', 'method' => 'post')) !!}
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
                    </tr>
                </thead>
                <tbody>
                    @if(count($cancel_list) > 0 )
                    <?php $total_amount = 0; ?>
                    @foreach($cancel_list as $t)

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
            @if(count($cancel_list) > 0 )
            <center>
                <div >
                    <button value="1" name="submit_btn" class="btn btn-success" type="submit">Transfer to Vault</button>
                    <button value="2" name="submit_btn" class="btn btn-info" type="submit">Re-Apply</button>
                </div>
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
            highlight_nav('bank_canceled', 'accounts_bills');

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
