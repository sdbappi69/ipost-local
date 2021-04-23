<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Traits\LogsTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SubOrder;
use App\Trip;
use App\SuborderTrip;
use App\Order;
use App\User;
use App\DeliveryTask;
use App\ReturnedProduct;
use App\OrderProduct;
use App\Shelf;
use App\ShelfProduct;
use App\Rack;
use App\RackProduct;

use Session;
use Redirect;
use Validator;
use DB;

class ReturnDeliveryController extends Controller
{

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|vehiclemanager|inboundmanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sub_orders = DeliveryTask::select(array(
                                                'delivery_task.id AS task_id',
                                                'delivery_task.status',
                                                'so.id AS id',
                                                'so.unique_suborder_id',
                                                'so.updated_at',
                                                'o.id AS order_id',
                                                'o.unique_order_id AS unique_order_id',
                                                'o.delivery_address1',
                                                'o.delivery_name',
                                                'o.delivery_email',
                                                'o.delivery_msisdn',
                                                'o.delivery_alt_msisdn',
                                                'z.name AS delivery_zone',
                                                'z.hub_id AS delivery_hub_id',
                                                'c.name AS delivery_city',
                                                's.name AS delivery_state',
                                                'r.reason',
                                            ))
                                            ->join('sub_orders AS so', 'so.unique_suborder_id', '=', 'delivery_task.unique_suborder_id')
                                            ->join('orders AS o', 'o.id', '=', 'so.order_id')
                                            ->join('zones AS z', 'z.id', '=', 'o.delivery_zone_id')
                                            ->join('cities AS c', 'c.id', '=', 'z.city_id')
                                            ->join('states AS s', 's.id', '=', 'c.state_id')
                                            ->join('users AS u', 'u.id', '=', 'delivery_task.deliveryman_id')
                                            ->leftJoin('reasons AS r', 'r.id', '=', 'delivery_task.reason_id')
                                            ->where('u.reference_id', '=', auth()->user()->reference_id)
                                            ->where('delivery_task.status', '>' ,'2')
                                            ->get();

