<?php


namespace App\Http\Controllers\ApiV3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hub;
use App\RiderReference;
use App\User;
use App\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use Image;
use App\Http\Traits\SmsApi;
use Auth;

class RiderRegistrationController extends Controller
{
    use SmsApi;

    public function index()
    {
        $data['transparent_modes'] = VehicleType::whereStatus(true)->pluck('title', 'id')->toArray();
        $data['reference_list'] = Hub::whereStatus(true)->lists('title', 'id')->toArray();
        $data['rider_types'] = ['Freelancer', 'Permanent'];

        $feedback['status'] = 'success';
        $feedback['status_code'] = 200;
        $feedback['message'] = ['data for rider registration.'];
        $feedback['response'] = $data;

        return response($feedback, 200);
    }

    public function storeRider(Request $request)
    {
//        dd($request->all(), json_decode($request->rider_reference_id));
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'sometimes|email|unique:users,email',
            'msisdn' => 'required|between:10,25|unique:users,msisdn',
            'rider_reference_id' => 'required',
            'photo' => 'sometimes|image|max:2000',
            'password' => 'required|between:5,32',
            'rider_type' => 'required|boolean',
            'transparent_mode' => 'required|exists:vehicle_types,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->all(),
                'response' => [],
                'status_code' => 422
            ], 200);
        }
        $ref_ids = json_decode($request->rider_reference_id);

        /**multiple hub assign not allowed**/
