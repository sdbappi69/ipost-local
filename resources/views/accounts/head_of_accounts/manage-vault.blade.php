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
      <span>Manage Vault</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Manage Vault
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
            {!! Form::model($_GET,array('method' => 'get', 'id' => 'filter-form')) !!}
            <?php if(!isset($_GET['status'])){$_GET['status'] = null;} ?>
            <div class="col-md-3">
                <label class="control-label">Status</label>
                {!! Form::select('status',['' => 'Select Status']+$status,$_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status']) !!}
            </div>

            <?php if(!isset($_GET['hub_id'])){$_GET['hub_id'] = null;} ?>
            <div class="col-md-3">
                <label class="control-label">Hubs</label>
                {!! Form::select('hub_id',['' => 'Select Hub']+$hub->toArray(),$_GET['hub_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'hub_id']) !!}
            </div>
            
            <?php if(!isset($_GET['search_date'])){$_GET['search_date'] = null;} ?>
            <div class="col-md-3">
                <label class="control-label">Hubs</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('search_date',$_GET['search_date'], ['class' => 'form-control picking_date','placeholder' => 'Created Date' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                </div>
            </div>

            <div class="col-md-2">
                <label class="control-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Filter</button>
            </div>
            <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<div class="col-md-12">
   <!-- BEGIN BUTTONS PORTLET-->
   <div class="portlet light tasks-widget bordered">
    <div class="portlet-body util-btn-margin-bottom-5">
        <div class="table-scrollable">
            <table class="table table-striped table-bordered table-hover">
                <thead class="flip-content">

                    {{-- <th>Vault Title</th> --}}
                    <th>Vault Name</th>
                    <th>Hub</th>
                    <th>Collected Amount</th>
                    <th>Approved By</th>
                    <th>Created At</th>
                    <th>Status</th>

                    {{-- <th>COD Amount</th>
                        <th>COD Charge</th>
                        <th>Biil Amount Charge</th>
                        <th>Total Biil Amount</th>
                        <th>Paid Amount</th> --}}

                    </thead>
                    <tbody>
                        @if(count($manage_vault) > 0 )
                        <?php $total_collected_ammount = 0; ?>
                        @foreach($manage_vault as $t)

                        <tr>

                            {{-- <td>{{$t->title or 'N/A'}}</td> --}}
                            <td>{{$t->vault->title or 'N/A'}}</td>
                            <td>{{$t->hub->title or 'N/A'}}</td>
                            <td>{{$t->amount}}</td>
                            <td>{{$t->manager_id->name or 'N/A'}}</td>
                            <td>{{$t->created_at}}</td>
                            <td>
                                @if($t->status == '0')
                                Declined
                                @elseif($t->status == '1')
                                Not Approved yet
                                @elseif($t->status == '2')
                                Approved to transfer
                                @elseif($t->status == '3')
                                Transfered to Bank
                                @endif
                            </td>


                            {{--  <td>{{$t->cod_amount}}</td>
                            <td>{{$t->cod_charge}}</td>
                            <td>{{$t->bill_amount}}</td>
                            <td>{{$t->total_bill_amount}}</td>
                            <td>{{$t->paid_amount}}</td> --}}

                        </tr>
                        <?php $total_collected_ammount += $t->amount; ?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td><b>Total Amount</b>          
                            </td>
                            <td><b>{{$total_collected_ammount}}</b></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="pagination pull-right">
                {{ $manage_vault->appends($_REQUEST)->render() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('manage_vault', 'accounts_bills');

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
