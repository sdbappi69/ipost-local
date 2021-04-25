<?php

namespace App\Http\Controllers\Charge;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Charge;
use App\ProductCategory;
use App\ChargeModel;
use App\ZoneGenre;
use App\Order;
use App\PickingLocations;
use DB;
use Validator;
use Redirect;
use Session;
use Entrust;
use Auth;

class ChargeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|merchantadmin|storeadmin|operationmanager|operationalhead');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductCategory::with('sub_cats.sub_cat_charge')
                        ->where('category_type', '=', 'parent')
                        ->whereHas('sub_cats.sub_cat_charge', function($q){
                            $q->where('store_id', '=', null);
                        })
                        ->get();
        // dd($data);

        return view('charges.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!Entrust::can('create-charge')) { abort(403); } 
        if($request->clone){
            $charge = Charge::where('id', '=', $request->clone)->first();
            $clone = '1';
            $store_id = $request->store;

            $categories = ProductCategory::select(array(
                                'product_categories.id AS id',
                                DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
                            ))
                        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        ->where('product_categories.parent_category_id', '!=', null)
                        ->where('product_categories.status', '=', '1')
                        ->where('product_categories.id', '=', $charge->product_category_id)
                        ->where('pc.status', '=', '1')
                        ->lists('cat_name', 'id')
                        ->toArray();
        }else{
            $clone = '0';
            $categories = ProductCategory::select(array(
                                'product_categories.id AS id',
                                DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
                            ))
                        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        ->where('product_categories.parent_category_id', '!=', null)
                        ->where('product_categories.status', '=', '1')
                        ->where('pc.status', '=', '1')
                        ->lists('cat_name', 'id')
                        ->toArray();
        }

        $charge_models = ChargeModel::whereStatus(true)
                        ->where('id', '!=', '1')
                        ->lists('title', 'id')->toArray();

        $zone_genres = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();

        if($request->clone){
            return view('charges.insert', compact('charge', 'categories', 'charge_models', 'zone_genres', 'clone', 'store_id'));
        }else{
            return view('charges.insert', compact('categories', 'charge_models', 'zone_genres', 'clone'));
        }
    }

    public function createcod(Request $request)
    {
        if(!Entrust::can('create-charge')) { abort(403); } 
        $charge = Charge::where('id', '=', $request->clone)->first();
        $store_id = $request->store;

        $charge_models = ChargeModel::whereStatus(true)
                        ->lists('title', 'id')->toArray();

        return view('charges.insertcod', compact('charge', 'charge_models', 'clone', 'store_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        if(!Entrust::can('create-charge')) { abort(403); } 
        $validation = Validator::make($request->all(), [
                'product_category_id' => 'sometimes',
                'store_id' => 'sometimes',
                'charge_model_id' => 'sometimes',
                'zone_genre_id' => 'sometimes',
                'fixed_charge' => 'required|numeric',
                'status' => 'required',
                'percentage_range_start' => 'required|numeric',
                'percentage_range_end' => 'required|numeric',
                'percentage_value' => 'required|numeric',
                'additional_range_per_slot' => 'required|numeric',
                'additional_charge_per_slot' => 'required|numeric',
                'additional_charge_type' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $charge = new Charge();
        $charge->fill($request->except('clone'));
        $charge->created_by = auth()->user()->id;
        $charge->updated_by = auth()->user()->id;
        
        if(Auth::user()->hasRole('salesteam')) {
          $charge->status = '0';
          $charge->is_approved = '0';
        }

        $charge->save();

        Session::flash('message', "Charge saved successfully");
        if($request->clone){
            return redirect('/store/'.$request->store_id.'/edit?step=2');
        }else{
            return redirect('/charge/');
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
        // if(!Entrust::can('edit-cod')) { abort(403); } 

        $charge = Charge::where('id', '=', $id)->first();

        $categories = ProductCategory::select(array(
                                'product_categories.id AS id',
                                DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
                            ))
                        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        ->where('product_categories.parent_category_id', '!=', null)
                        ->where('product_categories.status', '=', '1')
                        ->where('pc.status', '=', '1')
                        ->lists('cat_name', 'id')
                        ->toArray();

        $charge_models = ChargeModel::whereStatus(true)
                        ->where('id', '!=', '1')
                        ->lists('title', 'id')->toArray();

        $zone_genres = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();

        return view('charges.cod', compact('charge'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(!Entrust::can('edit-charge')) { abort(403); } 

        $charge = Charge::where('id', '=', $id)->first();

        $categories = ProductCategory::select(array(
                                'product_categories.id AS id',
                                DB::raw("CONCAT(pc.name,' - ',product_categories.name) AS cat_name")
                            ))
                        ->leftJoin('product_categories AS pc', 'pc.id', '=', 'product_categories.parent_category_id')
                        ->where('product_categories.parent_category_id', '!=', null)
                        ->where('product_categories.status', '=', '1')
                        ->where('pc.status', '=', '1')
                        ->lists('cat_name', 'id')
                        ->toArray();

        $charge_models = ChargeModel::whereStatus(true)
                        ->where('id', '!=', '1')
                        ->lists('title', 'id')->toArray();

        $zone_genres = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();

        if($request->overwrite){
            $overwrite = $request->overwrite;
            $store_id = $request->store_id;
        }else{
            $overwrite = '';
        }
        return view('charges.edit', compact('charge', 'categories', 'charge_models', 'zone_genres', 'overwrite', 'store_id'));
    }

    public function editcod(Request $request, $id)
    {
        if(!Entrust::can('edit-charge')) { abort(403); } 

        $charge = Charge::where('id', '=', $id)->first();

        $charge_models = ChargeModel::whereStatus(true)
                        ->where('id', '=', '1')
                        ->lists('title', 'id')->toArray();

        if($request->overwrite){
            $overwrite = $request->overwrite;
            $store_id = $request->store_id;
        }else{
            $overwrite = '';
        }
        return view('charges.editcod', compact('charge', 'charge_models', 'overwrite', 'store_id'));
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
        if(!Entrust::can('edit-charge')) { abort(403); } 

        $validation = Validator::make($request->all(), [
                // 'product_category_id' => 'required',
                'charge_model_id' => 'sometimes',
                'zone_genre_id' => 'sometimes',
                'fixed_charge' => 'required|numeric',
                'status' => 'required',
                'percentage_range_start' => 'required|numeric',
                'percentage_range_end' => 'required|numeric',
                'percentage_value' => 'required|numeric',
                'additional_range_per_slot' => 'required|numeric',
                'additional_charge_per_slot' => 'required|numeric',
                'additional_charge_type' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $charge = Charge::findOrFail($id);
        $charge->fill($request->except('overwrite'));
        // $charge->fill($request->all());
        $charge->save();

        Session::flash('message', "Charge information saved successfully");
        if($request->overwrite){
            return redirect('/store/'.$request->store_id.'/edit?step=2');
        }else{
            return Redirect::back();
        }
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

    public function bulk_charge($order_id, $width, $height, $length, $weight, $unit_price, $pickup_location_id){

        $order = Order::findOrFail($order_id);
        $picking_location = PickingLocations::findOrFail($pickup_location_id);

        // Call Charge Calculation API
        $post = [
            'store_id' => $order->store->store_id,
            'width' => $width,
            'height'   => $height,
            'length' => $length,
            'weight' => $weight,
            'product_category' => 'Bulk Product',
            'pickup_zone_id'   => $picking_location->zone_id,
            'delivery_zone_id' => $order->delivery_zone_id,
            'quantity' => '1',
            'unit_price'   => $unit_price,
        ];
        // return url('/').'/api/charge-calculator';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url('/').'/api/charge-calculator');
        // $ch = curl_init(env('APP_URL').'api/charge-calculator');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $charges = json_decode($response);
        // $charges = $charges[0];
        if($charges->status == 'Failed'){
            abort(403);
        }

        // return $_GET['callback']."(".json_encode($charges->product_delivery_charge).")";
        return $_GET['callback']."(".json_encode($charges).")";
    }
}
