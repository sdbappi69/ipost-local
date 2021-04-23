<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\User;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Http\Traits\SmsApi;

class UsersApiLoginController extends Controller
{
    use SmsApi;

    /**
     * User login
     * @get User name , password , remember
     */
    public function login(Request $request)
    {
        /**
         * get data from client side
         */
        $msisdn = $request->input('msisdn');
        $password = $request->input('password');
        $key = $request->input('key');
        $secret_key = '123456';

        /**
         * If client key not match to server private key
         * @return exception bad request
         */
        if ($key !== $secret_key) {
            $status = 'Bad Request';
            $status_code = 401;
            $message[] = 'invalid secret key';
            return $this->set_unauthorized($status, $status_code, $message, $response = '');
        }

        /**
         * checking user data
         * @get user msisdn and password
         * @return user object
         */
        $user_data = Auth::attempt(array(
            'msisdn' => $msisdn,
            'password' => $password,
            'user_type_id' => 8,
            'status' => 1
        ), false);

        $feedback = [];

        if ($user_data) {
            /**
             * Update user api_token every login success
             */
            $usr = User::find(Auth::user()->id);
            $usr->api_token = str_random(60);
            $usr->save();


            /**
             * set status and feedback
             */
            $status = 'success';
            $status_code = 200;
            $message[] = 'login success';
            $user = Auth::user();

            /**
             * checking user input authentication
             * IF match logout this user and save user object for next query.
             */
            Auth::logout();

            /**
             * Generate user data
             */
            $user_type_title = \APIHelper::get_user_type_by_id($user->user_type_id) ? \APIHelper::get_user_type_by_id($user->user_type_id)->title : null;
            $country_name = \APIHelper::get_country_by_id($user->country_id) ? \APIHelper::get_country_by_id($user->country_id)->name : null;
            $state_name = \APIHelper::get_state_by_id($user->state_id) ? \APIHelper::get_state_by_id($user->state_id)->name : null;
            $city_name = \APIHelper::get_city_by_id($user->city_id) ? \APIHelper::get_city_by_id($user->city_id)->name : null;
            $zone_name = \APIHelper::get_zone_by_id($user->zone_id) ? \APIHelper::get_zone_by_id($user->zone_id)->name : null;

            $user_info = User::select(['users.name', 'users.photo', 'users.email', 'users.api_token', 'users.msisdn', 'users.alt_msisdn', 'users.address1', 'users.address2', 'users.latitude', 'users.longitude'])
                ->where('users.id', $user->id)
                ->first();

            $user_info['user_type'] = ['user_type_id' => $user->user_type_id, 'title' => $user_type_title];
            $user_info['country'] = ['country_id' => $user->country_id, 'name' => $country_name];
            $user_info['state'] = ['state_id' => $user->state_id, 'name' => $state_name];
            $user_info['city'] = ['city_id' => $user->city_id, 'name' => $city_name];
            $user_info['zone'] = ['zone_id' => $user->zone_id, 'name' => $zone_name];
            // return (response()->json($user_info));

            $feedback['status'] = $status;
            $feedback['status_code'] = $status_code;
            $feedback['message'] = $message;
            $feedback['response'] = $user_info;

        } else {
            /**
             * This section is for unauthorized user Exception
             */
            $status = 'Unauthorized';
            $status_code = 404;
            $message[] = 'Invalid authentication or access denied';

            return $this->set_unauthorized($status, $status_code, $message, $response = '');
        }

        return response($feedback, 200);
    }

    /**
     * Invalid data
     * @return status code 200
     */
    private function set_unauthorized($status, $status_code, $message, $response)
    {
        $feedback = [];
        $feedback['status'] = $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
    }

    public function resetPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password' => 'required|between:5,32',
            'new_password' => 'required|between:5,32',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->all(),
                'response' => [],
                'status_code' => 422
            ], 200);
        }
        $user = User::findOrFail(Auth::guard('api')->user()->id);
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => ['Password not match'],
                'response' => [],
                'status_code' => 422
            ], 200);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => ['Password reset successfully.'],
            'response' => [],
            'status_code' => 200
        ], 200);
    }

    /**
     * receive msisdn
     * @return OTP
     */
    public function forgetPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'msisdn' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->all(),
                'response' => [],
                'status_code' => 422
            ], 200);
        }

        try {
            $user = User::where('msisdn', $request->msisdn)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['Invalid MSISDN'],
                    'response' => [],
                    'status_code' => 422
                ], 200);
            }
            $user->otp = rand(100000, 999999);
            $user->otp_verified = 0;
            $user->save();

            $this->sendOTP($user->msisdn, $user->otp, '');
            return response()->json([
                'status' => 'success',
                'message' => ['An OTP is sent to your number.'],
                'response' => [],
                'status_code' => 200
            ], 200);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return response()->json([
                'status' => 'error',
                'message' => ['Something went wrong, please try again.'],
                'response' => [],
                'status_code' => 500
            ], 200);
        }
    }

    public function newPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'msisdn' => 'required',
            'otp' => 'required|numeric',
            'password' => 'required|between:5,32',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->all(),
                'response' => [],
                'status_code' => 422
            ], 200);
        }
        $user = User::where('msisdn', $request->msisdn)->where('otp', $request->otp)->where('otp_verified', 0)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => ['Invalid OTP or MSISDN'],
                'response' => [],
                'status_code' => 422
            ], 200);
        }
        $user->api_token = str_random(60);
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => ['Password reset successfully.'],
            'response' => ['api_token' => $user->api_token],
            'status_code' => 200
        ], 200);
    }
}
