<?php

namespace App\Http\Controllers\Api;

use App\Reason;
use Illuminate\Http\Request;
use Log;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReasonController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'reason_type'  =>  'required'
            ]);

            if($validator->fails()) {
                return $this->sendResponse('error', 422, $validator->errors()->all(), []);
            }

            $reasons = Reason::select('id', 'reason')
                                    ->whereType($request->input('reason_type'))
                                        ->orWhere('type', 'Both')
                                            ->orderBy('reason', 'ASC')->get();

            return $this->sendResponse('success', 200, [], $reasons);

        } catch(\Exception $e) {

            Log::error($e->getMessage());
            return $this->sendResponse('error', 500, ['Something went wrong. Please try again.'], []);
        }
    }
}
