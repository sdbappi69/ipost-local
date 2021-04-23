function dayOfWeek(date) {
    var dayNames = new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
    var nData = new Date(date);
    return dayNames[nData.getDay()];
}

function pick_up_slot(day){
	$('#picking_time_slot_id').html('<option selected="selected" value="">Loading...</option>');
	var url = site_path + 'pick-up-slot/' + day;

	$.getJSON(url+'?callback=?',function(data){
		$("#picking_time_slot_id").attr('required', true);

		$('#picking_time_slot_id').html('<option selected="selected" value="">Select Pick Time</option>');
		$.each(data, function(key, item) {
		    $('#picking_time_slot_id').append('<option value="'+item.id+'">'+item.title+'</option>');
		});
	});
}

function pick_up_slot_by_id(id,day){
	$('#picking_time_slot_id_'+id).html('<option selected="selected" value="">Loading...</option>');
	var url = site_path + 'pick-up-slot/' + day;

	$.getJSON(url+'?callback=?',function(data){
		$("#picking_time_slot_id_"+id).attr('required', true);

		$('#picking_time_slot_id_'+id).html('<option selected="selected" value="">Select Pick Time</option>');
		$.each(data, function(key, item) {
			if(id == item.id){
				var selected = 'selected="selected"';
			}else{
				var selected = '';
			}
		    $('#picking_time_slot_id_'+id).append('<option value="'+item.id+'" '+selected+'>'+item.title+'</option>');
		});
	});
}