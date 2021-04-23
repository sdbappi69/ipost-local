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
      <span>Unique Heads</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Unique Head
  <small>List</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
@include('partials.errors')
@if(count($unique_heads) > 0)
<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">
    <p>
      <button data-toggle="modal" data-target="#create_unique_head" class="btn btn-primary">Create New</button>
    </p>
    @include('partials.errors')
    <div class="portlet-body util-btn-margin-bottom-5">
      <table class="table table-bordered table-hover" id="example0">
        <thead class="flip-content">
          <th>Title</th>
          <th>Status</th>
          <th>Action</th>
        </thead>
        <tbody>
          @foreach($unique_heads as $unique_head)
          <tr>
            <td>{{ $unique_head->title }}</td>
            <td>{{ ($unique_head->status ? 'Active' : 'Inactive') }}</td>
            <td>
              <button data-toggle="modal" data-target="#update_unique_head_{{$unique_head->id}}" class="btn btn-primary btn-xs">
                <i class="fa fa-edit"></i>
              </button>
              @include('customer-support.unique-head.edit')
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@else
<div class="col-md-12">
  <div class="portlet light tasks-widget bordered">
    <p>
      <button data-toggle="modal" data-target="#create_unique_head" class="btn btn-primary">Create New</button>
    </p>
    <p>
      No Data Found
    </p>
  </div>
</div>
@endIf

<script type="text/javascript">
  $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('unique_head','setting');
            $('#example0').DataTable({
              "order": [],
            });
          });
        </script>
        <style media="screen">
        .table-filtter .btn{ width: 100%;}
        .table-filtter {
          margin: 20px 0;
        }
      </style>
      @include('customer-support.unique-head.create')
      @endsection
