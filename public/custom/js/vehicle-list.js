function get_vehicles(vehicle_type_id){
	// alert(site_path);
	$('#vehicle_id').html('<option selected="selected" value="">Loading...</option>');
	// return site_path;
	var url = site_path + 'vehicle/vehicle/'+vehicle_type_id;

	$.getJSON(url+'?callback=?',function(data){

		$('#vehicle_id').html('<option selected="selected" value="">Select Vehicle</option>');
		$.each(data, function(key, item) { 
		    $('#vehicle_id').append('<option value="'+item.id+'">'+item.name+'</option>');
		});

	});
}

$(".js-example-basic-single").select2({
    // placeholder : 'SELECT ONE',
    // allowClear : true
}).trigger('change');