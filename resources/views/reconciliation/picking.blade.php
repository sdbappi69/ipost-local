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
<small>Picking ({{$consignments->consignment_unique_id}}) - {{$consignments->rider->name}} (Rider) </small>
</h1>




@if($consignments->type == 'picking')

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
          <a class="btn btn-primary" href="{{secure_url('reconciliation-picking-done/'.$consignments->id)}}">Reconciliation Done</a>
          @endif
        </td>
      </tr>
    </tbody>
  </table>
  <table class="table table-bordered table-hover" id="example0">
    <thead class="flip-content">
      <th>Picked Detail</th>
      <th>Quantity</th>
      <th>Status</th>
      <th>Remarks</th>
    </thead>
    <tbody>
      @if(count($products) > 0)
      @foreach($products as $p)
      <tr>

        <td>
          @if($p->return == 0)
            Task type: <strong>Picking</strong>
          @else
            Task type: <strong>Return</strong>
          @endIf
          <br>
          AWB: <strong>{{ $p->unique_suborder_id }}</strong>
          <br>
          Title: <strong>{{ $p->product_title }}</strong>
          <br>
          Category: {{ $p->product_category }}
          <br>
          Picking at: {{ $p->picke_at }}
          <br>
          Warehouse: <strong>{{ $p->title }}</strong>
          <br>
          Phone: {{ $p->msisdn }}, {{ $p->alt_msisdn }}
          <br>
          Address: {{ $p->address1 }}, {{ $p->zone_name }}, {{ $p->city_name }}, {{ $p->state_name }}
        </td>
        <td> 
          @if($consignments->status != '4')
          <form method="post" action="{{secure_url('reconciliation-update-picking')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="consignments_id" value="{{$consignments->id}}">
            <input type="hidden" name="product_unique_id" value="{{$p->product_unique_id}}">
            <div class="input-group">
              <label class="control-label">Requested</label>
              <input name="quantity" value="{{ $p->quantity }}" type="number" class="form-control" readonly="readonly" required="required">
            </div>
            <div class="input-group">
              <label class="control-label">Collected</label>
              <input name="picking_task_quantity" value="{{ $p->picking_task_quantity }}" type="number" class="form-control collected_quantity {{$p->product_unique_id}}" product_unique_id="{{$p->product_unique_id}}" max_quantity="{{$p->quantity}}" required="required">
            </div>
            <div class="input-group">

              <label class="control-label">Rest</label>
              <input name="rest_quantity" value="{{ $p->quantity - $p->picking_task_quantity }}" type="number" class="form-control rest_quantity_{{$p->product_unique_id}}" readonly="readonly" required="required">

              <div class="pertial_options pertial_option_{{ $p->product_unique_id }}" @if($p->quantity - $p->picking_task_quantity == 0) style="display:none;" @endIf>
                
                <label class="control-label">Sub-Order for rest</label>
                @if($p->return == 0)
                  {{-- */ $sub_order_status_list = $sub_order_status_list; /* --}}
                @else
                  {{-- */ $sub_order_status_list = $sub_order_status_return_list; /* --}}
                @endIf
                {!! Form::select('sub_order_status', array(''=>'Select Status')+$sub_order_status_list, null, ['class' => 'form-control js-example-basic-single', 'id' => 'sub_order_status_'.$p->product_unique_id]) !!}
                @if($p->return == 0)
                  <label class="control-label">Pick Date</label>
                @else
                  <label class="control-label">Return Date</label>
                @endIf
                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" data-date-start-date="+0d">
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                    {!! Form::text('picking_date', null, ['class' => 'form-control picking_date', 'readonly' => 'true', 'id' => $p->product_unique_id ]) !!}
                </div>
                @if($p->return == 0)
                  <label class="control-label">Pick Time</label>
                @else
                  <label class="control-label">Return Time</label>
                @endIf
                {!! Form::select('picking_time_slot_id', array(''=>'Select Pick Time'), null, ['class' => 'form-control js-example-basic-single picking_time_slot_id', 'id' => 'picking_time_slot_id_'.$p->product_unique_id]) !!}

              </div>

            </div>

            @if($p->reconcile == 0)

              <div class="input-group">
                <br><button class="btn blue" type="submit"><i class="fa fa-upload"></i> Reconcile</button>
              </div>

            @endIf
            
          </form>
          @else
          {{ $p->picking_task_quantity }}
          @endif
        </td>
        <td>
          <strong>
            @if($p->pickUpStatus == '0')
              Default
            @elseif($p->pickUpStatus == '1')
              Started
            @elseif($p->pickUpStatus == '2')
              Completed
            @elseif($p->pickUpStatus == '3')
              Partial
            @elseif($p->pickUpStatus == '4')
              Failed
            @endif
          </strong>
          <br>
          {{ $p->reason }}
        </td>
        <td>{{ $p->remarks or '' }}</td>
      </tr>

      <!-- /.modal-dialog -->

      @endforeach
      @endif
    </tbody>
  </table>

</div>

@endIf

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('custom/js/date-time.js') }}" type="text/javascript"></script>

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<script type="text/javascript">
  $(document ).ready(function() {
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
        $(".pertial_option_"+product_unique_id).show();
        $("#"+product_unique_id).attr('required', true);
        $("#sub_order_status_"+product_unique_id).attr('required', true);
        $("#picking_time_slot_id_"+product_unique_id).attr('required', true);
      }

  });

  // Get Pick-up time On date Change
  $('.picking_date').on('change', function() {
      var id = $(this).attr("id");
      var date = $(this).val();
      var day = dayOfWeek(date);

      pick_up_slot_by_id(id,day);
  });

</script>

        @endsection
