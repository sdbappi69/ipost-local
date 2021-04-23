<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<div id="addll"></div>
	<div id="add"></div>
	<input type="text" id="map-search" style="margin-top: 10px; height: 25px; width: 400px;">
	<div id="map_canvas" class="col-md-12" style="height: 450px; margin: 0.6em;"></div>
	
<script src="http://localhost/ipost/public/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="http://maps.google.com/maps/api/js?libraries=places&region=uk&language=en&sensor=true&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM"></script>
<script>
  $(function () {
    var coords = "23.847904593975734,90.27253100444796,23.84931763363798,90.28102824260714,23.845078468436682,90.28102824260714,23.84429342262976,90.2800841050339,23.842958833847405,90.28034159709932,23.84272331675373,90.27871081401827,23.843429866751162,90.27776667644503,23.842487799232217,90.27338931133272";
    var points = coords.split(',');
    var paths = [];
    for (var i in points) {
      if(i % 2 == 1)
        continue;
      paths.push({lat: parseFloat(points[i++]), lng: parseFloat(points[i])});
    }

    var image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png',
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
        position = new google.maps.LatLng(23.843283, 90.279312);
        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

        polygon = new google.maps.Polygon({
            paths: paths,
            strokeColor: '#5f5f5f',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillOpacity: 0
          });

        $('#add').text(JSON.stringify(polygon));
        
    polygon.setMap(map);
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
    polygon.addListener('click', function (event) {

      alert(google.maps.geometry.poly.containsLocation( event.latLng, polygon ));

      if(google.maps.geometry.poly.containsLocation(event.latLng, polygon)){
        var lat = event.latLng.lat(),
            lng = event.latLng.lng(),
            latlng = new google.maps.LatLng(lat, lng);
        marker.setIcon(image);
        marker.setPosition(latlng);
        $('.MapLat').val(lat);
        $('.MapLon').val(lng);
      }
    });

    var geocoder = new google.maps.Geocoder();
    google.maps.event.addListener(map, 'click', function(event) {
      // $('#addll').text(event.latLng);
      marker.setPosition(event.latLng);
      geocoder.geocode({
        'latLng': event.latLng
      }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
            // alert(results[0].formatted_address);
            // console.log(results[0]);
            // $('#add').text(JSON.stringify(results[0]));
          }
        }
      });
    });


    /*PLACE SEARCH*/
    var input = document.getElementById('map-search');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
      searchBox.setBounds(map.getBounds());
    });

    var markers = [];
    searchBox.addListener('places_changed', function() {
              var places = searchBox.getPlaces();

              if (places.length == 0) {
                return;
              }

              // Clear out the old markers.
              markers.forEach(function(marker) {
                marker.setMap(null);
              });
              markers = [];

              // For each place, get the icon, name and location.
              var bounds = new google.maps.LatLngBounds();
              places.forEach(function(place) {
                if (!place.geometry) {
                  console.log("Returned place contains no geometry");
                  return;
                }
                var icon = {
                  url: place.icon,
                  size: new google.maps.Size(71, 71),
                  origin: new google.maps.Point(0, 0),
                  anchor: new google.maps.Point(17, 34),
                  scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                markers.push(new google.maps.Marker({
                  map: map,
                  icon: icon,
                  title: place.name,
                  position: place.geometry.location
                }));

                if (place.geometry.viewport) {
                  // Only geocodes have viewport.
                  bounds.union(place.geometry.viewport);
                } else {
                  bounds.extend(place.geometry.location);
                }
              });
              map.fitBounds(bounds);
            });

  });
</script>

</body>
</html>