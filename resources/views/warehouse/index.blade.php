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
                <span>Pick-up location</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Pick-up location
        <small>view & update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">Warehouse</span>
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="my_datatable">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Title</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Alt. Mobile</th>
                                <th>Status</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('warehouse-manage', 'warehouses');

            var url = 'warehouses/data';
            loadDataTable(url);
        });

        function loadDataTable(url){
            // $('#my_datatable').DataTable();
            $('#my_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                // "ajax": url,
                "ajax": {
                    url: url,
                    type: 'POST'
                  },
                "responsive": true,
                // "aaSorting": [[1, 'desc']],
                "aaSorting": [],
                // "bFilter": false,
                "bDestroy": true
            });
        }
    </script>

@endsection
