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
                <a href="{{ secure_url('hub') }}">Hubs</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>View</span>
            </li>
        </ul>
        <a class="btn btn-warning pull-right" href="{{ secure_url('hub/'. $hub->id .'/edit') }}">Edit</a>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Hub
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
                        <div class="profile-usertitle-name"> {{ $hub->title }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <div class="profile-userbuttons">
                        {{ $hub->details }}
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
                        <h4 class="profile-desc-title">Contact</h4>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-envelope"></i>
                            <a href="mailto:{{ $hub->email }}">{{ $hub->responsible_user->email or '' }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $hub->msisdn }}">{{ $hub->msisdn }}</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:{{ $hub->alt_msisdn }}">{{ $hub->alt_msisdn }}</a>
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
                            <dl>
<!--                                <dt>Zone</dt>
                                <dd>{{ $hub->zone->name or '' }}</dd>-->
                                <dt>Address</dt>
                                <dd>{{ $hub->address1 or '' }}</dd>
                            </dl>
                        </span>
                    </div>
                </div>
                <!-- END PORTLET MAIN -->
            </div>
        </div>
    </div>

    <div class="row">
{{--         <div class="col-md-12 well">
            <h4 class="profile-desc-title">Location</h4>
            <span class="profile-desc-text">
                <dl>
                    <dt>Zone</dt>
                    <dd>{{ $hub->zone->name or '' }}</dd>
                    <dt>Address</dt>
                    <dd>{{ $hub->address1 or '' }}</dd>
                </dl>
            </span>
 --}}            <div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>                                
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('hub-manage', 'hubs');
        });
    </script>
    <script src="https://maps.google.com/maps/api/js?libraries=places&region=uk&language=en&sensor=true&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>
    <script>
        $(function () {
          var image = 'https://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png',
              bounds = new google.maps.LatLngBounds();
              mapOptions = {
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                panControl: true,
                panControlOptions: {
                  position: google.maps.ControlPosition.TOP_RIGHT
                },
                zoomControl: true,
                zoomControlOptions: {
                  style: google.maps.ZoomControlStyle.LARGE,
                  position: google.maps.ControlPosition.TOP_left
                }
              },
              position = new google.maps.LatLng({{ $hub->latitude }}, {{ $hub->longitude }});
              map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

          bounds.extend(position),
          map.fitBounds(bounds);

          var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
                this.setZoom(14);
                google.maps.event.removeListener(boundsListener);
              });

          marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: image
          });
        });
    </script>
@endsection
