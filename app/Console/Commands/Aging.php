<?php

namespace App\Console\Commands;

use App\AgingPickup;
use App\AgingDelivery;
use App\AgingReturn;
use App\AgingTrip;
use App\OrderLog;
use Illuminate\Console\Command;

class Aging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Aging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Aging';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set('max_execution_time', 7200);//7200 = 2 hours

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->pickupAgingSave();
        $this->deliveryAgingSave();
        $this->returnAgingSave();
        $this->tripAgingSave();
    }

    public function pickupAgingSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::where('order_logs.text', 'Picked')
                            ->where('order_logs.type', '!=', 'reference')
                            ->where('picking_task.reconcile', 1)
                            ->where('picking_task.type', 'Picking')
                            ->leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            ->leftJoin('picking_task','picking_task.product_unique_id','=','order_product.product_unique_id')
                            ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                            ->groupBy('sub_orders.id')
                            ->orderBy('orders.id', 'desc')
                            ->get();


        foreach($order_logs as $order_log) {
            if($order_log->sub_order->parent_sub_order_id != 0)
                $sub_order = $order_log->sub_order->parent_sub_order;
            else
                $sub_order = $order_log->sub_order;

            if(isset($sub_order)) {
                $agingDetail = pickupAging($sub_order->id);

                $agingPickup = new AgingPickup();

                $agingPickup->orderId = @$sub_order->order->id;
                $agingPickup->order_id = @$sub_order->order->unique_order_id;
                $agingPickup->sub_order_id = @$sub_order->unique_suborder_id;
                $agingPickup->merchant_order_id = @$sub_order->order->merchant_order_id;
                $agingPickup->merchant = @$sub_order->order->store->merchant->name;
                $agingPickup->store = @$sub_order->order->store->store_id;

                $agingPickup->pickup_requested = $agingDetail['start_at'];
                $agingPickup->pickup_attempt = $agingDetail['picking_attempt'];
                $agingPickup->pickup_hub = @$sub_order->product->pickup_location->zone->hub->title;
                $agingPickup->picked_date = $agingDetail['end_at'];
                $agingPickup->delivery_hub = @$sub_order->destination_hub->title;
                $agingPickup->aging = $agingDetail['aging'];

                if($sub_order->sub_order_last_status === NULL){
                    $agingPickup->current_status = hubGetStatus($sub_order->sub_order_status);
                    $agingPickup->status_code = @$sub_order->sub_order_status;
                } else {
                    $agingPickup->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $agingPickup->status_code = @$sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $agingPickup->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $agingPickup->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $agingPickup->picker_id = @$sub_order->product->picker->id;
                $agingPickup->deliveryman_id = @$sub_order->deliveryman->id;
                $agingPickup->order_created_at = @$sub_order->order->created_at;

                $agingPickup->created_at = date('Y-m-d H:i:s');

                $agingPickup->save();
            }
        }
    }

    public function deliveryAgingSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::whereIn('text', ['Product delivered to customer', 'Products Partial Delivered to Customer'])
                            ->where('order_logs.type', '!=', 'reference')
                            // ->where('delivery_task.reconcile', 1)
                            ->leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                            ->groupBy('sub_orders.id')
                            ->orderBy('orders.id', 'desc')
                            ->get();

        $i = 0;
        foreach($order_logs as $order_log){
            $i++;
            if($i > '1891') {
            if($order_log->sub_order->parent_sub_order_id != 0)
                $sub_order = $order_log->sub_order->parent_sub_order;
            else
                $sub_order = $order_log->sub_order;

            if(isset($sub_order)){
                $agingDetail = deliveryAging($sub_order->id);

                $agingDelivery = new AgingDelivery();

                $agingDelivery->orderId = @$sub_order->order->id;
                $agingDelivery->order_id = @$sub_order->order->unique_order_id;
                $agingDelivery->sub_order_id = @$sub_order->unique_suborder_id;
                $agingDelivery->merchant_order_id = @$sub_order->order->merchant_order_id;
                $agingDelivery->merchant = @$sub_order->order->store->merchant->name;
                $agingDelivery->store = @$sub_order->order->store->store_id;
                $agingDelivery->pickup_hub = @$sub_order->product->pickup_location->zone->hub->title;

                $agingDelivery->racked_at_destination_hub = $agingDetail['start_at'];
                $agingDelivery->delivery_attempt = $agingDetail['delivery_attempt'];
                $agingDelivery->delivery_date = $agingDetail['end_at'];
                $agingDelivery->delivery_hub = @$sub_order->destination_hub->title;
                $agingDelivery->aging = $agingDetail['aging'];

                if($sub_order->sub_order_last_status === NULL) {
                    $agingDelivery->current_status = hubGetStatus($sub_order->sub_order_status);
                    $agingDelivery->status_code = @$sub_order->sub_order_status;
                }
                else {
                    $agingDelivery->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $agingDelivery->status_code = @$sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $agingDelivery->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $agingDelivery->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $agingDelivery->picker_id = @$sub_order->product->picker->id;
                $agingDelivery->deliveryman_id = @$sub_order->deliveryman->id;
                $agingDelivery->order_created_at = @$sub_order->order->created_at;

                $agingDelivery->created_at = date('Y-m-d H:i:s');

                $agingDelivery->save();
            }
            }
        }                          

    }

    public function returnAgingSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::where('order_logs.text', 'Return Completed')
                            ->where('order_logs.type', '!=', 'reference')
                            ->where('picking_task.reconcile', 1)
                            ->where('picking_task.type', 'Return')
                            ->leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
                            ->leftJoin('picking_task','picking_task.product_unique_id','=','order_product.product_unique_id')
                            ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                            ->groupBy('sub_orders.id')
                            ->orderBy('orders.id', 'desc')
                            ->get();

        foreach($order_logs as $order_log){
            if($order_log->sub_order->parent_sub_order_id != 0)
                $sub_order = $order_log->sub_order->parent_sub_order;
            else
                $sub_order = $order_log->sub_order;

            if(isset($sub_order)){
                $agingDetail = returnAging($sub_order->id);

                $agingReturn = new AgingReturn();

                $agingReturn->orderId = @$sub_order->order->id;
                $agingReturn->order_id = @$sub_order->order->unique_order_id;
                $agingReturn->sub_order_id = @$sub_order->unique_suborder_id;
                $agingReturn->merchant_order_id = @$sub_order->order->merchant_order_id;
                $agingReturn->merchant = @$sub_order->order->store->merchant->name;
                $agingReturn->store = @$sub_order->order->store->store_id;
                $agingReturn->pickup_hub = @$sub_order->product->pickup_location->zone->hub->title;

                $agingReturn->racked_at_destination_hub = $agingDetail['start_at'];
                $agingReturn->delivery_attempt = $agingDetail['delivery_attempt'];
                $agingReturn->return_date = $agingDetail['end_at'];
                $agingReturn->delivery_hub = @$sub_order->source_hub->title;
                $agingReturn->aging = $agingDetail['aging'];

                if($sub_order->sub_order_last_status === NULL) {
                    $agingReturn->current_status = hubGetStatus($sub_order->sub_order_status);
                    $agingReturn->status_code = $sub_order->sub_order_status;
                }
                else {
                    $agingReturn->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $agingReturn->status_code = $sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $agingReturn->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $agingReturn->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $agingReturn->picker_id = @$sub_order->product->picker->id;
                $agingReturn->deliveryman_id = @$sub_order->deliveryman->id;
                $agingReturn->order_created_at = @$sub_order->order->created_at;

                $agingReturn->created_at = date('Y-m-d H:i:s');

                $agingReturn->save();
            }
        }
    }

    public function tripAgingSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')                            
                            ->where('sub_orders.source_hub_id', '!=', null)
                            ->where('text', 'Full Order Racked at Destination Hub')
                            ->where('order_logs.type', '!=', 'reference')
                            ->whereRaw('sub_orders.source_hub_id != sub_orders.destination_hub_id')
                            ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                            ->groupBy('sub_orders.id')
                            ->orderBy('orders.id', 'desc')
                            ->get();

        foreach($order_logs as $order_log){
            if($order_log->sub_order->parent_sub_order_id != 0)
                $sub_order = $order_log->sub_order->parent_sub_order;
            else
                $sub_order = $order_log->sub_order;

            if(isset($sub_order)){
                $agingDetail = tripAging($sub_order->id);

                $agingTrip = new AgingTrip();

                $agingTrip->orderId = @$sub_order->order->id;
                $agingTrip->order_id = @$sub_order->order->unique_order_id;
                $agingTrip->sub_order_id = @$sub_order->unique_suborder_id;
                $agingTrip->merchant_order_id = @$sub_order->order->merchant_order_id;
                $agingTrip->merchant = @$sub_order->order->store->merchant->name;
                $agingTrip->store = @$sub_order->order->store->store_id;

                $agingTrip->pickup_hub = @$sub_order->product->pickup_location->zone->hub->title;

                $agingTrip->product_racked_at_pickup_hub = $agingDetail['start_at'];
                $agingTrip->product_racked_at_delivery_hub = $agingDetail['end_at'];
                $agingTrip->delivery_hub = @$sub_order->destination_hub->title;
                $agingTrip->aging = $agingDetail['aging'];

                if($sub_order->sub_order_last_status === NULL) {
                    $agingTrip->current_status = hubGetStatus($sub_order->sub_order_status);
                    $agingTrip->status_code = @$sub_order->sub_order_status;
                }
                else {
                    $agingTrip->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $agingTrip->status_code = @$sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $agingTrip->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $agingTrip->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $agingTrip->picker_id = @$sub_order->product->picker->id;
                $agingTrip->deliveryman_id = @$sub_order->deliveryman->id;
                $agingTrip->order_created_at = @$sub_order->order->created_at;

                $agingTrip->created_at = date('Y-m-d H:i:s');

                $agingTrip->save();
            }
        }
    }
}
