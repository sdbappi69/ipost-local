<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Order;
use App\Hub;
use App\OrderProduct;
use Session;
use Redirect;
use Validator;

class VerifyOrderController extends Controller
{

    use LogsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::whereStatus(true)->where('order_status', '=', '1')->orderBy('id', 'desc')->paginate(6);
        // $orders = Order::whereStatus(true)->where('order_status', '=', '1')->orderBy('id', 'desc')->paginate(6)->toArray();
        // dd($orders);
        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

        return view('verify-orders.index', compact('orders', 'hubs'));
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
        // return $request->all();
        $validation = Validator::make($request->all(), [
                'order_status' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $order = Order::findOrFail($id);
        $order->fill($request->all());
        $order->updated_by = auth()->user()->id;
        $order->verified_by = auth()->user()->id;
        $order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'Order verified');

        // foreach ($order->suborders as $sub_order){
        //     return $sub_order->id;
        //     $this->suborderStatus($sub_order->id, '2');
        // }

        Session::flash('message', "Order updated successfully");
        return redirect('/verify-order');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // OrderProduct::where('order_id', '=', $id)->delete();
        // Order::where('id', '=', $id)->delete();
        
        $order = Order::findOrFail($id);
        $order->status = '0';
        $order->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'Order Canceled');
        
        Session::flash('message', "Order Cleard successfully");
        return Redirect::back();
    }
}
