<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CollectFeedback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:collectFeedback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will run one in a day to collect deliverd and returned product data to set feedback';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        // Test Purpose
    // Start
        try {
    //Try Start
    //$current_date = date('Y-m-d');
            $current_date = "2018-06-06";
            $number_of_chunk_data = 50;
    // To get delivered sub order
            $order_logs = \App\OrderLog::with(['sub_order.product','sub_order.destination_hub','sub_order.dTask','sub_order.product.pTask','sub_order.order.store.merchant'])->whereIn('text',['Delivery Completed','Return Completed'])->whereDate('created_at','=',$current_date)->chunk($number_of_chunk_data, function($querys) {
                $i=0;
                $inserted_data = null;
                foreach ($querys as $order_log) {
                // dump($order_log); die();
            // apply some action to the chunked results here
                    if(!$order_log->sub_order or !$order_log->sub_order->product or !$order_log->sub_order->order){
                        continue;
                    }
                    $inserted_data[$i]['suborder_id'] = $order_log->sub_order->id;
                    $inserted_data[$i]['unique_suborder_id'] = $order_log->sub_order->unique_suborder_id;
                    $inserted_data[$i]['order_id'] = $order_log->sub_order->order_id;
                    $inserted_data[$i]['company_name'] = $order_log->sub_order->order->store->store_id." - ".$order_log->sub_order->order->store->merchant->name;
                    $inserted_data[$i]['hub'] = ($order_log->sub_order->destination_hub ? $order_log->sub_order->destination_hub->title : null);
                    $inserted_data[$i]['product'] = ($order_log->sub_order->product ? $order_log->sub_order->product->product_title : null);
                    $inserted_data[$i]['order_created_at'] = $order_log->sub_order->order->created_at;
                    $inserted_data[$i]['customer_name'] = $order_log->sub_order->order->delivery_name;
                    $inserted_data[$i]['customer_address'] = app('\App\Http\Controllers\CustomerSupport\ComplainController')->__create_address_by_concat_all($order_log->sub_order);
                    $inserted_data[$i]['customer_number'] = $order_log->sub_order->order->delivery_msisdn;
                    $inserted_data[$i]['amount_to_collect'] = $order_log->sub_order->product->total_payable_amount;
                    $inserted_data[$i]['amount_collected'] = $order_log->sub_order->product->delivery_paid_amount;
                    if($order_log->text == 'Delivery Completed'){
                        $inserted_data[$i]['type'] = 'Delivery';
                        $inserted_data[$i]['rider'] = ($order_log->sub_order->dTask ? $order_log->sub_order->dTask->deliveryman_id : null);
                        $inserted_data[$i]['delivered_date'] = ($order_log->sub_order->dTask ? $order_log->sub_order->dTask->updated_at : null);
                    }
                    else{
                        $inserted_data[$i]['type'] = 'Return';
                        $inserted_data[$i]['rider'] = ($order_log->sub_order->product->pTask ? $order_log->sub_order->product->pTask->picker_id : null);
                        $inserted_data[$i]['delivered_date'] = ($order_log->sub_order->product->pTask ? $order_log->sub_order->product->pTask->updated_at : null);
                    }
                    $inserted_data[$i]['mode_selection'] = "Outbound";
                    $inserted_data[$i]['status'] = 0;
                    $i++;
                }
                if(!is_null($inserted_data)){
                    \App\CustomerSupportModel\FeedBack::insert($inserted_data);
                }
            });
        //die();
    //Try End
        } catch (\Exception $e) {
            \Log::error("Error message : ".$e);
            \Log::error("Error message : ".$e->getMessage());
        }
    //End
    }
}
