<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use DB;

class FIBOrderStatus extends Job implements ShouldQueue {

    use InteractsWithQueue,
        Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    protected $sub_order_id;
    protected $merchant_order_id;
    protected $fib_url;
    protected $sub_order_status;
    protected $fib_status;
    public $retryAfter = 120;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sub_order_id, $merchant_order_id, $fib_url, $fib_status) {
//        Log::info("FIB URL: $fib_url, merchant order id: $merchant_order_id, suborder: $sub_order_id");
        $this->sub_order_id = $sub_order_id;
        $this->merchant_order_id = $merchant_order_id;
        $this->fib_url = $fib_url;
        $this->fib_status = $fib_status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            Log::info("FIB Merchant Order Id: $this->merchant_order_id");

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->fib_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{"merchantOrderId":"' . $this->merchant_order_id . '","status": "' . $this->fib_status . '"}', // raw data
//                "{\n  \"merchantOrderId\":\"" . $this->merchant_order_id . "\", \n  \"status\":\"" . $this->fib_status . "\"\n}", // form data
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
//                    "Authorization: Bearer g246d49n9d88fb78994nvkqtl25j1szf"
                ),
            ));

            $response = curl_exec($curl);
            Log::info("FIB Response: $response");
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error("FIB Error: " . $err);
            }
//            Log::info("FIB call end($this->sub_order_id): " . date("Y-m-d H:i:s"));
        } catch (Exception $e) {
            Log::error("FIB Curl Error: " . $e);
        }
    }

}
