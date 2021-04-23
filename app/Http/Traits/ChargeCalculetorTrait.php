<?php

namespace App\Http\Traits;

use App\Zone;
use App\Charge;
use App\Discount;
use App\IpostCharge;
use App\TripMap;

trait ChargeCalculetorTrait {

    public function get_weight_charge_model_id($weight) {
        if ($weight <= 0.5) {
            return 5;
        } else if ($weight <= 1) {
            return 3;
        } else {
            return 4;
        }
    }

    public function get_zone_genre_id($pickup_zone_id, $delivery_zone_id) {

        $pickup_detail = Zone::whereStatus(true)->where('id', '=', $pickup_zone_id)->first();
        $delivery_detail = Zone::whereStatus(true)->where('id', '=', $delivery_zone_id)->first();

        if ($pickup_detail->city_id == $delivery_detail->city_id) {
            $zone_genre_id = '1';
        } else if ($pickup_detail->city->state_id == $delivery_detail->city->state_id) {
            $zone_genre_id = '2';
        } else if ($pickup_detail->city->divisional_city == 1 && $delivery_detail->city->divisional_city == 1) {
            $zone_genre_id = '3';
        } else {
            $zone_genre_id = '4';
        }

        return $zone_genre_id;
    }

    public function get_charge($store_id, $product_category_id, $zone_genre_id, $weight_charge_model_id) {
        $charge = Charge::whereStatus(true)
                ->where('store_id', $store_id)
                ->where('product_category_id', $product_category_id)
                ->where('zone_genre_id', $zone_genre_id)
                ->where('charge_model_id', 2)
                ->first();

        if ($charge == null) {

            $charge = Charge::whereStatus(true)
                    ->where('store_id', $store_id)
                    ->where('product_category_id', $product_category_id)
                    ->where('zone_genre_id', $zone_genre_id)
                    ->where('charge_model_id', $weight_charge_model_id)
                    ->first();

            if ($charge == null) {

                $charge = Charge::whereStatus(true)
                        ->where('store_id', null)
                        ->where('product_category_id', $product_category_id)
                        ->where('zone_genre_id', $zone_genre_id)
                        ->where('charge_model_id', 2)
                        ->first();

                if ($charge == null) {

                    $charge = Charge::whereStatus(true)
                            ->where('store_id', null)
                            ->where('product_category_id', $product_category_id)
                            ->where('zone_genre_id', $zone_genre_id)
                            ->where('charge_model_id', $weight_charge_model_id)
                            ->first();
                }
            }
        }

        return $charge;
    }

    public function get_open_charge($product_category_id, $zone_genre_id, $weight_charge_model_id) {
        // return $weight_charge_model_id;
        $charge = Charge::whereStatus(true)
                ->where('product_category_id', $product_category_id)
                ->where('zone_genre_id', $zone_genre_id)
                ->where('charge_model_id', 2)
                ->first();

        if ($charge == null) {

            $charge = Charge::whereStatus(true)
                    ->where('product_category_id', $product_category_id)
                    ->where('zone_genre_id', $zone_genre_id)
                    ->where('charge_model_id', $weight_charge_model_id)
                    ->first();
        }

        return $charge;
    }

    public function get_delivery_charge($charge, $weight) {

        switch ($charge->charge_model_id) {
            case '2':
                // Fixed
                $delivery_charge = $charge->fixed_charge;
                break;

            case '3':
                // 0.5 to 1 Kg
                if ($weight <= 1) {
                    $delivery_charge = $charge->fixed_charge;
                } else {
                    $additionalWeight = $weight - 1;
                    if ($additionalWeight <= 0 || $charge->additional_range_per_slot == 0) {
                        $additionalUnits = 0;
                    } else {
                        $additionalUnits = $additionalWeight / $charge->additional_range_per_slot;
                    }
                    if ($charge->additional_charge_type == 1) {
                        $additionalUnits = ceil($additionalUnits);
                    }
                    $delivery_charge = $charge->fixed_charge + ($charge->additional_charge_per_slot * $additionalUnits);
                }
                break;

            case '4':
                // 1 to 2 Kg
                if ($weight <= 2) {
                    $delivery_charge = $charge->fixed_charge;
                } else {
                    $additionalWeight = $weight - 2;
                    if ($additionalWeight <= 0 || $charge->additional_range_per_slot == 0) {
                        $additionalUnits = 0;
                    } else {
                        $additionalUnits = $additionalWeight / $charge->additional_range_per_slot;
                    }
                    if ($charge->additional_charge_type == 1) {
                        $additionalUnits = ceil($additionalUnits);
                    }
                    $delivery_charge = $charge->fixed_charge + ($charge->additional_charge_per_slot * $additionalUnits);
                }
                break;

            case '5':
                // 0 to 0.5 Kg
                if ($weight <= 0.5) {
                    $delivery_charge = $charge->fixed_charge;
                } else {
                    $additionalWeight = $weight - 0.5;
                    if ($additionalWeight <= 0 || $charge->additional_range_per_slot == 0) {
                        $additionalUnits = 0;
                    } else {
                        $additionalUnits = $additionalWeight / $charge->additional_range_per_slot;
                    }
                    if ($charge->additional_charge_type == 1) {
                        $additionalUnits = ceil($additionalUnits);
                    }
                    $delivery_charge = $charge->fixed_charge + ($charge->additional_charge_per_slot * $additionalUnits);
                }
                break;

            default:
                // Defult
                $delivery_charge = 0;
                break;
        }

        return $delivery_charge;
    }

