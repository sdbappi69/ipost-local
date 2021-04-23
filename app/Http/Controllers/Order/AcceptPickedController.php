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
use App\Store;
use App\Merchant;
use App\State;
use App\City;
use App\Zone;
use App\Consignment;

use DB;
use Session;
use Redirect;
use Validator;

class AcceptPickedController extends Controller
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
        $this->middleware('role:hubmanager|inboundmanager|inventoryoperator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = OrderProduct::select(array(
                                            'order_product.id AS id',
                                            'order_product.product_unique_id',
                                            'order_product.product_title',
                                            'order_product.quantity',
                                            'order_product.picking_date',
                                            'order_product.order_id AS order_id',
                                            'order_product.sub_order_id AS sub_order_id',
                                            'o.unique_order_id AS unique_order_id',
                                            'so.unique_suborder_id AS unique_suborder_id',
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
                                            'u.name AS picker_name',
                                        ))
                ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->leftJoin('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->leftJoin('users AS u', 'u.id', '=', 'order_product.picker_id')
                ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                // ->leftJoin('picking_task AS ptsk', 'ptsk.product_unique_id', '=', 'order_product.product_unique_id')
                ->leftJoin('consignments_tasks AS ct', 'ct.sub_order_id', '=', 'so.id')
                ->leftJoin('consignments_common AS con', 'con.id', '=', 'ct.consignment_id')
                ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->leftJoin('stores','stores.id','=','o.store_id')
                ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->leftJoin('cities AS c', 'c.id', '=', 'z.city_id')
                ->leftJoin('states AS s', 's.id', '=', 'c.state_id')
                // ->where('o.order_status', '=', '3')
                // ->where('order_product.status', '=', '4')
                ->where('so.sub_order_status', '=', '9')
                ->where('so.return', '=', 0)
                ->where(function ($q){
                    $q->where(function ($q1){
                        $q1->where('con.status', '=', '4');
                        $q1->where('z.hub_id', '=', auth()->user()->reference_id);
                        $q1->whereIn('ct.status', [2, 3]);
                        $q1->where('ct.reconcile', '=', 1);
                    });
                    $q->orWhere(function ($q2){
                        $q2->where('so.source_hub_id', '=', auth()->user()->reference_id);
                        $q2->where('so.return', 1);
                    });
                });
                
                // ->where('order_product.hub_transfer', '=', '0')
                
        
        if($request->has('order_id')){
            $query->where('o.unique_order_id', $request->order_id);
        }

        if($request->has('merchant_order_id')){
            $query->where('o.merchant_order_id', $request->merchant_order_id);
        }

        if($request->has('sub_order_id')){
            $query->where('so.unique_suborder_id', $request->sub_order_id);
        }

        if($request->has('sub_order_status')){
            $query->where('so.sub_order_status', $request->sub_order_status);
        }

        if($request->has('customer_mobile_no')){
            $query->where('o.delivery_msisdn', $request->customer_mobile_no)->orWhere('o.delivery_alt_msisdn', $request->customer_mobile_no);
        }

        if($request->has('store_id')){
            $query->where('o.store_id', $request->store_id);
        }

        if($request->has('merchant_id')){
            $query->where('stores.merchant_id', $request->merchant_id);
        }

        if($request->has('pickup_man_id')){
            $query->where('order_product.picker_id', $request->pickup_man_id);
        }

        if($request->has('delivary_man_id')){
            $query->where('so.deliveryman_id', $request->delivary_man_id);
        }

        if($request->has('pickup_zone_id')){
            $query->where('pl.zone_id', $request->pickup_zone_id);
        }

        if($request->has('delivery_zone_id')){
            $query->where('o.delivery_zone_id', $request->delivery_zone_id);
        }

        if($request->has('consignment_id')){
            $query->where('con.consignment_unique_id', $request->consignment_id);
        }

        if($request->has('start_date')){
            $start_date = $request->start_date;
        }else{
            $start_date = '2017-03-21';
        }

        if($request->has('end_date')){
            $end_date = $request->end_date;
        }else{
            $end_date = date('Y-m-d');
        }
        $query->WhereBetween('so.updated_at',array($start_date.' 00:00:01',$end_date.' 23:59:59'));

        $products = $query->orderBy('so.id', 'desc')->get();

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $pickupman = User::
         select(DB::raw('CONCAT(users.name, " - ",hubs.title) AS name'),'users.id')
         ->leftJoin('hubs','hubs.id','=','users.reference_id')
         ->where('reference_id', '=', auth()->user()->reference_id)
         ->where('users.status',true)->where('users.user_type_id', '=', '8')->lists('name','users.id')->toArray();

        $zones = Zone::
        select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'),'zones.id')
        ->leftJoin('cities','cities.id','=','zones.city_id')->
        where('zones.status',true)->lists('name','zones.id')->toArray();

        return view('accept-picked.index', compact('products', 'stores', 'merchants', 'pickupman', 'zones'));
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
        // return $request->unique_suborder_id;
        // $validation = Validator::make($request->all(), [
        //     'unique_suborder_id' => 'required',
        // ]);

        // if($validation->fails()) {
        //     return Redirect::back()->withErrors($validation)->withInput();
        // }

        if($request->has('unique_suborder_id') && $request->unique_suborder_id){

            $acceptPicked = $this->acceptPicked($request->unique_suborder_id);

            if($acceptPicked == 1){
                $message = "Product Accepted";
            }else{
                $message = "Product Can't Accepted";
            }

            Session::flash('message', $message);
            return redirect('/accept-picked');

        }else if ($request->has('unique_suborder_ids') && count($request->unique_suborder_ids)) {
            
            foreach ($request->unique_suborder_ids as $unique_suborder_id) {
                $acceptPicked = $this->acceptPicked($unique_suborder_id);
                if($acceptPicked == 0){
                    $message = $unique_suborder_id." Can't Accepted";
                    Session::flash('message', $message);
                    return redirect('/accept-picked');
                }
            }

            $message = "Products Accepted";
            Session::flash('message', $message);
            return redirect('/accept-picked');

        }else{
            Session::flash('message', 'Invalid request');
            return redirect('/accept-picked');
        }
        
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

    public function acceptPicked($unique_suborder_id){
        $product_detail = OrderProduct::select(array(
                                            'order_product.id AS id',
                                            'order_product.product_unique_id',
                                            'order_product.product_title',
                                            'order_product.quantity',
                                            'order_product.picking_date',
                                            'order_product.order_id AS order_id',
                                            'order_product.sub_order_id AS sub_order_id',
                                            'o.unique_order_id AS unique_order_id',
                                            'so.unique_suborder_id AS unique_suborder_id',
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
                                            'u.name AS picker_name',
                                        ))
                ->leftJoin('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->leftJoin('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->leftJoin('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->leftJoin('users AS u', 'u.id', '=', 'order_product.picker_id')
                ->leftJoin('sub_orders AS so', 'so.id', '=', 'order_product.sub_order_id')
                // ->leftJoin('picking_task AS ptsk', 'ptsk.product_unique_id', '=', 'order_product.product_unique_id')
                ->leftJoin('consignments_tasks AS ct', 'ct.sub_order_id', '=', 'so.id')
                ->leftJoin('consignments_common AS con', 'con.id', '=', 'ct.consignment_id')
                ->leftJoin('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->leftJoin('stores','stores.id','=','o.store_id')
                ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                ->leftJoin('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->leftJoin('cities AS c', 'c.id', '=', 'z.city_id')
                ->leftJoin('states AS s', 's.id', '=', 'c.state_id')
                // ->where('o.order_status', '=', '3')
                // ->where('order_product.status', '=', '4')
                ->where('so.sub_order_status', '=', '9')
                ->where('so.return', '=', 0)
                ->where(function ($q){
                    $q->where(function ($q1){
                        $q1->where('con.status', '=', '4');
                        $q1->where('z.hub_id', '=', auth()->user()->reference_id);
                        $q1->whereIn('ct.status', [2, 3]);
                        $q1->where('ct.reconcile', '=', 1);
                    });
                    $q->orWhere(function ($q2){
                        $q2->where('so.source_hub_id', '=', auth()->user()->reference_id);
                        $q2->where('so.return', 1);
                    });
                })
                // ->where('order_product.hub_transfer', '=', '0')
                ->where('so.unique_suborder_id', '=', $unique_suborder_id)
                ->first();
        // return $product_detail->count();
        // if($product_detail->count() == 1){
        if($product_detail){
            // $product = $product_detail[0];
            $product = $product_detail;
            $product_update = OrderProduct::where('id', '=', $product->id)->first();
            $product_update->picking_status = 1;
            $product_update->save();

            // Update Sub-Order Status
            $this->suborderStatus($product_update->sub_order_id, '10');

            $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product->id, 'order_product', 'Receive Product from picker');
            return 1;
        }else{
            return 0;
        }
    }
}
