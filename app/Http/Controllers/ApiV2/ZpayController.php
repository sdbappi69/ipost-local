<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use Log;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use App\DeliveryTask;
use App\ZpayPaymentHistory;

use App\Http\Traits\SmsApi;

class ZpayController extends Controller
{
    use ApiResponseTrait;
    use SmsApi;

    public function zpay_ipn(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'status'        =>  'required',
                'tran_id'       =>  'required',
                'amount'        =>  'required'
            ]);

            if($validator->fails()) {
                return $this->sendResponse('error', 422, $validator->errors()->all(), []);
            }

            $delivery_task = DeliveryTask::where('unique_suborder_id', $request->tran_id)->first();
            
            switch ($request->status) {
                case 'VALID':
                        $message = "Success! ".$request->amount." TK received using Zpay for the order ".$request->tran_id.". Zpay Transection ID:".$request->bank_tran_id;
                        $log_status = 1;
                    break;

                case 'FAILED':
                        $message = "Failed! ".$request->amount." TK transection failed using Zpay for the order ".$request->tran_id.".";
                        $log_status = 0;
                    break;
                
                default:
                        $message = "Cancelled! ".$request->amount." TK transection cancelled using Zpay for the order ".$request->tran_id.".";
                        $log_status = 2;
                    break;
            }

            if($request->has('card_no')){
                $card_no = $request->card_no;
            }else{
                $card_no = '';
            }

            if($request->has('card_no')){
                $zpay_tran_id = $request->bank_tran_id;
            }else{
                $zpay_tran_id = '';
            }

            $zpayPaymentHistory = New ZpayPaymentHistory;
            $zpayPaymentHistory->unique_suborder_id = $request->tran_id;
            $zpayPaymentHistory->zpay_tran_id = $zpay_tran_id;
            $zpayPaymentHistory->card_no = $card_no;
            $zpayPaymentHistory->amount = $request->amount;
            $zpayPaymentHistory->status = $log_status;
            $zpayPaymentHistory->save();

            $this->sendCustomMessage("01681692786", $message, $zpayPaymentHistory->id);

            return $this->sendResponse('success', 200, [$message], []);

        } catch(\Exception $e) {

            Log::error($e->getMessage());
            return $this->sendResponse('error', 500, ['Something went wrong. Please try again.'], []);
        }
    }
}
