<?php

namespace App\Http\Controllers\ApiV2;

use App\Reason;
use Illuminate\Http\Request;
use Log;
use Validator;
use App\Http\Controllers\Controller;

class ReasonController extends Controller {

    use ApiResponseTrait;

    public function index(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                        'reason_type' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendResponse('error', 422, $validator->errors()->all(), []);
            }
            $reason_type = ucfirst($request->input('reason_type'));
            $query = Reason::whereType($reason_type)
                    ->orWhere('type', 'Both')
                    ->orderBy('reason', 'ASC');
            switch ($request->header('lang')) {
                case 'ar':
                    $query->select('id', 'reason_ar as reason');
                    break;
                case 'ku':
                    $query->select('id', 'reason_ku as reason');
                    break;
                default :
                    $query->select('id', 'reason');
                    break;
            }
            $reasons = $query->get();

            return $this->sendResponse('success', 200, [], $reasons);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->sendResponse('error', 500, ['Something went wrong. Please try again.'], []);
        }
    }

}
