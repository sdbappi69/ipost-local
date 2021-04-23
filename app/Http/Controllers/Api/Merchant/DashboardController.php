<?php

namespace App\Http\Controllers\Api\Merchant;

use App\RiderLocation;
use App\User;
use App\State;
use App\City;
use App\Zone;
// masud
use App\Country;
use App\Store;
use Auth;
// end masud
use Illuminate\Http\Request;
use DB;
use Log;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ProductCategory;
use App\PickingLocations;
use App\PickingTimeSlot;
use App\PickingTask;
use App\DeliveryTask;
use App\SubOrder;
use App\Order;

class DashboardController extends Controller {

    public function index(Request $request) {

        if($request->has('date_from') && $request->has('date_to')){

            $merchant_home_info = $this->MerchantAppInfo($request->date_from, $request->date_to, Auth::guard('api')->user()->reference_id);

            $panding_pickup_req     = $merchant_home_info['panding_pickup_req'];
            $success_pickup_req     = $merchant_home_info['success_pickup_req'];
            $partial_pickup_req     = $merchant_home_info['partial_pickup_req'];
            $failed_pickup_req      = $merchant_home_info['failed_pickup_req'];
            $total_pickup_req       = $panding_pickup_req + $success_pickup_req + $partial_pickup_req + $failed_pickup_req;
            
            $panding_delivery_req   = $merchant_home_info['panding_delivery_req'];
            $success_delivery_req   = $merchant_home_info['success_delivery_req'];
            $partial_delivery_req   = $merchant_home_info['partial_delivery_req'];
            $failed_delivery_req    = $merchant_home_info['failed_delivery_req'];
            $total_delivery_req     = $panding_delivery_req + $success_delivery_req + $partial_delivery_req + $failed_delivery_req;

            $panding_return_req     = $merchant_home_info['panding_return_req'];
            $success_return_req     = $merchant_home_info['success_return_req'];
            $failed_return_req      = $merchant_home_info['failed_return_req'];
            $total_return_req       = $panding_return_req + $success_return_req + $failed_return_req;

            $trip_in_transit        = $merchant_home_info['trip_in_transit'];

            $pickup = array(
                            "total"     => $total_pickup_req,
                            "pending"   => $panding_pickup_req,
                            "success"   => $success_pickup_req,
                            "partial"   => $partial_pickup_req,
                            "failed"    => $failed_pickup_req
                        );

            $delivery = array(
                            "total"     => $total_delivery_req,
                            "pending"   => $panding_delivery_req,
                            "success"   => $success_delivery_req,
                            "partial"   => $partial_delivery_req,
                            "failed"    => $failed_delivery_req
                        );

            $return = array(
                            "total"     => $total_return_req,
                            "pending"   => $panding_return_req,
                            "success"   => $success_return_req,
                            "failed"    => $failed_return_req
                        );

            $transit = array(
                            "total"     => $trip_in_transit
                        );

            $feedback['status_code']            = 200;
            $message[]                          = "Data Found";
            $feedback['message']                = $message;
            $feedback['response']['pickup']     = $pickup;
            $feedback['response']['delivery']   = $delivery;
            $feedback['response']['return']     = $return;
            $feedback['response']['in_transit'] = $transit;

        } else {
            $status_code = 404;
            $message[] = 'No data found';
            return $this->set_unauthorized($status_code, $message, $response = '');
        }

        return response($feedback, 200);
        
    }

    //private function set_unauthorized( $status, $status_code, $message, $response )
    private function set_unauthorized($status_code, $message, $response) {
        $feedback = [];
        //$feedback['status']        =  $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
    }

    public function MerchantAppInfo($from_date, $to_date, $merchant_id) {

        // PickUp
        $panding_pickup_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 1)
                            ->where('picking_task.type', 'Picking')
                            ->count();
        $success_pickup_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 2)
                            ->where('picking_task.type', 'Picking')
                            ->count();
        $partial_pickup_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 3)
                            ->where('picking_task.type', 'Picking')
                            ->count();
        $failed_pickup_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 4)
                            ->where('picking_task.type', 'Picking')
                            ->count();

        // Delivery
        $panding_delivery_req = DeliveryTask::leftJoin('sub_orders','sub_orders.unique_suborder_id','=','delivery_task.unique_suborder_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('delivery_task.status', 1)
                            ->count();
        $success_delivery_req = DeliveryTask::leftJoin('sub_orders','sub_orders.unique_suborder_id','=','delivery_task.unique_suborder_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('delivery_task.status', 2)
                            ->count();
        $partial_delivery_req = DeliveryTask::leftJoin('sub_orders','sub_orders.unique_suborder_id','=','delivery_task.unique_suborder_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('delivery_task.status', 3)
                            ->count();
        $failed_delivery_req = DeliveryTask::leftJoin('sub_orders','sub_orders.unique_suborder_id','=','delivery_task.unique_suborder_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('delivery_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('delivery_task.status', 4)
                            ->count();

        // Return
        $panding_return_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 1)
                            ->where('picking_task.type', 'Return')
                            ->count();
        $success_return_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 2)
                            ->where('picking_task.type', 'Return')
                            ->count();
        $failed_return_req = PickingTask::leftJoin('order_product','order_product.product_unique_id','=','picking_task.product_unique_id')
                            ->leftJoin('orders','orders.id','=','order_product.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->where('stores.merchant_id', $merchant_id)
                            ->WhereBetween('picking_task.created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))
                            ->where('picking_task.status', 4)
                            ->where('picking_task.type', 'Return')
                            ->count();

        $trip_in_transit = SubOrder::leftJoin('orders', 'orders.id', '=', 'sub_orders.order_id')
                                    ->leftJoin('stores','stores.id','=','orders.store_id')
                                    ->where('stores.merchant_id', $merchant_id)
                                    ->where('sub_order_status', 20)
                                    ->count();

        return $merchant_info = array(
                                        'panding_pickup_req'    => $panding_pickup_req,
                                        'success_pickup_req'    => $success_pickup_req,
                                        'partial_pickup_req'    => $partial_pickup_req,
                                        'failed_pickup_req'     => $failed_pickup_req,
                                        'panding_delivery_req'  => $panding_delivery_req,
                                        'success_delivery_req'  => $success_delivery_req,
                                        'partial_delivery_req'  => $partial_delivery_req,
                                        'failed_delivery_req'   => $failed_delivery_req,
                                        'panding_return_req'    => $panding_return_req,
                                        'success_return_req'    => $success_return_req,
                                        'failed_return_req'     => $failed_return_req,
                                        'trip_in_transit'       => $trip_in_transit
                                    );

    }

}
