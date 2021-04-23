function get_states(country_id) {
    $('#state_id').html('<option selected="selected" value="">Loading...</option>');
    var url = site_path + 'location/states/' + country_id;

    $.getJSON(url + '?callback=?', function (data) {

        $('#state_id').html('<option selected="selected" value="">Select State</option>');
        $.each(data, function (key, item) {
            $('#state_id').append('<option value="' + item.id + '">' + item.name + '</option>');
        });

        $('.state_id').html('<option selected="selected" value="">Select State</option>');
        $.each(data, function (key, item) {
            $('.state_id').append('<option value="' + item.id + '">' + item.name + '</option>');
        });

    });
}

function get_cities(state_id) {
    var url = site_path + 'location/cities/' + state_id;

    $.getJSON(url + '?callback=?', function (data) {

        $('#city_id').html('<option selected="selected" value="">Select City</option>');
        $.each(data, function (key, item) {
            $('#city_id').append('<option value="' + item.id + '">' + item.name + '</option>');
        });

        $('.city_id').html('<option selected="selected" value="">Select City</option>');
        $.each(data, function (key, item) {
            $('.city_id').append('<option value="' + item.id + '">' + item.name + '</option>');
        });

    });
}

function get_zones(city_id) {
    var url = site_path + 'location/zones/' + city_id;

    $.getJSON(url + '?callback=?', function (data) {

        $('#zone_id').html('<option selected="selected" value="">Select Zone</option>');
        $.each(data, function (key, item) {
            $('#zone_id').append('<option value="' + item.id + '">' + item.name + '</option>');
        });

        $('.zone_id').html('<option selected="selected" value="">Select Zone</option>');
        $.each(data, function (key, item) {
            $('.zone_id').append('<option value="' + item.id + '">' + item.name + '</option>');
        });

    });
}

function get_zone_bound(lat, lng) {
    var url = site_path + 'location/zonebound/' + lat + '/' + lng;
    console.log(url);
    $.getJSON(url + '?callback=?', function (data) {
        $('#zone_id').html('<option value="">Select Zone</option>');
        if (data != null)
            $('#zone_id').append('<option selected="selected" value="' + data.id + '">' + data.name + '</option>');
    });
}

function set_zone_id(lat, lng) {
    var url = site_path + 'location/zonebound/' + lat + '/' + lng;
    $.getJSON(url + '?callback=?', function (data) {
        console.log(data);
        if (data != null)
            $('#zone_id').val(data.id);
    });
}

$(".js-example-basic-single").select2({
    // placeholder : 'SELECT ONE',
    // allowClear : true
}).trigger('change');