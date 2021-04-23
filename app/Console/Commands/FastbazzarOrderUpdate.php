<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FastbazzarOrderUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb-order:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updates the orders in fastbazzar panel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            // manage with job as the return status not in use
            $orders = \App\FastbazzarOrderUpdate::whereIn('status', [0, 3])->where('attempt', '<', 4)->take(20)->get();
            if (!$orders->count()) {
                Log::info("Noting to update in fastbazzar");
                return TRUE;
            }
            foreach ($orders as $order) {
                Log::info("FastBazzar call start($order->id): " . date("Y-m-d H:i:s"));
                $order->status = 1;
                $order->attempt = $order->attempt + 1;
                $order->save();
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $order->fb_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n  \"orderNumber\":\"" . $order->merchant_order_id . "\", \n  \"status\":\"" . $order->fb_status . "\"\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer g246d49n9d88fb78994nvkqtl25j1szf"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                $order->response = $response;
                if ($err) {
                    $order->status = 3;
                    Log::error("FastBazzar Error: " . $err);
                } else {
                    $response = json_decode($response);
                    if ($response[0] == 'failed') {
                        $order->status = 3;
                    } else {
                        $order->status = 2;
                    }
                }
                $order->save();
                Log::info("FastBazzar call end($order->id): " . date("Y-m-d H:i:s"));
            }
        } catch (Exception $e) {
            Log::error("FastBazzar Curl Error: " . $e);
        }
    }

}
