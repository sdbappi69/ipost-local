<?php

namespace App\Http\Controllers\ApiV2\Merchant;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Store;
use DB;

class MerchantApiLoginController extends Controller {

    

    /**
     * User login
     * @get User name , password , remember
     */
    public function login(Request $request) {
        $message = array();

        /**
         * get data from client side
         */
        
        $key = $request->input('key');
        $secret_key = '123456';
        $merchant_seq_key = '7d2ApP4';

        /**
         * If client key not match to server private key
         * @return exception bad request
         */
        if ($key !== $secret_key && $key !== $merchant_seq_key) {

            //$status        =  'Bad Request';
            $status_code = 401;
            $message[] = 'invalid secret key';
            //return $this->set_unauthorized($status, $status_code, $message, $response = '');
            return $this->set_unauthorized($status_code, $message, $response = '');

        }else if($key == $secret_key){

            $email = $request->input('email');
            $password = $request->input('password');
            
            /**
             * checking user data
             * @get user email and password
             * @return user object
             */
            $user_data = Auth::attempt(array(
                        'email' => $email,
                        'password' => $password,
                        'user_type_id' => 9,
                        'status' => 1
                            ), false);

            $feedback = [];

            if ($user_data) {
                /**
                 * Update user api_token every login success
                 */
                $usr = User::find(Auth::user()->id);
                if(is_null($usr->api_token)){
                    $usr->api_token = str_random(60);
                }
                $usr->save();


                /**
                 * set status and feedback
                 */
                //$status        =  'success';
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

                $user_info = User::select(['users.name', 'users.photo', 'users.email', 'users.api_token', 'users.msisdn', 'users.alt_msisdn', 'users.address1', 'users.latitude', 'users.longitude'])
                        ->where('users.id', $user->id)
                        ->first();

                $user_info['user_type'] = ['user_type_id' => $user->user_type_id, 'title' => $user_type_title];
                $user_info['country'] = ['country_id' => $user->country_id, 'name' => $country_name];
                $user_info['state'] = ['state_id' => $user->state_id, 'name' => $state_name];
                $user_info['city'] = ['city_id' => $user->city_id, 'name' => $city_name];
                $user_info['zone'] = ['zone_id' => $user->zone_id, 'name' => $zone_name];
                // return (response()->json($user_info));
                //$feedback['status']        =  $status;
                $feedback['status_code'] = $status_code;
                $feedback['message'] = $message;
                $feedback['response'] = $user_info;
            } else {
                /**
                 * This section is for unauthorized user Exception
                 */
                //$status        =  'Unauthorized';
                $status_code = 401;
                $message[] = 'Invalid authentication or access denied';

                //return $this->set_unauthorized($status, $status_code, $message, $response = '');
                return $this->set_unauthorized($status_code, $message, $response = '');
            }

            return response($feedback, 200);

        }else if($key == $merchant_seq_key){

            $store_id = $request->store_user;
            $store_password = $request->store_password;
            $store_data = Store::whereStatus(true)->where('store_id', $store_id)->where('store_password', $store_password)->first();
            if(!$store_data){
                $status_code = 401;
                $message[] = 'Invalid store or access denied';

                //return $this->set_unauthorized($status, $status_code, $message, $response = '');
                return $this->set_unauthorized($status_code, $message, $response = '');
            }

            $merchant_id = $store_data->merchant_id;
            $merchant_user = User::whereStatus(true)->where('user_type_id', 9)->where('reference_id', $merchant_id)->first();
            if(!$merchant_user){
                $status_code = 401;
                $message[] = 'Invalid merchant user or access denied';

                //return $this->set_unauthorized($status, $status_code, $message, $response = '');
                return $this->set_unauthorized($status_code, $message, $response = '');
            }

            $user_data = Auth::loginUsingId($merchant_user->id);

            $feedback = [];

            if ($user_data) {
                /**
                 * Update user api_token every login success
                 */
                $usr = User::find(Auth::user()->id);
                if(is_null($usr->api_token)){
                    $usr->api_token = str_random(60);
                }
                $usr->save();


                /**
                 * set status and feedback
                 */
                //$status        =  'success';
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

                $user_info = User::select(['users.name', 'users.photo', 'users.email', 'users.api_token', 'users.msisdn', 'users.alt_msisdn', 'users.address1', 'users.latitude', 'users.longitude'])
                        ->where('users.id', $user->id)
                        ->first();

                $user_info['user_type'] = ['user_type_id' => $user->user_type_id, 'title' => $user_type_title];
                $user_info['country'] = ['country_id' => $user->country_id, 'name' => $country_name];
                $user_info['state'] = ['state_id' => $user->state_id, 'name' => $state_name];
                $user_info['city'] = ['city_id' => $user->city_id, 'name' => $city_name];
                $user_info['zone'] = ['zone_id' => $user->zone_id, 'name' => $zone_name];
                // return (response()->json($user_info));
                //$feedback['status']        =  $status;
                $feedback['status_code'] = $status_code;
                $feedback['message'] = $message;
                $feedback['response'] = $user_info;
            } else {
                /**
                 * This section is for unauthorized user Exception
                 */
                //$status        =  'Unauthorized';
                $status_code = 401;
                $message[] = 'Invalid authentication or access denied';

                //return $this->set_unauthorized($status, $status_code, $message, $response = '');
                return $this->set_unauthorized($status_code, $message, $response = '');
            }

            return response($feedback, 200);

        }else{

            $status_code = 401;
            $message[] = 'Invalid access key or access denied';

            //return $this->set_unauthorized($status, $status_code, $message, $response = '');
            return $this->set_unauthorized($status_code, $message, $response = '');

        }

    }

    /**
     * Invalid data
     * @return status code 200
     */
    //private function set_unauthorized( $status, $status_code, $message, $response )
    private function set_unauthorized($status_code, $message, $response) {
        $feedback = [];
        //$feedback['status']        =  $status;
        $feedback['status_code'] = $status_code;
        $feedback['message'] = $message;
        // $feedback['response']      =  $response;

        return response($feedback, 200);
        ;
    }

}
