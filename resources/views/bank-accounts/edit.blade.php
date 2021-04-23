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
            <a href="{{ URL::to('bank-accounts') }}">Bank Accounts</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Edit</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Bank Acconts
    <small>Edit ({{$bank_account->name}})</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

{!! Form::model($bank_account, array('url' => '/bank-accounts/'.$bank_account->id, 'method' => 'put')) !!}

<div class="row">

    @include('partials.errors')

    <div class="col-md-6">

        <div class="form-group">
            <label class="control-label">Name</label>
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Bank</label>
            {!! Form::select('bank_id', ['' => 'Select Bank']+$bank, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'reference_id']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Account No</label>
            {!! Form::text('account_no', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Account Of</label>
            {!! Form::text('account_of', null, ['class' => 'form-control', 'required' => 'required']) !!}
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
            highlight_nav('all-bank-accounts', 'bank_accounts');

        });


    </script>

    @endsection