        $deliveryman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();
        return view('return-delivery.index', compact('sub_orders', 'deliveryman'));
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
    public function show($id, Request $request)
    {
        //echo "a"; die();
        $delivery_task = DeliveryTask::where('id', $id)->first();
        $sub_order_data = SubOrder::where('unique_suborder_id', $delivery_task->unique_suborder_id)->first();
        // return $request->type;
        if($request->type == 'pertial'){
            $products_data = ReturnedProduct::where('sub_order_id', $sub_order_data->id)->get();
            // $products_data = array();
            // foreach ($return_products AS $products) {
            //     $products_data[] = $products->product;
            //     $products_data['quantity'] = $products->quantity;
            // }
            // exit;
            // return $products_data;
        }else if($request->type == 'full'){
            $products_data = OrderProduct::where('sub_order_id', $sub_order_data->id)->get();
            //dd($products_data->toArray());
        }else{
            abort(403);
        }

        // Create Order
        // $unique_order_id = 'R'.$sub_order_data->order->unique_order_id;
        // $countOrder = Order::where('unique_order_id', $unique_order_id)->count();
        // if($countOrder == 0){
        //     $order = new Order();
        //     $order->store_id = $sub_order_data->order->store_id;
        //     $order->delivery_name = $sub_order_data->order->store->merchant->name;
        //     $order->delivery_email = $sub_order_data->order->store->merchant->email;
        //     $order->delivery_msisdn = $sub_order_data->order->store->merchant->msisdn;
        //     $order->delivery_alt_msisdn = $sub_order_data->order->store->merchant->alt_msisdn;
        //     $order->delivery_country_id = $sub_order_data->order->store->merchant->country_id;
        //     $order->delivery_state_id = $sub_order_data->order->store->merchant->state_id;
        //     $order->delivery_city_id = $sub_order_data->order->store->merchant->city_id;
        //     $order->delivery_zone_id = $sub_order_data->order->store->merchant->zone_id;
        //     $order->delivery_address1 = $sub_order_data->order->store->merchant->address1;
        //     $order->delivery_latitude = $sub_order_data->order->store->merchant->latitude;
        //     $order->delivery_longitude = $sub_order_data->order->store->merchant->longitude;
        //     $order->merchant_order_id = $sub_order_data->order->merchant_order_id;
        //     $order->unique_order_id = $unique_order_id;
        //     $order->verified_by = auth()->user()->id;
        //     $order->order_status = 5;
        //     $order->hub_id = auth()->user()->reference_id;
        //     $order->save();
        // }else{
        //     $order = Order::where('unique_order_id', $unique_order_id)->first();
        // }

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        // $this->orderLog(auth()->user()->id, $order->id, '', '', $sub_order_data->order->id, 'orders', 'Created a reverse order: '.$order->unique_order_id);

        // Create Sub-Order
        // $source_hub_id = auth()->user()->reference_id;
        // $destination_hub_id = $sub_order_data->order->hub_id;
        // $sub_order = new SubOrder();
        // $sub_order->unique_suborder_id = 'R'.$sub_order_data->unique_suborder_id;
        // $sub_order->order_id = $order->id;
        // if($source_hub_id == $destination_hub_id){
        //     $sub_order->sub_order_status = 7;

            // Update Order
            // $order_due = SubOrder::where('sub_order_status', '!=', '7')->where('order_id', '=', $order->id)->count();
            // if($order_due == 0){
            //     $order = Order::findOrFail($order->id);
            //     $order->order_status = '7';
            //     $order->save();

                // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                // $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) received');
        //     }

        // }else{
        //     $sub_order->sub_order_status = 5;
        // }
        // $sub_order->source_hub_id = $source_hub_id;
        // $sub_order->destination_hub_id = $destination_hub_id;
        // $sub_order->next_hub_id = $sub_order_data->order->hub_id;
        // $sub_order->responsible_user_id = $sub_order_data->responsible_user_id;
        // $sub_order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        // $this->orderLog(auth()->user()->id, $order->id, $sub_order->id, '', $sub_order_data->id, 'sub_orders', 'Created defult return Sub-Order: '.$sub_order->unique_suborder_id);

        // Insert Products
        foreach ($products_data as $row) {

            if($request->type == 'full'){
                //dd($row->toArray());
                $source_hub_id = auth()->user()->reference_id;
                $next_hub_id = $row->pickup_location->zone->hub_id;
                $delivery_hub_id = $row->pickup_location->zone->hub_id;

                $order_id = $row->order_id;
                $sub_order_id = $row->sub_order_id;
                $product_title = $row->product_title;
                $url = $row->product_title;
                $product_category_id = $row->product_category_id;
                $unit_price = $row->unit_price;
                $quantity = $row->quantity;
                $width = $row->width;
                $height = $row->height;
                $length = $row->length;
                $weight = $row->weight;
                $order_product_id = $row->id;
                // $pickup_location_id = ;
                // $picking_date = ;
                // $picking_time_slot_id = ;
                $status = 1;
                // $hub_id = $order->hub_id;

                $product = new ReturnedProduct();
                $product->product_unique_id = $row->product_unique_id."-R";
                $product->created_by = auth()->user()->id;
                $product->updated_by = auth()->user()->id;

                $product->order_product_id = $order_product_id;
                $product->sub_order_id = $sub_order_id;
                $product->order_id = $order_id;
                $product->product_title = $product_title;
                $product->url = $url;
                $product->product_category_id = $product_category_id;
                $product->quantity = $quantity;
                $product->width = $width;
                $product->height = $height;
                $product->length = $length;
                $product->weight = $weight;

                $product->source_hub_id = $source_hub_id;
                $product->next_hub_id = $next_hub_id;
                $product->delivery_hub_id = $delivery_hub_id;

                $product->status = $status;

                $product->unit_deivery_charge = $row->unit_deivery_charge/2;
                $product->total_delivery_charge = $product->unit_deivery_charge * $quantity;

                if($source_hub_id == $delivery_hub_id){
                    $product->hub_transfer = 1;
                    $product->hub_transfer_responsible_user_id = auth()->user()->id;
                }
               // dd($product->toArray());
                $product->save();

            }else{

                // $order_id = $order->id;
                // $sub_order_id = $sub_order->id;
                // $product_title = $row->product_title;
                // $url = $row->product_title;
                // $product_category_id = $row->product_category_id;
                // $unit_price = $row->unit_price;
                // $quantity = $row->quantity;
                // $width = $row->width;
                // $height = $row->height;
                // $length = $row->length;
                // $pickup_location_id = ;
                // $picking_date = ;
                // $picking_time_slot_id = ;
                $status = 1;
                // $hub_id = $order->hub_id;

                $source_hub_id = auth()->user()->reference_id;
                $next_hub_id = $row->product->pickup_location->zone->hub_id;
                $delivery_hub_id = $row->product->pickup_location->zone->hub_id;

                $product = ReturnedProduct::where('product_unique_id', $row->product_unique_id)->first();
                $product->updated_by = auth()->user()->id;
                $product->status = $status;

                $product->source_hub_id = $source_hub_id;
                $product->next_hub_id = $next_hub_id;
                $product->delivery_hub_id = $delivery_hub_id;

                if($source_hub_id == $delivery_hub_id){
                    $product->hub_transfer = 1;
                    $product->hub_transfer_responsible_user_id = auth()->user()->id;
                }

                $product->save();

            }

            $message = "Keep the product on return Shelf.";

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product->id, 'return_products', 'Product decided to return: '.$product->product_unique_id);

            // return $row->product->product_title;
        }

