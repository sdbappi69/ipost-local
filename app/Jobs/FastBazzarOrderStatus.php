<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class FastBazzarOrderStatus extends Job implements ShouldQueue {

    use InteractsWithQueue,
        Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    protected $sub_order_id;
    protected $merchant_order_id;
    protected $fb_url;
    protected $sub_order_status;
    protected $fb_status;
    public $retryAfter = 120;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sub_order_id, $merchant_order_id, $fb_url, $fb_status) {
        $this->sub_order_id = $sub_order_id;
        $this->merchant_order_id = $merchant_order_id;
        $this->fb_url = $fb_url;
        $this->fb_status = $fb_status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            Log::info("FastBazzar call start($this->sub_order_id): " . date("Y-m-d H:i:s"));

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->fb_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"orderNumber\":\"" . $this->merchant_order_id . "\", \n  \"status\":\"" . $this->fb_status . "\"\n}",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer g246d49n9d88fb78994nvkqtl25j1szf"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error("FastBazzar Error: " . $err);
            }
            Log::info("FastBazzar call end($this->sub_order_id): " . date("Y-m-d H:i:s"));
        } catch (Exception $e) {
            Log::error("FastBazzar Curl Error: " . $e);
        }
    }

}
