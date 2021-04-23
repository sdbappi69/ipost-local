<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\OperationDeliveryFailed;
use App\SubOrder;
use App\PickingTask;
use App\Consignment;
use App\OrderLog;

class FailedDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:faileddelivery';

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

        $deliveryFailedData = OrderLog::select('B.unique_order_id',
                            'A.created_at AS order_created_at',
                            'B.merchant_order_id',
                            'D.name AS merchant_name',
                            'order_logs.text AS current_status',
                            'L.title AS pickup_hub',
                            'J.title AS delivery_hub',
                            'H.reason AS failed_reason',
                            'B.order_remarks AS remarks',
                            'A.parent_sub_order_id')
                            ->where('order_logs.text', 'Products Delivery Failed')
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

        foreach($deliveryFailedData as $deliveryFailed){
            $OperationDeliveryFailed = new OperationDeliveryFailed();
            $OperationDeliveryFailed->order_id = $deliveryFailed->unique_order_id;
            $OperationDeliveryFailed->order_created_at = $deliveryFailed->order_created_at;
            $OperationDeliveryFailed->merchant_order_id = $deliveryFailed->merchant_order_id;
            $OperationDeliveryFailed->merchant_name = $deliveryFailed->merchant_name;
            $OperationDeliveryFailed->current_status = $deliveryFailed->current_status;
            $OperationDeliveryFailed->status_code = '33';
            $OperationDeliveryFailed->failed_reason = $deliveryFailed->failed_reason;
            $OperationDeliveryFailed->remarks = $deliveryFailed->remarks;
            $OperationDeliveryFailed->delivery_hub = $deliveryFailed->delivery_hub;

            $pickup_hub = '';

            if($deliveryFailed->pickup_hub != '') {
                $pickup_hub = $deliveryFailed->pickup_hub;
            } else {
                $parent_sub_order_id = $deliveryFailed->parent_sub_order_id;
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

            $OperationDeliveryFailed->pickup_hub = $pickup_hub;

            $OperationDeliveryFailed->save();
        }
    }
}
