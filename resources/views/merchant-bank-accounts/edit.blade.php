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
            <a href="{{ secure_url('hub') }}">Merchant bank Accounts</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Edit</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Merchant bank  Accounts
    <small>Edit ({{$merchant_bank_account->name}})</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->


{!! Form::model($merchant_bank_account, array('url' => secure_url('') . '/merchant-bank-accounts/'.$merchant_bank_account->id, 'method' => 'put')) !!}
<div class="row">

    @include('partials.errors')

    <div class="col-md-6">

        <div class="form-group">
            <label class="control-label">Name</label>
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Bank Account</label>
            {!! Form::select('account_id', ['' => 'Select Bank Accounts']+$bank_accounts, null, ['class' => 'form-control js-example-basic-single ', 'required' => 'required', 'id' => 'account_id']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Merchant</label>
            {!! Form::select('merchant_id', ['' => 'Select Merchant']+$merchant, null, ['class' => 'form-control js-example-basic-single ', 'required' => 'required', 'id' => 'merchant_id','onchange' => 'get_store()']) !!}
        </div>
        <div class="form-group">
            <label class="control-label">Select Store</label>
            {!! Form::select('store_id', ['' => 'Select Store']+$store, null, ['class' => 'form-control js-example-basic-single ', 'required' => 'required', 'id' => 'store_id']) !!}
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

    function get_store(){

event.preventDefault();
        var merchant_id =  $("#merchant_id").val();

        if(merchant_id != ''){
           $.get('{{ secure_url('get-store-for-merchant-bank-accounts') }}?merchant_id='+merchant_id,function(data) {

            $('#store_id').empty();
            $('#store_id').append($('<option>').text('Select Store').attr('value',''));
            $.each(data, function(index,subCatObj){
                  //console.log(subCatObj);
                  $('#store_id').append($('<option>').text(subCatObj.store_id).attr('value', subCatObj.id));
              //  $('#account_id').append(''+subCatObj.name+'');
          });

        });

       }
    //    else{
    //     $('#store_id').empty();
    // }


};

</script>
<script type="text/javascript">

    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('add-hub-bank-accounts', 'hub_bank_accounts');

        });


    </script>


    @endsection
