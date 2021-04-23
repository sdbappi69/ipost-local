<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\OperationPickupFailed;
use App\OrderLog;

class FailedPickup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:failedpickup';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Command description';

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
        $start_date = date('Y-m-d');
        // $start_date = '2017-03-21';
        $end_date = date('Y-m-d');

        $pickupFailedData = OrderLog::select('B.unique_order_id',
                            'A.created_at AS order_created_at',
                            'B.merchant_order_id',
                            'D.name AS merchant_name',
                            'order_logs.text AS current_status',
                            'G.reason AS failed_reason',
                            'B.order_remarks AS remarks')
                            ->where('order_logs.text', 'Pick up failed')
                            ->where('order_logs.type', '!=', 'reference')
                            ->join('sub_orders AS A', 'A.id','=','order_logs.sub_order_id')
                            ->join('orders AS B', 'B.id','=','A.order_id')
                            ->join('stores AS C','C.id','=','B.store_id')
                            ->join('merchants AS D','D.id','=','C.merchant_id')
                            ->join('order_product AS E','E.sub_order_id','=','A.id')
                            ->join('picking_task AS F','F.product_unique_id','=','E.product_unique_id')
                            ->join('reasons AS G','G.id','=','F.reason_id')
                            ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                            ->groupBy('A.id')
                            ->orderBy('B.id', 'desc')
                            ->get();

        foreach($pickupFailedData as $pickupFailed){
            $OperationPickupFailed = new OperationPickupFailed();
            $OperationPickupFailed->order_id = $pickupFailed->unique_order_id;
            $OperationPickupFailed->order_created_at = $pickupFailed->order_created_at;
            $OperationPickupFailed->merchant_order_id = $pickupFailed->merchant_order_id;
            $OperationPickupFailed->merchant_name = $pickupFailed->merchant_name;
            $OperationPickupFailed->current_status = $pickupFailed->current_status;
            $OperationPickupFailed->status_code = '6';
            $OperationPickupFailed->failed_reason = $pickupFailed->failed_reason;
            $OperationPickupFailed->remarks = $pickupFailed->remarks;
            $OperationPickupFailed->save();
        }
    }
}
