<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\SubOrder;
use App\Trip;
use App\SuborderTrip;
use App\Order;
use App\OrderProduct;
use App\ProductTrip;
use App\ShelfProduct;
use Session;
use Redirect;
use Validator;

class TransferProductController extends Controller
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
        $this->middleware('role:vehiclemanager|hubmanager');
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
                                            'pc.name AS product_category',
                                            'o.hub_id',
                                            'h.title AS transfer_hub'
                                        ))
                ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->join('hubs AS h', 'h.id', '=', 'o.hub_id')
                ->join('users AS u', 'u.id', '=', 'order_product.hub_transfer_responsible_user_id')
                ->where('order_product.status', '=', '4')
                ->where('order_product.hub_transfer', '=', '1')
                ->where('u.reference_id', '=', auth()->user()->reference_id)
                ->where('order_product.hub_transfer_status', '=', '0')
                ->orderBy('order_product.id', 'desc')
                ->paginate(6);

        $trips = Trip::whereStatus(true)->where('source_hub_id', '=', auth()->user()->reference_id)->where('trip_status', '=', '1')->lists('unique_trip_id', 'id')->toArray();

        return view('transfer-product.index', compact('products', 'trips'));
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
        $product_unique_id = $request->product_unique_id;

        $products = OrderProduct::select(array(
                                            'order_product.id AS id',
                                            'order_product.product_unique_id',
                                            'order_product.product_title',
                                            'order_product.quantity',
                                            'order_product.picking_date',
                                            'pc.name AS product_category',
                                            'o.hub_id',
                                            'h.title AS transfer_hub'
                                        ))
                ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->join('hubs AS h', 'h.id', '=', 'o.hub_id')
                ->join('users AS u', 'u.id', '=', 'order_product.hub_transfer_responsible_user_id')
                ->where('order_product.status', '=', '4')
                ->where('order_product.hub_transfer', '=', '1')
                ->where('u.reference_id', '=', auth()->user()->reference_id)
                ->where('order_product.hub_transfer_status', '=', '0')
                ->where('order_product.product_unique_id', '=', $product_unique_id)
                ->orderBy('order_product.id', 'desc')
                ->paginate(1);

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        // $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product_trip->id, 'trips', 'Product uploaded to a trip');

        $trips = Trip::whereStatus(true)->where('source_hub_id', '=', auth()->user()->reference_id)->where('trip_status', '=', '1')->lists('unique_trip_id', 'id')->toArray();

        return view('transfer-product.index', compact('products', 'trips'));
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
        // return $request->all();
        $validation = Validator::make($request->all(), [
            'trip_id' => 'required',
            'receive_hub_id' => 'required',
            'remarks' => 'sometimes',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Insert Sub-Order & Trip
        $product_trip = new ProductTrip();
        $product_trip->trip_id = $request->trip_id;
        $product_trip->product_id = $id;
        $product_trip->remarks = $request->remarks;
        $product_trip->receive_hub_id = $request->receive_hub_id;
        $product_trip->save();

        $product = OrderProduct::findOrFail($id);
        $product->hub_transfer_status = '1';
        $product->save();

        $shelf = ShelfProduct::where('product_id', '=', $id)->where('status', '=', '1')->firstOrFail();
        $shelf->status = '0';
        $shelf->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $product_trip->id, 'trips', 'Product uploaded to a trip');

        Session::flash('message', "Product processed successfully");
        return redirect('/transfer-product');
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
