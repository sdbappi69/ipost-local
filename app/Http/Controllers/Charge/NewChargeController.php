<?php

namespace App\Http\Controllers\Charge;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Charge;
use App\ProductCategory;
use App\ChargeModel;
use App\ZoneGenre;

use Validator;
use Redirect;
use Session;
use Entrust;

use Auth;

class NewChargeController extends Controller
{
    public function view($category_id, Request $request){

        if(!Entrust::can('view-charge')) { abort(403); } 

        $product_category = ProductCategory::where('id', $category_id)->first();
        $charge_types = array('fixed' => 'Fixed', 'weight_based' => 'Weight based' );

        if($request->has('store_id')){
            $charges = Charge::where('product_category_id', $category_id)
                        ->where(function($query) use ($request) {
                            $query->where('charges.store_id', $request->store_id);
                            $query->orWhere('charges.store_id', null);
                        })
                        ->orderBy('charges.store_id', 'desc')
                        ->get();
        }else{
            $charges = Charge::where('product_category_id', $category_id)->where('store_id', null)->get();
        }

        // return $charges;

        $charge_models = ChargeModel::whereStatus(true)->where('id', '!=', 1)->orderBy('title', 'asc')->get();
        $zone_genres = ZoneGenre::whereStatus(true)->orderBy('title', 'asc')->get();

        // Get Charge Type
        if(count($charges) > 0){

            foreach ($charges as $charge) {
                $charge_model_id = $charge->charge_model_id;
                break;
            }

            if($charge_model_id == 2){
                $charge_type = 'fixed';
            }else if($charge_model_id == 3 || $charge_model_id == 4 || $charge_model_id == 5){
                $charge_type = 'weight_based';
            }else{
                $charge_type = '';
            }
        }else{
            $charge_type = '';
        }
        if(Auth::user()->hasRole('kam')) {
            return view('charges.viewcharges', compact('product_category', 'charge_types', 'charge_type', 'charge_models', 'zone_genres', 'charges'));
        } else {
            return view('charges.new.view', compact('product_category', 'charge_types', 'charge_type', 'charge_models', 'zone_genres', 'charges'));
        }
    }

    public function save(Request $request){

        if(!Entrust::can('create-charge')) { abort(403); }

        if(!Entrust::can('edit-charge')) { abort(403); } 

        $validation = Validator::make($request->all(), [
            'product_category_id' => 'required',
            'charge_model_id' => 'required',
            'zone_genre_id' => 'required',
            'fixed_charge' => 'required|numeric',
            'store_id' => 'sometimes',
            'additional_charge_type' => 'sometimes|numeric',
            'additional_range_per_slot' => 'sometimes|numeric',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // Delete First
        if($request->charge_model_id == 2){
            if($request->has('store_id')){
                Charge::where('store_id', $request->store_id)
                    ->whereNotIn('charge_model_id', [1,2])
                    ->delete();
            }else{
                Charge::where('store_id', null)
                    ->whereNotIn('charge_model_id', [1,2])
                    ->delete();
            }
        }else if($request->charge_model_id == 3 || $request->charge_model_id == 4 || $request->charge_model_id == 5){
            if($request->has('store_id')){
                Charge::where('store_id', $request->store_id)
                    ->whereNotIn('charge_model_id', [1,3,4,5])
                    ->delete();
            }else{
                Charge::where('store_id', null)
                    ->whereNotIn('charge_model_id', [1,3,4,5])
                    ->delete();
            }
        }

        if($request->has('store_id')){
            Charge::where('store_id', $request->store_id)
                ->where('product_category_id', $request->product_category_id)
                ->where('charge_model_id', $request->charge_model_id)
                ->where('zone_genre_id', $request->zone_genre_id)
                ->delete();
        }else{
            Charge::where('store_id', null)
                ->where('product_category_id', $request->product_category_id)
                ->where('charge_model_id', $request->charge_model_id)
                ->where('zone_genre_id', $request->zone_genre_id)
                ->delete();
        }

        $charge = new Charge();
        $charge->fill($request->all());

        if(Auth::user()->hasRole('salesteam')) {
          $charge->status = '0';
          $charge->is_approved = '0';
        }

        $charge->created_by = auth()->user()->id;
        $charge->updated_by = auth()->user()->id;
        $charge->save();

        Session::flash('message', "Charge modified successfully");
        return Redirect::back();

    }

    public function priceApproval(){

        if(!Entrust::can('view-charge')) { abort(403); }

        $charges = Charge::where('status', '0')->where('is_approved', '0')->get();

        return view('charges.new.priceapproval', compact('charges'));

    }

    public function categoryApproval(){

        if(!Entrust::can('view-charge')) { abort(403); }

        $charges = Charge::where('status', '0')->where('is_approved', '0')->get();

        return view('charges.new.categoryApproval', compact('charges'));

    }

    public function approvePrice($id){

        if(!Entrust::can('view-charge')) { abort(403); }

        $charge = Charge::findOrFail($id);
        $charge->status = '1';
        $charge->is_approved = '1';
        $charge->updated_by = auth()->user()->id;
        $charge->save();

        Session::flash('message', "Charge successfully approved.");
        return Redirect::back();
    }

}
