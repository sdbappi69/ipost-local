<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\OperationIntransit;
use App\SubOrder;

class InTransit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:intransit';

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

        $query = SubOrder::select(
                'D.name AS merchant_name',
                'A.merchant_order_id',
                'F.title AS pickup_hub',
                'G.title AS delivery_hub',
                'I.created_at AS delivery_date',
                'H.title AS current_status',
                'H.code AS status_code',
                'K.created_at AS trip_created_date')
                ->whereIn('sub_orders.sub_order_status', ['19', '20', '21'])
                ->join('orders AS A','A.id','=','sub_orders.order_id')
                ->join('stores AS C','C.id','=','A.store_id')
                ->join('merchants AS D','D.id','=','C.merchant_id')
                ->join('zones AS E','E.id','=','A.delivery_zone_id')
                ->join('hubs AS F','F.id','=','sub_orders.source_hub_id')
                ->join('hubs AS G','G.id','=','sub_orders.destination_hub_id')
                ->join('status AS H','H.id','=','sub_orders.sub_order_status')
                ->join('order_logs AS I', 'I.sub_order_id','=','sub_orders.id')
                ->join('suborder_trip AS J', 'J.sub_order_id','=','sub_orders.id')
                ->join('trips AS K', 'K.id','=','J.trip_id')
                ->WhereBetween('sub_orders.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'));
                
        $intransitData = $query->get();

        foreach($intransitData as $intransit){
            $OperationIntransit = new OperationIntransit();
            $OperationIntransit->order_id = $intransit->unique_order_id;
            $OperationIntransit->order_created_at = $intransit->order_created_at;
            $OperationIntransit->merchant_name = $intransit->merchant_name;
            $OperationIntransit->merchant_order_id = $intransit->merchant_order_id;
            $OperationIntransit->pickup_hub = $intransit->pickup_hub;
            $OperationIntransit->delivery_hub = $intransit->delivery_hub;
            $OperationIntransit->current_status = $intransit->current_status;
            $OperationIntransit->status_code = $intransit->status_code;
            $OperationIntransit->trip_created_date = $intransit->trip_created_date;
            $OperationIntransit->save();
        }
    }
}
