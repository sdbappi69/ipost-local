<?php

namespace App\Console\Commands;

use App\OrderLog;
use App\TatDelivery;
use App\TatReturn;
use App\TatTrip;
use Illuminate\Console\Command;

class Tat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Tat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Tat';

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
        $this->deliveryTatSave();
        $this->returnTatSave();
        $this->tripTatSave();
    }

    public function deliveryTatSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::where('order_logs.text', 'Delivery Completed')
                            ->where('order_logs.type', '!=', 'reference')
                            ->leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
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
                $tatDetail = deliveryTat($sub_order->id);

                $tatDelivery = new TatDelivery();

                $tatDelivery->orderId = @$sub_order->order->id;
                $tatDelivery->order_id = @$sub_order->order->unique_order_id;
                $tatDelivery->sub_order_id = @$sub_order->unique_suborder_id;
                $tatDelivery->merchant_order_id = @$sub_order->order->merchant_order_id;
                $tatDelivery->merchant = @$sub_order->order->store->merchant->name;
                $tatDelivery->store = @$sub_order->order->store->store_id;

                $tatDelivery->tat = $tatDetail['tat'];
                $tatDelivery->order_created = @$sub_order->order->created_at;
                $tatDelivery->picked_at = $tatDetail['picked_at'];
                $tatDelivery->delivered_at = $tatDetail['delivered_at'];
                $tatDelivery->picking_attempt = $tatDetail['picking_attempt'];
                $tatDelivery->delivery_attempt = $tatDetail['delivery_attempt'];
                $tatDelivery->product = @$sub_order->product->product_title;
                $tatDelivery->quantity = @$sub_order->product->quantity;
                
                if($sub_order->sub_order_last_status === NULL) {
                    $tatDelivery->current_status = hubGetStatus($sub_order->sub_order_status);
                    $tatDelivery->status_code = @$sub_order->sub_order_status;
                }
                else {
                    $tatDelivery->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $tatDelivery->status_code = @$sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $tatDelivery->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $tatDelivery->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $tatDelivery->picker_id = @$sub_order->product->picker->id;
                $tatDelivery->deliveryman_id = @$sub_order->deliveryman->id;
                $tatDelivery->order_created_at = @$sub_order->order->created_at;

                $tatDelivery->created_at = date('Y-m-d H:i:s');

                $tatDelivery->save();
            }
        }
    }

    public function returnTatSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::where('order_logs.text', 'Return Completed')
                            ->where('order_logs.type', '!=', 'reference')
                            ->leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
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
                $tatDetail = returnTat($sub_order->id);

                $tatReturn = new TatReturn();

                $tatReturn->orderId = @$sub_order->order->id;
                $tatReturn->order_id = @$sub_order->order->unique_order_id;
                $tatReturn->sub_order_id = @$sub_order->unique_suborder_id;
                $tatReturn->merchant_order_id = @$sub_order->order->merchant_order_id;
                $tatReturn->merchant = @$sub_order->order->store->merchant->name;
                $tatReturn->store = @$sub_order->order->store->store_id;

                $tatReturn->tat = $tatDetail['tat'];
                $tatReturn->order_created = @$sub_order->order->created_at;
                $tatReturn->picked_at = $tatDetail['picked_at'];
                $tatReturn->returned_at = $tatDetail['delivered_at'];
                $tatReturn->picking_attempt = $tatDetail['picking_attempt'];
                $tatReturn->return_attempt = $tatDetail['delivery_attempt'];
                $tatReturn->product = @$sub_order->product->product_title;
                $tatReturn->quantity = @$sub_order->product->quantity;
                
                if($sub_order->sub_order_last_status === NULL) {
                    $tatReturn->current_status = hubGetStatus($sub_order->sub_order_status);
                    $tatReturn->status_code = @$sub_order->sub_order_status;
                }
                else {
                    $tatReturn->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $tatReturn->status_code = @$sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $tatReturn->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $tatReturn->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $tatReturn->picker_id = @$sub_order->product->picker->id;
                $tatReturn->deliveryman_id = @$sub_order->deliveryman->id;
                $tatReturn->order_created_at = @$sub_order->order->created_at;

                $tatReturn->created_at = date('Y-m-d H:i:s');
                $tatReturn->save();
            }
        }                            
    }

    public function tripTatSave(){
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $order_logs = OrderLog::where('order_logs.text', 'Product Trip in Transit')
                            ->where('order_logs.type', '!=', 'reference')
                            ->leftJoin('sub_orders','sub_orders.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders','orders.id','=','sub_orders.order_id')
                            ->leftJoin('stores','stores.id','=','orders.store_id')
                            ->leftJoin('order_product','order_product.sub_order_id','=','sub_orders.id')
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
                $tatDetail = titTat($sub_order->id);

                $tatTrip = new TatTrip();

                $tatTrip->orderId = @$sub_order->order->id;
                $tatTrip->order_id = $sub_order->order->unique_order_id;
                $tatTrip->sub_order_id = $sub_order->unique_suborder_id;
                $tatTrip->merchant_order_id = $sub_order->order->merchant_order_id;
                $tatTrip->merchant = $sub_order->order->store->merchant->name;
                $tatTrip->store = $sub_order->order->store->store_id;

                $tatTrip->tat = $tatDetail['tat'];
                $tatTrip->order_created = $sub_order->order->created_at;
                $tatTrip->picked_at = $tatDetail['picked_at'];
                $tatTrip->triped_at = $tatDetail['triped_at'];
                $tatTrip->picking_attempt = $tatDetail['picking_attempt'];
                
                $tatTrip->product = $sub_order->product->product_title;
                $tatTrip->quantity = $sub_order->product->quantity;
                
                if($sub_order->sub_order_last_status === NULL) {
                    $tatTrip->current_status = hubGetStatus($sub_order->sub_order_status);
                    $tatTrip->status_code = $sub_order->sub_order_status;
                }
                else {
                    $tatTrip->current_status = hubGetStatus($sub_order->sub_order_last_status);
                    $tatTrip->status_code = $sub_order->sub_order_last_status;
                }

                if($sub_order->order->delivery_msisdn != '')
                    $tatTrip->customer_mobile_no = @$sub_order->order->delivery_msisdn;
                else
                    $tatTrip->customer_mobile_no = @$sub_order->order->delivery_alt_msisdn;

                $tatTrip->picker_id = @$sub_order->product->picker->id;
                $tatTrip->deliveryman_id = @$sub_order->deliveryman->id;
                $tatTrip->order_created_at = @$sub_order->order->created_at;

                $tatTrip->created_at = date('Y-m-d H:i:s');

                $tatTrip->save();
            }
        }                            
    }
}
