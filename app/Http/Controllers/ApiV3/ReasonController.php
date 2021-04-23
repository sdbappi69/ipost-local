<?php

namespace App\Http\Controllers\ApiV3;

use App\Reason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Log;
use Validator;

class ReasonController extends Controller {

    use ApiResponseTrait;

    public function index(Request $request) {
        try {
            $reasons = Reason::select('id', 'type', 'reason')
                            ->orderBy('reason', 'ASC')->get();

            $hubs = DB::table('rider_references')
                    ->join('hubs', 'hubs.id', '=', 'rider_references.reference_id')
                    ->select('hubs.id', 'hubs.title', 'hubs.address1 as address')
                    ->distinct()
                    ->where('rider_references.user_id', Auth::guard('api')->user()->id)
                    ->get();
            
            $allHubs = DB::table('hubs')
                    ->select('hubs.id', 'hubs.title', 'hubs.address1 as address')
                    ->where('status', 1)
                    ->get();

            return $this->sendResponse('success', 200, [], ['reasons' => $reasons, 'hubs' => $hubs, 'allHubs' => $allHubs]);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->sendResponse('error', 500, ['Something went wrong. Please try again.'], []);
        }
    }

    public function reasons(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                        'reason_type' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendResponse('error', 422, $validator->errors()->all(), []);
            }
            $reason_type = ucfirst($request->input('reason_type'));
            $reasons = Reason::select('id', 'reason')
                            ->whereType($reason_type)
                            ->orWhere('type', 'Both')
                            ->orderBy('reason', 'ASC')->get();

            return $this->sendResponse('success', 200, [], $reasons);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->sendResponse('error', 500, ['Something went wrong. Please try again.'], []);
        }
    }

}
