<?php

function calculate_cod_charge($store_id, $cod_amount) {

    $temp = \App\Charge::where('charge_model_id', 1)->where('store_id', $store_id)->where('status', 1)->orderBy('id', 'desc')->first();

    if (!count((array)$temp) > 0) {
        $temp = \App\Charge::where('charge_model_id', 1)->where('store_id', '=', null)->where('status', 1)->orderBy('id', 'desc')->first();
    }
    //dd($temp->toArray());
    $cod_charge = 0;

    if ($cod_amount >= $temp->percentage_range_start and $cod_amount <= $temp->percentage_range_end) {

        $cod_charge = ($temp->percentage_value * $cod_amount) / 100;
        //dd($cod_charge);
    } else if ($cod_amount > $temp->percentage_range_end) {

        $additional = $cod_amount - $temp->percentage_range_end;
        if ($temp->additional_charge_type == 1) {
            $additional_slote = ceil($additional / $temp->additional_range_per_slot);
            $additional_charge = $additional_slote * $temp->additional_charge_per_slot;
            $cod_charge = (($temp->percentage_value * $temp->percentage_range_end) / 100) + $additional_charge;
        } elseif ($temp->additional_charge_type == 0) {
            $additional_slote = $additional / $temp->additional_range_per_slot;
            $additional_charge = $additional_slote * $temp->additional_charge_per_slot;
            $cod_charge = (($temp->percentage_value * $temp->percentage_range_end) / 100) + $additional_charge;
        }
    } else {
        $cod_charge = 0;
    }

    $return_array ['cod_charge'] = $cod_charge;
    $return_array ['charge_model'] = json_encode($temp);

    return $return_array;
}

function get_store_name_merchant_check_out($store_id) {
    $store_id = explode(',', $store_id);
    $temp_str = '';
    foreach ($store_id as $x) {
        $temp = \App\Store::select('store_id')->where('id', $x)->first();
        $temp_str .= $temp->store_id . ',';
    }
    return rtrim($temp_str, ',');
}
