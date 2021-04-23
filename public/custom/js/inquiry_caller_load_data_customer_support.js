$(document).ready(function() {
	$("#calling_number").on('change', function () {
		$('#customer_name').val(null);
		$('#customer_email').val(null);
		$('#customer_address').val(null);
		$('#customer_alt_number').val(null);
		$('#company_name').val(null);
		var calling_number = $(this).val();
		// $('#custom_inquiry_msg_to_load_existing_caller').text(calling_number);
		$.post("inquiry/get-caller-info",
		{
			calling_number: calling_number,
		},
		function(data, status){
			data = JSON.parse(data);
			if(data == null){
				$('#custom_inquiry_msg_to_load_existing_caller').text('New caller');
			}
			else{
				$('#custom_inquiry_msg_to_load_existing_caller').text('Existing caller');
				$('#customer_name').val(data.customer_name);
				$('#customer_email').val(data.customer_email);
				$('#customer_address').val(data.customer_address);
				$('#customer_alt_number').val(data.customer_alt_number);
				$('#company_name').val(data.company_name);
			}
		});
	});
});