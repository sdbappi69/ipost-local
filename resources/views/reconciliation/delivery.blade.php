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
            <span>Consignments</span>
        </li>

    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Reconciliation
    <small>Delivery ({{$consignments->consignment_unique_id}}) - {{$consignments->rider->name}} (Rider)</small>
</h1>




@if($consignments->type == 'delivery')

<div class="col-md-12">

    <table class="table table-bordered table-hover" id="example0">
        <thead class="flip-content">

        <th>Amount to collect</th>
        <th>Amount Collected</th>
        <th>Quantity</th>
        <th>Quantity Available</th>
        <th>Action</th>
        </thead>
        <tbody>
            <tr>
                <td>{{ $consignments->amount_to_collect}}</td>
                <td>{{  $consignments->amount_collected }}</td>
                <td>{{  $consignments->quantity }}</td>
                <td>{{  $consignments->quantity_available }}</td>
                <td>
                    @if(is_null($details))
                    <a class="btn btn-primary" href="{{secure_url('reconciliation-delivery-done/'.$consignments->id)}}">Reconciliation Done</a>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered table-hover" id="example0">
        <thead class="flip-content">
            <th>Detail</th>
            <th>Reconcile</th>
            <th>Status</th>
            <th>Remarks</th>
        </thead>
        <?php
        foreach ($sub_orders as $sub_order) {
            $sub_array[] = $sub_order->id;
            $sub_str = implode(',', $sub_array);
        }
        ?>
        <tbody>
            @if(count($sub_orders) > 0)
            @foreach($sub_orders as $sub_order)


        <td> 
            AWB: <strong>{{ $sub_order->unique_suborder_id }}</strong>
            <br>
            Merchant: {{ $sub_order->order->store->merchant->name or 'N/A' }}
            <br>
            Last Attempt: {{ $sub_order->dTask->updated_at or 'N/A'}}
            <br>
            Shipping Address: {{ $sub_order->order->delivery_address1 }}, {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}, {{ $sub_order->order->delivery_zone->city->state->name }}
        </td>
        <td>
            <?php $total_qty = 0; ?>
            <?php $total_payable_amount = 0; ?>
            <?php $total_delivery_paid_amount = 0; ?>
            <?php $total_delivered_qty = 0; ?>

            @foreach($sub_order->products as $product)

                <?php $total_qty += $product->quantity; ?>
                <?php $total_payable_amount += $product->total_payable_amount; ?>
                <?php $total_delivery_paid_amount += $product->delivery_paid_amount; ?>
                <?php $total_delivered_qty += $product->delivered_quantity; ?>

                <form method="post" action="{{secure_url('reconciliation-update-delivery')}}">

                    <input type="hidden" name="sub_orders_id_for_consingment" value="{{rtrim($sub_str,',')}}">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="consignments_id" value="{{$consignments->id}}" required="required">
                    <input type="hidden" name="product_unique_id" value="{{$product->product_unique_id}}" required="required">
                    <input type="hidden" name="sub_order_id" value="{{$sub_order->id}}" required="required">
                    <div class="input-group">
                        <table width="100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <label class="control-label">Quantity</label>
                                    <input name="quantity" value="{{$total_qty}}" type="number" class="form-control" readonly="readonly" required="required">
                                </td>
                                <td>
                                    <label class="control-label">Payable</label>
                                    <input name="payable" value="{{$total_payable_amount}}" type="number" class="form-control total_payable_amount" readonly="readonly" required="required">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="input-group">

                        <table width="100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <label class="control-label">Delivered</label>
                                    <input name="delivery_task_quantity" value="{{$total_delivered_qty}}" type="number" class="form-control collected_quantity {{$product->product_unique_id}}" product_unique_id="{{$product->product_unique_id}}" max_quantity="{{$product->quantity}}" required="required">
                                </td>
                                <td>
                                    <label class="control-label">Paid</label>
                                    <input name="delivery_paid_amount" value="{{$total_delivery_paid_amount}}" type="number" class="form-control delivery_paid_amount amount_{{$product->product_unique_id}}" max_amount="{{$total_payable_amount}} required="required">
                                </td>
                            </tr>
                        </table>

                    </div>
                    <div class="input-group">

                      <label class="control-label">Rest</label>
                      <input name="rest_quantity" value="{{ $total_qty - $total_delivered_qty }}" type="number" class="form-control rest_quantity_{{$product->product_unique_id}}" readonly="readonly" required="required">

                      <div class="pertial_options" @if(($total_qty - $total_delivered_qty) == 0) style="display:none;" @endIf>
                        
                        <label class="control-label">Action for rest</label>
                        {!! Form::select('sub_order_status', array(''=>'Select Status')+$sub_order_status_list, null, ['class' => 'form-control js-example-basic-single', 'id' => 'sub_order_status_'.$product->product_unique_id]) !!}

                      </div>

                    </div>

                    @if($sub_order->dTask->reconcile == 0)

                        <div class="input-group">
                          <br><button class="btn blue" type="submit"><i class="fa fa-upload"></i> Reconcile</button>
                        </div>

                    @endIf

                </form>
            @endforeach
        </td>
        <td>
            <strong>
                @if($sub_order->dTask->status == '0')
                    Default
                @elseif($sub_order->dTask->status == '1')
                    Started
                @elseif($sub_order->dTask->status == '2')
                    Completed
                @elseif($sub_order->dTask->status == '3')
                    Partial
                @elseif($sub_order->dTask->status == '4')
                    Failed
                @endif
            </strong>
            <br>
            {{$sub_order->dTask->reason->reason or ''}}
        </td>

        <td>{{$sub_order->dTask->reason->remarks or ''}}</td>

        </tr>
        <!-- /.modal-dialog -->
