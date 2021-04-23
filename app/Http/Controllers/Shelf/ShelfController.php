<?php

namespace App\Http\Controllers\Shelf;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Datatables;
use App\Role;
use App\Hub;
use App\Shelf;
use App\ShelfProduct;
use Validator;
use Session;
use Redirect;
use DB;
use Auth;
use Entrust;

class ShelfController extends Controller
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


    public function index( Request $request )
    {
      if(!Entrust::can('view-shelf')) { abort(403); }
      $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
      $req  = $request->all();
      $query = Shelf::select(['shelfs.*'])->where('shelfs.status', 1);

      if (Auth::user()->hasRole('hubmanager')) {
        // $query->leftJoin('hubs', 'hubs.id', '=', 'shelfs.hub_id');
        // $query->where('hubs.responsible_user_id', '=', auth()->user()->id);
        $query->where('shelfs.hub_id', '=', auth()->user()->reference_id);
      }

      ( $request->has('hub_id') )         ? $query->where('hub_id', trim($request->hub_id))                          : null;
      ( $request->has('assignd_hub_id') ) ? $query->where('assignd_hub_id', trim($request->assignd_hub_id))          : null;
      ( $request->has('shelf_title') )    ? $query->where('shelf_title', 'like', trim($request->shelf_title)."%")    : null;
      ( $request->has('width') )          ? $query->where('width', trim($request->width))                            : null;
      ( $request->has('height') )         ? $query->where('height', trim($request->height))                          : null;
      ( $request->has('length') )         ? $query->where('length', trim($request->length))                          : null;

      $shelfs = $query->orderBy('id', 'desc')->paginate(10);

      return view('shelfs.index', compact('shelfs', 'hubs', 'req'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      if(!Entrust::can('create-shelf')) { abort(403); }
      $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
      return view('shelfs.insert', compact('hubs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if(!Entrust::can('create-shelf')) { abort(403); }

      // return $request->all();

      $validation = Validator::make($request->all(), [
         'shelf_title'     => 'required',
         'shelf_type'     => 'required',
         'hub_id'          => 'required',
         'assignd_hub_id'  => 'required',
         'width'           => 'required',
         'height'          => 'required',
         'length'          => 'required'
      ]);

      if($validation->fails()) {
          return Redirect::back()->withErrors($validation)->withInput();
      }

      $shelf = new Shelf();
      $shelf->fill($request->except('_token'));
      $shelf->created_by = auth()->user()->id;
      $shelf->updated_by = auth()->user()->id;
      $shelf->shelf_type = $request->shelf_type;
      $shelf->save();

      Session::flash('message', "Shelf saved successfully");
      return redirect('/shelf');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      if(!Entrust::can('view-shelf')) { abort(403); }

      $shelf = Shelf::whereStatus(true)->findOrFail($id);
      return view('shelfs.view', compact('shelf'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if(!Entrust::can('edit-shelf')) { abort(403); }
      $shelf = Shelf::where('id', '=', $id)->first();

      $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

      return view('shelfs.edit', compact('shelf','hubs'));
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
      if(!Entrust::can('edit-shelf')) { abort(403); }

      $validation = Validator::make($request->all(), [
         'shelf_title'     => 'required',
         'hub_id'          => 'required',
         'assignd_hub_id'  => 'required',
         'width'           => 'required',
         'height'          => 'required',
         'length'          => 'required'
      ]);

      if($validation->fails()) {
          return Redirect::back()->withErrors($validation)->withInput();
      }

      $shelf = Shelf::findOrFail($id);
      $shelf->fill($request->except('_token'));
      $shelf->updated_by = auth()->user()->id;
      $shelf->save();

      Session::flash('message', "Shelf updated successfully");
      return redirect('/shelf');
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
      // return 1;
      if(!Entrust::can('view-shelf')) { abort(403); }
      $shelf_products = ShelfProduct::select(['*'])
                        ->leftJoin('order_product', 'order_product.id', '=', 'shelf_products.product_id')
                        ->leftJoin('orders', 'orders.id', '=', 'order_product.order_id')
                        ->leftJoin('sub_orders', 'sub_orders.id', '=', 'order_product.sub_order_id')
                        ->where('shelf_products.shelf_id', $request->id)
                        ->where('shelf_products.status', 1)
                        ->get();

      return view('shelfs.products', compact('shelf_products'));
    }
}
