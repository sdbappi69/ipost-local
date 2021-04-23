@extends('layouts.appinside')

@section('content')

    <link href="{{ URL::asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL::to('user') }}">User</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>View</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> User
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
                        <img src="{{ $user->photo }}" class="img-responsive" alt=""> </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ $user->name }} </div>
                        <div class="profile-usertitle-job"> {{ $user->user_type }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <div class="profile-userbuttons">
                        @if($user->status == 1)
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
                                <a href="{{ URL::to('user/'.$user->id.'/edit') }}">
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
                        <h4 class="profile-desc-title">Location</h4>
                        <span class="profile-desc-text">
                            <div class="table-responsive">
                              <table class="table">
                                 <tr>
                                     <th>Country</th>
                                     <td>:</td>
                                     <td>{{ $user->country }}</td>
                                 </tr> 
                                 <tr>
                                     <th>State</th>
                                     <td>:</td>
                                     <td>{{ $user->state }}</td>
                                 </tr> 
                                 <tr>
                                     <th>City</th>
                                     <td>:</td>
                                     <td>{{ $user->city }}</td>
                                 </tr> 
                                 <tr>
                                     <th>Zone</th>
                                     <td>:</td>
                                     <td>{{ $user->zone }}</td>
                                 </tr>
                                 <tr>
                                     <th>Address A</th>
                                     <td>:</td>
                                     <td>{{ $user->address1 }}</td>
                                 </tr>
                                 <tr>
                                     <th>Address B</th>
                                     <td>:</td>
                                     <td>{{ $user->address2 }}</td>
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
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $user->msisdn }}">{{ $user->msisdn }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $user->alt_msisdn }}">{{ $user->alt_msisdn }}</a>
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
            highlight_nav('user-manage', 'users');
        });
    </script>

@endsection
