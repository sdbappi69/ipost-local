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
                <span>Settings</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Settings
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-info-circle"></i> {!! $title !!}

                    <a href="{!! secure_url('settings/1/edit') !!}" class="pull-right text-danger">
                        <i class="fa fa-edit"></i> Update Inforamtion
                    </a>
                </div>
                <div class="panel-body small">
                    <table class="table table-bordered table-striped table-responsive">
                        <tobdy>
                            <tr>
                                <th>Title</th>
                                <td>{!! $settings->title !!}</td>
                            </tr>
                            <tr>
                                <th>Email Address</th>
                                <td>{!! $settings->email !!}</td>
                            </tr>
                            <tr>
                                <th>Website</th>
                                <td>{!! $settings->website !!}</td>
                            </tr>
                            <tr>
                                <th>Contact No.</th>
                                <td>{!! $settings->msisdn . ', '. $settings->alt_msisdn !!}</td>
                            </tr>
                            <tr>
                                <th>Address 1</th>
                                <td>{!! $settings->address1 !!}</td>
                            </tr>
                            <tr>
                                <th>Address 1</th>
                                <td>{!! $settings->address2 !!}</td>
                            </tr>
                            <tr>
                                <th>Tier</th>
                                <td>{!! $settings->zone->name. ' - '. $settings->zone->zip_code !!}</td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td>{!! $settings->city->name !!}</td>
                            </tr>
                            <tr>
                                <th>State/Division</th>
                                <td>{!! $settings->state->name !!}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{!! $settings->country->name !!}</td>
                            </tr>
                            <tr>
                                <th>Latitude</th>
                                <td>{!! $settings->latitude !!}</td>
                            </tr>
                            <tr>
                                <th>Longitude</th>
                                <td>{!! $settings->longitude !!}</td>
                            </tr>

                        </tobdy>
                    </table>
                </div>
                <div class="panel-footer">
                    <p class="text-center text-info">
                        Above information will reflect on each and every page of the application.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <p>
                <img src="{!! $settings->logo !!}" alt="Loading...">
            </p>
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('settings', 'settings');
        });
    </script>
@endsection