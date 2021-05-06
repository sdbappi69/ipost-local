<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait SmsApi {

    /**
     * Send OTP SMS to user mobile no.
     *
     * @param $msisdn
     * @param $OTP
     * @param $additionalMessage
     */
    public function sendOTP($msisdn, $OTP, $additionalMessage = "") {
        $otpMessage = $additionalMessage . 'Your OTP is: ' . $OTP;

        $curl = curl_init();
        curl_setopt_array(
                $curl,
                array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => 'http://sms.sslwireless.com/pushapi/dynamic/server.php?user=' . env('SMS_USER') . '&pass=' . env('SMS_PASSWORD') . '&sid=' . env('SMS_STAKEHOLDER') . '&sms=' . urlencode($otpMessage) . '&msisdn=' . $msisdn . '&csmsid=123456789',
                    CURLOPT_USERAGENT => 'Sample cURL Request')
        );

        $resp = curl_exec($curl);
        curl_close($curl);
        Log::info($otpMessage);
        Log::info($resp);
    }

    /**
     * Send custom SMS to user mobile no.
     *
     * @param $msisdn
     * @param $sms
     */
    public function __sendCustomMessage($msisdn, $sms, $ref_id) {
        // return true;
        // $sms=strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $sms)));
        $msisdn = str_replace(' ', '', $msisdn);
        // $msisdn = '01'.substr($msisdn, -9);
        $curl = curl_init();
        curl_setopt_array(
                $curl,
                array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => 'http://sms.sslwireless.com/pushapi/dynamic/server.php?user=' . config('app.sms_user') . '&pass=' . config('app.sms_password') . '&sid=' . config('app.sid') . '&sms=' . urlencode($sms) . '&msisdn=' . $msisdn . '&csmsid=' . $ref_id,
                    CURLOPT_USERAGENT => 'Sample cURL Request'
                )
        );
        Log::info('http://sms.sslwireless.com/pushapi/dynamic/server.php?user=' . config('app.sms_user') . '&pass=' . config('app.sms_password') . '&sid=' . config('app.sid') . '&sms=' . urlencode($sms) . '&msisdn=' . $msisdn . '&csmsid=' . $ref_id);
        $resp = curl_exec($curl);
        curl_close($curl);
        Log::info($resp);

        return true;
    }

    // Global SMS
    public function sendCustomMessage($msisdn, $text, $ref_id, $from = "FastBazzar") {
        $user = "6u09fvgk";
        $pass = "RnZEkbu7";
        $sms = urlencode($text);
        $apiUrl = "https://api.smsglobal.com/http-api.php";
        $pageurl = "$apiUrl?action=sendsms&user=$user&password=$pass&from=$from&to=$msisdn&text=$sms&api=0";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $pageurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $mainhtml = curl_exec($ch);
        curl_close($ch);
        $res_arr = explode(";", $mainhtml);
        if (str_replace(" ", "", trim(strtoupper($res_arr[0]))) == "OK:0") {
            Log::info("sms sent: $pageurl");
        } else {
            Log::info("SMS Failed! $pageurl");
        }
        return "true";
    }

}
