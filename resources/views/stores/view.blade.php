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
                <a href="{{ secure_url('store') }}">Stores</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>View</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Store
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
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ $store->store_id }} / {{ $store->store_password }}</div>
                        <div class="profile-usertitle-job"> {{ $store->store_url }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <div class="profile-userbuttons">
                        Store type:<p class="btn btn-circle green btn-sm">{{ $store->store_type->title }}</p>
                    </div>
                    <br>
                    <!-- END SIDEBAR BUTTONS -->
                </div>
                <!-- END PORTLET MAIN -->

            </div>

            <!-- PORTLET MAIN -->
            <div class="profile-sidebar">
            
                <div class="portlet light ">
                    <div>
                        <h4 class="profile-desc-title">Location</h4>
                        <span class="profile-desc-text">
                            <div class="table-responsive">
                              <table class="table">
                                 <tr>
                                     <th>Country</th>
                                     <td>:</td>
                                     <td>{{ $store->merchant->zone->city->state->country->name }}</td>
                                 </tr> 
                                 <tr>
                                     <th>State</th>
                                     <td>:</td>
                                     <td>{{ $store->merchant->zone->city->state->name }}</td>
                                 </tr> 
                                 <tr>
                                     <th>City</th>
                                     <td>:</td>
                                     <td>{{ $store->merchant->zone->city->name }}</td>
                                 </tr> 
                                 <tr>
                                     <th>Zone</th>
                                     <td>:</td>
                                     <td>{{ $store->merchant->zone->name }}</td>
                                 </tr>
                                 <tr>
                                     <th>Address A</th>
                                     <td>:</td>
                                     <td>{{ $store->merchant->address1 }}</td>
                                 </tr>
                              </table>
                            </div>
                        </span>
                    </div>
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
                            <a href="mailto:{{ $store->email }}">{{ $store->merchant->email }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $store->msisdn }}">{{ $store->merchant->msisdn }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $store->alt_msisdn }}">{{ $store->merchant->alt_msisdn }}</a>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET MAIN -->
            </div>

        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('store-manage', 'stores');
        });
    </script>

@endsection
