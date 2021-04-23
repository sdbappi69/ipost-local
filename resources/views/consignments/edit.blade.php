@extends('layouts.appinside')

@section('content')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="{{ URL::to('home') }}">Home</a>
      <i class="fa fa-circle"></i>
    </li>
    <li>
      <span>Consignments</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Consignments
  <small>All Pick-Up</small>
</h1>
<div class="col-md-12">
  <div class="table-filtter">
   {!! Form::open(array('method' => 'get')) !!}
   <?php if(!isset($_GET['c_unique_id'])){$_GET['c_unique_id'] = null;} ?>
   <div class="col-md-2">
    <div class="row">
     <input type="text" value="{{$_GET['c_unique_id']}}" class="form-control" name="c_unique_id" id="c_unique_id" placeholder="Consignment Unique ID">
   </div>
 </div>


 <?php if(!isset($_GET['type'])){$_GET['type'] = null;} ?>
 <div class="col-md-2">
  <div class="row">
   {!! Form::select('type',['' => 'Select Type','picking' => 'Picking','delivery' => 'Delivery'],$_GET['type'], ['class' => 'form-control js-example-basic-single', 'id' => 'type']) !!}
 </div>
</div>

<?php if(!isset($_GET['status'])){$_GET['status'] = null;} ?>
<div class="col-md-2">
  <div class="row">
   {!! Form::select('status',['' => 'Select Status','0' => 'Cancel','1' => 'Ready','2' => 'On The Way','3' => 'Complete'],$_GET['status'], ['class' => 'form-control js-example-basic-single', 'id' => 'status']) !!}
 </div>
</div>



<?php if(!isset($_GET['rider_id'])){$_GET['rider_id'] = null;} ?>
<div class="col-md-2">
  <div class="row">
   {!! Form::select('rider_id', array(''=>'Select Rider')+$rider,$_GET['rider_id'], ['class' => 'form-control js-example-basic-single','id' => 'rider_id']) !!}
 </div>
</div>
<?php if(!isset($_GET['search_date'])){$_GET['search_date'] = null;} ?>
<div class="col-md-2">


 <div class="row">

  <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" >
    <span class="input-group-btn">
      <button class="btn default" type="button">
        <i class="fa fa-calendar"></i>
      </button>
    </span>
    {!! Form::text('search_date',$_GET['search_date'], ['class' => 'form-control picking_date','placeholder' => 'Create Date' ,'readonly' => 'true', 'id' => 'search_date']) !!}
  </div>
</div>

</div>




<div class="col-md-1">
  <div class="row">
   <button type="submit" class="btn btn-primary">Filter</button>
 </div>
</div>
<div class="clearfix"></div>
{!! Form::close() !!}
</div>
</div> <br><br>
<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">
    <div class="portlet-body util-btn-margin-bottom-5">
      <table class="table table-bordered table-hover" id="example0">
        <thead class="flip-content">

          <th>Consignment Unique ID</th>
          <th>Type</th>
          <th>Rider</th>
          <th>Amount To Collect</th>
          <th>Amount Collected</th>
          <th>Quantity</th>
          <th>Quantity Available</th>
          <th>Status</th>
          <th>Action</th>
          

        </thead>
        <tbody>
          @if(count($consignments) > 0)
          @foreach($consignments as $c)
          <tr>
            <td>{{$c->consignment_unique_id}}</td>
            <td>{{$c->type}}</td>
            <td>{{$c->rider->name or 'N/A'}}</td>
            <td>{{$c->amount_to_collect}}</td>
            <td>{{$c->amount_collected}}</td>
            <td>{{$c->quantity}}</td>
            <td>{{$c->quantity_available}}</td>
            <td>
              @if($c->status == 0)
              Cancel
              @elseif ($c->status == 1)
              Ready
              @elseif ($c->status == 2)
              On The Way
              @elseif ($c->status == 3)
              Complete
              @endif

            </td>
            
            <td>
            <a class="btn btn-primary btn-xs" target="_blank" href="{{url('consignments/'.$c->id.'/'.$c->type)}}">View</a>
            <a class="btn btn-info btn-xs" target="_blank" href="{{url('consignments-all-invoice/'.$c->id.'/'.$c->type)}}">All Invoice</a>
            @if ($c->status != 2)
            <a class="btn btn-success btn-xs"  href="{{url('consignments-start/'.$c->id)}}">Start</a>
            <a class="btn btn-danger btn-xs"  href="{{url('consignments-cancel/'.$c->id.'/'.$c->type)}}">Cancel</a>
            @endif
            </td>

          </tr>

          <!-- /.modal-dialog -->
        </div>
        @endforeach
        @endif
      </tbody>
    </table>
    <div class="pagination pull-right">
      {{ $consignments->render() }}
    </div>

  </div>
</div>
</div>

<script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('all_consignments', 'consignments');

            // $('#example0').DataTable({
            //     "order": [],
            // });
          });
        </script>





        @endsection
