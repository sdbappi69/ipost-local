@extends('layouts.appinside')

@section('content')
	
	<!-- BEGIN MARKERS PORTLET-->
	 <div class="col-md-12">
        <div class="portlet light portlet-fit bordered">
            <!-- <form class="form-inline margin-bottom-10" action="#"> -->
                <div class="input-group">
                    <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="address...">
                    <span class="input-group-btn">
                        <button class="btn blue" id="gmap_geocoding_btn">
                            <i class="fa fa-search"></i>
                        <!-- </a> -->
                    </span>
                </div>
            <!-- </form> -->
            <div id="gmap_geocoding" class="gmaps" style="height: 500px;"> </div>
        </div>
    </div>
        <!-- END MARKERS PORTLET-->

	<script src="
https://maps.google.com/maps/api/js?key=AIzaSyCBWhNYtf2cofZBppq9lfBqzGpJDjLBc4g&callback=initMap&sensor=false" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/gmaps/gmaps.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('custom/js/maps-google-geo.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
        $(document ).ready(function() {
            // Close Navigation
            close_nav();
            $("#gmap_geocoding_address").val({{ $latitude }}+', '+{{ $longitude }});
			$("#gmap_geocoding_btn").trigger("click");
        });
    </script>
@endsection