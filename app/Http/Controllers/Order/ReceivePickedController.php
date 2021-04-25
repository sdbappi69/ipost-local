<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Order;
use App\Hub;
use App\OrderProduct;
use App\SubOrder;
use App\ProductCategory;
use App\PickingLocations;
use App\PickingTimeSlot;
use App\User;
use App\Rack;
use App\RackProduct;
use App\Shelf;
use App\ShelfProduct;
use App\Status;
use App\Store;
use App\Merchant;
use App\State;
use App\City;
use App\Zone;
use App\SubOrderTripMap;
use DB;
use Session;
use Redirect;
use Validator;

class ReceivePickedController extends Controller {

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        $this->middleware('role:hubmanager|inboundmanager|inventoryoperator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $query = OrderProduct::select(array(
                    'order_product.id AS id',
                    'order_product.product_unique_id',
                    'order_product.product_title',
                    'order_product.quantity',
                    'order_product.picking_date',
                    'pl.title',
                    'pl.msisdn',
                    'pl.alt_msisdn',
                    'pl.address1',
                    'pt.start_time',
                    'pt.end_time',
                    'pc.name AS product_category',
                    'z.name AS zone_name',
                    'c.name AS city_name',
                    's.name AS state_name',
                    'o.id AS order_id',
                    'o.unique_order_id AS unique_order_id',
                    'so.unique_suborder_id',
                ))
                ->where('so.sub_order_status', '=', '10')
                ->where(function ($q) {
                    $q->where(function ($q1) {
                        $q1->where('z.hub_id', '=', auth()->user()->reference_id);
                        $q1->where('order_product.picking_status', '=', '1');
                    });
                    $q->orWhere(function ($q2) {
                        $q2->where('so.source_hub_id', '=', auth()->user()->reference_id);
                        $q2->where('so.return', 1);
                    });
                })
                ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->leftJoin('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->leftJoin('stores', 'stores.id', '=', 'o.store_id')
                ->leftJoin('merchants', 'merchants.id', '=', 'stores.merchant_id')
                ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->leftJoin('cities AS c', 'c.id', '=', 'z.city_id')
                ->leftJoin('states AS s', 's.id', '=', 'c.state_id')
                ->leftJoin('consignments_tasks AS ct', 'ct.sub_order_id', '=', 'so.id')
                ->leftJoin('consignments_common AS con', 'con.id', '=', 'ct.consignment_id');

        if ($request->has('order_id')) {
            $query->where('o.unique_order_id', $request->order_id);
        }

        if ($request->has('merchant_order_id')) {
            $query->where('o.merchant_order_id', $request->merchant_order_id);
        }

        if ($request->has('sub_order_id')) {
            $query->where('so.unique_suborder_id', $request->sub_order_id);
        }

        if ($request->has('sub_order_status')) {
            $query->where('so.sub_order_status', $request->sub_order_status);
        }

        if ($request->has('customer_mobile_no')) {
            $query->where('o.delivery_msisdn', $request->customer_mobile_no)->orWhere('o.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if ($request->has('store_id')) {
            $query->where('o.store_id', $request->store_id);
        }

        if ($request->has('merchant_id')) {
            $query->where('stores.merchant_id', $request->merchant_id);
        }

        if ($request->has('pickup_man_id')) {
            $query->where('order_product.picker_id', $request->pickup_man_id);
        }

        if ($request->has('delivary_man_id')) {
            $query->where('so.deliveryman_id', $request->delivary_man_id);
        }

        if ($request->has('pickup_zone_id')) {
            $query->where('pl.zone_id', $request->pickup_zone_id);
        }

        if ($request->has('delivery_zone_id')) {
            $query->where('o.delivery_zone_id', $request->delivery_zone_id);
        }

        if ($request->has('consignment_id')) {
            $query->where('con.consignment_unique_id', $request->consignment_id);
        }

        if ($request->has('start_date')) {
            $start_date = $request->start_date;
        } else {
            $start_date = '2017-03-21';
        }

        if ($request->has('end_date')) {
            $end_date = $request->end_date;
        } else {
            $end_date = date('Y-m-d');
        }
        $query->WhereBetween('so.updated_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));

        $products = $query->orderBy('so.id', 'desc')->paginate(10);

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $pickupman = User::
                        select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'), 'users.id')
                        ->leftJoin('hubs', 'hubs.id', '=', 'users.reference_id')
                        ->where('reference_id', '=', auth()->user()->reference_id)
                        ->where('users.status', true)->where('users.user_type_id', '=', '8')->lists('name', 'users.id')->toArray();

        $zones = Zone::
                        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'), 'zones.id')
                        ->leftJoin('cities', 'cities.id', '=', 'zones.city_id')->
                        where('zones.status', true)->lists('name', 'zones.id')->toArray();

        return view('receive-picked.index', compact('products', 'stores', 'merchants', 'pickupman', 'zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $product_unique_id = $request->product_unique_id;

        $products = OrderProduct::select(array(
                    'order_product.id AS id',
                    'order_product.product_unique_id',
                    'order_product.product_title',
                    'order_product.quantity',
                    'order_product.picking_date',
                    'pl.title',
                    'pl.msisdn',
                    'pl.alt_msisdn',
                    'pl.address1',
                    'pt.start_time',
                    'pt.end_time',
                    'pc.name AS product_category',
                    'z.name AS zone_name',
                    'c.name AS city_name',
                    's.name AS state_name',
                    'o.id AS order_id',
                    'o.unique_order_id AS unique_order_id',
                    'so.unique_suborder_id',
                ))
                ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->join('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->join('cities AS c', 'c.id', '=', 'z.city_id')
                ->join('states AS s', 's.id', '=', 'c.state_id')
                // ->where('o.order_status', '=', '3')
                // ->where('order_product.status', '=', '4')
                // ->where('order_product.hub_transfer', '=', '0')
                ->where('so.sub_order_status', '=', '10')
                ->orderBy('order_product.id', 'desc')
                ->where(function ($q) {
                    $q->where(function ($q1) {
                        $q1->where('z.hub_id', '=', auth()->user()->reference_id);
                        $q1->where('order_product.picking_status', '=', '1');
                    });
                    $q->orWhere(function ($q2) {
                        $q2->where('so.source_hub_id', '=', auth()->user()->reference_id);
                        $q2->where('so.return', 1);
                    });
                })
                ->paginate(1);

        return view('receive-picked.index', compact('products'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $product = OrderProduct::where('id', '=', $id)->first();
        $categories = ProductCategory::select(array(
                    'product_categories.id AS id',
                    DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
                ))
                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                // ->where('product_categories.parent_category_id', '!=', null)
                ->where('product_categories.status', '=', '1')
                ->where('pc.status', '=', '1')
                ->lists('cat_name', 'id')
                ->toArray();
        $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', $product->order->store->merchant->id)->lists('title', 'id')->toArray();
        $picking_time_slot = PickingTimeSlot::addSelect(DB::raw("CONCAT(day,' (',start_time,' - ',end_time,')') AS title"), "id")->whereStatus(true)->lists("title", "id")->toArray();

        $suborders = SubOrder::whereStatus(true)->where("order_id", $product->sub_order_id)->first();
//        dd($suborders);
//        $responsible_user = User::whereStatus(true)->where('user_type_id', '=', '7')->where('reference_id', '=', auth()->user()->reference_id)->first();
//        $responsible_user_id = $responsible_user->id;
        $responsible_user_id = auth()->user()->reference_id; //making hum manager as responsible user
        $vehiclemanager = User::whereStatus(true)->where('user_type_id', '=', '7')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        $sub_order_status_list = Status::where('active', '1')->orderBy('id', 'asc')->lists('title', 'id')->toArray();

        return view('receive-picked.edit', compact('categories', 'warehouse', 'product', 'picking_time_slot', 'suborders', 'vehiclemanager', 'responsible_user_id', 'sub_order_status_list'));
    }

    public function bulk_verify(Request $request) {

        $products = $request->product_id;

        if (count($products) > 0) {

            foreach ($products as $id) {

                $product = OrderProduct::where('id', '=', $id)->first();

                // Select Hubs
                $picking_hub_id = $product->sub_order->source_hub_id;
                $delivery_hub_id = $product->sub_order->destination_hub_id;
                $master_hub_id = $product->order->hub_id;

                if ($master_hub_id != $picking_hub_id) {
                    // return 0;
                    $product->hub_transfer = '1';
                    $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
                    // $delivery_hub_id = $master_hub_id;
                } else if ($delivery_hub_id != $picking_hub_id) {
                    // return 10;
                    $product->hub_transfer = '1';
                    $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
                }
                // return 1;
                if ($master_hub_id == auth()->user()->reference_id) {
                    $product->status = '5';
                }
                $product->save();

                $message = '';

                // Decide Hub or Rack
                if ($picking_hub_id == $delivery_hub_id) { // Go for Rack
                    $rackData = Rack::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->get();
                    if ($rackData->count() != 0) {
                        foreach ($rackData as $rack) {
                            $rackUsed = RackProduct::select(array(
                                        DB::raw("SUM(op.width) AS total_width"),
                                        DB::raw("SUM(op.height) AS total_height"),
                                        DB::raw("SUM(op.length) AS total_length"),
                                    ))
                                    ->join('order_product AS op', 'op.id', '=', 'rack_products.product_id')
                                    ->where('rack_products.status', '=', '1')
                                    ->where('rack_products.rack_id', '=', $rack->id)
                                    ->first();
                            $available_width = $rack->width - $rackUsed->width;
                            $available_height = $rack->height - $rackUsed->height;
                            $available_length = $rack->length - $rackUsed->length;

                            if ($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length) {
                                $rack_id = $rack->id;
                                $message = $message . "$product->product_unique_id: Please keep the product on " . $rack->rack_title . "\n";
                                break;
                            } else {
                                $rack_id = 0;
                                $message = $message . "$product->product_unique_id: Dedicated rack hasn't enough space. Please use defult rack" . "\n";
                            }
                        }
                    } else {
                        $rack_id = 0;
                        $message = $message . "$product->product_unique_id: No Rack defined for this delivery zone." . "\n";
                    }

                    // $sub_order = SubOrder::findOrFail($product_info->sub_order_id);
                    $orderDetail = Order::findOrFail($product->order_id);
                    foreach ($orderDetail->suborders as $row) {

                        $verify_suborder = OrderProduct::where('sub_order_id', '=', $row->id)->where('status', '!=', '0')->count();
                        if ($verify_suborder == 0) {
                            $sub_order = SubOrder::where('id', '=', $row->id)->delete();
                        } else {

                            $sub_order = SubOrder::findOrFail($row->id);
                            $sub_order->responsible_user_id = $request->responsible_user_id;
                            $sub_order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('sub_order_id', '=', $row->id)->count();
                            // if($sub_order_due == 0){
                            //     $sub_order->sub_order_status = '7';
                            // }
                            $sub_order->destination_hub_id = $delivery_hub_id;
                            $sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order->id)->orderBy('id', 'asc')->first();

                            if ($sub_order_trip_map) {
                                $sub_order->next_hub_id = $sub_order_trip_map->hub_id;
                            } else {
                                $sub_order->next_hub_id = $delivery_hub_id;
                            }
                            $sub_order->current_hub_id = auth()->user()->reference_id;
                            $sub_order->save();

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            // $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $delivery_hub_id, 'hubs', 'Decided destination for Sub-Order: '.$sub_order->unique_suborder_id);
                        }
                    }

                    // Insert product on rack
                    $rack_product = new RackProduct();
                    $rack_product->rack_id = $rack_id;
                    $rack_product->product_id = $product->id;
                    $rack_product->status = '1';
                    $rack_product->created_by = auth()->user()->id;
                    $rack_product->updated_by = auth()->user()->id;
                    $rack_product->save();

                    // Update Sub-Order Status
                    $this->suborderStatus($product->sub_order->id, '26');

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    // $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $rack_product->id, 'rack_products', 'Product racked');
                    // Update Order
                    // $order_due = SubOrder::where('sub_order_status', '!=', '7')->where('status', '!=', '0')->where('order_id', '=', $orderDetail->id)->count();
                    // if($order_due == 0){
                    //     $order = Order::findOrFail($orderDetail->id);
                    //     // $order->order_status = '7';
                    //     $order->save();
                    //     // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    //     // $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) on destination');
                    // }
                } else {

                    // return 1;

                    $shelfData = Shelf::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->where('assignd_hub_id', '=', $delivery_hub_id)->where('shelf_type', '=', 'delivery')->get();

                    if ($shelfData->count() != 0) {

                        foreach ($shelfData as $shelf) {
                            $shelfUsed = ShelfProduct::select(array(
                                        DB::raw("SUM(op.width) AS total_width"),
                                        DB::raw("SUM(op.height) AS total_height"),
                                        DB::raw("SUM(op.length) AS total_length"),
                                    ))
                                    ->join('order_product AS op', 'op.id', '=', 'shelf_products.product_id')
                                    ->where('shelf_products.status', '=', '1')
                                    ->where('shelf_products.shelf_id', '=', $shelf->id)
                                    ->first();
                            $available_width = $shelf->width - $shelfUsed->width;
                            $available_height = $shelf->height - $shelfUsed->height;
                            $available_length = $shelf->length - $shelfUsed->length;

                            if ($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length) {
                                $shelf_id = $shelf->id;
                                $message = $message . "$product->product_unique_id: Please keep the product on " . $shelf->shelf_title . "\n";
                                break;
                            } else {
                                $shelf_id = 0;
                                $message = $message . "$product->product_unique_id: Dedicated rack hasn't enough space. Please use defult rack" . "\n";
                            }
                        }
                    } else {
                        $shelf_id = 0;
                        $message = $message . "$product->product_unique_id: No Shelf defined for this delivery hub." . "\n";
                    }

                    $orderDetail = Order::findOrFail($product->order_id);
                    foreach ($orderDetail->suborders as $row) {

                        $verify_suborder = OrderProduct::where('sub_order_id', '=', $row->id)->where('status', '!=', '0')->count();
                        if ($verify_suborder == 0) {
                            $sub_order = SubOrder::where('id', '=', $row->id)->delete();
                        } else {

                            $sub_order = SubOrder::findOrFail($row->id);
                            $sub_order->responsible_user_id = $request->responsible_user_id;
                            // $sub_order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('sub_order_id', '=', $row->id)->count();
                            // if($sub_order_due == 0){
                            // $sub_order->sub_order_status = '5';
                            $sub_order->destination_hub_id = $delivery_hub_id;
                            $sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order->id)->orderBy('id', 'asc')->first();
                            if ($sub_order_trip_map) {
                                $sub_order->next_hub_id = $sub_order_trip_map->hub_id;
                            } else {
                                $sub_order->next_hub_id = $delivery_hub_id;
                            }
                            $sub_order->source_hub_id = auth()->user()->reference_id;
                            $sub_order->current_hub_id = auth()->user()->reference_id;
                            // }
                            $sub_order->save();

                            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                            // $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $delivery_hub_id, 'hubs', 'Decided destination for Sub-Order: '.$sub_order->unique_suborder_id);
                        }
                    }

                    // Insert product on rack
                    $shelf_product = new ShelfProduct();
                    $shelf_product->shelf_id = $shelf_id;
                    $shelf_product->product_id = $product->id;
                    $shelf_product->status = '1';
                    $shelf_product->created_by = auth()->user()->id;
                    $shelf_product->updated_by = auth()->user()->id;
                    $shelf_product->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    // $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $shelf_product->shelf_id, 'shelf_products', 'Product shelfed');
                    // Update Sub-Order Status
                    $this->suborderStatus($product->sub_order->id, '15');
                }
            }
        }

        Session::flash('message', $message);
        return redirect('/receive-picked');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //echo "a"; die();
//         dd($request->all());
        $validation = Validator::make($request->all(), [
                    'responsible_user_id' => 'required',
                    'product_title' => 'required',
                    'url' => 'sometimes',
                    'product_category_id' => 'required|numeric',
                    'unit_price' => 'required|numeric',
                    // 'quantity' => 'required|numeric',
                    'width' => 'required|numeric',
                    'height' => 'required|numeric',
                    'length' => 'required|numeric',
                    'pickup_location_id' => 'required|numeric',
                    'picking_date' => 'required|date',
                    'tm_delivery_status' => 'required|boolean',
                        // 'sub_order_status' => 'required',
        ]);

        // return $request->sub_order_status;

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $product_info = OrderProduct::where('id', '=', $id)->first();
        $product_category = ProductCategory::whereStatus(true)->where('id', '=', $request->product_category_id)->first();
        $pickup_location = PickingLocations::whereStatus(true)->where('id', '=', $request->pickup_location_id)->first();

        // Select Hubs
        $picking_hub_id = $product_info->sub_order->source_hub_id;
        $delivery_hub_id = $product_info->sub_order->destination_hub_id;
        $master_hub_id = $product_info->order->hub_id;
        
        // Call Charge Calculation API
        $post = [
            'store_id' => $product_info->sub_order->order->store->store_id,
            'width' => $request->width,
            'height' => $request->height,
            'length' => $request->length,
            'weight' => $request->weight,
            'product_category' => $product_category->name,
            'pickup_zone_id' => $pickup_location->zone_id,
            'delivery_zone_id' => $delivery_hub_id,
            'quantity' => $product_info->quantity,
            'unit_price' => $request->unit_price,
        ];
        // return env('APP_URL').'api/charge-calculator';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config("app.url") . 'api/charge-calculator');
        // $ch = curl_init(env('APP_URL').'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

        $response = curl_exec($ch);
//        dd($response);
        $charges = json_decode($response);
//        $charges = $charges[0];
//        dd($charges);
        if ($charges->status == 'Failed') {
            abort(403);
        }

        $product = OrderProduct::findOrFail($id);
        $product->fill($request->except('responsible_user_id', 'sub_order_status','tm_delivery_status'));
        $product->sub_total = $request->unit_price * $product->quantity;
        $product->updated_by = auth()->user()->id;
        $product->unit_deivery_charge = $charges->product_unit_delivery_charge;
        $product->total_delivery_charge = $charges->product_delivery_charge;
        $payable_product_price = ($product_info->sub_order->order->percent_of_collection / 100) * $product->sub_total;
        $product->payable_product_price = $payable_product_price;
        if ($product->delivery_pay_by_cus == 1) {
            $product->total_payable_amount = $payable_product_price + $charges->product_delivery_charge;
        } else {
            $product->total_payable_amount = $payable_product_price;
        }



        if ($master_hub_id != $picking_hub_id) {
            // return 0;
            $product->hub_transfer = '1';
            $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
            $delivery_hub_id = $master_hub_id;
        } else if ($delivery_hub_id != $picking_hub_id) {
            // return 10;
            $product->hub_transfer = '1';
            $product->hub_transfer_responsible_user_id = $request->responsible_user_id;
        }
        // return 1;
        if ($master_hub_id == auth()->user()->reference_id) {
            $product->status = '5';
        }
        $product->save();

        // Update Sub-Order Status
        // $this->suborderStatus($product->sub_order->id, $request->sub_order_status);
        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $request->sub_order_status, 'status', 'Product verified at ' . $product_info->pickup_location->zone->hub->title);

        $order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('order_id', '=', $product_info->order_id)->count();
        $order_status = 4;
        if ($order_due == 0) {
            $order = Order::findOrFail($product_info->order_id);
            $order->updated_by = auth()->user()->id;
            // $order->order_status = '5';
            $order->save();

            $order_status = $order->order_status;
            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'All products picked');
        }

        $sub_order = SubOrder::findOrFail($product_info->sub_order_id);
        $sub_order->updated_by = auth()->user()->id;
        $sub_order->tm_delivery_status = $request->tm_delivery_status;
        $sub_order->responsible_user_id = $request->responsible_user_id;
        // $sub_order->sub_order_status = $order_status;
        $sub_order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $sub_order->responsible_user_id, 'users', 'Assigned a responsible for the Sub-Order');

        // Decide Hub or Rack
        if ($picking_hub_id == $delivery_hub_id) { // Go for Rack
            $rackData = Rack::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->get();
            if ($rackData->count() != 0) {
                foreach ($rackData as $rack) {
                    $rackUsed = RackProduct::select(array(
                                DB::raw("SUM(op.width) AS total_width"),
                                DB::raw("SUM(op.height) AS total_height"),
                                DB::raw("SUM(op.length) AS total_length"),
                            ))
                            ->join('order_product AS op', 'op.id', '=', 'rack_products.product_id')
                            ->where('rack_products.status', '=', '1')
                            ->where('rack_products.rack_id', '=', $rack->id)
                            ->first();
                    $available_width = $rack->width - $rackUsed->width;
                    $available_height = $rack->height - $rackUsed->height;
                    $available_length = $rack->length - $rackUsed->length;

                    if ($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length) {
                        $rack_id = $rack->id;
                        $message = "Please keep the product on " . $rack->rack_title;
                        break;
                    } else {
                        $rack_id = 0;
                        $message = "Dedicated rack hasn't enough space. Please use defult rack";
                    }
                }
            } else {
                $rack_id = 0;
                $message = "No Rack defined for this delivery zone.";
            }

            // $sub_order = SubOrder::findOrFail($product_info->sub_order_id);
            $orderDetail = Order::findOrFail($product_info->order_id);
            foreach ($orderDetail->suborders as $row) {

                $verify_suborder = OrderProduct::where('sub_order_id', '=', $row->id)->where('status', '!=', '0')->count();
                if ($verify_suborder == 0) {
                    $sub_order = SubOrder::where('id', '=', $row->id)->delete();
                } else {

                    $sub_order = SubOrder::findOrFail($row->id);
                    $sub_order->responsible_user_id = $request->responsible_user_id;
                    $sub_order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('sub_order_id', '=', $row->id)->count();
                    // if($sub_order_due == 0){
                    //     $sub_order->sub_order_status = '7';
                    // }
                    $sub_order->destination_hub_id = $delivery_hub_id;
                    $sub_order->next_hub_id = $delivery_hub_id;
                    $sub_order->current_hub_id = auth()->user()->reference_id;
                    $sub_order->save();

                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $delivery_hub_id, 'hubs', 'Decided destination for Sub-Order: ' . $sub_order->unique_suborder_id);
                }
            }

            // Insert product on rack
            $rack_product = new RackProduct();
            $rack_product->rack_id = $rack_id;
            $rack_product->product_id = $product_info->id;
            $rack_product->status = '1';
            $rack_product->created_by = auth()->user()->id;
            $rack_product->updated_by = auth()->user()->id;
            $rack_product->save();

            // Update Sub-Order Status
            $this->suborderStatus($product->sub_order->id, '26');

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $rack_product->id, 'rack_products', 'Product racked');

            // Update Order
            $order_due = SubOrder::where('sub_order_status', '!=', '7')->where('status', '!=', '0')->where('order_id', '=', $orderDetail->id)->count();
            if ($order_due == 0) {
                $order = Order::findOrFail($orderDetail->id);
                // $order->order_status = '7';
                $order->save();

                // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) on destination');
            }
        } else {

            // return 1;

            $shelfData = Shelf::whereStatus(true)->where('hub_id', '=', $picking_hub_id)->where('assignd_hub_id', '=', $delivery_hub_id)->where('shelf_type', '=', 'delivery')->get();

            if ($shelfData->count() != 0) {

                foreach ($shelfData as $shelf) {
                    $shelfUsed = ShelfProduct::select(array(
                                DB::raw("SUM(op.width) AS total_width"),
                                DB::raw("SUM(op.height) AS total_height"),
                                DB::raw("SUM(op.length) AS total_length"),
                            ))
                            ->join('order_product AS op', 'op.id', '=', 'shelf_products.product_id')
                            ->where('shelf_products.status', '=', '1')
                            ->where('shelf_products.shelf_id', '=', $shelf->id)
                            ->first();
                    $available_width = $shelf->width - $shelfUsed->width;
                    $available_height = $shelf->height - $shelfUsed->height;
                    $available_length = $shelf->length - $shelfUsed->length;

                    if ($available_width >= $request->width && $available_height >= $request->height && $available_length >= $request->length) {
                        $shelf_id = $shelf->id;
                        $message = "Please keep the product on " . $shelf->shelf_title;
                        break;
                    } else {
                        $shelf_id = 0;
                        $message = "Dedicated rack hasn't enough space. Please use defult rack";
                    }
                }
            } else {
                $shelf_id = 0;
                $message = "No Shelf defined for this delivery hub.";
            }

            $orderDetail = Order::findOrFail($product_info->order_id);
//            dd($orderDetail->suborders);
            foreach ($orderDetail->suborders as $row) {

                $verify_suborder = OrderProduct::where('sub_order_id', '=', $row->id)->where('status', '!=', '0')->count();
                if ($verify_suborder == 0) {
                    $sub_order = SubOrder::where('id', '=', $row->id)->delete();
                } else {

                    $sub_order = SubOrder::findOrFail($row->id);
                    $sub_order->responsible_user_id = $request->responsible_user_id;
//                    $sub_order_due = OrderProduct::where('status', '!=', '5')->where('status', '!=', '0')->where('sub_order_id', '=', $row->id)->count();
//                    if ($sub_order_due == 0) {
                        // $sub_order->sub_order_status = '5';
                        $sub_order->destination_hub_id = $delivery_hub_id;
                        $sub_order_trip_map = SubOrderTripMap::where('sub_order_id', $sub_order->id)->orderBy('id', 'asc')->first();
                        if ($sub_order_trip_map) {
                            $sub_order->next_hub_id = $sub_order_trip_map->hub_id;
                        } else {
                            $sub_order->next_hub_id = $delivery_hub_id;
                        }

                        $sub_order->current_hub_id = auth()->user()->reference_id;
                        $sub_order->source_hub_id = auth()->user()->reference_id;
//                    }
                    $sub_order->save();
//                    dd($sub_order);
                    // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
                    $this->orderLog(auth()->user()->id, $sub_order->order_id, $sub_order->id, '', $delivery_hub_id, 'hubs', 'Decided destination for Sub-Order: ' . $sub_order->unique_suborder_id);
                }
            }

            // Insert product on rack
            $shelf_product = new ShelfProduct();
            $shelf_product->shelf_id = $shelf_id;
            $shelf_product->product_id = $product_info->id;
            $shelf_product->status = '1';
            $shelf_product->created_by = auth()->user()->id;
            $shelf_product->updated_by = auth()->user()->id;
            $shelf_product->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $product_info->order_id, $product_info->sub_order_id, $product_info->id, $shelf_product->shelf_id, 'shelf_products', 'Product shelfed');

            // Update Sub-Order Status
            $this->suborderStatus($product->sub_order->id, '15');
        }

        // return $message;

        Session::flash('inventory', $message);
        return redirect('/receive-picked');
        // return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function addsuborder($unique_order_id) {

        $last_suborder = SubOrder::select(array('sub_orders.unique_suborder_id', 'sub_orders.order_id'))
                ->join('orders AS o', 'o.id', '=', 'sub_orders.order_id')
                ->where('o.unique_order_id', '=', $unique_order_id)
                ->where('o.status', '=', '1')
                ->orderBy('sub_orders.id', 'desc')
                ->first();

        $last_unique_suborder_id = $last_suborder->unique_suborder_id;
        $split_last_unique_suborder_id = explode('-D', $last_unique_suborder_id);
        $new_unique_suborder_id = $split_last_unique_suborder_id[0] . "-D" . ($split_last_unique_suborder_id[1] + 1);

        // Create Sub-Order
        $sub_order = new SubOrder();
        $sub_order->unique_suborder_id = $new_unique_suborder_id;
        $sub_order->order_id = $last_suborder->order_id;
        $sub_order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $sub_order->order_id, '', '', $sub_order->id, 'sub_orders', 'Created a new Sub-Order: ' . $sub_order->unique_suborder_id);

        // return $sub_order->id;
        // $result = array(
        //                 "id" => $sub_order->id,
        //                 "unique_suborder_id" => $sub_order->unique_suborder_id,
        //                 );

        $result = SubOrder::where('order_id', '=', $last_suborder->order_id)->orderBy('id', 'desc')->limit(1)->get();

        return $_GET['callback'] . "(" . json_encode($result) . ")";
    }

    public function update_product_suborder($product_unique_id, $suborder_id) {
        $product = OrderProduct::where('product_unique_id', $product_unique_id)->firstOrFail();
        $product->sub_order_id = $suborder_id;
        $product->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product->sub_order_id, 'sub_orders', 'Product transferd to another Sub-Order');

        return $_GET['callback'] . "(" . json_encode($product->sub_order_id) . ")";
    }

}
