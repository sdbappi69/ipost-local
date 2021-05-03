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
      <span>Mange Checkout</span>
  </li>
</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Mange Checkout
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
                        <tr>
                            <th>Hub</th>
                            <th>Account Name</th>
                            <th>Account NO</th>
                            <th>Amount</th>
                            <th>Approved By</th>
                            <th>Depositor</th>
                            <th>Status</th>
                            <th>Created At</th>

                            <th>Transaction Doc.</th>
                            <th>Transaction ID.</th>
                            <th>Action</th>

                            {{-- <th>COD Amount</th>
                                <th>COD Charge</th>
                                <th>Biil Amount Charge</th>
                                <th>Total Biil Amount</th>
                                <th>Paid Amount</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($manage_checkout) > 0 )
                            <?php $total_amount = 0;?>
                            @foreach($manage_checkout as $t)

                            <tr>
                                <td>{{$t->hub->title or "N/A"}}</td>
                                <td>
                                    Acc. Name :{{$t->hub_ban_account->account->name or 'N/A'}}<br>
                                    Bank : {{$t->hub_ban_account->account->bank->name or 'N/A'}}
                                </td>
                                <td>{{$t->hub_ban_account->account->account_no or 'N/A'}}</td>
                                <td>{{$t->amount}}</td>
                                <td>{{$t->manager_id->name or 'N/A'}}</td>
                                <td>{{$t->depositor->name or 'N/A'}}</td>
                                <td>
                                    @if($t->status == '0')
                                    Declined
                                    @elseif($t->status == '1')
                                    Not Approved yet
                                    @elseif($t->status == '2')
                                    Approved
                                    @elseif($t->status == '3')
                                    Canceled
                                    @endif
                                </td>
                                <td>{{$t->created_at}}</td>

                                <td>
                                    @if($t->bank_transection_doc_id !=0)
                                    {{--<a title="view document" class="btn btn-info" target="_blank" href="{{$t->transactionDoc->doc_url}}"><i class="fa fa-file-o"></i></a>--}}
                                    <a title="view document" class="btn btn-info" href="javascript:;" onclick="showDocument('{{@$t->transactionDoc->doc_url}}')"><i class="fa fa-file-o"></i></a>
                                    @endif
                                </td>
                                <td>{{$t->bank_transection_id}}</td>
                                <td>
                                    @if($t->bank_transection_doc_id == 0 and $t->status != 0 and $t->status != 3)
                                    <button title="upload transaction document" onclick="upload_doc({{$t->id}})" type="button" class="btn btn-primary" >
                                        <i class="fa fa-upload"></i>
                                    </button>
                                    @endif

                                    @if($t->bank_transection_doc_id != 0 and !$t->bank_transection_id and $t->status != 0 and $t->status != 3)
                                    <button title="Set Transaction id." onclick="set_transaction_id({{$t->id}})" type="button" class="btn btn-success" >
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    @endif

                                    @if($t->bank_transection_doc_id == 0 and $t->status != 3 and $t->status != 0)
                                    <button title="Cancel Transaction." onclick="cancelTransaction({{$t->id}})" type="button" class="btn btn-danger" >
                                        <i class="fa fa-ban"></i>
                                    </button>
                                    @endif
                                </td>


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
                                <td><b>Total Amount</b>          
                                </td>
                                <td><b>{{$total_amount}}</b></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="pagination pull-right">
                    {{ $manage_checkout->appends($_REQUEST)->render() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    {{-- upload doc modal  --}}
    <div class="modal fade" id="upload_doc_modal" tabindex="-1" role="basic" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title">Upload Bank Document</h4>
        </div>
        {!! Form::open(array('url' => secure_url('') . '/upload-bank-doc', 'method' => 'post','enctype' => 'multipart/form-data')) !!}
        <div class="modal-body">
            <input type="hidden" name="checkout_id" id="checkout_id">
            <div class="form-group">
              <div class="fileinput fileinput-new" data-provides="fileinput">

                <div>
                  <span class="btn default btn-file">
                    <span class="fileinput-new"> Select file (Only image file) </span>

                    <input  accept=".jpeg,.jpg,.png" type="file" id="doc" name="doc"> </span>
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
            {!! Form::open(array('url' => secure_url('') . '/set-bank-transaction-id', 'method' => 'post')) !!}
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
<!-- /.modal-dialog -->
<script src="{{ secure_asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('manage_checkout', 'accounts_bills');

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
    function upload_doc(id){
        $('#checkout_id').val(id);
        $('#upload_doc_modal').modal('show'); 
    }

    function set_transaction_id(id){
        $('#checkout_transaction_id').val(id);
        $('#set_transaction_id').modal('show'); 
    }

    function cancelTransaction(id){
        var ans = confirm('Are you sure?');
        if(ans) {
            /*$.get('{{ secure_url('cancel-checkout') }}?id=' + id, function(data) {
                console.log('var');
            });*/
            $.ajax({
                url : "{{ secure_url('cancel-checkout') }}",
                type : "GET",
                cache : false,
                data: {
                    id:id,
                },
                success : function(result) {
                    window.location.href = "{{ secure_url('manage-checkout-accounts') }}";
                }
            });
        }
    }

    function showDocument(docUrl){
        $('#trans_doc').attr('src', docUrl);
        $('#doc_modal').modal('show'); 
    }
</script>

@endsection
