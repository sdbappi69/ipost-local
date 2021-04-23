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
            <span>Reconciliation</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Reconciliation
    <small>{{ date('Y-m-d') }}</small>
</h1>
<div class="col-md-12">
  <div class="table-filtter">
  {!! Form::open(array('url' => 'reconciliation', 'method' => 'post')) !!}

     <div class="col-md-2">
        <div class="row">
            {!! Form::select('rider_id',['' => 'Select Rider']+$riders,old('rider_id'), ['class' => 'form-control js-example-basic-single', 'id' => 'rider_id']) !!}
        </div>
    </div>

    <div class="col-md-1">
        <div class="row">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>Search</button>
        </div>
    </div>
    <div class="clearfix"></div>

    {!! Form::close() !!}

  </div>
</div>

@if($consignment != '')

  @if($consignment['type'] == 'delivery')

    <div class="col-md-12">

      <table class="table table-bordered table-hover" id="example0">
          <thead class="flip-content">
            <th>Amount to collect</th>
            <th>Amount Collected</th>
            <th>Action</th>
          </thead>
          <tbody>
            <tr>
              <td>{{ $consignment['amount_to_collect'] }}</td>
              <td>{{ $consignment['amount_collected'] }}</td>
              <td>
                <a class="btn btn-primary" href="reconciliation/{{ $rider_id }}">Amount received</a>
              </td>
            </tr>
          </tbody>
      </table>

    </div>

  @endIf

@endIf

<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

   <script type="text/javascript">
    $(document ).ready(function() {
      // Navigation Highlight
      highlight_nav('reconciliation', 'reconciliation');

      // $('#example0').DataTable({
      //     "order": [],
      // });
    });
  </script>

    @endsection
