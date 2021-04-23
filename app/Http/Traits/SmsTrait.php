<?php
namespace App\Http\Traits;

use App\SmsQueue;
use App\PickingTask;
use App\OrderProduct;
use App\SubOrder;
use App\Merchant;
use App\User;

use App\Http\Traits\SmsApi;

trait SmsTrait {

    use SmsApi;

    public function setSms($user_id, $source, $sms_type, $ref_id, $ref_table, $to, $body) {

        $this->sendCustomMessage($to,$body,$ref_id);
        // $this->sendCustomMessage('01681692786',$body,$ref_id);

    	$sms = new SmsQueue();
    	$sms->source = $source;
    	$sms->sms_type = $sms_type;
    	$sms->ref_id = $ref_id;
    	$sms->ref_table = $ref_table;
    	$sms->to = $to;
    	$sms->body = $body;
    	$sms->created_by = $user_id;
    	$sms->updated_by = $user_id;
        $sms->status = 1;
		$sms->save();
        return $sms->id;
    }

    // Start Delivery Consignment
    public function smsStartDeliveryConsignment($unique_suborder_id){
        
        $data = SubOrder::select('sub_orders.id', 'm.name', 'o.delivery_msisdn', 'o.unique_order_id')
                            ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('sub_orders.unique_suborder_id', $unique_suborder_id)
                            ->first();

        if(count($data) > 0){

            $source = 'system';
            $sms_type = 'Product In Delivery';
            $ref_id = $data->id;
            $ref_table = 'sub_orders';
            $to = $data->delivery_msisdn;
            // $to = '01763456950';
            $user_id = auth()->user()->id;

            $body = 'Dear Customer, your product is being shipped to your location from our HUB. Your tracking ID is '.$data->unique_order_id;

            return $this->setSms($user_id, $source, $sms_type, $ref_id, $ref_table, $to, $body);

        }else{
            return 0;
        }

    }

    // Pickup Failed
    public function smsPickupFailed($unique_suborder_id){
        
        $data = SubOrder::select('sub_orders.id', 'm.name', 'm.msisdn')
                            ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('sub_orders.unique_suborder_id', $unique_suborder_id)
                            ->first();

        if($data){

            $source = 'system';
            $sms_type = 'Product Pickup Failed';
            $ref_id = $data->id;
            $ref_table = 'sub_orders';
            $to = $data->msisdn;
            // $to = '01763456950';
            $user_id = auth()->user()->id;

            $body = "Dear ".$data->name.",\nYour product bearing order ID ".$unique_suborder_id." is failed to pickup. Kindly call your KAM or call customer service for query: 09612433988\nRgds,\nBiddyut";

            // $test = $this->setSms($user_id, $source, $sms_type, $ref_id, $ref_table, "01681692786", $body);
            return $this->setSms($user_id, $source, $sms_type, $ref_id, $ref_table, $to, $body);

        }else{
            return 0;
        }

    }

    // Delivery Failed
    public function smsDeliveryFailed($unique_suborder_id){
        
        $data = SubOrder::select('sub_orders.id', 'm.name', 'm.msisdn')
                            ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('sub_orders.unique_suborder_id', $unique_suborder_id)
                            ->first();

        if($data){

            $source = 'system';
            $sms_type = 'Product Delivery Failed';
            $ref_id = $data->id;
            $ref_table = 'sub_orders';
            $to = $data->msisdn;
            // $to = '01763456950';
            $user_id = auth()->user()->id;

            $body = "Dear ".$data->name.",\nYour product bearing order ID ".$unique_suborder_id." is failed to delivery. Kindly call your KAM or call customer service for query: 09612433988\nRgds,\nBiddyut";

            // $test = $this->setSms($user_id, $source, $sms_type, $ref_id, $ref_table, "01681692786", $body);
            return $this->setSms($user_id, $source, $sms_type, $ref_id, $ref_table, $to, $body);

        }else{
            return 0;
        }

    }

    // Return Complete
    // public function smsReturnComplete($product_unique_id){
        
    //     $data = OrderProduct::select('order_product.id', 'm.name', 'm.email')
    //                         ->join('order AS o', 'o.id', '=', 'order_product.order_id')
    //                         ->join('stores AS s', 's.id', '=', 'o.store_id')
    //                         ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
    //                         ->where('order_product.product_unique_id', $product_unique_id)
    //                         ->first();

    //     if(count($data) > 0){

    //         $source = 'system';
    //         $sms_type = 'Return Complete';
    //         $ref_id = $data->id;
    //         $ref_table = 'sub_orders';
    //         $to = $data->email;
    //         $user_id = auth()->user()->id;

    //         $body = $product_unique_id;

    //         return $this->setSms($user_id, $source, $sms_type, $ref_id, $ref_table, $to, $body);

    //     }else{
    //         return 0;
    //     }
        
    // }

}
