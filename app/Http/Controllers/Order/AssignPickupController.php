<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Order;
use App\Hub;
use App\OrderProduct;
use App\User;
use Session;
use Redirect;
use Validator;

class AssignPickupController extends Controller
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
                                            'order_product.sub_total',
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
                                            'm.name as merchant_name',
                                            'm.email as merchant_email',
                                            'm.msisdn as merchant_msisdn',
                                            'o.delivery_name as cus_name',
                                            'o.delivery_email as cus_email',
                                            'o.delivery_msisdn as cus_msisdn',
                                            'o.delivery_alt_msisdn as cus_alt_msisdn',
                                        ))
                ->join('pickup_locations AS pl', 'pl.id', '=', 'order_product.pickup_location_id')
                ->join('picking_time_slots AS pt', 'pt.id', '=', 'order_product.picking_time_slot_id')
                ->join('product_categories AS pc', 'pc.id', '=', 'order_product.product_category_id')
                ->join('orders AS o', 'o.id', '=', 'order_product.order_id')
                ->join('stores AS st', 'st.id', '=', 'o.store_id')
                ->join('merchants AS m', 'm.id', '=', 'st.merchant_id')
                ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                ->join('cities AS c', 'c.id', '=', 'z.city_id')
                ->join('states AS s', 's.id', '=', 'c.state_id')
                ->where('o.order_status', '=', '2')
                ->where('order_product.status', '=', '1')
                ->orderBy('order_product.id', 'desc')
                ->where('z.hub_id', '=', auth()->user()->reference_id)
                ->paginate(6);
                // ->get();

        // return $products;

        $pickupman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        return view('assign-pickup.index', compact('products', 'pickupman'));
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
        $validation = Validator::make($request->all(), [
                'status' => 'required',
                'picker_id' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $products = OrderProduct::findOrFail($id);
        $products->fill($request->all());
        $products->updated_by = auth()->user()->id;
        $products->picker_assign_by = auth()->user()->id;
        $products->picker_id = $request->picker_id;
        $products->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $products->order_id, $products->sub_order_id, $products->id, $products->id, 'order_product', 'Assigned a picker for product: '.$products->product_unique_id);

        $due = OrderProduct::where('status', '!=', $request->status)->where('order_id', '=', $products->order_id)->count();
        if($due == 0){
            $order = Order::findOrFail($products->order_id);
            $order->updated_by = auth()->user()->id;
            $order->order_status = $request->status;
            $order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'Assigned picker for all product');
        }

        Session::flash('message', "Order updated successfully");
        return redirect('/assign-pickup');
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
