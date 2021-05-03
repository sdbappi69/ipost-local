@extends('layouts.appinside')

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ secure_url('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ secure_url('hub') }}">Hub bank Accounts</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Insert</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Hub bank  Acconts
    <small>create new</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

{!! Form::model($hub_bank_accounts, array('url' => secure_url('') . '/hub-bank-accounts/'.$hub_bank_accounts->id, 'method' => 'put')) !!}

<div class="row">

    @include('partials.errors')

    <div class="col-md-6">

        <div class="form-group">
            <label class="control-label">Name</label>
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Bank Account</label>
            {!! Form::select('account_id', ['' => 'Select Bank Accounts']+$bank_accounts, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'reference_id']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Hub</label>
            {!! Form::select('hub_id', ['' => 'Select Hub']+$hub, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'reference_id']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Notification Time</label>
            {!! Form::text('notification_time', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="row padding-top-10">
            <a href="javascript:history.back()" class="btn default"> Cancel </a>
            {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
        </div>

    </div>
    

</div>

&nbsp;


{!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('all-hub-bank-accounts', 'hub_bank_accounts');

        });


    </script>

    @endsection
