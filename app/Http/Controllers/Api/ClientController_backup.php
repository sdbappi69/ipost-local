<?php

namespace App\Http\Controllers\Api;

use Validator;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\User;
use App\Order;
use App\SubOrder;


class ClientController extends Controller
{
   /**
   * Client registration
   * @param email, password, name, phone
   * @retuen success message for registration complete
   */
   public function registration( Request $request )
   {
      $validator = Validator::make($request->all(), [
         'name' => 'required|min:3',
         'email' => 'required|unique:users|email|max:50',
         'password' => 'required|min:6',
         'phone' => 'required|min:11',
         'date_of_birth' => 'required|min:6',
      ]);

      if ($validator->fails()) {
         $error            = $validator->errors();

         /**
         * Sending response
         * @return ErrorException
         */
         $status        =  'failed';
         $status_code   =  401;
         $message       =  'validation failed';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']['error']      =  $error->all();

         return response($feedback, 200);
      }
      else
      {
         // save user
         $user = new User;
         $user->email = $request->email;
         $user->password = bcrypt($request->password);
         $user->msisdn = $request->phone;
         $user->name = $request->name;
         $user->user_type_id = 13;
         $user->date_of_birth = $request->date_of_birth;
         // $user->country_id = 20; // Bangladesh
         // $user->state_id = 1; // Dhaka
         // $user->city_id = 18; // Dhaka
         // $user->zone_id = 1;
         // $user->created_by = 13;
         $user->status = 1;

         $user->save();

         /**
         * Sending response
         * @return success
         */
         $status        =  'success';
         $status_code   =  200;
         $message       =  'Registration success';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']['success'] =  "Registration completed. Now you can login.";

         return response($feedback, $status_code);

      }

   }

   /**
   * Client login
   * @param email, password, key
   * @return user object
   */
   public function login( Request $request )
   {
      /**
      * get data from client side
      */
      $email      = $request->input('email');
      $password   = $request->input('password');
      $key        = $request->input('key');
      $secret_key = '123456';

      /**
      * If client key not match to server private key
      * @return exception bad request
      */
      if( $key !== $secret_key )
      {
         $status        =  'Bad Request';
         $status_code   =  200;
         $message       =  'invalid secret key';
         return $this->set_unauthorized($status, $status_code, $message, $response = '');
      }

      /**
      * checking user data
      * @get user email and password
      * @return user object
      */
      $user_data  = Auth::attempt(array(
         'email'    => $email,
         'password' => $password,
         'user_type_id' => 13,
         'status'    => 1
      ), false);

      $feedback = [];

      if ($user_data)
      {
         /**
         * Update user api_token every login success
         */
         $usr           =  User::find(Auth::user()->id);
         $usr->api_token=  str_random(60);
         $usr->save();


         /**
         * set status and feedback
         */
         $status        =  'success';
         $status_code   =  200;
         $message       =  'login success';
         $user          =  Auth::user();

         /**
         * checking user input authentication
         * IF match logout this user and save user object for next query.
         */
         Auth::logout();

         /**
         * Generate user data
         */
         $user_type_title  =  \APIHelper::get_user_type_by_id($user->user_type_id)  ? \APIHelper::get_user_type_by_id($user->user_type_id)->title : null;
         $country_name     =  \APIHelper::get_country_by_id($user->country_id)      ? \APIHelper::get_country_by_id($user->country_id)->name : null;
         $state_name       =  \APIHelper::get_state_by_id($user->state_id)          ? \APIHelper::get_state_by_id($user->state_id)->name : null;
         $city_name        =  \APIHelper::get_city_by_id($user->city_id)            ? \APIHelper::get_city_by_id($user->city_id)->name : null;
         $zone_name        =  \APIHelper::get_zone_by_id($user->zone_id)            ? \APIHelper::get_zone_by_id($user->zone_id)->name : null;

         $user_info = User::select(['users.name','users.photo','users.email','users.api_token','users.msisdn','users.alt_msisdn','users.address1','users.address2','users.latitude','users.longitude'])
                           ->where('users.id', $user->id)
                           ->first();

         $user_info['user_type'] =  ['user_type_id' =>  $user->user_type_id,  'title'  => $user_type_title];
         $user_info['country']   =  ['country_id'   =>  $user->country_id,    'name'   => $country_name];
         $user_info['state']     =  ['state_id'     =>  $user->state_id,      'name'   => $state_name];
         $user_info['city']      =  ['city_id'      =>  $user->city_id,       'name'   => $city_name];
         $user_info['zone']      =  ['zone_id'      =>  $user->zone_id,       'name'   => $zone_name];
         // return (response()->json($user_info));

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']      =  $user_info;

      }
      else
      {
         /**
         * This section is for unauthorized user Exception
         */
         $status        =  'Unauthorized';
         $status_code   =  200;
         $message       =  'Invalid authentication or access denied';

         return $this->set_unauthorized($status, $status_code, $message, $response = '');
      }

      return response($feedback, $status_code);
   }