        // Close the task
        $dtask = DeliveryTask::findOrFail($id);
        $dtask->status = 2;
        $dtask->save();

        // Select Hubs
        // $source_hub_id = $sub_order->source_hub_id;
        // $delivery_hub_id = $sub_order->destination_hub_id;

        // Decide Shelf or Rack
        // $sub_order_size = OrderProduct::select(array(
        //                                             DB::raw("SUM(`width`) AS width"),
        //                                             DB::raw("SUM(`height`) AS height"),
        //                                             DB::raw("SUM(`length`) AS length"),
        //                                         ))
        //                                     ->where('sub_order_id', '=', $sub_order->id)
        //                                     ->where('status', '!=', '0')
        //                                     ->first();

        // if($source_hub_id == $delivery_hub_id){

        //     $rackData = Rack::whereStatus(true)->where('hub_id', '=', $delivery_hub_id)->get();
        //     if($rackData->count() != 0){
        //         foreach ($rackData as $rack) {
        //             $rackUsed = RackProduct::select(array(
        //                                                 DB::raw("SUM(`width`) AS total_width"),
        //                                                 DB::raw("SUM(`height`) AS total_height"),
        //                                                 DB::raw("SUM(`length`) AS total_length"),
        //                                             ))
        //                                         ->join('order_product AS op', 'op.id', '=', 'rack_products.product_id')
        //                                         ->where('rack_products.status', '=', '1')
        //                                         ->where('rack_products.rack_id', '=', $rack->id)
        //                                         ->first();
        //             $available_width = $rack->width - $rackUsed->width;
        //             $available_height = $rack->height - $rackUsed->height;
        //             $available_length = $rack->length - $rackUsed->length;

        //             if($available_width >= $sub_order_size->width && $available_height >= $sub_order_size->height && $available_length >= $sub_order_size->length){
        //                 $rack_id = $rack->id;
        //                 $message = "Please keep the product on ".$rack->rack_title;
        //                 break;
        //             }else{
        //                 $rack_id = 0;
        //                 $message = "Dedicated rack hasn't enough space. Please use defult rack";
        //             }
        //         }

        //     }else{
        //         $rack_id = 0;
        //         $message = "No Rack defined for this delivery zone.";
        //     }

