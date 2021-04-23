
function calculate_bulk_charge(order_id,width,height,length,weight,unit_price,pickup_location_id){

	var url = site_path + 'bulk_charge/' + order_id + '/' + width + '/' + height + '/' + length + '/' + weight + '/' + unit_price + '/' + pickup_location_id;

	$.getJSON(url+'?callback=?',function(data){ 

        $.each(data, function(key, item) {
            $("#delivery_payment_amount_package").val(item.product_delivery_charge);
            $(".total_delivery_charge").html(item.product_delivery_charge);

            $(".actual_delivery_charge").html(item.product_actual_delivery_charge);
            $(".discount_delivery_charge").html(item.product_discount+'<br><span style="font-size: 10px; color: green">'+item.delivery_discount_title+'</span>');

            $("#delivery_discount_id").val(item.delivery_discount_id);
            $("#product_actual_unit_delivery_charge").val(item.product_actual_unit_delivery_charge);
            $("#product_unit_discount").val(item.product_unit_discount);
            $("#product_unit_delivery_charge").val(item.product_unit_delivery_charge);
            $("#product_actual_delivery_charge").val(item.product_actual_delivery_charge);
            $("#product_discount").val(item.product_discount);
            $("#product_delivery_charge").val(item.product_delivery_charge);
        });

		var delivery_payment_amount = $('#delivery_payment_amount_package').val();
        var include_delivery = $('#include_delivery_package:checkbox:checked').val();

        if(include_delivery == 1){
            var max = Number($('#order_product_charge_package').val()) + Number(delivery_payment_amount);
        }else{
            var max = $('#order_product_charge_package').val();
        }

        if(Number($('#amount_package').val()) > max){
            $('#amount_package').val(max);
            var amount = max;
        }else{
            var amount = $('#amount_package').val();
        }

        collectableAmount_package(amount, delivery_payment_amount, include_delivery);

		// $('#picking_time_slot_id').html('<option selected="selected" value="">Select Pick Time</option>');
		// $.each(data, function(key, item) {
		//     $('#picking_time_slot_id').append('<option value="'+item.id+'">'+item.title+'</option>');
		// });
	});
}

// function discountReset(order_id){
//     var url = site_path + 'discount_reset/' + order_id;

//     $.getJSON(url+'?callback=?',function(data){ 
//         return data;
//     });
// }