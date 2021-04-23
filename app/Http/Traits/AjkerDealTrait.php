<?php
namespace App\Http\Traits;

trait AjkerDealTrait {

    public function ajkerDealOrderUpdate($ajker_deal_orders) {

        $orders = array();
        foreach ($ajker_deal_orders as $ajker_deal_order) {

            $merchant_order_ids = explode(',', $ajker_deal_order['merchant_order_id']);
            if(count($merchant_order_ids) > 0){

                $OrderIds = array();
                foreach ($merchant_order_ids as $merchant_order_id) {

                    if(strlen((int)$merchant_order_id) == 7){

                      $OrderIds[] = (int)$merchant_order_id;

                    }else if(strlen((int)$merchant_order_id) > 13){

                      $merchant_order_id_explode = str_split((int)$merchant_order_id, 7);

                      foreach ($merchant_order_id_explode as $order_id) {
                        
                        if(strlen($order_id) == 7){

                          $OrderIds[] = $order_id;
                          
                        }

                      }

                    }

                }

            }else{
                $OrderIds = array((int)$ajker_deal_order['merchant_order_id']);
            }

            $ThirdPartyOrderId = $ajker_deal_order['unique_order_id'];
            $ThirdPartyId = 26;
            $orders[] = array('OrderIds' => $OrderIds, 'ThirdPartyOrderId' => $ThirdPartyOrderId, 'ThirdPartyId' => $ThirdPartyId);
        }
        $json_orders = json_encode($orders);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateBulkPODnumberStatus",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $json_orders,
          CURLOPT_HTTPHEADER => array(
            "api_key: Ajkerdeal_~La?Rj73FcLm",
            "authorization: Basic QmlkZHl1dDpoamRzNzQ4NDg5Mw==",
            "cache-control: no-cache",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }
        
    }

    public function ajkerDealOrderStatus($merchant_order_id) {

        $merchant_order_ids = explode(',', $merchant_order_id);
        if(count($merchant_order_ids) > 0){

            $OrderIds = array();
            foreach ($merchant_order_ids as $merchant_order_id) {
                $OrderIds[] = (int)$merchant_order_id;
            }

        }else{
            $OrderIds = array((int)$merchant_order_id);
        }

        $ThirdPartyId = 26;
        $orders = array('OrderIds' => $OrderIds, 'ThirdPartyId' => $ThirdPartyId);

        $json_orders = json_encode($orders);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $json_orders,
          CURLOPT_HTTPHEADER => array(
            "api_key: Ajkerdeal_~La?Rj73FcLm",
            "authorization: Basic QmlkZHl1dDpoamRzNzQ4NDg5Mw==",
            "cache-control: no-cache",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }

    }



}