//        if (count($ref_ids) > 1) {
//            return response()->json([
//                'status' => 'error',
//                'message' => ['Multiple Hub(reference id) not allowed'],
//                'response' => [],
//                'status_code' => 422
//            ], 200);
//        }
        try {
            DB::beginTransaction();

            $user = new User();

            $user->fill($request->except('rider_reference_id', 'rider_type', 'transparent_mode', 'msisdn_country', 'photo', 'password','lat','lng'));
            $user->user_type_id = 8;
            $user->rider_type = $request->rider_type;
            $user->transparent_mode = $request->transparent_mode;
            $user->password = bcrypt($request->password);
            $user->otp = 123456; // default opt set for testing
//            $user->otp = rand(100000, 999999);
            $user->status = 0;

            $zone = getZoneBound($request->lat, $request->lng);
//        dd($zone->id, $zone->city->id, $zone->city->state->id, $zone->city->state->country->id);
            if ($zone) {
                $user->zone_id = $zone->id;
                $user->city_id = $zone->city->id;
                $user->state_id = $zone->city->state->id;
                $user->country_id = $zone->city->state->country->id;
            }

            $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$request->lat,$request->lng&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM");
            $output = json_decode($geocode);
            $delivery_address = $output->results[0]->formatted_address;
            $user->address1 = $delivery_address;

            $user->save();

            $user->roles()->sync((array)8); //user role set

            foreach ($ref_ids as $ref_id) {
                $riderRef = new RiderReference();
                $riderRef->user_id = $user->id;
                $riderRef->reference_id = $ref_id;
                $riderRef->save();
            }

            /**user image set**/
            $user = User::find($user->id);
            if ($request->hasFile('photo')) {
                $fileExtent = $request->file('photo')->getClientOriginalExtension();
                $extent = array('jpeg', 'jpg', 'png', 'svg', 'JPEG', 'JPG', 'PNG', 'SVG');
                if (!in_array($fileExtent, $extent)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid Image.',
                        'response' => [],
                        'status_code' => 422
                    ], 200);
                }
                $fileName = $user->id . '.' . $fileExtent;
                $url = 'uploads/users/';

                $img = Image::make($request->file('photo'))->resize(200, 200)->save($url . $fileName);

                $user->photo = env('APP_URL') . $url . $fileName;
            } else {
                $url = 'uploads/users/';
                $user->photo = env('APP_URL') . $url . 'no_image.jpg';
            }
            $user->save();

            DB::commit();

            $this->sendOTP($user->msisdn, $user->otp, '');
            return response()->json([
                'status' => 'success',
                'message' => ['An OTP is sent to your number.'],
                'response' => ['msisdn' => $user->msisdn],
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

    public function otpVerify(Request $request)
    {
//        dd($request->all());
        $validation = Validator::make($request->all(), [
            'msisdn' => 'required',
            'otp' => 'required|numeric',
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
            $user->api_token = str_random(60);
            $user->otp_verified = 1;
            $user->save();

            $this->sendOTP($user->msisdn, $user->otp, '');
            return response()->json([
                'status' => 'success',
                'message' => ['OTP Verified.'],
                'response' => ['api_token' => $user->api_token],
                'status_code' => 200
            ], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json([
                'status' => 'error',
                'message' => ['Something went wrong, please try again.'],
                'response' => [],
                'status_code' => 500
            ], 200);
        }
    }

    public function updateProfile(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . Auth::guard('api')->user()->id,
            'msisdn' => 'required|between:10,25|unique:users,msisdn,' . Auth::guard('api')->user()->id,
            'rider_reference_id' => 'required',
            'photo' => 'sometimes|image|max:2000',
            'rider_type' => 'required|boolean',
            'transparent_mode' => 'required|exists:vehicle_types,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->all(),
                'response' => [],
                'status_code' => 422
            ], 200);
        }
        $ref_ids = json_decode($request->rider_reference_id);

        /**multiple hub assign not allowed**/
//        if (count($ref_ids) > 1) {
//            return response()->json([
//                'status' => 'error',
//                'message' => ['Multiple Hub(reference id) not allowed'],
//                'response' => [],
//                'status_code' => 422
//            ], 200);
//        }
        try {
            $user = User::find(Auth::guard('api')->user()->id);
            if ($request->hasFile('photo')) {
                $fileExtent = $request->file('photo')->getClientOriginalExtension();
                $extent = array('jpeg', 'jpg', 'png', 'svg', 'JPEG', 'JPG', 'PNG', 'SVG');
                if (!in_array($fileExtent, $extent)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid Image.',
                        'response' => [],
                        'status_code' => 422
                    ], 200);
                }
                $fileName = 'p_' . $user->id . '.' . $fileExtent;
                $url = 'uploads/users/';

                $img = Image::make($request->file('photo'))->resize(200, 200)->save($url . $fileName);

                $photo = env('APP_URL') . $url . $fileName;
            } else {
                $photo = '';
            }


            $tem_data = [
                'name' => $request->name,
                'email' => $request->email,
                'msisdn' => $request->msisdn,
                'rider_reference_id' => $request->rider_reference_id,
                'photo' => $photo,
                'rider_type' => $request->rider_type,
                'transparent_mode' => $request->transparent_mode,
            ];

            $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$request->lat,$request->lng&key=AIzaSyA9cwN7Zh-5ovTgvnVEXZFQABABa-KTBUM");
            $output = json_decode($geocode);
            $delivery_address = $output->results[0]->formatted_address;
            $tem_data['address1'] = $delivery_address;

            $zone = getZoneBound($request->lat, $request->lng);
            if ($zone) {
                $tem_data['zone_id'] = $zone->id;
                $tem_data['city_id'] = $zone->city->id;
                $tem_data['state_id'] = $zone->city->state->id;
                $tem_data['country_id'] = $zone->city->state->country->id;
            } else {
                $tem_data['zone_id'] = '';
                $tem_data['city_id'] = '';
                $tem_data['state_id'] = '';
                $tem_data['country_id'] = '';
            }

            $user->tem_info = json_encode($tem_data);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => ['Request send successful.'],
                'response' => [],
                'status_code' => 200
            ], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json([
                'status' => 'error',
                'message' => ['Something went wrong, please try again.'],
                'response' => [],
                'status_code' => 500
            ], 200);
        }
    }
}