@extends('layouts.appinside')

@section('content')

    <link href="{{ secure_asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ secure_url('user') }}">Merchant</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>View</span>
            </li>
        </ul>
        <div class="page-toolbar">
            <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i>&nbsp;
                <span class="thin uppercase hidden-xs"></span>&nbsp;
                <i class="fa fa-angle-down"></i>
            </div>
        </div>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Merchant
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">

                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet ">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="{{ $merchant->photo }}" class="img-responsive" alt=""> </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ $merchant->name }} </div>
                        <div class="profile-usertitle-job"> {{ $merchant->website }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <div class="profile-userbuttons">
                        @if($merchant->status == 1)
                            <p class="btn btn-circle green btn-sm">Active</p>
                        @else
                            <p class="btn btn-circle red btn-sm">Inactive</p>
                        @endif
                    </div>
                    <!-- END SIDEBAR BUTTONS -->
                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li>
                                <a href="{{ secure_url('merchant/'.$merchant->id.'/edit') }}">
                                <i class="icon-pencil"></i> Update </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->

            </div>

            <!-- PORTLET MAIN -->
            <div class="profile-sidebar">
            
                <div class="portlet light ">
                    <div>
                        <h4 class="profile-desc-title">Contact</h4>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-envelope"></i>
                            <a href="mailto:{{ $merchant->email }}">{{ $merchant->email }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $merchant->msisdn }}">{{ $merchant->msisdn }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $merchant->alt_msisdn }}">{{ $merchant->alt_msisdn }}</a>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET MAIN -->

                <div class="portlet light ">
                    <div class="row list-separated profile-stat">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <div class="uppercase profile-stat-title"> {{ $merchant->billing_date }} </div>
                          <div class="uppercase profile-stat-text"> Billing Date </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <div class="uppercase profile-stat-title"> {{ $merchant->due_date }} </div>
                          <div class="uppercase profile-stat-text"> Due Date </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET MAIN -->

            </div>

            <!-- PORTLET MAIN -->
            <div class="profile-sidebar">
            
                <div class="portlet light ">
                    <div>
                        <h4 class="profile-desc-title">Location</h4>
                        <span class="profile-desc-text">
                            <table width="100%">
                               <tr>
                                   <td>Country</td>
                                   <td>:</td>
                                   <td>{{ $merchant->country }}</td>
                               </tr> 
                               <tr>
                                   <td>State</td>
                                   <td>:</td>
                                   <td>{{ $merchant->state }}</td>
                               </tr> 
                               <tr>
                                   <td>City</td>
                                   <td>:</td>
                                   <td>{{ $merchant->city }}</td>
                               </tr> 
                               <tr>
                                   <td>Zone</td>
                                   <td>:</td>
                                   <td>{{ $merchant->zone }}</td>
                               </tr>
                               <tr>
                                   <td>Address A</td>
                                   <td>:</td>
                                   <td>{{ $merchant->address1 }}</td>
                               </tr>
                               <tr>
                                   <td>Address B</td>
                                   <td>:</td>
                                   <td>{{ $merchant->address2 }}</td>
                               </tr>
                            </table>
                        </span>
                    </div>
                </div>
                <!-- END PORTLET MAIN -->

            </div>

        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-manage', 'merchants');
        });
    </script>

@endsection
