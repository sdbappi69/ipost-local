<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\OperationReturnFailed;
use App\SubOrder;
use App\PickingTask;
use App\Consignment;
use App\OrderLog;


class FailedReturn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:failedreturn';

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

        $returnFailedData = OrderLog::select('B.unique_order_id',
                            'A.created_at AS order_created_at',
                            'B.merchant_order_id',
                            'D.name AS merchant_name',
                            'order_logs.text AS current_status',
                            'L.title AS pickup_hub',
                            'J.title AS delivery_hub',
                            'H.reason AS failed_reason',
                            'B.order_remarks AS remarks',
                            'A.parent_sub_order_id')
                            ->where('order_logs.text', 'Product return failed')
                            ->where('order_logs.type', '!=', 'reference')
                            ->leftJoin('sub_orders AS A', 'A.id','=','order_logs.sub_order_id')
                            ->leftJoin('orders AS B', 'B.id','=','A.order_id')
                            ->leftJoin('stores AS C','C.id','=','B.store_id')
                            ->leftJoin('merchants AS D','D.id','=','C.merchant_id')
                            ->leftJoin('order_product AS E','E.sub_order_id','=','A.id')
                            ->leftJoin('picking_task AS F','F.product_unique_id','=','E.product_unique_id')
                            ->leftJoin('delivery_task AS G','G.unique_suborder_id','=','A.unique_suborder_id')
                            ->leftJoin('reasons AS H','H.id','=','G.reason_id')
                            ->leftJoin('consignments AS I','I.id','=','G.consignment_id')
                            ->leftJoin('hubs AS J','J.id','=','I.hub_id')
                            ->leftJoin('consignments AS K','K.id','=','F.consignment_id')
                            ->leftJoin('hubs AS L','L.id','=','K.hub_id')
                            ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                            ->groupBy('A.id')
                            ->orderBy('B.id', 'desc')
                            ->get();

        foreach($returnFailedData as $retuenFailed){
            $OperationReturnFailed = new OperationReturnFailed();
            $OperationReturnFailed->order_id = $retuenFailed->unique_order_id;
            $OperationReturnFailed->order_created_at = $retuenFailed->order_created_at;
            $OperationReturnFailed->merchant_order_id = $retuenFailed->merchant_order_id;
            $OperationReturnFailed->merchant_name = $retuenFailed->merchant_name;
            $OperationReturnFailed->current_status = $retuenFailed->current_status;
            $OperationReturnFailed->status_code = '40';
            $OperationReturnFailed->failed_reason = $retuenFailed->failed_reason;
            $OperationReturnFailed->remarks = $retuenFailed->remarks;
            $OperationReturnFailed->delivery_hub = $retuenFailed->delivery_hub;

            $pickup_hub = '';

            if($retuenFailed->pickup_hub != '') {
                $pickup_hub = $retuenFailed->pickup_hub;
            } else {
                $parent_sub_order_id = $retuenFailed->parent_sub_order_id;
                while($parent_sub_order_id > 0) {
                    $SubOrderInfo = SubOrder::findOrFail($parent_sub_order_id);
                    $PickingTask = PickingTask::where('product_unique_id', $SubOrderInfo->unique_suborder_id);
                    if($PickingTask->count() > 0) {
                        $PickingTaskInfo = $PickingTask->first();
                        $HubInfo = Consignment::select('hubs.title AS pickup_hub')
                            ->leftJoin('hubs','hubs.id','=','consignments.hub_id')
                            ->where('consignments.id', $PickingTaskInfo->consignment_id)
                            ->first();
                        $pickup_hub = $HubInfo->pickup_hub;

                        break;
                    }
                }
            }

            $OperationReturnFailed->pickup_hub = $pickup_hub;

            $OperationReturnFailed->save();
        }
    }
}
