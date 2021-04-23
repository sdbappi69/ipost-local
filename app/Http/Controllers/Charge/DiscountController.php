<?php

namespace App\Http\Controllers\Charge;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Discount;
use App\Store;
use App\ProductCategory;

use Validator;
use Session;
use Redirect;
use Image;
use DB;
use Auth;
use Entrust;

class DiscountController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $req = $request->all();

        // For filter
        $stores = Store::whereStatus(true)->orderBy('store_id', 'asc')->lists('store_id', 'id')->toArray();
        $product_categories = ProductCategory::whereStatus(true)->orderBy('name', 'asc')->lists('name', 'id')->toArray();
        $discount_types = array('percentage' => 'Percentage', 'fixed' => 'Fixed' );
        $unit_types = array('kg' => 'KG', 'bdt' => 'BDT' );

        $first_from_date = Discount::select('from_date')->orderBy('from_date', 'asc')->first();
        $last_to_date = Discount::select('to_date')->orderBy('to_date', 'desc')->first();

        $query = Discount::orderBy('id', 'desc');

        if($request->has('from_date')){
            $start_date = $request->from_date.':00';
        }else{
			if(count($first_from_date) > 0){
				$start_date = $first_from_date->from_date;
			}else{
				$start_date = date('Y-m-d H:i:s');
			}
        }

        if($request->has('to_date')){
            $end_date = $request->to_date.':59';
        }else{
			if(count($first_from_date) > 0){
				$end_date = $last_to_date->to_date;
			}else{
				$end_date = date('Y-m-d H:i:s');
			}
        }

        $query->where('from_date', '>=', $start_date);
        $query->where('to_date', '<=', $end_date);

        if($request->has('discount_title')){
            $query->where('discount_title', 'like', '%'.$request->discount_title.'%');
        }

        if($request->has('status')){
            $query->where('status', $request->status);
        }

        if($request->has('store_id')){
            $query->where('store_id', $request->store_id);
        }

        if($request->has('product_category_id')){
            $query->where('product_category_id', $request->product_category_id);
        }

        if($request->has('discount_type')){
            $query->where('discount_type', $request->discount_type);
        }

        if($request->has('min_discount') || $request->has('max_discount')){
            $query->WhereBetween('discount_value', array($request->min_discount, $request->max_discount));
        }else if($request->has('min_discount')){
            $query->where('discount_value', '>=', $request->min_discount);
        }else if($request->has('max_discount')){
            $query->where('discount_value', '<=', $request->max_discount);
        }

        if($request->has('unit_type')){
            $query->where('unit_type', $request->unit_type);
        }

        if($request->has('min_discount_range')){
            $query->where('start_unit', '>=', $request->min_discount_range);
        }

        if($request->has('max_discount_range')){
            $query->where('end_unit', '<=', $request->max_discount_range);
        }

        $discounts = $query->paginate(10);

        return view('discount.index', compact('discounts', 'req', 'stores', 'product_categories', 'discount_types', 'unit_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::whereStatus(true)->orderBy('store_id', 'asc')->lists('store_id', 'id')->toArray();
        $product_categories = ProductCategory::whereStatus(true)->orderBy('name', 'asc')->lists('name', 'id')->toArray();
        $discount_types = array('percentage' => 'Percentage', 'fixed' => 'Fixed' );
        $unit_types = array('kg' => 'KG', 'bdt' => 'BDT' );

        return view('discount.insert', compact('stores', 'product_categories', 'discount_types', 'unit_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'discount_title' => 'required',
            'status' => 'required',
            'store_id' => 'sometimes',
            'product_category_id' => 'sometimes',
            'discount_type' => 'required',
            'discount_value' => 'required',
            'unit_type' => 'required',
            'start_unit' => 'required',
            'end_unit' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $discount = new Discount();
        $discount->fill($request->all());
        $discount->from_date = $request->from_date.':00';
        $discount->to_date = $request->to_date.':00';
        $discount->created_by = auth()->user()->id;
        $discount->updated_by = auth()->user()->id;
        $discount->save();

        Session::flash('message', "Discount saved successfully");
        return redirect('/discount');
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
        $discount = Discount::where('id', '=', $id)->first();

        $stores = Store::whereStatus(true)->orderBy('store_id', 'asc')->lists('store_id', 'id')->toArray();
        $product_categories = ProductCategory::whereStatus(true)->orderBy('name', 'asc')->lists('name', 'id')->toArray();
        $discount_types = array('percentage' => 'Percentage', 'fixed' => 'Fixed' );
        $unit_types = array('kg' => 'KG', 'bdt' => 'BDT' );

        return view('discount.edit', compact('discount', 'stores', 'product_categories', 'discount_types', 'unit_types'));
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
            'discount_title' => 'required',
            'status' => 'required',
            'store_id' => 'sometimes',
            'product_category_id' => 'sometimes',
            'discount_type' => 'required',
            'discount_value' => 'required',
            'unit_type' => 'required',
            'start_unit' => 'required',
            'end_unit' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if(strlen($request->from_date) == 19){
            $from_date = $request->from_date;
        }else{
            $from_date = $request->from_date.':00';
        }

        if(strlen($request->to_date) == 19){
            $to_date = $request->to_date;
        }else{
            $to_date = $request->to_date.':00';
        }

        $discount = Discount::findOrFail($id);
        $discount->fill($request->all());
        $discount->from_date = $from_date;
        $discount->to_date = $to_date;
        $discount->created_by = auth()->user()->id;
        $discount->updated_by = auth()->user()->id;
        $discount->save();

        Session::flash('message', "Discount updated successfully");
        return redirect('/discount');

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
