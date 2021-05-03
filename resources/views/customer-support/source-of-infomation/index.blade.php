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
      <span>Source of information</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Source of information
  <small>List</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
@include('partials.errors')
@if(count($src_of_infos) > 0)
<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">
    <p>
      <button data-toggle="modal" data-target="#create_src_of_info" class="btn btn-primary">Create New</button>
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
          @foreach($src_of_infos as $src_of_info)
          <tr>
            <td>{{ $src_of_info->title }}</td>
            <td>{{ ($src_of_info->status ? 'Active' : 'Inactive') }}</td>
            <td>
              <button data-toggle="modal" data-target="#update_src_of_info_{{$src_of_info->id}}" class="btn btn-primary btn-xs">
                <i class="fa fa-edit"></i>
              </button>
              @include('customer-support.source-of-infomation.edit')
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
      <button data-toggle="modal" data-target="#create_src_of_info" class="btn btn-primary">Create New</button>
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
            highlight_nav('src_of_info','setting');
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
      @include('customer-support.source-of-infomation.create')
      @endsection
