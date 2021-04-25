<?php
namespace App\Http\Traits;

use Auth;
use DB;

use App\TripMap;
use App\SubOrderTripMap;
use App\SubOrder;
use App\SubOrderTrip;
use Illuminate\Support\Facades\Log;

trait MerchantSubOrder {


    public function merchantAllStatus() {

        $sub_order_status = [
                                '1' => 'At the hub requested',
                                '2' => 'Being Picked',
                                '3' => 'Picked',
                                '4' => 'Full order Racked at Pickup hub',
                                '5' => 'Pick up failed',
                                '6' => 'Pickup order Cancelled',
                                '7' => 'Trip in Transit',
                                '8' => 'Full order racked at Destination Hub',
                                '9' => 'In Delivery',
                                '10' => 'Product delivered to customer',
                                '11' => 'Delivery Completed',
                                '12' => 'Product delivery failed',
                                '13' => 'Product waiting to be reassigned',
                                '14' => 'Product to be returned',
                                '15' => 'Being Returned',
                                '16' => 'Return Completed',
                                '17' => 'Product return failed'
                            ];

        return $sub_order_status;

    }

    public function merchantWhereInStatus($sub_order_status){

        switch ($sub_order_status) {
            case "1":
                $whereIn = array(2);
                break;
            case "2":
                $whereIn = array(3, 4, 5);
                break;
            case "3":
                $whereIn = array(7, 8);
                break;
            case "4":
                $whereIn = array(9, 10, 11, 15, 16);
                break;
            case "5":
                $whereIn = array(6, 12);
                break;
            case "6":
                $whereIn = array(13);
                break;
            case "7":
                $whereIn = array(18, 19, 20, 21, 46);
                break;
            case "8":
                $whereIn = array(22, 26, 27);
                break;
            case "9":
                $whereIn = array(28, 29, 30);
                break;
            case "10":
                $whereIn = array(31, 32);
                break;
            case "11":
                $whereIn = array(38, 39, 41, 42, 43, 44, 45);
                break;
            case "12":
                $whereIn = array(33);
                break;
            case "13":
                $whereIn = array(34);
                break;
            case "14":
                $whereIn = array(35);
                break;
            case "15":
                $whereIn = array(36);
                break;
            case "16":
                $whereIn = array(37);
                break;
            case "17":
                $whereIn = array(40);
                break;
            default:
                $whereIn = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47);
        }

        return $whereIn;

    }

    public function merchantGetStatus($sub_order_status){
        switch ($sub_order_status) {
            case "2":
                $merchant_status_id = 1;
                break;
            case "3":
            case "4":
            case "5":
                $merchant_status_id = 2;
                break;
            case "7":
            case "8":
                $merchant_status_id = 3;
                break;
            case "9":
            case "10":
            case "11":
            case "15":
            case "16":
                $merchant_status_id = 4;
                break;
            case "6":
            case "12":
                $merchant_status_id = 5;
                break;
            case "13":
                $merchant_status_id = 6;
                break;
            case "18":
            case "19":
            case "20":
            case "21":
            case "47":
                $merchant_status_id = 7;
                break;
            case "22":
            case "26":
            case "27":
                $merchant_status_id = 8;
                break;
            case "28":
            case "29":
            case "30":
                $merchant_status_id = 9;
                break;
            case "31":
            case "32":
                $merchant_status_id = 10;
                break;
            case "38":
            case "39":
            case "41":
            case "42":
            case "43":
            case "44":
            case "45":
                $merchant_status_id = 11;
                break;
            case "33":
                $merchant_status_id = 12;
                break;
            case "34":
                $merchant_status_id = 13;
                break;
            case "35":
                $merchant_status_id = 14;
                break;
            case "36":
                $merchant_status_id = 15;
                break;
            case "37":
                $merchant_status_id = 16;
                break;
            case "40":
                $merchant_status_id = 17;
                break;
        }

        $all_sub_order_status = $this->merchantAllStatus();
        return $all_sub_order_status["$merchant_status_id"];
    }

    public function createTransitMap($sub_order_id, $start_hub_id, $end_hub_id){

        $trip_maps = TripMap::where('start_hub_id', $start_hub_id)
                            ->where('end_hub_id', $end_hub_id)
                            ->orderBy('priority', 'asc')
                            ->get();
        Log::info('Total Trip Found: ' . count($trip_maps));
        if(count($trip_maps) > 0){

            try {
                DB::beginTransaction();

                    $i = 1;

                    $sub_order_trip_map_dlt = SubOrderTripMap::where('sub_order_id', $sub_order_id)->delete();

                    foreach ($trip_maps as $trip_map) {
                        
                        $sub_order_trip_map = new SubOrderTripMap();
                        $sub_order_trip_map->sub_order_id = $sub_order_id;
                        $sub_order_trip_map->trip_map_id = $trip_map->id;
                        $sub_order_trip_map->hub_id = $trip_map->hub_id;
                        if(Auth::guard('api')->user()){
                            $sub_order_trip_map->created_by = Auth::guard('api')->user()->id;
                            $sub_order_trip_map->updated_by = Auth::guard('api')->user()->id;
                        }else{
                            $sub_order_trip_map->created_by = auth()->user()->id;
                            $sub_order_trip_map->updated_by = auth()->user()->id;
                        }

                        $sub_order_trip_map->save();

                        if($i == 1){
                            $next_hub_id = $sub_order_trip_map->hub_id;
                        }

                        $i++;

                    }

                DB::commit();

                return $next_hub_id;

            } catch (Exception $e) {

                DB::rollback();

                return $end_hub_id;

            }

        }else{
            return $end_hub_id;
        }

    }

    public function getNextHubId($sub_order_id, $hub_id){

        $sub_order = SubOrder::where('id', $sub_order_id)->first();

        try {
            $sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order_id)
                                        ->where('hub_id', $hub_id)
                                        ->where('status', 0)
                                        ->first();
            if($sub_order_trip_map){
                // "where('id' , '<=', $sub_order_trip_map->id)" as client asked if there is any way to skip any transit hub
                SubOrderTripMap::where('sub_order_id', $sub_order_id)->where('id' , '<=', $sub_order_trip_map->id)->update(['status' => 1]);
//                    $sub_order_trip_map->status = 1;
//                    $sub_order_trip_map->save();
            }

            $new_sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order_id)
                ->where('status', 0)
                ->orderBy('id', 'asc')
                ->first();

            if($new_sub_order_trip_map){
                $next_hub_id = $new_sub_order_trip_map->hub_id;
            }else{
                $next_hub_id = $sub_order->destination_hub_id;
            }

            return $next_hub_id;

        } catch (Exception $e) {
            Log::error($e);
            return $next_hub_id = $sub_order->destination_hub_id;
        }

    }

}