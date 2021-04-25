<?php

namespace App\Http\Controllers;

use App\CollectedCashAccumulated;
use App\Http\Controllers\Controller;
use App\ConsignmentCommon;
use App\MerchantAccumulatedCash;
use App\SubOrder;
use Auth;
use DB;
use App\OrderProduct;
use App\PickingLocations;
use App\PickingTimeSlot;
use Carbon\Carbon;
use App\PickingTask;
use App\DeliveryTask;
use App\DeliverySurvey;
use App\Order;

/**
 * Description of NavController
 *
 * @author johnny
 */
class NavController extends Controller {

    public function hubManager() {
        $data['delivery_nav'] = $this->toatlDelivery();
        $data['pickup_nav'] = $this->totalPickup();
        $data['return_nav'] = $this->totalReturn();
        $data['consignment_nav'] = $this->totalConsignment();
        $data['consignment_total_nav'] = $data['delivery_nav'] + $data['pickup_nav'] + $data['return_nav'] + $data['consignment_nav'];

        $data['receive_prodcut_nav'] = $this->totalReceive();
        $data['picked_nav'] = $this->totalPicked();
        $data['received_nav'] = $this->totalReceived();
        $data['inbound_nav'] = $data['received_nav'] + $data['picked_nav'] + $data['receive_prodcut_nav'];

        $data['office_delivery_nav'] = $this->totalOfficeDelivery();
        $data['accept_suborder_nav'] = $this->totalAcceptSuborder();
        $data['outbound_nav'] = $data['office_delivery_nav'] + $data['accept_suborder_nav'];

        $data['trip_nav'] = $this->totalTrip();

        $data['cash_collection'] = $this->totalCashCollection();
        $data['cash_transfer'] = $this->totalCashTransfer();
        $data['account_bill'] = $this->totalCashCollection() + $this->totalCashTransfer();

        return $data;
    }

    public function headAccountManager() {
        $data['receive_hub_payment'] = $this->receiveHubPayment();
        $data['merchant_checkout'] = $this->merchantCheckout();
        $data['manage_checkout'] = $this->manageCheckout();
        $data['final_account'] = $this->manageCheckout() + $this->merchantCheckout() + $this->receiveHubPayment();
        return $data;
    }

    private function receiveHubPayment() {

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        $query = CollectedCashAccumulated::WhereBetween('date', array($start_date, $end_date))->where('status', 1);
        return $receiveHubPayment = $query->count();
    }

    private function merchantCheckout() {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $query = CollectedCashAccumulated::WhereBetween('date', array($start_date, $end_date))
                ->where('merchant_accumulated_cash_status', 0)
                ->where('status', 2);
        return $merchantCheckout = $query->count();
    }

    private function manageCheckout() {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $query = MerchantAccumulatedCash::WhereBetween('date', array($start_date, $end_date))->where('status', 1);
        return $manageCheckout = $query->count();
    }

    private function toatlDelivery() {
        $query = SubOrder::select(
                        'sub_orders.id AS suborder_id'
                )
                ->distinct()
                ->where('sub_orders.status', 1)
                ->where('sub_orders.tm_delivery_status', 1)
                ->where('sub_orders.return', 0)
                ->whereIn('sub_orders.sub_order_status', [26, 34])
                ->whereRaw("IF (`sub_orders`.`post_delivery_return` = 0, `sub_orders`.`destination_hub_id`,`sub_orders`.`source_hub_id`) = " . auth()->user()->reference_id);
        return $sub_orders = $query->count();
    }

    private function totalPickup() {
        $query = SubOrder::select(
                        'sub_orders.id AS suborder_id'
                )
                ->where('sub_orders.status', 1)
                // ->whereIn('sub_orders.sub_order_status', [2, 6])
                ->whereIn('sub_orders.sub_order_status', [2])
                ->where('sub_orders.tm_picking_status', '=', 1)
                ->whereRaw("IF (`sub_orders`.`return` = 0, `zones_p`.`hub_id`,`zones_d`.`hub_id`) = " . auth()->user()->reference_id)
                ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->join('order_product AS op', 'op.sub_order_id', '=', 'sub_orders.id')
                ->join('pickup_locations', 'pickup_locations.id', '=', 'op.pickup_location_id')
                ->join('zones AS zones_p', 'zones_p.id', '=', 'pickup_locations.zone_id')
                ->join('zones AS zones_d', 'zones_d.id', '=', 'orders.delivery_zone_id');
        return $sub_orders = $query->count();
    }

