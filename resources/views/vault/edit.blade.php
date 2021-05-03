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
            <a href="{{ secure_url('vault') }}">Vault</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Insert</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Vault Acconts
    <small>Edit ({{$vault->title}})</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
@include('partials.errors')

{!! Form::model($vault, array('url' => secure_url('') . '/vault/'.$vault->id, 'method' => 'put')) !!}

<div class="row">



    <div class="col-md-6">

        <div class="form-group">
            <label class="control-label">Title</label>
            {!! Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Hub</label>
            {!! Form::select('hub_id', ['' => 'Select Hub']+$hub, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'reference_id']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Amount</label>
            {!! Form::text('amount', null, ['class' => 'form-control', 'required' => 'required']) !!}
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
            highlight_nav('add-vault', 'vault');

        });


    </script>

    @endsection
