<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\PickingLocations;
use App\Country;
use App\State;
use App\City;
use App\Zone;
use Entrust;
use Validator;
use Session;
use Redirect;
use Datatables;

class WarehouseController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:merchantadmin|merchantsupport|salesteam|operationmanager|operationalhead');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('warehouse.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
//        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->lists('name', 'id')->toArray();

        return view('warehouse.insert', compact('prefix', 'zones'));
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
                'title' => 'required',
                'email' => 'required|email',
                'msisdn' => 'required|between:10,25',
                'alt_msisdn' => 'sometimes|between:10,25',
                // 'country_id' => 'required',
                // 'state_id' => 'required',
                // 'city_id' => 'required',
                'zone_id' => 'required',
                'address1' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $zone = Zone::find($request->zone_id);

        $warehouse = new PickingLocations();
        $warehouse->fill($request->except('msisdn_country', 'alt_msisdn_country', 'city_id', 'state_id', 'country_id'));
        $warehouse->city_id = $zone->city->id;
        $warehouse->state_id = $zone->city->state->id;
        $warehouse->country_id = $zone->city->state->country->id;
        $warehouse->created_by = auth()->user()->id;
        $warehouse->updated_by = auth()->user()->id;
        $warehouse->save();

        Session::flash('message', "Warehose saved successfully");
        return redirect('/warehouse');
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
        $warehouse = PickingLocations::where('id', '=', $id)->first();

        if($warehouse->merchant_id != auth()->user()->reference_id){
            abort(403);
        }

        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $warehouse->country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $warehouse->state_id)->lists('name', 'id')->toArray();
        // $zones = Zone::whereStatus(true)->where('city_id', '=', $warehouse->city_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->lists('name', 'id')->toArray();

        return view('warehouse.edit', compact('prefix', 'countries', 'id', 'warehouse', 'states', 'cities', 'zones'));
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
                'title' => 'sometimes',
                'email' => 'sometimes|email',
                'msisdn' => 'sometimes|between:10,25',
                'alt_msisdn' => 'sometimes|between:10,25',
                // 'country_id' => 'sometimes',
                // 'state_id' => 'sometimes',
                // 'city_id' => 'sometimes',
                'zone_id' => 'sometimes',
                'address1' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $zone = Zone::find($request->zone_id);

        $warehouse = PickingLocations::findOrFail($id);

        $warehouse->fill($request->except('msisdn_country', 'alt_msisdn_country', 'city_id', 'state_id', 'country_id'));
        $warehouse->city_id = $zone->city->id;
        $warehouse->state_id = $zone->city->state->id;
        $warehouse->country_id = $zone->city->state->country->id;

        $warehouse->save();

        Session::flash('message', "Warehose updated successfully");
        return redirect('/warehouse');
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

    public function warehouseList(){

        $query = PickingLocations::select(array(
                'pickup_locations.id',
                'pickup_locations.title',
                'pickup_locations.email',
                'pickup_locations.msisdn',
                'pickup_locations.alt_msisdn',
                'pickup_locations.status',
                'pickup_locations.address1',
                'pickup_locations.latitude',
                'pickup_locations.longitude',
            ))
            ->where('pickup_locations.merchant_id', '=', auth()->user()->reference_id)
            ->orderBy('pickup_locations.id', 'desc');

        $requesters = $query->get();

        return Datatables::of($requesters)
                ->remove_column('id')
                ->remove_column('latitude')
                ->remove_column('longitude')
                ->editColumn('status', '@if($status=="1")
                                <span class="label label-success"> Active </span>
                             @else
                                <span class="label label-danger"> Inactive </span>
                             @endif')
                ->add_column('action', 
                                    '<a class="label label-success" href="warehouse/{{ $id }}/edit">
                                                    <i class="fa fa-pencil"></i> View/Update 
                                                    </a>'
                            )
                ->add_column('view_map', 
                                    '<a class="label label-success" target="_blank" href="maps/{{ $latitude }}/{{ $longitude }}">
                                                    <i class="fa fa-map-marker"></i> Map View 
                                                    </a>'
                            )
                ->make();

    }
}
