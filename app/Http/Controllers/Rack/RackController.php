<?php

namespace App\Http\Controllers\Rack;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Datatables;
use App\Role;
use App\Hub;
use App\Zone;
use App\Rack;
use App\RackProduct;
use Validator;
use Session;
use Redirect;
use DB;
use Auth;
use Entrust;

class RackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
      $this->middleware('role:superadministrator|systemadministrator|hubmanager|inventoryoperator');
    }

    public function index(Request $request)
    {
      if(!Entrust::can('view-rack')) { abort(403); }
      $hubs    = Hub::whereStatus(true)->lists('title', 'id')->toArray();
      $zones   = Zone::whereStatus(true)->lists('name', 'id')->toArray();
      $req     = $request->all();
      $query   = Rack::select(['racks.*'])->where('racks.status', 1);

      if (Auth::user()->hasRole('hubmanager')) {
        // $query->leftJoin('hubs', 'hubs.id', '=', 'racks.hub_id');
        $query->where('racks.hub_id', '=', auth()->user()->reference_id);
      }

      ( $request->has('hub_id') )         ? $query->where('hub_id', trim($request->hub_id))                       : null;
      ( $request->has('zone_id') )        ? $query->where('zone_id', trim($request->zone_id))                     : null;
      ( $request->has('rack_title') )     ? $query->where('rack_title', 'like', trim($request->rack_title)."%")   : null;
      ( $request->has('width') )          ? $query->where('width', trim($request->width))                         : null;
      ( $request->has('height') )         ? $query->where('height', trim($request->height))                       : null;
      ( $request->has('length') )         ? $query->where('length', trim($request->length))                       : null;

      $racks   = $query->orderBy('id', 'desc')->paginate(10);

      return view('racks.index', compact('racks', 'hubs', 'zones', 'req'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      if(!Entrust::can('create-rack')) { abort(403); }
      $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
      $zones = Zone::whereStatus(true)->lists('name', 'id')->toArray();
      return view('racks.insert', compact('hubs', 'zones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if(!Entrust::can('create-rack')) { abort(403); }

      $validation = Validator::make($request->all(), [
         'rack_title'      => 'required',
         'hub_id'          => 'required',
         'zone_id'         => 'required',
         'width'           => 'required',
         'height'          => 'required',
         'length'          => 'required'
      ]);

      if($validation->fails()) {
          return Redirect::back()->withErrors($validation)->withInput();
      }

      $rack = new Rack();
      $rack->fill($request->except('_token'));
      $rack->created_by = auth()->user()->id;
      $rack->updated_by = auth()->user()->id;
      $rack->save();

      Session::flash('message', "Rack saved successfully");
      return redirect('/rack');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      if(!Entrust::can('view-rack')) { abort(403); }

      $rack = Rack::whereStatus(true)->findOrFail($id);
      return view('racks.view', compact('rack'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if(!Entrust::can('edit-rack')) { abort(403); }
      $rack = Rack::where('id', '=', $id)->first();
      $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
      $zones = Zone::whereStatus(true)->lists('name', 'id')->toArray();

      return view('racks.edit', compact('rack','hubs', 'zones'));
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
      if(!Entrust::can('edit-rack')) { abort(403); }

      $validation = Validator::make($request->all(), [
         'rack_title'      => 'required',
         'hub_id'          => 'required',
         'zone_id'         => 'required',
         'width'           => 'required',
         'height'          => 'required',
         'length'          => 'required'
      ]);

      if($validation->fails()) {
          return Redirect::back()->withErrors($validation)->withInput();
      }

      $rack = Rack::findOrFail($id);
      $rack->fill($request->except('_token'));
      $rack->updated_by = auth()->user()->id;
      $rack->save();

      Session::flash('message', "Rack updated successfully");
      return redirect('/rack');
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

    public function product_lists( Request $request )
    {
      if(!Entrust::can('view-rack')) { abort(403); }
      
      $rack_products = RackProduct::select(['*'])
                        ->leftJoin('order_product', 'order_product.id', '=', 'rack_products.product_id')
                        ->leftJoin('orders', 'orders.id', '=', 'order_product.order_id')
                        ->leftJoin('sub_orders', 'sub_orders.id', '=', 'order_product.sub_order_id')
                        ->where('rack_products.rack_id', $request->id)
                        ->where('rack_products.status', 1)
                        ->get();

      return view('racks.products', compact('rack_products'));
    }

}
