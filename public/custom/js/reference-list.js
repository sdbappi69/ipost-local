function get_reference(user_type_id, reference_id) {
    $('#reference_id').html('<option selected="selected" value="">Loading...</option>');
    var url = site_path + 'reference/' + user_type_id;

    $.getJSON(url + '?callback=?', function (data) {
        if (data == null) {
            $("#reference_id").attr('required', false);
            $(".reference-area").hide();
        } else {
            $("#reference_id").attr('required', true);
            $("#rider_reference_id").attr('required', false);
            $(".rider-reference-area").hide();

            $('#reference_id').html('<option selected="selected" value="">Select Reference</option>');
            $.each(data, function (key, item) {

                if (reference_id == item.id) {
                    var selected = 'selected="selected"';
                } else {
                    var selected = '';
                }
                $('#reference_id').append('<option value="' + item.id + '" ' + selected + '>' + item.title + '</option>');
            });
            $(".reference-area").show();
        }
    });
}

function get_rider_reference(user_type_id, reference_ids = null) {
    $('#rider_reference_id').html('<option selected="selected" value="">Loading...</option>');
    var url = site_path + 'reference/' + user_type_id;

    $.getJSON(url + '?callback=?', function (data) {
        if (data == null) {
            $("#rider_reference_id").attr('required', false);
            $(".rider-reference-area").hide();
        } else {
            $("#rider_reference_id").attr('required', true);
            $("#reference_id").attr('required', false);
            $(".reference-area").hide();


            $('#rider_reference_id').html('<option value="">Select Reference</option>');
            $.each(data, function (key, item) {

                if (jQuery.inArray(item.id, reference_ids) !== -1) {
                    var selected = 'selected="selected"';
                } else {
                    var selected = '';
                }
                $('#rider_reference_id').append('<option value="' + item.id + '" ' + selected + '>' + item.title + '</option>');
            });
            $(".rider-reference-area").show();
        }
    });
}