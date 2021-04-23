<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Merchant;
use App\PickingTask;
use App\DeliveryTask;

use Mail;
use Log;

class ScheduleMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All Schedule Mail to merchants';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $merchants = Merchant::whereStatus(true)->get();

        // Success Pickup
        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'picking_task.quantity',
                                            'picking_task.status'
                                        )
                                ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [2, 3])
                                ->where('picking_task.type', 'Picking')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                $to = $merchant->email;
                // $to = 'bappi_ifci@yahoo.com';

                Mail::queue("email.successpickup", ['picking_data' => $picking_data->toArray(), 'merchant' => $merchant->toArray()], function ($message) use ($to) {
                        
                        $message->to($to);
                        $message->subject('Daily Success Pickup Report');
                        $message->cc('report@biddyut.com');
                    });

            }

        }

        // Fail Pickup
        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'r.reason'
                                        )
                                ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('reasons AS r','r.id','=','picking_task.reason_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [4])
                                ->where('picking_task.type', 'Picking')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                $to = $merchant->email;
                // $to = 'bappi_ifci@yahoo.com';

                Mail::queue("email.failpickup", ['picking_data' => $picking_data->toArray(), 'merchant' => $merchant->toArray()], function ($message) use ($to) {
                        
                        $message->to($to);
                        $message->subject('Daily Failure Pickup Report');
                        $message->cc('report@biddyut.com');
                    });

            }

        }

        // Success Return
        foreach ($merchants as $merchant) {

            $picking_data = PickingTask::select(
                                            'picking_task.product_unique_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'picking_task.quantity',
                                            'picking_task.status'
                                        )
                                ->WhereBetween('picking_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('picking_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','picking_task.product_unique_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('picking_task.status', [2, 3])
                                ->where('picking_task.type', 'Return')
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($picking_data) > 0){

                $to = $merchant->email;
                // $to = 'bappi_ifci@yahoo.com';

                Mail::queue("email.successreturn", ['picking_data' => $picking_data->toArray(), 'merchant' => $merchant->toArray()], function ($message) use ($to) {
                        
                        $message->to($to);
                        $message->subject('Daily Success Return Report');
                        $message->cc('report@biddyut.com');
                    });

            }

        }

        // Success Delivery
        foreach ($merchants as $merchant) {

            $delivery_data = DeliveryTask::select(
                                            'delivery_task.unique_suborder_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'delivery_task.status'
                                        )
                                ->WhereBetween('delivery_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('delivery_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','delivery_task.unique_suborder_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('delivery_task.status', [2, 3])
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($delivery_data) > 0){

                $to = $merchant->email;
                // $to = 'bappi_ifci@yahoo.com';

                Mail::queue("email.successdelivery", ['delivery_data' => $delivery_data->toArray(), 'merchant' => $merchant->toArray()], function ($message) use ($to) {
                        
                        $message->to($to);
                        $message->subject('Daily Success Delivery Report');
                        $message->cc('report@biddyut.com');
                    });

            }

        }

        // Fail Delivery
        foreach ($merchants as $merchant) {

            $delivery_data = DeliveryTask::select(
                                            'delivery_task.unique_suborder_id AS unique_id',
                                            'o.merchant_order_id',
                                            'op.product_title',
                                            'r.reason'
                                        )
                                ->WhereBetween('delivery_task.created_at', array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))
                                // ->WhereBetween('delivery_task.created_at', array('2017-05-01 00:00:01',date('Y-m-d').' 23:59:59'))
                                ->leftJoin('order_product AS op','op.product_unique_id','=','delivery_task.unique_suborder_id')
                                ->leftJoin('reasons AS r','r.id','=','delivery_task.reason_id')
                                ->leftJoin('orders AS o','o.id','=','op.order_id')
                                ->leftJoin('stores AS s','s.id','=','o.store_id')
                                ->whereIn('delivery_task.status', [4])
                                ->where('s.merchant_id', $merchant->id)
                                ->get();

            if(count($delivery_data) > 0){

                $to = $merchant->email;
                // $to = 'bappi_ifci@yahoo.com';

                Mail::queue("email.faildelivery", ['delivery_data' => $delivery_data->toArray(), 'merchant' => $merchant->toArray()], function ($message) use ($to) {
                        
                        $message->to($to);
                        $message->subject('Daily Fail Delivery Report');
                        $message->cc('report@biddyut.com');
                    });

            }

        }

    }
}