</div>
@endforeach
@endif
</tbody>
</table>

</div>

@endIf



<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<script type="text/javascript">
    $(document).ready(function () {
        // Navigation Highlight
        highlight_nav('all_consignments', 'consignments');

        // $('#example0').DataTable({
        //     "order": [],
        // });
    });

    $(".collected_quantity").change(function(){

      // Quantity
      var product_unique_id = $(this).attr("product_unique_id");
      var max_quantity = parseInt($(this).attr("max_quantity"));
      // alert(max_quantity);
      var collected_quantity = parseInt($("."+product_unique_id).val());
      if(collected_quantity > max_quantity){
        collected_quantity = max_quantity;
        $("."+product_unique_id).val(collected_quantity);
      }else if(collected_quantity < 0){
        collected_quantity = 0;
        $("."+product_unique_id).val(collected_quantity);
      }
      var rest_quantity = parseInt(max_quantity - collected_quantity);
      $(".rest_quantity_"+product_unique_id).val(rest_quantity);
      // Pertial
      if(rest_quantity == 0){
        $(".pertial_options").hide();
        $("#"+product_unique_id).attr('required', false);
        $("#sub_order_status_"+product_unique_id).attr('required', false);
        $("#picking_time_slot_id_"+product_unique_id).attr('required', false);
      }else{
        $(".pertial_options").show();
        $("#"+product_unique_id).attr('required', true);
        $("#sub_order_status_"+product_unique_id).attr('required', true);
        $("#picking_time_slot_id_"+product_unique_id).attr('required', true);
      }

    });

    $(".delivery_paid_amount").change(function(){

        // Amount
        var delivery_paid_amount = parseInt($(this).val());
        var max_amount = parseInt($(this).attr("max_amount"));

        if(delivery_paid_amount > max_amount){
            delivery_paid_amount = max_amount;
            $(".amount_"+product_unique_id).val(delivery_paid_amount);
        }else if(delivery_paid_amount < 0){
            delivery_paid_amount = 0;
            $(".amount_"+product_unique_id).val(delivery_paid_amount);
        }

    });

</script>

@endsection
