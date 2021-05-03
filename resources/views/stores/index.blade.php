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
            <span>Stores</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Stores
    <small>view & update</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="portlet light tasks-widget bordered animated flipInX">

    <div class="portlet-title">
        <div class="caption">
            <i class="icon-edit font-dark"></i>
            <span class="caption-subject font-dark bold uppercase">Filter</span>
        </div>
    </div>
    <div class="portlet-body util-btn-margin-bottom-5">
        {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}

        @if(Auth::user()->hasRole('superadministrator')||Auth::user()->hasRole('systemadministrator')||Auth::user()->hasRole('systemmoderator')||Auth::user()->hasRole('kam'))
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Merchants</label>
            {!! Form::select('merchant_id', array(''=>'Select Merchant') + $merchants, null, ['class' => 'form-control', 'id' => 'merchant_id']) !!}
        </div>
        @endIf
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Store ID</label>
            <input type="text" class="form-control" name="store_id" id="store_id" placeholder="Store ID">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">URL</label>
            <input type="text" class="form-control" name="store_url" id="store_url" placeholder="URL">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Stores</label>
            {!! Form::select('store_type_id', array(''=>'Select Type') + $storeType, null, ['class' => 'form-control', 'id' => 'store_type_id']) !!}
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Status</label>
            {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'id' => 'status']) !!}
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
        </div>
        <div class="clearfix"></div>
        {!! Form::close() !!}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="fa fa-shopping-cart font-dark"></i>
                    <span class="caption-subject bold uppercase">Stores</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="example0">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Merchant Name</th>
                            <th>Store Id</th>
                            <th>Store Password</th>
                            <th>Store URL</th>
                            <th>Store Type</th>
                            <th>Status</th>
                            @if(Auth::user()->hasRole('superadministrator')||Auth::user()->hasRole('systemadministrator')||Auth::user()->hasRole('systemmoderator')||Auth::user()->hasRole('salesteam'))
                            <th>Action</th>
                            @endIf
                        </tr>
                    </thead>
                    <tbody>
                     @if(count($stores) > 0)
                     @foreach($stores as $store)
                     <tr>
                         <td>{{ $store->merchant_name }}</td>
                         <td>{{ $store->store_id }}</td>
                         <td>{{ $store->store_password }}</td>
                         <td>{{ $store->store_url }}</td>
                         <td>{{ $store->store_type }}</td>
                         <td>
                            {!! $store->status == 1 ? '<span class="label label-success"> Active </span>' : '<span class="label label-danger"> Inactive </span>' !!}
                            @if(Auth::user()->hasRole('kam'))
                            &nbsp; <a href="store/{{ $store->id }}/edit?step=2">
                                <span class="label label-success"><i class="fa fa-eye"></i> Cost</span>
                            </a>
                            @endIf
                         </td>
                         @if(Auth::user()->hasRole('superadministrator')||Auth::user()->hasRole('systemadministrator')||Auth::user()->hasRole('systemmoderator')||Auth::user()->hasRole('salesteam'))
                         <td>
                            <div class="btn-group pull-right">
                                <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                     <a href="store/{{ $store->id }}">
                                        <i class="fa fa-file-o"></i> View </a>
                                    </li>
                                    <li>
                                     <a href="store/{{ $store->id }}/edit">
                                        <i class="fa fa-pencil"></i> Update </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        @endIf
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="pagination">
                {!! $stores->appends($req)->render() !!}
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
</div>
</div>

<script type="text/javascript">
    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('store-manage', 'stores');

            // $('#example0').DataTable({
            //     "order": [],
            // });

            // var url = 'stores/data';
            // loadDataTable(url);
            {{--@php
            if( !empty($req) )
            {
                foreach($req as $key => $val)
                {
                    echo "document.getElementById('".$key."').value = '".$val."';";
                }
            }
            @endphp--}}
   });

      //   function loadDataTable(url){
      //       // $('#my_datatable').DataTable();
      //       $('#my_datatable').DataTable({
      //           "processing": true,
      //           "serverSide": true,
      //           "ajax": url,
      //           // "ajax": {
      //           //     url: url,
      //           //     type: 'POST'
      //           //   },
      //           "responsive": true,
      //           // "aaSorting": [[1, 'desc']],
      //           "aaSorting": [],
      //           // "bFilter": false,
      //           "bDestroy": true
      //       });
      //   }
  </script>
  <style media="screen">
  .table-filtter .btn{ width: 100%;}
  .table-filtter {
      margin: 20px 0;
  }
</style>

@endsection
