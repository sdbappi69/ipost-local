@extends('layouts.appinside')

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ URL::to('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Hub Bank Accounts</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Hub Bank Accounts
    <small> view</small>
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
            {!! Form::model($_GET,array('method' => 'get', 'id' => 'filter-form')) !!}
            <div class="col-md-4">
                <label class="control-label">Hubs</label>
                {!! Form::select('hub_id', array(''=>'Select Hub') + $hub, null, ['class' => 'form-control js-example-basic-single', 'id' => 'id']) !!}
            </div>
            <div class="col-md-4">
                <label class="control-label">Bank Accounts</label>
                {!! Form::select('account_id', array(''=>'Select Bank Accounts') + $bank_accounts, null, ['class' => 'form-control js-example-basic-single', 'id' => 'id2']) !!}
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

@if(count($hub_bank_accounts) > 0)

<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-bordered table-hover" id="example0">
                <thead class="flip-content">
                    <th>Name</th>
                    <th>Hub</th>
                    <th>Account</th>
                    <th>Bank</th>
                    <th>Notification Time</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach($hub_bank_accounts as $hub_bank_account)
                    <tr>
                        <td>{{ $hub_bank_account->name }}</td>
                        <td>{{ $hub_bank_account->hub->title or "N/A"}}</td>
                        <td>{{ $hub_bank_account->account->name or "N/A"}}</td>
                        <td>{{ $hub_bank_account->account->bank->name or "N/A" }}</td>
                        <td>{{ $hub_bank_account->notification_time or "N/A" }}</td>
                        <td>
                            <a href="{{url('/hub-bank-accounts/'.$hub_bank_account->id.'/edit')}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="portlet light tasks-widget bordered">
        <p>
            No Data Found
        </p>
    </div>
</div>
@endIf

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('all-hub-bank-accounts', 'hub_bank_accounts');

        $('#example0').DataTable({
            "order": [],
        });
    });
</script>
<style media="screen">
.table-filtter .btn{ width: 100%;}
.table-filtter {
    margin: 20px 0;
}
</style>

@endsection
