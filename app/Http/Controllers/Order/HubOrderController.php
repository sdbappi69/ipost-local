<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Country;
use App\Order;
use App\Store;
use App\State;
use App\City;
use App\Zone;
use App\ProductCategory;
use App\PickingLocations;
use App\OrderProduct;
use App\PickingTimeSlot;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Entrust;
use App\Merchant;
use App\User;
use App\SubOrder;
use App\Status;
use App\ExtractLog;
use Excel;

class HubOrderController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $query = $this->__getQuery($request);

        $sub_orders = $query->paginate(10);

        $stores = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
        $sub_order_status = hubAllStatus();
        // pick man 

        $pickupman = User::select('name', 'id')->where('status', true)->where('user_type_id', '=', '8')->lists('name', 'users.id')->toArray();

        $zones = Zone::select(DB::raw('CONCAT(zones.name, " - ",cities.name) AS name'), 'zones.id')
                        ->leftJoin('cities', 'cities.id', '=', 'zones.city_id')->
                        where('zones.status', true)->lists('name', 'zones.id')->toArray();

        return view('hub-orders.index', compact('pickupman', 'merchants', 'stores', 'sub_order_status', 'sub_orders', 'zones'));
    }

    private function __getQuery($request) {

        $query = SubOrder::with("order.store", "product")
                ->where('status', '!=', 0)
                ->where('sub_order_status', '>', 1)
                ->where('parent_sub_order_id', '=', 0)
                ->where(function($query) {
                    $query->where('destination_hub_id', '=', auth()->user()->reference_id);
                    $query->orWhere('source_hub_id', '=', auth()->user()->reference_id);
                    $query->orWhere('next_hub_id', '=', auth()->user()->reference_id);
                })
                ->orderBy('id', 'desc');
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

        if ($request->has('sub_order_status')) {
            $query->whereHas('orderLogs', function($q) use ($request, $start_date, $end_date) {
                $q->whereIn('sub_order_status', $request->sub_order_status)
                        ->where('created_at', '!=', '0000-00-00 00:00:00')
                        ->WhereBetween('created_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));
            });
        } else {
            $query->where('updated_at', '!=', '0000-00-00 00:00:00')->WhereBetween('updated_at', array($start_date . ' 00:00:01', $end_date . ' 23:59:59'));
        }

        $query->whereHas('order', function($query)use($request) {
            if ($request->has('sub_order_id')) {
                $query->whereHas('suborders', function($query) use ($request) {
                    $query->where('unique_suborder_id', $request->sub_order_id);
                });
            }
            if ($request->has('order_id')) {
                $query->where('unique_order_id', $request->order_id);
            }
            if ($request->has('merchant_order_id')) {
                $query->where('merchant_order_id', $request->merchant_order_id);
            }
            if ($request->has('customer_mobile_no')) {
                $query->where('delivery_msisdn', $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', $request->customer_mobile_no);
            }
            if ($request->has('store_id')) {
                $query->whereIn('store_id', $request->store_id);
            }
            if ($request->has('merchant_id')) {
                $query->whereHas('store', function($query)use($request) {
                    $query->whereIn('merchant_id', $request->merchant_id);
                });
            }
        });

        return $query;
    }

    public function orderexport(Request $request, $type) {
        $query = $this->__getQuery($request);

        $sub_orders = $query->get();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Hub Orders';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('order_' . time(), function($excel) use ($sub_orders) {
                    $excel->sheet('orders', function($sheet) use ($sub_orders) {

                        $datasheet = array();
                        $datasheet[0] = array('Order Id', 'Sub-Order Id', 'Type', 'Merchant Order Id', 'Current Status', 'Store', 'Seller', 'Created', 'Product', 'Quantity', 'Verified Weight', 'Picking Attempt', 'Picking Attempt', 'Delivery Name', 'Delivery Email', 'Delivery Mobile', 'Amount to be collected', 'Amount collected');
                        $i = 1;
                        foreach ($sub_orders as $datanew) {

                            if ($datanew->return == 1) {
                                $type = 'Return';
                            } else {
                                $type = 'Delivery';
                            }

                            if ($datanew->sub_order_last_status === NULL) {
                                $sub_order_status = hubGetStatus($datanew->sub_order_status);
                            } else {
                                $sub_order_status = hubGetStatus($datanew->sub_order_last_status);
                            }
                            if(is_null($datanew->product)){
                                continue;
                            }

                            $datasheet[$i] = array(
                                $datanew->order->unique_order_id,
                                $datanew->unique_suborder_id,
                                $type,
                                $datanew->order->merchant_order_id,
                                $sub_order_status,
                                $datanew->order->store->store_id,
                                $datanew->product->pickup_location->title,
                                $datanew->created_at,
                                $datanew->product->product_title,
                                $datanew->product->quantity,
                                $datanew->product->weight,
                                $datanew->product->picking_attempts,
                                $datanew->order->delivery_name,
                                $datanew->order->delivery_email,
                                $datanew->order->delivery_msisdn . ", " . $datanew->order->delivery_alt_msisdn,
                                $datanew->product->sub_total,
                                $datanew->product->delivery_paid_amount
                            );

                            $i++;
                        }

                        $sheet->setOrientation('landscape');

                        // Freeze first row
                        $sheet->freezeFirstRow();

                        $sheet->fromArray($datasheet);
                    });
                })->download($type);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $order = Order::whereStatus(true)->findOrFail($id);
//        dd($order->suborders[0]->products[0]->charge_details, json_decode($order->suborders[0]->products[0]->charge_details));
//        return view('hub-orders.view', compact('order'));    
        return view('hub-orders.view-suborders', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $order = Order::findOrFail($id);

        // For Page One
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $order->delivery_country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $order->delivery_state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $order->delivery_city_id)->lists('name', 'id')->toArray();

        $stores = Store::whereStatus(true)->where('merchant_id', '=', $order->store->merchant->id)->lists('store_id', 'id')->toArray();

        // // For Page Two
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
        $warehouse = PickingLocations::whereStatus(true)->where('merchant_id', '=', $order->store->merchant->id)->lists('title', 'id')->toArray();
        $products = OrderProduct::where('order_id', '=', $id)->orderBy('id', 'desc')->get();
        $picking_time_slot = PickingTimeSlot::addSelect(DB::raw("CONCAT(day,' (',start_time,' - ',end_time,')') AS title"), "id")->whereStatus(true)->lists("title", "id")->toArray();

        // For Page Three
        $shipping_loc = Order::select(array(
                    'countries.name AS country_title',
                    'states.name AS state_title',
                    'cities.name AS city_title',
                    'zones.name AS zone_title'
                ))
                ->leftJoin('countries', 'countries.id', '=', 'orders.delivery_country_id')
                ->leftJoin('states', 'states.id', '=', 'orders.delivery_state_id')
                ->leftJoin('cities', 'cities.id', '=', 'orders.delivery_city_id')
                ->leftJoin('zones', 'zones.id', '=', 'orders.delivery_zone_id')
                ->where('orders.id', '=', $id)
                ->first();

        // Call Charge Calculation API
        // return env('APP_URL').'api/charge-calculator?store_id='.$order->store->store_id.'&order_id='.$order->unique_order_id;
        $chargesJson = file_get_contents(env('APP_URL') . 'api/charge-calculator?store_id=' . $order->store->store_id . '&order_id=' . $order->unique_order_id);
        $charges = json_decode($chargesJson);
        if ($charges->status != 'Success') {
            abort(403);
        }

        if ($request->step) {
            $step = $request->step;
        } else {
            $step = 1;
        }

        return view('hub-orders.edit', compact('prefix', 'countries', 'step', 'id', 'order', 'states', 'cities', 'zones', 'stores', 'categories', 'warehouse', 'products', 'picking_time_slot', 'shipping_loc', 'charges'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validation = Validator::make($request->all(), [
                    'store_id' => 'sometimes',
                    'delivery_name' => 'sometimes',
                    'delivery_email' => 'sometimes|email',
                    'delivery_msisdn' => 'sometimes|between:10,25',
                    'delivery_alt_msisdn' => 'sometimes|between:10,25',
                    'delivery_country_id' => 'sometimes',
                    'delivery_state_id' => 'sometimes',
                    'delivery_city_id' => 'sometimes',
                    'delivery_zone_id' => 'sometimes',
                    'delivery_address1' => 'sometimes',
                    'amount' => 'sometimes',
                    'delivery_payment_amount' => 'sometimes',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $order = Order::findOrFail($id);

        $order->fill($request->except('msisdn_country', 'alt_msisdn_country', 'step', 'include_delivery', 'amount_hidden'));
        if ($request->include_delivery) {
            if ($request->include_delivery == '1') {
                $order->total_amount = $request->amount + $request->delivery_payment_amount;
            }
        }
        $order->save();

        if ($request->step) {
            if ($request->step == 'complete') {
                // return $request->step;
                Session::flash('message', "Order information updated successfully");
                return redirect('/receive-picked');
            } else {
                $step = $request->step;
            }
        } else {
            $step = 1;
        }
        Session::flash('message', "Order information saved successfully");
        return redirect('/hub-order/' . $id . '/edit?step=' . $step);
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

}