        //     // Insert product on rack
        //     $sub_order_products = OrderProduct::where('sub_order_id', '=', $id)
        //                                         ->where('status', '!=', '0')
        //                                         ->get();

        //     foreach ($sub_order_products as $product) {
        //         $rack_suborder = new RackProduct();
        //         $rack_suborder->rack_id = $rack_id;
        //         $rack_suborder->product_id = $product->id;
        //         $rack_suborder->status = '1';
        //         $rack_suborder->created_by = auth()->user()->id;
        //         $rack_suborder->updated_by = auth()->user()->id;
        //         $rack_suborder->save();

        //         // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        //         $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, $rack_suborder->product_id, $rack_suborder->id, 'rack_products', 'Product racked');
        //     }

        // }else{

        //     $shelfData = Shelf::whereStatus(true)->where('hub_id', '=', $source_hub_id)->where('assignd_hub_id', '=', $delivery_hub_id)->get();
        //     if($shelfData->count() != 0){
        //         foreach ($shelfData as $shelf) {
        //             $shelfUsed = ShelfProduct::select(array(
        //                                                 DB::raw("SUM(`width`) AS total_width"),
        //                                                 DB::raw("SUM(`height`) AS total_height"),
        //                                                 DB::raw("SUM(`length`) AS total_length"),
        //                                             ))
        //                                         ->join('order_product AS op', 'op.id', '=', 'shelf_products.product_id')
        //                                         ->where('shelf_products.status', '=', '1')
        //                                         ->where('shelf_products.shelf_id', '=', $shelf->id)
        //                                         ->first();
        //             $available_width = $shelf->width - $shelfUsed->width;
        //             $available_height = $shelf->height - $shelfUsed->height;
        //             $available_length = $shelf->length - $shelfUsed->length;

        //             if($available_width >= $sub_order_size->width && $available_height >= $sub_order_size->height && $available_length >= $sub_order_size->length){
        //                 $shelf_id = $shelf->id;
        //                 $message = "Please keep the product on ".$shelf->rack_title;
        //                 break;
        //             }else{
        //                 $shelf_id = 0;
        //                 $message = "Dedicated shelf hasn't enough space. Please use defult shelf";
        //             }
        //         }

        //     }else{
        //         $shelf_id = 0;
        //         $message = "No Rack defined for this delivery zone.";
        //     }

        //     // Insert product on shelf
        //     $sub_order_products = OrderProduct::where('sub_order_id', '=', $id)
        //                                         ->where('status', '!=', '0')
        //                                         ->get();

        //     foreach ($sub_order_products as $product_row) {
        //         $shelf_product = new ShelfProduct();
        //         $shelf_product->shelf_id = $shelf_id;
        //         $shelf_product->product_id = $product_row->id;
        //         $shelf_product->status = '1';
        //         $shelf_product->created_by = auth()->user()->id;
        //         $shelf_product->updated_by = auth()->user()->id;
        //         $shelf_product->save();

        //         // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        //         $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, $shelf_product->product_id, $shelf_product->id, 'shelf_products', 'Product racked');
        //     }
        // }

        Session::flash('inventory', $message);
        return redirect('/return-delivery');
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

        $dtask = DeliveryTask::findOrFail($id);
        $dtask->status = 0;
        $dtask->save();

        // return $dtask->status;

        // Update Sub-Order
        $suborderUp = SubOrder::findOrFail($request->sub_order_id);
        $suborderUp->sub_order_status = '7';
        // $suborderUp->delivery_assigned_by = auth()->user()->id;
        // $suborderUp->deliveryman_id = $request->deliveryman_id;
        // $suborderUp->updated_at = $request->updated_at;
        $suborderUp->delivery_status = '0';
        $suborderUp->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $suborderUp->order_id, $suborderUp->id, '', $dtask->id, 'delivery_task', 'Delivery attempt failed');

        Session::flash('message', "Product keep on Pending Rack");
        return redirect('/return-delivery');
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