   /**
   * client order history
   * @param order_id, api_token
   * @note order_id is called unique_order_id not id of order
   * @return order full history
   */
   public function order_history( Request $request, $order_id )
   {
      $order_id   =  $order_id ?: 0;
      $order      = Order::whereStatus(true)->where('unique_order_id', $order_id)->first();

      if( $order )
      {
         $verifing      =  '';
         $verified      =  '';
         $picking       =  '';
         $picked        =  '';
         $deliveryman   =  [];
         $on_shipping   =  '';
         $shipping      =  '';
         $on_delivery   =  '';
         $delivered     =  '';

         if($order->order_status > 1)
         {
            $verifing   = 'Product Verifing';
            $verified   = 'Product has been verified';
         }
         elseif($order->order_status == 1)
         {
            $verifing   =  'Product Verifing';
         }
         if($order->order_status >= 5)
         {
            $picking    =  'Product Picking';
            $picked     =  'Product has been picked';
         }
         elseif($order->order_status >= 2)
         {
            $picking    =  'Product Picking';
         }
         // deliverman assign
         if($order->order_status >= 8)
         {
            $suborder        = SubOrder::where('order_id', $order->id)->first();
            $deliveryman_id  =  $suborder->deliveryman_id;
            /**
            * set deliveryman_info
            */
            $user                   =  User::find($deliveryman_id);
            $deliveryman['name']    =  $user->name;
            $deliveryman['photo']   =  $user->photo;
         }
         // END
         if($order->order_status >= 9)
         {
            $on_shipping=  'Product On Shipping';
            $shipping   =  'Product Shipping';
         }
         elseif($order->order_status >= 5)
         {
            $on_shipping=  'Product On Shipping';
         }
         if($order->order_status == 10)
         {
            $on_delivery=  'Product On Delivery';
            $delivered  =  'Product Delivered';
         }
         elseif($order->order_status == 9)
         {
            $on_delivery=  'Product On Delivery';
         }

         /**
         * order history
         */
         $history                    =  [];
         $history[0]['verifing']     =  $verifing;
         $history[0]['verified']     =  $verified;
         $history[0]['picking']      =  $picking;
         $history[0]['picked']       =  $picked;
         $history[0]['on_shipping']  =  $on_shipping;
         $history[0]['shipping']     =  $shipping;
         $history[0]['on_delivery']  =  $on_delivery;
         $history[0]['delivered']    =  $delivered;

         /**
         * shipping information
         */
         $shipping                            =  [];
         $shipping[0]['delivery_name']        =  $order->delivery_name;
         $shipping[0]['delivery_email']       =  $order->delivery_email;
         $shipping[0]['delivery_msisdn']      =  $order->delivery_msisdn;
         $shipping[0]['delivery_alt_msisdn']  =  $order->delivery_alt_msisdn;
         $shipping[0]['country']              =  $order->delivery_zone->city->state->country->name;
         $shipping[0]['state']                =  $order->delivery_zone->city->state->name;
         $shipping[0]['city']                 =  $order->delivery_zone->city->name;
         $shipping[0]['zone']                 =  $order->delivery_zone->name;
         $shipping[0]['address']              =  $order->delivery_address1;

         /**
         * product information
         */
         $product                            =  [];
         foreach($order->products as $key => $row)
         {
            $product[$key]['title']             =  $row->product_title;
            $product[$key]['quantity']          =  $row->quantity;
            $product[$key]['sub_total']         =  $row->sub_total;
            $product[$key]['payable_price']     =  $row->payable_product_price;
            $product[$key]['delivery_charge']   =  $row->total_delivery_charge;
            $product[$key]['total_payable_amount']   =  $row->total_payable_amount;
            $product[$key]['delivery_paid_amount']   =  $row->delivery_paid_amount;

         }

         /**
         * Payment Information
         */
         $payment                            =  [];
         $payment[0]['amount']               =  $order->total_product_price;
         $payment[0]['delivery_paid_amount'] =  $order->delivery_payment_amount;
         $payment[0]['COD']                  =  $order->total_amount;



         /**
         * Sending history
         * @return history
         */
         $status        =  'success';
         $status_code   =  200;
         $message       =  'Order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response'][0]['history']     =  $history;
         $feedback['response'][0]['shipping']    =  $shipping;
         $feedback['response'][0]['product']     =  $product;
         $feedback['response'][0]['payment']     =  $payment;
         $feedback['response'][0]['deliveryman'] =  $deliveryman;

         return response($feedback, $status_code);
      }
      else
      {
         /**
         * Sending response
         * @return ErrorException
         */
         $status        =  'failed';
         $status_code   =  401;
         $message       =  'No order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']      =  "";

         return response($feedback, 200);
      }

   }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
