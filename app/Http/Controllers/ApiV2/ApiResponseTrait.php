<?php
/**
 * Created by PhpStorm.
 * User: Mahmudul Alam
 * Date: 11/22/2016
 * Time: 2:25 PM
 */

namespace App\Http\Controllers\ApiV2;


trait ApiResponseTrait
{
    protected function sendResponse($status, $status_code, $message, $response = [])
    {
        $feedback['status']        =  $status;
        $feedback['status_code']   =  $status_code;
        $feedback['message']       =  $message;
        $feedback['response']      =  $response;

        return response($feedback, 200);
    }
}