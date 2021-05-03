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
                <a href="{{ secure_url('hub') }}">Rack</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>View</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Rack
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
               <div class="portlet light ">
                   <div>
                       <h4 class="profile-desc-title">Source</h4>
                       <span class="profile-desc-text">
                           <div class="table-responsive">
                             <table class="table">
                                <tr>
                                    <th>Hub</th>
                                    <td>:</td>
                                    <td>{{ $rack->get_hub->title }}</td>
                                </tr>
                             </table>
                           </div>
                       </span>
                   </div>
               </div>
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
                                     <th>Zone</th>
                                     <td>:</td>
                                     <td>{{ $rack->get_zone['name'] }}</td>
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
                       <h4 class="profile-desc-title">Dimension</h4>
                       <span class="profile-desc-text">
                           <div class="table-responsive">
                             <table class="table">
                                <tr>
                                    <th>Width</th>
                                    <td>:</td>
                                    <td>{{ $rack->width }}</td>
                                </tr>
                                <tr>
                                    <th>Height</th>
                                    <td>:</td>
                                    <td>{{ $rack->height }}</td>
                                </tr>
                                <tr>
                                    <th>Length</th>
                                    <td>:</td>
                                    <td>{{ $rack->length }}</td>
                                </tr>
                             </table>
                           </div>
                       </span>
                   </div>
               </div>
            </div>



        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('rack-manage', 'racks');
        });
    </script>

@endsection
