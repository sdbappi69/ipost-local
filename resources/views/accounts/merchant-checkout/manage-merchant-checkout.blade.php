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
      <span>Manage Merchant Checkout</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Manage Merchant Checkout
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
            <div class="col-md-4">
                <label class="control-label">Status</label>
                {!! Form::select('status',['' => 'Select Status']+$status,$_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status']) !!}
            </div>
            <?php if(!isset($_GET['merchant_id'])){$_GET['merchant_id'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Marchants</label>
                {!! Form::select('merchant_id',['' => 'Select Merchant']+$merchant->toArray(),$_GET['merchant_id'], ['class' => 'form-control js-example-basic-single', 'id' => 'merchant_id']) !!}
            </div>

            <?php if(!isset($_GET['search_date'])){$_GET['search_date'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Date</label>
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('search_date',$_GET['search_date'], ['class' => 'form-control picking_date','placeholder' => 'Created Date' ,'readonly' => 'true', 'id' => 'search_date']) !!}
                </div>
            </div>


            <?php if(!isset($_GET['invoice_no'])){$_GET['invoice_no'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Invoice No.</label>
                {!! Form::text('invoice_no',$_GET['invoice_no'], ['class' => 'form-control','placeholder' => '#Invoice No.', 'id' => 'invoice_no']) !!}
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
                    <thead>
                        <tr>
                            <th scope="col">Invoice</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Action</th>
                            <th scope="col">Merchant</th>
                            <th scope="col">Store</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Total</th>
                            <th scope="col">Bank Account</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Status</th>
                            <th scope="col">Transaction Doc.</th>
                            <th scope="col">Transaction ID.</th>
                            <th scope="col">Check NO.</th>
                            <th scope="col">Cheque Bank Acc.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($manage_merchant_checkout) > 0 )
                        <?php $total_collected_ammount = 0; ?>
                        @foreach($manage_merchant_checkout as $t)
                        <tr>
                            <td>
                                @if(!is_null($t->invoice_no))
                                <a target="_blank" title="Click to view pdf (invoice no : #{{$t->invoice_no}} )" class="btn btn-danger" href="{{secure_url('/view-merchant-invoice'.'/'.$t->invoice_no).'/'.$t->id}}">
                                    <i  class="fa fa-file-pdf-o"></i>
                                </a>
                                @endif
                            </td>

                            <td>
                                @if($t->bank_transection_doc_id == 0)
                                <button title="Add Discount Info" onclick="update_discount({{$t->id}})" type="button" class="btn btn-primary" >
                                    <i class="fa fa-tags"></i>
                                </button>
                                @endif
                            </td>

                            <td>
                                @if($t->bank_transection_doc_id == 0)
                                <button title="upload transaction document" onclick="upload_doc({{$t->id}}, {{$t->merchant_id}})" type="button" class="btn btn-primary" >
                                    <i class="fa fa-upload"></i>
                                </button>
                                @endif
                                @if($t->bank_transection_doc_id != 0 and !$t->bank_transection_id)
                                <button title="Set Transaction id." onclick="set_transaction_id({{$t->id}})" type="button" class="btn btn-success" >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endif
                            </td>

                            <td>{{$t->merchant->name or 'N/A'}}</td>
                            <td>{{get_store_name_merchant_check_out($t->store_id)}}</td>
                            <td>{{$t->amount}}</td>
                            <td>{{$t->discount_amount}}</td>
                            <td>{{$t->total_amount}}</td>
                            <td>
                                Merchant Accounts : {{$t->account->name or 'N/A'}} <br> 
                                Bank : {{$t->account->account->bank->name or 'N/A'}}<br>
                                Account Name : {{$t->account->account->name or 'N/A'}}<br>
                                Account No. : {{$t->account->account->account_no or 'N/A'}}<br>
                          </td>
                          <td>{{$t->created_at}}</td>
                          <td>
                              @if($t->status == '1')
                              Active
                              @elseif($t->status == '2')
                              Document Uploaded
                              @elseif($t->status == '3')
                              Transaction ID Set
                              @endif
                          </td>
                          <td>
                              @if($t->bank_transection_doc_id !=0 and !is_null($t->cheque_no))
                              {{--<a title="view document" class="btn btn-info" target="_blank" href="{{@$t->transactionDoc->doc_url}}"><i class="fa fa-file-o"></i></a>--}}
                              <a title="view document" class="btn btn-info" href="javascript:;" onclick="showDocument('{{@$t->transactionDoc->doc_url}}')"><i class="fa fa-file-o"></i></a>
                              @endif
                          </td>
                          <td>{{$t->bank_transection_id}}</td>          
                          <td>{{$t->cheque_no}}</td>
                          <td>
                            Cheque Accounts : {{$t->cheque_account->name or 'N/A'}} <br> 
                            Bank : {{$t->cheque_account->bank->name or 'N/A'}}<br>
                            Account : {{$t->cheque_account->bank->account_no or 'N/A'}}<br>
                        </td>
                    </tr>
                    <?php $total_collected_ammount += $t->amount; ?>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total Amount</b>          
                        </td>
                        <td><b>{{$total_collected_ammount}}</b></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="pagination pull-right">
            {{ $manage_merchant_checkout->appends($_REQUEST)->render() }}
        </div>
    </div>
</div>
</div>

@if(count($manage_merchant_checkout) > 0 )
{{-- upload doc modal  --}}
<div class="modal fade" id="upload_doc_modal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Upload Bank Document</h4>
            </div>
            {!! Form::open(array('url' => secure_url('') . '/upload-bank-doc-merchant-checkout', 'method' => 'post','enctype' => 'multipart/form-data')) !!}
            <div class="modal-body">
                <input type="hidden" name="checkout_id" id="checkout_id">
                {{--<div class="form-group">

                    {!! Form::number('discount_amount', 0, ['class' => 'form-control', 'placeholder' => 'Discount', 'required' => 'required']) !!}
                </div>--}}

                <div class="form-group">

                    {!! Form::text('cheque_no', null, ['class' => 'form-control', 'placeholder' => 'Cheque NO', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::select('bank_account',['' => 'Select Account'],null, ['class' => 'form-control js-example-basic-single', 'id' => 'bank_account']) !!}
                </div>
                <div class="form-group">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div>
                            <span class="btn default btn-file">
                                <span class="fileinput-new"> Select file (Only image file) </span>
                                <input  accept=".jpeg,.jpg,.png" type="file" id="doc" name="doc">
                            </span>
                            <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
</div>

{{-- set transaction id  --}}
<div class="modal fade" id="set_transaction_id" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Bank Transaction ID</h4>
            </div>
            {!! Form::open(array('url' => secure_url('') . '/merchant-checkout-transaction-id', 'method' => 'post')) !!}
            <div class="modal-body">
                <input type="hidden" name="checkout_transaction_id" id="checkout_transaction_id">

                <div class="form-group">

                    {!! Form::text('transaction_id', null, ['class' => 'form-control', 'placeholder' => 'Transaction ID', 'required' => 'required']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<!-- /.modal-dialog -->

<!-- /.Discount Modal -->
<div class="modal fade" id="discount_modal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Discount</h4>
            </div>
            {!! Form::open(array('url' => secure_url('') . '/discount-merchant-checkout', 'method' => 'post')) !!}
            <div class="modal-body">
                <input type="hidden" name="discount_checkout_id" id="discount_checkout_id">
                <div class="form-group">
                    <label class="control-label">Discount</label>
                    {!! Form::number('discount_amount', 0, ['id' => 'discount_amount', 'class' => 'form-control', 'placeholder' => 'Discount', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    <label class="control-label">Discount Show to Merchant?</label>
                    <br/>
                    {!! Form::radio('is_showing', 1, ['id' => 'show', 'class' => 'form-control', 'required' => 'required']) !!} Show in Merchant end 
                    <br/>

                    {!! Form::radio('is_showing', 0, ['id' => 'hide', 'class' => 'form-control', 'required' => 'required']) !!} Hide from Merchant end
                </div>
                <div class="form-group">
                    <label class="control-label">Discount Remarks</label>
                    {!! Form::textarea('discount_remarks', null, ['id' => 'discount_remarks', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
</div>

{{-- doc modal  --}}
<div class="modal fade" id="doc_modal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Document</h4>
            </div>
            <div class="modal-body">
                <div class="slimScrollDiv" style="">
                    <img src="" id="trans_doc" style="width: 100%" />
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

@endif
<script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('manage_merchant_checkout', 'accounts_bills');

        // $('#example0').DataTable({
        //     "order": [],
        // });
    });

    $("#select_all_chk").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });

    function upload_doc(id, merchant_id){
        $('#checkout_id').val(id);
        $.get('{{ secure_url('get-merchant-account') }}?all=all&merchant_id='+merchant_id,function(data) {

            $('#bank_account').empty();
            $.each(data, function(index,subCatObj){
              console.log(subCatObj);
              $('#bank_account').append($('<option>').text(subCatObj.name).attr('value', subCatObj.id));
              //  $('#account_id').append(''+subCatObj.name+'');
          });
            $('#upload_doc_modal').modal('show');
        });

    }

    function set_transaction_id(id){
        $('#checkout_transaction_id').val(id);
        $('#set_transaction_id').modal('show'); 
    }

    function update_discount(id){
        $('#discount_checkout_id').val(id);
        $('#discount_remarks').val('');

        $.ajax({
            url : '{{ secure_url('get-merchant-discount') }}',
            type : "GET",
            cache : false,
            data: {
                id:id,
            },
            success : function(result) {
                var obj = JSON.parse(result);

                $('#discount_amount').val(obj.discount_amount);
                
                if(obj.showing_status == '1'){
                    // $('#show').prop('checked',true);
                    $("input[name=is_showing][value='1']").prop("checked",true);
                }
                else if(obj.showing_status == '0') {
                    // $('#hide').prop('checked',true);
                    $("input[name=is_showing][value='0']").prop("checked",true);
                }

                if(obj.discount_remarks != null)
                    $('#discount_remarks').val(obj.discount_remarks);

                $('#discount_modal').modal('show');
            }
        });
    }

    function showDocument(docUrl){
        $('#trans_doc').attr('src', docUrl);
        $('#doc_modal').modal('show'); 
    }
</script>

@endsection
