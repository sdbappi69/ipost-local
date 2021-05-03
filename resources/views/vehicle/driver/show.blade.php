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
                <span>Drivers</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Drivers
        <small> show</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-info-circle"></i> {!! $title !!}

                    <a href="{!! url('/driver') !!}" class="pull-right">
                        <i class="fa fa-backward"></i> Back To Listing
                    </a>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped table-responsive">
                        <tobdy>
                            <tr>
                                <th>Name</th>
                                <td>{!! $driver->name !!}</td>
                            </tr>
                            <tr>
                                <th>Job Type</th>
                                <td>{!! $driver->job_type !!}</td>
                            </tr>
                            <tr>
                                <th>Contact No.</th>
                                <td>{!! $driver->contact_msisdn. ' ,'. $driver->contact_alt_msisdn !!}</td>
                            </tr>
                            <tr>
                                <th>Email Address</th>
                                <td>{!! $driver->contact_email. ' , '.$driver->contact_alt_email !!}</td>
                            </tr>
                            <tr>
                                <th>Ref. Name</th>
                                <td>{!! $driver->reference_name !!}</td>
                            </tr>
                            <tr>
                                <th>Ref. Contact No.</th>
                                <td>{!! $driver->reference_msisdn. ' , '. $driver->reference_alt_msisdn !!}</td>
                            </tr>
                            <tr>
                                <th>Ref. Email Address</th>
                                <td>{!! $driver->reference_email. ' , '.$driver->reference_alt_email !!}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{!! $driver->created_at->format('Y-m-d H:i:s') !!}</td>
                            </tr>
                            <tr>
                                <th>Last Updated At</th>
                                <td>{!! $driver->updated_at->format('Y-m-d H:i:s') !!}</td>
                            </tr>

                        </tobdy>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <p>
                <img src="{!! $driver->photo !!}" alt="Loading..." class="img-responsive">
            </p>
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('drivers', 'drivers');
        });
    </script>
@endsection