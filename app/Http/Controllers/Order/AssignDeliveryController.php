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

use Session;
use Redirect;
use Validator;

class AssignDeliveryController extends Controller
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
        $this->middleware('role:hubmanager|vehiclemanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sub_orders = SubOrder::whereStatus(true)->where('sub_order_status', '=', '7')->orderBy('id', 'desc')->where('destination_hub_id', '=', auth()->user()->reference_id)->where('deliveryman_id', '=', null)->paginate(9);
        $deliveryman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        return view('assign-delivery.index', compact('sub_orders', 'deliveryman'));
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
        $suborder_unique_id = $request->unique_id;

        $sub_orders = SubOrder::whereStatus(true)->where('sub_order_status', '=', '7')->orderBy('id', 'desc')->where('destination_hub_id', '=', auth()->user()->reference_id)->where('deliveryman_id', '=', null)->where('unique_suborder_id', '=', $suborder_unique_id)->paginate(1);
        $deliveryman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        return view('assign-delivery.index', compact('sub_orders', 'deliveryman'));
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
            'order_id' => 'required',
            'sub_order_id' => 'required',
            'deliveryman_id' => 'required',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Update Sub-Order
        $suborderUp = SubOrder::findOrFail($request->sub_order_id);
        $suborderUp->sub_order_status = '8';
        $suborderUp->delivery_assigned_by = auth()->user()->id;
        $suborderUp->deliveryman_id = $request->deliveryman_id;
        $suborderUp->delivery_status = '0';
        $suborderUp->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $suborderUp->order_id, $suborderUp->id, '', $suborderUp->deliveryman_id, 'users', 'Assigned a delivery man');

        // Update Order
        $order_due = SubOrder::where('sub_order_status', '!=', '8')->where('order_id', '=', $suborderUp->order_id)->count();
        if($order_due == 0){
            $order = Order::findOrFail($request->order_id);
            $order->order_status = '8';
            $order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $order->id, '', '', auth()->user()->reference_id, 'hubs', 'All Sub-Order(s) assigned delivery man');
        }

        Session::flash('message', "Delivery assigned successfully");
        return redirect('/assign-delivery');
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