    public function get_discount($charge, $store_id, $product_category_id) {

        $today = date("Y-m-d h:i:s");

        $discount = Discount::whereStatus(true)
                ->where('store_id', $store_id)
                ->where('product_category_id', $product_category_id)
                ->where('from_date', '<=', $today)
                ->where('to_date', '>=', $today)
                ->first();

        if ($discount == null) {
            $discount = Discount::whereStatus(true)
                    ->where('store_id', $store_id)
                    ->where('product_category_id', null)
                    ->where('from_date', '<=', $today)
                    ->where('to_date', '>=', $today)
                    ->first();

            if ($discount == null) {
                $discount = Discount::whereStatus(true)
                        ->where('store_id', null)
                        ->where('product_category_id', $product_category_id)
                        ->where('from_date', '<=', $today)
                        ->where('to_date', '>=', $today)
                        ->first();

                if ($discount == null) {
                    $discount = Discount::whereStatus(true)
                            ->where('store_id', null)
                            ->where('product_category_id', null)
                            ->where('from_date', '<=', $today)
                            ->where('to_date', '>=', $today)
                            ->first();
                }
            }
        }

        return $discount;
    }

    public function get_discount_data($discount, $delivery_charge, $weight) {

        if ($discount->discount_type == 'fixed' && $discount->unit_type == 'bdt' && $discount->start_unit <= $delivery_charge && $discount->end_unit >= $delivery_charge) {

            $delivery_discount_id = $discount->id;
            $delivery_discount = $discount->discount_value;
            $discount_title = $discount->discount_title;
        } else if ($discount->discount_type == 'fixed' && $discount->unit_type == 'kg' && $discount->start_unit <= $weight && $discount->end_unit >= $weight) {

            $delivery_discount_id = $discount->id;
            $delivery_discount = $discount->discount_value;
            $discount_title = $discount->discount_title;
        } else if ($discount->discount_type == 'percentage' && $discount->unit_type == 'bdt' && $discount->start_unit <= $delivery_charge && $discount->end_unit >= $delivery_charge) {

            $delivery_discount_id = $discount->id;
            if ($discount->discount_value == 0 || $delivery_charge == 0) {
                $delivery_discount = 0;
            } else {
                $delivery_discount = ($discount->discount_value / 100) * $delivery_charge;
            }
            $discount_title = $discount->discount_title;
        } else if ($discount->discount_type == 'percentage' && $discount->unit_type == 'kg' && $discount->start_unit <= $weight && $discount->end_unit >= $weight) {

            $delivery_discount_id = $discount->id;
            if ($discount->discount_value == 0 || $delivery_charge == 0) {
                $delivery_discount = 0;
            } else {
                $delivery_discount = ($discount->discount_value / 100) * $delivery_charge;
            }
            $discount_title = $discount->discount_title;
        } else {
            $delivery_discount_id = 0;
            $delivery_discount = 0;
            $discount_title = '';
        }

        return $delivery_discount_data = array('delivery_discount_id' => $delivery_discount_id, 'delivery_discount' => $delivery_discount, 'discount_title' => $discount_title);
    }

