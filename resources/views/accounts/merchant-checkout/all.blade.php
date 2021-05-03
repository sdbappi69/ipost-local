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
      <span>Merchant Checkout</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Merchant Checkout
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

            <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Merchants</label>
                {!! Form::select('merchant_id', array(''=>'Select Merchant')+$merchant,$_GET['merchant_id'], ['class' => 'form-control js-example-basic-single','id' => 'merchant_id']) !!}
            </div>
            <?php if(!isset($_GET['store_id'])){$_GET['store_id'] = null;} ?>
            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
            <div class="col-md-4">
                <label class="control-label">Stores</label>
                {!! Form::select('store_id', array(''=>'Select Store','all' => 'All')+$store,$_GET['store_id'], ['class' => 'form-control js-example-basic-single','id' => 'store_id']) !!}
            </div>
            @endif
            <?php if(!isset($_GET['account_id'])){$_GET['account_id'] = null;} ?>
            <?php if(!isset($_GET['invoice_id'])){$_GET['invoice_id'] = null;} ?>
            @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
            @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
            <div class="col-md-4">
                <label class="control-label">Accounts</label>
                {!! Form::select('account_id', array(''=>'Select Accounts')+$accounts,$_GET['account_id'], ['class' => 'form-control js-example-basic-single','id' => 'account_id']) !!}
            </div>

            <div class="col-md-4">
                <label class="control-label">Invoice</label>
                {!! Form::select('invoice_id', array('' => 'Select Invoice','new'=>'Add new')+$invoices,$_GET['invoice_id'], ['class' => 'form-control js-example-basic-single','id' => 'invoice_id']) !!}
            </div>
            @endif
            @endif
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
 {!! Form::open(array('url' => secure_url('') . '/merchant-checkout-submit', 'method' => 'post')) !!}
 @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))

 <input type="hidden" name="merchant_id" value="{{$_GET['merchant_id']}}">
 @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
 <input type="hidden" name="store_id" value="{{$_GET['store_id']}}">
 <input type="hidden" name="account" id="account_id_2">
 <input type="hidden" name="invoice" id="invoice_id_2">
 @endif
 @endif

 <!-- BEGIN BUTTONS PORTLET-->
 <div class="portlet light tasks-widget bordered">
  <div class="portlet-body util-btn-margin-bottom-5">


    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
                    @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
                    <th scope="col">{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                    @endif
                    @endif
                    <th scope="col">AWB</th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Merchant</th>
                    <th scope="col">Store</th>
                    <th scope="col">Collected Amount</th>
                    <!-- <th scope="col">COD Amount</th> -->
                    <!-- <th scope="col">COD Charge</th> -->
                    <th scope="col">Bill Amount</th>
                    <!-- <th scope="col">Total BillAmount</th> -->
                    <th scope="col">Paid Amount</th>
                </tr>
            </thead>
            <tbody>
                @if(count($merchant_checkout) > 0 )
                <?php 
                $total_collected_amount = 0;
                $total_cod_amount = 0;
                $total_cod_charge = 0;
                $total_bill_amount = 0;
                $total_total_bill_amount = 0;
                $total_paid_amount = 0;
                ?>
                @foreach($merchant_checkout as $c)

                <tr>
                   @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
                   @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
                   <td>{!!Form::checkbox('cashCollectionId[]',$c->id, false) !!}</td>
                   @endif
                   @endif

                   <td>{{$c->sub_order->unique_suborder_id or 'N/A'}}</td>
                   <td>{{$c->order->unique_order_id or 'N/A'}}</td>
                   <td>{{$c->merchant->name or 'N/A'}}</td>


                   <td>{{$c->store->store_id or 'N/A'}}</td>

                   <td>{{$c->collected_amount}}</td>
                   <input type="hidden" name="collected_amount_{{$c->id}}" value="{{$c->collected_amount}}">
                   <!-- <td>{{$c->cod_amount}}</td> -->
                   <!-- <td>{{$c->cod_charge}}</td> -->
                   <td>{{$c->bill_amount}}</td>
                   <!-- <td>{{$c->total_bill_amount}}</td> -->
                   <td>{{$c->paid_amount}}</td>
                   <?php 
                   $total_collected_amount += $c->collected_amount;
                   $total_cod_amount += $c->cod_amount;
                   $total_cod_charge += $c->cod_charge;
                   $total_bill_amount += $c->bill_amount;
                   $total_total_bill_amount += $c->total_bill_amount;
                   $total_paid_amount += $c->paid_amount;
                   ?>
                   @endforeach
                   <tr>
                    @if(isset($_GET['merchant_id']) and !empty($_GET['merchant_id']))
                    <td style="text-align: center;" colspan="5"><b>Total: </b></td>
                    @else
                    <td style="text-align: center;" colspan="4"><b>Total: </b></td>
                    @endif
                    <td><b>{{$total_collected_amount}}</b></td>
                    <!-- <td><b>{{$total_cod_amount}}</b></td> -->
                    <!-- <td><b>{{$total_cod_charge}}</b></td> -->
                    <td><b>{{$total_bill_amount}}</b></td>
                    <!-- <td><b>{{$total_total_bill_amount}}</b></td> -->
                    <td><b>{{$total_paid_amount}}</b></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if(!isset($_GET['merchant_id']) or empty($_GET['merchant_id']) )
    <div class="pagination pull-right">
      {{$merchant_checkout->appends($_REQUEST)->render()}}
  </div>

  @endif
  @if(count($merchant_checkout) > 0 and isset($_GET['merchant_id']) and !empty($_GET['merchant_id']) )
  @if(isset($_GET['store_id']) and !empty($_GET['store_id']))
  <center><div >
     <button class="btn btn-success" type="submit">Submit</button>
 </div></center>
 @endif
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
            highlight_nav('create_merchant_checkout', 'accounts_bills');

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
<script type="text/javascript">
  $("#account_id").change(function () {

    if($("#account_id").val() != '' ){
              //alert($("#account_id").val());
              $("#account_id_2").val($("#account_id").val());
          }
          else{
              $("#account_id_2").val(null);
          }
      });
  </script> 
  <script type="text/javascript">
      $("#invoice_id").change(function () {
        $("#invoice_id_2").val($("#invoice_id").val());
    });
</script>

{{--   <script type="text/javascript">
  $("#store_id").change(function () {
    $('#account_id_div').hide();
    var store_id =  $("#store_id").val();
    var merchant_id =  $("#merchant_id").val();
    if(merchant_id != ''){
      if(store_id != '' ){
        $.get( "/get-merchant-account", { merchant_id: merchant_id,store_id:store_id } ).done(function( data ) {
            $.each(data, function(i, value){
            $('#account_id').append($('<option>').text(value).attr('value', value));
        });
        });
        
        if(store_id != 'all'){
          $.get('{{ secure_url('get-merchant-account') }}?merchant_id=' + merchant_id + '&store_id=' + store_id, function(data) {

            $('#account_id').empty();
            $.each(data, function(index,subCatObj){
              console.log(subCatObj);
              $('#account_id').append($('<option>').text(subCatObj.name).attr('value', subCatObj.id));
              //  $('#account_id').append(''+subCatObj.name+'');
          });
            $('#account_id_div').show('slow');
        });
      }
      else{
          $.get('{{ secure_url('get-merchant-account') }}?all=all&merchant_id='+merchant_id,function(data) {

            $('#account_id').empty();
            $.each(data, function(index,subCatObj){
              console.log(subCatObj);
              $('#account_id').append($('<option>').text(subCatObj.name).attr('value', subCatObj.id));
              //  $('#account_id').append(''+subCatObj.name+'');
          });
            $('#account_id_div').show('slow');
        });
      }
  }
}
else{
  alert("Merchant must be selected");
}

});
</script> --}}

@endsection
