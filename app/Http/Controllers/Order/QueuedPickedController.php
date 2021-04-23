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
use DB;
use Session;
use Redirect;
use Validator;

class QueuedPickedController extends Controller
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
        $this->middleware('role:hubmanager|inboundmanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = OrderProduct::select(array(
                                            'order_product.id AS id',
                                            'order_product.product_unique_id',
                                            'order_product.product_title',
                                            'order_product.quantity',
                                            'order_product.picking_date',
                                            'order_product.order_id AS order_id',
                                            'order_product.sub_order_id AS sub_order_id',
                                            'o.unique_order_id AS unique_order_id',
                                            'dh.title AS delivery_title',
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
                                        ))
                ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->join('hubs AS dh', 'dh.id', '=', 'o.hub_id')
                ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->join('cities AS c', 'c.id', '=', 'z.city_id')
                ->join('states AS s', 's.id', '=', 'c.state_id')
                // ->where('o.order_status', '=', '3')
                ->where('order_product.status', '=', '4')
                ->where('order_product.hub_transfer', '=', '1')
                ->where('z.hub_id', '=', auth()->user()->reference_id)
                ->where('order_product.picking_status', '=', '1')
                ->where('order_product.hub_transfer_status', '=', '0')
                ->orderBy('order_product.id', 'desc')
                ->get();
        return view('queued-picked.index', compact('products'));
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