    private function totalReturn() {
        $query = SubOrder::select(
                        'sub_orders.id AS suborder_id'
                )
                ->distinct()
                ->where('sub_orders.status', 1)
                ->whereIn('sub_orders.sub_order_status', [26, 27])
                ->where('sub_orders.return', '=', 1)
                ->where('sub_orders.tm_delivery_status', '=', 1)
                ->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id);
        return $sub_orders = $query->count();
    }

    private function totalConsignment() {
        $query = ConsignmentCommon::with('task')->where('hub_id', auth()->user()->reference_id)->where('status', 3);

        return $consignments = $query->count();
    }

    private function totalReceive() {
        return DB::table('consignments_tasks')
                        ->join('sub_orders', 'sub_orders.id', '=', 'consignments_tasks.sub_order_id')
                        ->join('order_product', 'sub_orders.id', '=', 'order_product.sub_order_id')
                        ->join('users', 'users.id', '=', 'consignments_tasks.rider_id')
                        ->join('pickup_locations', 'pickup_locations.id', '=', 'order_product.pickup_location_id')
                        ->join('zones AS pick_zones', 'pick_zones.id', '=', 'pickup_locations.zone_id')
                        ->select('users.name as rider_name', 'unique_suborder_id', 'product_title', 'order_product.quantity', 'consignments_tasks.otp')
                        ->whereIn('task_type_id', [1, 5])
                        // ->whereIn('sub_order_status', [7, 8])
                        ->where('sub_order_status', 9)
                        ->where('consignments_tasks.status', '>', 1)
                        ->where('tm_picking_status', '=', 1)
                        ->where('sub_orders.source_hub_id', '=', auth()->user()->reference_id)
                        ->count();
    }

    public function totalPicked() {
        return 0; // as the option remove
        $query = OrderProduct::select(array(
                    'order_product.id AS id'
                ))
                ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                ->leftJoin('consignments_tasks AS ct', 'ct.sub_order_id', '=', 'so.id')
                ->leftJoin('consignments_common AS con', 'con.id', '=', 'ct.consignment_id')
                ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->leftJoin('stores', 'stores.id', '=', 'o.store_id')
                ->leftJoin('merchants', 'merchants.id', '=', 'stores.merchant_id')
                ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id')
                // ->where('o.order_status', '=', '3')
                // ->where('order_product.status', '=', '4')
                ->where('so.sub_order_status', '=', '9')
                ->where('so.return', '=', 0)
                ->where(function ($q) {
            $q->where(function ($q1) {
                $q1->where('con.status', '=', '4');
                $q1->where('z.hub_id', '=', auth()->user()->reference_id);
                $q1->whereIn('ct.status', [2, 3]);
                $q1->where('ct.reconcile', '=', 1);
            });
            $q->orWhere(function ($q2) {
                $q2->where('so.source_hub_id', '=', auth()->user()->reference_id);
                $q2->where('so.return', 1);
            });
        });

        return $products = $query->count();
    }

    private function totalReceived() {
        return 0; // option removed
        $query = OrderProduct::select(array(
                    'order_product.id AS id'
                ))
                ->where('so.sub_order_status', '=', '10')
                ->where(function ($q) {
                    $q->where(function ($q1) {
                        $q1->where('z.hub_id', '=', auth()->user()->reference_id);
                        $q1->where('order_product.picking_status', '=', '1');
                    });
                    $q->orWhere(function ($q2) {
                        $q2->where('so.source_hub_id', '=', auth()->user()->reference_id);
                        $q2->where('so.return', 1);
                    });
                })
                ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id');
        return $query->count();
    }

    private function totalOfficeDelivery() {
        $query = SubOrder::select(
                        'sub_orders.id AS suborder_id'
                )
                ->where('sub_orders.delivery_from_office', '=', 1)
                ->where('sub_orders.status', '!=', 0)
                ->where('sub_orders.sub_order_status', '=', 26)
                ->orWhere('sub_orders.sub_order_status', '=', 27)
                ->where('sub_orders.parent_sub_order_id', 0)
                ->where(function ($query) {
            $query->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id);
            $query->orWhere('sub_orders.source_hub_id', '=', auth()->user()->reference_id);
            $query->orWhere('sub_orders.next_hub_id', '=', auth()->user()->reference_id);
        });
        return $query->count();
    }

    private function totalAcceptSuborder() {
        $query = SubOrder::select(
                        'sub_orders.id AS suborder_id'
                )
                ->where('sub_orders.status', '!=', 0)
                ->where('sub_orders.current_hub_id', '!=', auth()->user()->reference_id)
                ->where('sub_orders.next_hub_id', '=', auth()->user()->reference_id)
                ->whereIn('sub_orders.sub_order_status', [20, 21]);
        return $query->count();
    }

    private function totalTrip() {
        $query = SubOrder::select(
                        'sub_orders.id AS suborder_id'
                )
                ->where('sub_orders.status', 1)
                ->whereIn('sub_orders.sub_order_status', [15, 16, 17, 47])
                ->where('sub_orders.current_hub_id', '=', auth()->user()->reference_id)
                ->where('sub_orders.next_hub_id', '!=', auth()->user()->reference_id);
        return $query->count();
    }

    //saif start
    private function totalCashCollection() {
        $query = SubOrder::select('sub_orders.*', 'order_product.product_title', 'order_product.product_category_id', 'users.name as rider_name', 'consignments_tasks.end_time as delivery_time', 'orders.merchant_order_id', 'payment_types.name as payment_name')
                ->join('order_product', 'order_product.product_unique_id', '=', 'sub_orders.unique_suborder_id')
                ->join('consignments_tasks', 'consignments_tasks.sub_order_id', '=', 'sub_orders.id')
                ->join('consignments_common', 'consignments_tasks.consignment_id', '=', 'consignments_common.id')
                ->join('users', 'consignments_tasks.rider_id', '=', 'users.id')
                ->join('orders', 'orders.id', '=', 'sub_orders.order_id')
                ->join('payment_types', 'payment_types.id', '=', 'orders.payment_type_id')
                ->whereIn('sub_orders.sub_order_status', [37, 38, 39])
                ->where('sub_orders.accounts', 0)
                ->where('consignments_tasks.task_type_id', 2)
                ->where('sub_orders.collected_cash_status', 0)
                ->where('consignments_common.hub_id', auth()->user()->reference_id)
                ->distinct();
        return $query->count();
    }

    private function totalCashTransfer() {
        $start_date = date('Y-m-d', strtotime('-10 day'));
        $end_date = date('Y-m-d');
        $query = CollectedCashAccumulated::whereHubId(auth()->user()->reference_id)
                        ->WhereBetween('date', array($start_date, $end_date))->where('status', 1);
        return $query->count();
    }

}
