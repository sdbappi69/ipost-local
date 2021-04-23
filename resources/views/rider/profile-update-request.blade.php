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
                <span>Profile Update Request</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    {{--<h1 class="page-title"> Request
        <small>Profile Update Request</small>
    </h1>--}}
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <div class="table-filtter">
                {!! Form::open(array('method' => 'get')) !!}
                <div class="col-md-2">
                    <div class="row">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <input type="text" class="form-control" name="msisdn" id="msisdn" placeholder="Primary Contact">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <input type="text" class="form-control" name="alt_msisdn" id="alt_msisdn" placeholder="Secondary Contact">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="row">
                        {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'id' => 'status']) !!}
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
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">Profile Update Request</span>
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%">
                        <thead>
                        <tr role="row" class="heading">
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>User Type</th>
                            <th>Rider Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($users) > 0)
                            @foreach($users as $user)
                                <tr>
                                    <td><img class="table-thumb" class="img-circle" src="{{ $user->photo }}" alt=""></td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->msisdn }}</td>
                                    <td>{{ $user->display_name }}</td>
                                    @if($user->display_name == 'Rider')
                                        <td>{!! $user->status == 1 ? '<span class="label label-success"> Permanent </span>' : '<span class="label label-danger"> Freelancer </span>' !!}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{!! $user->status == 1 ? '<span class="label label-success"> Active </span>' : '<span class="label label-danger"> Inactive </span>' !!}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="rider/{{ $user->id }}/edit" class="btn btn-info">
                                                <i class="fa fa-pencil"></i> Update </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pagination pull-right">
                        {!! $users->appends($req)->render() !!}
                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('profile-update-request', 'users');

            $('#example0').DataTable({
                "order": [],
            });

            loadStoreDataTable();

            @php
                if( !empty($req) )
                {
                   foreach($req as $key => $val)
                   {
                      echo "document.getElementById('".$key."').value = '".$val."';";
                   }
                }
            @endphp

        });

        function loadStoreDataTable(){
            var url = 'users/storedata';
            $('#my_store_datatable').DataTable({
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
    </script>
    <style media="screen">
    .table-filtter .btn{ width: 100%;}
    .table-filtter {
        margin: 20px 0;
    }
    </style>

@endsection
