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
use App\PickingTask;
use App\Status;
use DB;
use Session;
use Redirect;
use Validator;

class ReturnPickupController extends Controller
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
        $products = PickingTask::select(array(
                                            'picking_task.id AS task_id',
                                            'picking_task.status AS task_status',
                                            'picking_task.quantity AS picking_quantity',
                                            'op.id AS id',
                                            'op.product_unique_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'op.picking_date',
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
                                            'r.reason',
                                            'o.delivery_name as cus_name',
                                            'o.delivery_email as cus_email',
                                            'o.delivery_msisdn as cus_msisdn',
                                            'o.delivery_alt_msisdn as cus_alt_msisdn',
                                            'o.unique_order_id as unique_order_id',
                                            'o.id as order_id'
                                        ))
                            ->join('order_product AS op', 'op.product_unique_id', '=', 'picking_task.product_unique_id')
                            ->join('pickup_locations AS pl', 'pl.id', '=', 'op.pickup_location_id')
                            ->join('picking_time_slots AS pt', 'pt.id', '=', 'op.picking_time_slot_id')
                            ->join('product_categories AS pc', 'pc.id', '=', 'op.product_category_id')
                            ->join('users AS u', 'u.id', '=', 'picking_task.picker_id')
                            ->join('orders AS o', 'o.id', '=', 'op.order_id')
                            ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                            ->join('cities AS c', 'c.id', '=', 'z.city_id')
                            ->join('states AS s', 's.id', '=', 'c.state_id')
                            ->leftJoin('reasons AS r', 'r.id', '=', 'picking_task.reason_id')
                            ->where('u.reference_id', '=', auth()->user()->reference_id)
                            // ->where('op.picking_status', '=', '0')
                            // ->where('op.picking_attempts', '<', '3')
                            ->where('picking_task.status', '>', '2')
                            ->orderBy('picking_task.id', 'desc')
                            ->get();

        // $orders = Order::whereStatus(true)->where('order_status', '=', '4')->orderBy('id', 'desc')->where('hub_id', '=', auth()->user()->reference_id)->paginate(6);

        // $vehiclemanager = User::whereStatus(true)->where('user_type_id', '=', '7')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        // $pickupman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        $sub_order_status_list = Status::where('active', '1')->orderBy('id', 'asc')->lists('title', 'id')->toArray();

        return view('return-pickup.index', compact('products', 'sub_order_status_list'));
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

        $products = PickingTask::select(array(
                                            'picking_task.id AS task_id',
                                            'picking_task.quantity AS picking_quantity',
                                            'op.id AS id',
                                            'op.product_unique_id',
                                            'op.product_title',
                                            'op.quantity',
                                            'op.picking_date',
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
                            ->join('order_product AS op', 'op.product_unique_id', '=', 'picking_task.product_unique_id')
                            ->join('pickup_locations AS pl', 'pl.id', '=', 'op.pickup_location_id')
                            ->join('picking_time_slots AS pt', 'pt.id', '=', 'op.picking_time_slot_id')
                            ->join('product_categories AS pc', 'pc.id', '=', 'op.product_category_id')
                            ->join('users AS u', 'u.id', '=', 'picking_task.picker_id')
                            ->join('orders AS o', 'o.id', '=', 'op.order_id')
                            ->join('zones AS z', 'z.id', '=', 'pl.zone_id')
                            ->join('cities AS c', 'c.id', '=', 'z.city_id')
                            ->join('states AS s', 's.id', '=', 'c.state_id')
                            ->where('u.reference_id', '=', auth()->user()->reference_id)
                            // ->where('op.picking_status', '=', '0')
                            ->where('op.picking_attempts', '<', '3')
                            ->where('picking_task.status', '>', '2')
                            ->where('picking_task.product_unique_id', '=', $product_unique_id)
                            ->orderBy('picking_task.id', 'desc')
                            ->paginate(6);
        
        $pickupman = User::whereStatus(true)->where('user_type_id', '=', '8')->where('reference_id', '=', auth()->user()->reference_id)->lists('name', 'id')->toArray();

        return view('return-pickup.index', compact('products', 'pickupman'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ptask = PickingTask::findOrFail($id);
        $ptask->status = 0;
        $ptask->save();

        $product = OrderProduct::where('product_unique_id', $ptask->product_unique_id)->first();
        $product->status = 0;
        $product->save();

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $ptask->id, 'picking_task', 'Product Canceled: '.$ptask->product_unique_id);

        Session::flash('message', "Product Canceled");
        return redirect('/return-pickup');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ptask = PickingTask::findOrFail($id);
        $ptask->status = 2;
        $ptask->save();

        $product = OrderProduct::where('product_unique_id', $ptask->product_unique_id)->first();
        $product->status = 4;
        $product->quantity = $ptask->quantity;
        $product->picking_status = 1;
        $product->save();

        $due = OrderProduct::where('status', '<', '4')->where('status', '!=', '0')->where('order_id', '=', $products->order_id)->count();
        if($due == 0){
            $order = Order::findOrFail($products->order_id);
            $order->updated_by = auth()->user()->id;
            $order->order_status = '4';
            $order->save();

            // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
            $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'All Product Picked');
        }

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $product->order_id, $product->sub_order_id, $product->id, $ptask->id, 'picking_task', 'Product quantity updated: '.$product->product_unique_id);

        Session::flash('message', "Product quantity updated");
        return redirect('/return-pickup');
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
                // 'picking_date' => 'required',
                // 'picker_id' => 'required',
                'task_id' => 'required',
                // 'picking_quantity' => 'required',
                'sub_order_status' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $products = OrderProduct::findOrFail($id);
        $products->updated_by = auth()->user()->id;
        $products->status = '1';
        $products->save();

        // return $request->task_id;
        $ptask = PickingTask::findOrFail($request->task_id);
        $ptask->status = 0;
        $ptask->save();

        // Update Sub-Order Status
        $this->suborderStatus($products->sub_order_id, $request->sub_order_status);

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        $this->orderLog(auth()->user()->id, $products->order_id, $products->sub_order_id, $products->id, $ptask->id, 'picking_task', 'Failed Picking Attempt: '.$products->product_unique_id);

        // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        // $this->orderLog(auth()->user()->id, $products->order_id, $products->sub_order_id, $products->id, $ptask->id, 'picking_task', 'Re-Assigned a picker for product: '.$products->product_unique_id);

        // $due = OrderProduct::where('status', '!=', '3')->where('order_id', '=', $products->order_id)->count();
        // if($due == 0){
        //     $order = Order::findOrFail($products->order_id);
        //     $order->updated_by = auth()->user()->id;
        //     $order->order_status = '3';
        //     $order->save();

        //     // orderLog($user_id, $order_id, $sub_order_id, $product_id, $ref_id, $ref_table, $text)
        //     $this->orderLog(auth()->user()->id, $order->id, '', '', $order->id, 'orders', 'Assigned picker for all product');
        // }

        $order = Order::findOrFail($products->order_id);
        $order->updated_by = auth()->user()->id;
        $order->order_status = '2';
        $order->save();

        // if($request->picking_quantity > 0){
        //     $message = "Please keep the product on Pending Shelf";
        //     Session::flash('inventory', $message);
        //     return redirect('/return-pickup');
        // }else{
        //     Session::flash('message', "Picking action updated");
        //     return redirect('/return-pickup');
        // }

        Session::flash('message', "Picking action updated");
        return redirect('/return-pickup');
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
