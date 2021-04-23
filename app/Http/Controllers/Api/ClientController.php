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
use App\OrderLog;
use App\OrderProduct;
use App\Status;


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
         $message[]       =  'validation failed';

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
         $message[]       =  'Registration success';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         $feedback['response']['success'] =  "Registration completed. Now you can login.";

         return response($feedback, 200);

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
         $status_code   =  401;
         $message[]       =  'invalid secret key';
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
         $message[]       =  'login success';
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
         $status_code   =  404;
         $message[]       =  'Invalid authentication or access denied';

         return $this->set_unauthorized($status, $status_code, $message, $response = '');
      }

      return response($feedback, 200);
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

        // Order History
        $orderlogs = OrderLog::where('order_id', $order->id)->where('sub_order_id', '0')->where('product_id', '0')->get();
        $order_log = array();
        foreach ($orderlogs as $orderlog) {
            $date = (string)$orderlog->created_at;
            $order_log[] = array(
                                   'text' => $orderlog->text,
                                   'date' => $date,
                               );
        }

        // Sub Orders
        $sub_orders = array();
        foreach ($order->suborders as $suborder) {
            // Sub-Order History
            $suborderlogs =  OrderLog::where('order_id', $order->id)->where('sub_order_id', $suborder->id)->where('product_id', '0')->get();
            $sub_order_log = array();
            foreach ($suborderlogs as $suborderlog) {
                $date = (string)$suborderlog->created_at;
                $sub_order_log[] = array(
                                       'text' => $suborderlog->text,
                                       'date' => $date,
                                   );
            }

            // Delivery Man
            if(!empty($suborder->deliveryman_id)){
                $sub_order_rider_name = $suborder->deliveryman->name;
                $sub_order_rider_photo = $suborder->deliveryman->photo;
            }else{
                $sub_order_rider_name = '';
                $sub_order_rider_photo = '';
            }

            // Sub-Order Status
            if($suborder->sub_order_status > 8){
                $sub_order_status = 'Delivered';
            }elseif($suborder->sub_order_status > 5){
                $sub_order_status = 'In Transit';
            }elseif($suborder->sub_order_status > 3){
                $sub_order_status = 'Picked';
            }elseif($suborder->sub_order_status > 1){
                $sub_order_status = 'Verified';
            }else{
                $sub_order_status = '';
            }

            // Products
            $products = array();
            foreach ($suborder->products as $product) {
                // Product History
                $productlogs =  OrderLog::where('order_id', $order->id)->where('sub_order_id', $suborder->id)->where('product_id', $product->id)->get();
                $product_log = array();
                foreach ($productlogs as $productlog) {
                    $date = (string)$productlog->created_at;
                    $product_log[] = array(
                                           'text' => $productlog->text,
                                           'date' => $date,
                                       );
                }

                // Pickup Man
                if(!empty($product->deliveryman_id)){
                    $product_rider_name = $product->picker->name;
                    $product_rider_photo = $product->picker->photo;
                }else{
                    $product_rider_name = '';
                    $product_rider_photo = '';
                }

                // Sub-Order Status
                if($product->status > 8){
                    $product_status = 'Delivered';
                }elseif($product->status > 5){
                    $product_status = 'In Transit';
                }elseif($product->status > 3){
                    $product_status = 'Picked';
                }elseif($product->status > 1){
                    $product_status = 'Verified';
                }else{
                    $product_status = '';
                }

                $product_data = array(
                                    'product_id' => $product->product_unique_id,
                                    'product_title' => $product->product_title,
                                    'product_category' => $product->product_category->name,
                                    'pickup_location' => $product->pickup_location->title.', '.$product->pickup_location->address1.', '.$product->pickup_location->zone->name.', '.$product->pickup_location->zone->city->name.', '.$product->pickup_location->zone->city->state->name,
                                    'product_unit_price' => $product->unit_price,
                                    'product_unit_deivery_charge' => $product->unit_deivery_charge,
                                    'product_quantity' => $product->quantity,
                                    'product_payable_amount' => $product->total_payable_amount,
                                    'product_width' => $product->width,
                                    'product_height' => $product->height,
                                    'product_length' => $product->length,
                                    'product_pickup_attempts' => $product->picking_attempts,
                                    'product_rider_name' => $product_rider_name,
                                    'product_rider_photo' => $product_rider_photo,
                                    'product_log' => $product_log,
                                    'product_status' => $product_status,
                                );
                $products[] = $product_data;

            }

            $sub_order_data = array(
                                    'sub_order_id' => $suborder->unique_suborder_id,
                                    'sub_order_delivery_attempts' => $suborder->no_of_delivery_attempts,
                                    'sub_order_rider_name' => $sub_order_rider_name,
                                    'sub_order_rider_photo' => $sub_order_rider_photo,
                                    'sub_order_log' => $sub_order_log,
                                    'sub_order_status' => $sub_order_status,
                                    'products' => $products,
                                );
            $sub_orders[] = $sub_order_data;
        }

        // Order Status
        if($order->order_status > 8){
            $order_status = 'Complete';
        }elseif($order->order_status > 5){
            $order_status = 'In Transit';
        }elseif($order->order_status > 3){
            $order_status = 'Picked';
        }elseif($order->order_status > 1){
            $order_status = 'Verified';
        }else{
            $order_status = '';
        }

        $order_data = array(
                                'order_id' => $order->unique_order_id,
                                'order_merchant' => $order->store->merchant->name,
                                'order_shipping_address' => $order->delivery_address1.', '.$order->delivery_zone->name.', '.$order->delivery_zone->city->name.', '.$order->delivery_zone->city->state->name,
                                'order_log' => $order_log,
                                'order_status' => $order_status,
                                'sub_orders' => $sub_orders,
                            );

         /**
         * Sending history
         * @return history
         */
         $status        =  'success';
         $status_code   =  200;
         $message[]       =  'Order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         // $feedback['response'][0]['order']     =  $order_data;
         $feedback['response']     =  $order_data;

         return response($feedback, 200);
      }
      else
      {
         /**
         * Sending response
         * @return ErrorException
         */
         $status        =  'failed';
         $status_code   =  401;
         $message[]       =  'No order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         // $feedback['response']      =  "";

         return response($feedback, 200);
      }

   }


   public function open_order_history( Request $request, $order_id )
   {
      $order_id   =  $order_id ?: 0;
      $order      = Order::whereStatus(true)->where('unique_order_id', $order_id)->first();

      if( $order )
      {

        // Order History
        // $orderlogs = OrderLog::where('order_id', $order->id)->where('sub_order_id', '0')->where('product_id', '0')->get();
        // $order_log = array();
        // foreach ($orderlogs as $orderlog) {
        //     $date = (string)$orderlog->created_at;
        //     $order_log[] = array(
        //                            'text' => $orderlog->text,
        //                            'date' => $date,
        //                        );
        // }

        // Sub Orders
        $sub_orders = array();
        foreach ($order->suborders as $suborder) {
            // Sub-Order History
            // $suborderlogs =  OrderLog::where('order_id', $order->id)->where('sub_order_id', $suborder->id)->where('product_id', '0')->get();
            // $sub_order_log = array();
            // foreach ($suborderlogs as $suborderlog) {
            //     $date = (string)$suborderlog->created_at;
            //     $sub_order_log[] = array(
            //                            'text' => $suborderlog->text,
            //                            'date' => $date,
            //                        );
            // }

            // Delivery Man
            if(isset($suborder->deliveryman_id) && !is_null($suborder->deliveryman_id)){
                $sub_order_rider_name = $suborder->deliveryman->name;
                $sub_order_rider_photo = $suborder->deliveryman->photo;
            }else{
                $sub_order_rider_name = '';
                $sub_order_rider_photo = '';
            }

            // Sub-Order Status
            // if($suborder->sub_order_status > 8){
            //     $sub_order_status = 'Delivered';
            // }elseif($suborder->sub_order_status > 5){
            //     $sub_order_status = 'In Transit';
            // }elseif($suborder->sub_order_status > 3){
            //     $sub_order_status = 'Picked';
            // }elseif($suborder->sub_order_status > 1){
            //     $sub_order_status = 'Verified';
            // }else{
            //     $sub_order_status = '';
            // }
            $sub_order_status_data = Status::where('code', $suborder->sub_order_status)->first();
            if(count($sub_order_status_data) > 0){
              $sub_order_status = $sub_order_status_data->title;
            }else{
              $sub_order_status = 'Unknown';
            }

            // Products
            $products = array();
            foreach ($suborder->products as $product) {
                // Product History
                // $productlogs =  OrderLog::where('order_id', $order->id)->where('sub_order_id', $suborder->id)->where('product_id', $product->id)->get();
                // $product_log = array();
                // foreach ($productlogs as $productlog) {
                //     $date = (string)$productlog->created_at;
                //     $product_log[] = array(
                //                            'text' => $productlog->text,
                //                            'date' => $date,
                //                        );
                // }

                // Pickup Man
                if(!empty($product->deliveryman_id)){
                    $product_rider_name = $product->picker->name;
                    $product_rider_photo = $product->picker->photo;
                }else{
                    $product_rider_name = '';
                    $product_rider_photo = '';
                }

                // Sub-Order Status
                // if($product->status > 8){
                //     $product_status = 'Delivered';
                // }elseif($product->status > 5){
                //     $product_status = 'In Transit';
                // }elseif($product->status > 3){
                //     $product_status = 'Picked';
                // }elseif($product->status > 1){
                //     $product_status = 'Verified';
                // }else{
                //     $product_status = '';
                // }

                $product_data = array(
                                    // 'product_id' => $product->product_unique_id,
                                    'product_title' => $product->product_title,
                                    'product_category' => $product->product_category->name,
                                    // 'pickup_location' => $product->pickup_location->title.', '.$product->pickup_location->address1.', '.$product->pickup_location->zone->name.', '.$product->pickup_location->zone->city->name.', '.$product->pickup_location->zone->city->state->name,
                                    // 'product_unit_price' => $product->unit_price,
                                    // 'product_unit_deivery_charge' => $product->unit_deivery_charge,
                                    'product_quantity' => $product->quantity,
                                    // 'product_payable_amount' => $product->total_payable_amount,
                                    'product_width' => $product->width,
                                    'product_height' => $product->height,
                                    'product_length' => $product->length,
                                    // 'product_pickup_attempts' => $product->picking_attempts,
                                    // 'product_rider_name' => $product_rider_name,
                                    // 'product_rider_photo' => $product_rider_photo,
                                    // 'product_log' => $product_log,
                                    // 'product_status' => $product_status,
                                );
                $products[] = $product_data;

            }

            $sub_order_data = array(
                                    'sub_order_id' => $suborder->unique_suborder_id,
                                    // 'sub_order_delivery_attempts' => $suborder->no_of_delivery_attempts,
                                    // 'sub_order_rider_name' => $sub_order_rider_name,
                                    // 'sub_order_rider_photo' => $sub_order_rider_photo,
                                    // 'sub_order_log' => $sub_order_log,
                                    'sub_order_status' => $sub_order_status,
                                    'products' => $products,
                                );
            $sub_orders[] = $sub_order_data;
        }

        // Order Status
        if($order->order_status > 8){
            $order_status = 'Complete';
        }elseif($order->order_status > 5){
            $order_status = 'In Transit';
        }elseif($order->order_status > 3){
            $order_status = 'Picked';
        }elseif($order->order_status > 1){
            $order_status = 'Verified';
        }else{
            $order_status = '';
        }

        $order_data = array(
                                'order_id' => $order->unique_order_id,
                                'order_merchant' => $order->store->merchant->name,
                                // 'order_shipping_address' => $order->delivery_address1.', '.$order->delivery_zone->name.', '.$order->delivery_zone->city->name.', '.$order->delivery_zone->city->state->name,
                                // 'order_log' => $order_log,
                                'order_status' => $order_status,
                                'sub_orders' => $sub_orders,
                            );

         /**
         * Sending history
         * @return history
         */
         $status        =  'success';
         $status_code   =  200;
         $message[]       =  'Order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         // $feedback['response'][0]['order']     =  $order_data;
         $feedback['response']     =  $order_data;

         return response($feedback, 200);
      }
      else
      {
         /**
         * Sending response
         * @return ErrorException
         */
         $status        =  'failed';
         $status_code   =  401;
         $message[]       =  'No order found';

         $feedback['status']        =  $status;
         $feedback['status_code']   =  $status_code;
         $feedback['message']       =  $message;
         // $feedback['response']      =  "";

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