    public function store_charge($category_id, $store_id, $pickup_zone, $delivery_zone, $weight) {
        $store_charge = IpostCharge::whereProductCategoryId($category_id)->whereStoreId($store_id)->whereStatus(1)->whereApproved(1)->first();

        if ($store_charge) {
            if ($store_charge->charge_type == 'Fixed') {
                $fixed_charge = $store_charge;
                return [
                    'status' => 'Success',
                    'status_code' => 200,
                    'message' => [],
                    'data' => [
                        'charge_type' => 'Fixed',
                        'initial_charge' => $fixed_charge->initial_charge,
                        'hub_transfer_charge' => $fixed_charge->hub_transfer_charge,
                    ]
                ];
            }

            $weight_charge = IpostCharge::whereProductCategoryId($category_id)
                    ->whereStoreId($store_id)
                    ->where('min_weight', '<=', $weight)
                    ->where('max_weight', '>=', $weight)
                    ->whereStatus(1)->whereApproved(1)
                    ->first();
            if ($weight_charge) {
                return [
                    'status' => 'Success',
                    'status_code' => 200,
                    'message' => [],
                    'data' => [
                        'charge_type' => 'Weight Based',
                        'initial_charge' => $weight_charge->initial_charge,
                        'hub_transfer_charge' => $weight_charge->hub_transfer_charge,
                    ]
                ];
            } else {
                return [
                    'status' => 'Fail',
                    'status_code' => 404,
                    'message' => ['Charge not found for this weight.'],
                    'data' => []
                ];
            }
        }

        $default_charge = IpostCharge::whereProductCategoryId($category_id)->whereStatus(1)->whereApproved(1)->first();

        if ($default_charge) {
            if ($default_charge->charge_type == 'Fixed') {
                $fixed_charge = $default_charge;
                return [
                    'status' => 'Success',
                    'status_code' => 200,
                    'message' => [],
                    'data' => [
                        'charge_type' => 'Fixed',
                        'initial_charge' => $fixed_charge->initial_charge,
                        'hub_transfer_charge' => $fixed_charge->hub_transfer_charge,
                    ]
                ];
            }

            $weight_charge = IpostCharge::whereProductCategoryId($category_id)
                    ->where('min_weight', '<=', $weight)
                    ->where('max_weight', '>=', $weight)
                    ->whereStatus(1)->whereApproved(1)
                    ->first();
            if ($weight_charge) {
                return [
                    'status' => 'Success',
                    'status_code' => 200,
                    'message' => [],
                    'data' => [
                        'charge_type' => 'Weight Based',
                        'initial_charge' => $weight_charge->initial_charge,
                        'hub_transfer_charge' => $weight_charge->hub_transfer_charge,
                    ]
                ];
            } else {
                return [
                    'status' => 'Fail',
                    'status_code' => 404,
                    'message' => ['Charge not found for this weight.'],
                    'data' => []
                ];
            }
        }

        return [
            'status' => 'Fail',
            'status_code' => 404,
            'message' => ['Charge not found for this category.'],
            'data' => []
        ];
    }

    public function hub_number($pickup_zone_id, $delivery_zone_id) {
        $picking_zone = Zone::find($pickup_zone_id);
        $delivery_zone = Zone::find($delivery_zone_id);

        if ($picking_zone->hub_id == $delivery_zone->hub_id) {
            return 0;
        }

//        $hub_transit = TripMap::whereStartHubId($picking_zone->hub_id)->whereEndHubId($delivery_zone->hub_id)->count();

        $trip = array();
        $tripMap = TripMap::join('hubs as ph', 'ph.id', '=', 'trip_map.start_hub_id')
                        ->join('hubs as th', 'th.id', '=', 'trip_map.hub_id')
                        ->join('hubs as dh', 'dh.id', '=', 'trip_map.end_hub_id')
                        ->select('ph.id as picking_hub_id', 'ph.title as picking_hub', 'th.id as transit_hub_id', 'th.title as transit_hub', 'dh.id as delivery_hub_id', 'dh.title as delivery_hub')
                        ->where('trip_map.status', 1)
                        ->orderBy('priority', 'asc')->get();

        if (count($tripMap) > 0) {
            $i = 0;
            $j = 0;
            foreach ($tripMap as $index => $val) {
                if ($j == 0) {
                    $trip[$i]['start_hub_id'] = $val->picking_hub_id;
                    $trip[$i]['start_hub'] = $val->picking_hub;
                    $trip[$i]['end_hub_id'] = $val->transit_hub_id;
                    $trip[$i]['end_hub'] = $val->transit_hub;
                    $trip[$i]['serial'] = 1;
                    $i++;
                } 
                if($j != 0) {
                    $trip[$i]['start_hub_id'] = $tripMap[$index - 1]->transit_hub_id;
                    $trip[$i]['start_hub'] = $tripMap[$index - 1]->transit_hub;
                    $trip[$i]['end_hub_id'] = $val->transit_hub_id;
                    $trip[$i]['end_hub'] = $val->transit_hub;
                    $trip[$i]['serial'] = $j + 1;
                    $i++;
                }
                if (($j + 1) == count($tripMap)) {
                    $trip[$i]['start_hub_id'] = $val->transit_hub_id;
                    $trip[$i]['start_hub'] = $val->transit_hub;
                    $trip[$i]['end_hub_id'] = $val->delivery_hub_id;
                    $trip[$i]['end_hub'] = $val->delivery_hub;
                    $trip[$i]['serial'] = $j + 1;
                    $i++;
                } 
                $j++;
            }
        }

        return [
            'totalHub' => count($tripMap) + 1,
            'trip' => $trip
        ];
    }

}
