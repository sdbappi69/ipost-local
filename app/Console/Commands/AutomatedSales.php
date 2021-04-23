<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\OperationSales;
use App\SubOrder;

class AutomatedSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:automatedsales';

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

        ini_set('max_execution_time', 7200);//7200 = 2 hours
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

        $query = SubOrder::select('A.unique_order_id',
                'A.merchant_order_id',
                'D.name AS merchant_name',
                'F.title AS pickup_hub',
                'I.created_at AS delivery_date',
                'B.total_payable_amount AS cash_on_delivery',
                'B.delivery_paid_amount AS collected_amount',
                'A.order_remarks AS remarks',
                'sub_orders.created_at AS order_created_at',
                'H.title AS current_status',
                'H.code AS status_code')
                ->whereIn('sub_orders.sub_order_status', ['38', '39'])
                ->join('orders AS A','A.id','=','sub_orders.order_id')
                ->join('order_product AS B','B.order_id','=','A.id')
                ->join('stores AS C','C.id','=','A.store_id')
                ->join('merchants AS D','D.id','=','C.merchant_id')
                ->join('zones AS E','E.id','=','A.delivery_zone_id')
                ->join('hubs AS F','F.id','=','sub_orders.source_hub_id')
                ->join('hubs AS G','G.id','=','sub_orders.destination_hub_id')
                ->join('status AS H','H.id','=','sub_orders.sub_order_status')
                ->join('order_logs AS I', 'I.sub_order_id','=','sub_orders.id')
                ->WhereBetween('sub_orders.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
                
        $salesData = $query->get();

        foreach($salesData as $sales){
            $OperationSales = new OperationSales();
            $OperationSales->order_id = $sales->unique_order_id;
            $OperationSales->merchant_order_id = $sales->merchant_order_id;
            $OperationSales->merchant_name = $sales->merchant_name;
            $OperationSales->pickup_hub = $sales->pickup_hub;
            $OperationSales->delivery_date = $sales->delivery_date;
            $OperationSales->cash_on_delivery = $sales->cash_on_delivery;
            $OperationSales->collected_amount = $sales->collected_amount;
            $OperationSales->remarks = $sales->remarks;
            $OperationSales->order_created_at = $sales->order_created_at;
            $OperationSales->current_status = $sales->current_status;
            $OperationSales->status_code = $sales->status_code;
            $OperationSales->save();
        }
    }
}
