<?php

namespace App\Http\Controllers\Hub;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Datatables;
use App\Country;
use App\State;
use App\City;
use App\Zone;
use App\Role;
use App\Hub;
use App\Merchant;
use App\Store;
use App\ZoneGenre;
use Validator;
use Session;
use Redirect;
use Image;
use DB;
use Auth;
use Entrust;

class HubController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|coo|operationmanager|operationalhead');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        if(!Entrust::can('view-hub')) { abort(403); }

        $zoneGenre   = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();
        $req         = $request->all();
        $query       = Hub::whereStatus(true);

        ( $request->has('id') )              ? $query->where('id', trim($request->id))                       : null;
        ( $request->has('msisdn') )          ? $query->where('msisdn', trim($request->msisdn))               : null;
        ( $request->has('alt_msisdn') )      ? $query->where('alt_msisdn', trim($request->alt_msisdn))       : null;
        ( $request->has('zone_genre_id') )   ? $query->where('zone_genre_id', trim($request->zone_genre_id)) : null;

        $hubs        = $query->orderBy('id', 'desc')->paginate(10);
        $hubs_lists  = Hub::whereStatus(true)->lists('title', 'id')->toArray();

        return view('hubs.index', compact('hubs', 'zoneGenre', 'req', 'hubs_lists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Entrust::can('create-hub')) { abort(403); }
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $zone_genres = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();
        $users = User::select(array(
                                'users.id AS id',
                                DB::raw("CONCAT(users.name,' - ',roles.display_name) AS name")
                            ))
                            ->leftJoin('roles', 'users.user_type_id', '=', 'roles.id')
                            ->where('status', '=', '1')
                            ->where('user_type_id', '<=', '6')
                            ->lists("name", 'id')
                            ->toArray();

        return view('hubs.insert', compact('prefix', 'countries', 'users', 'zone_genres'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Entrust::can('create-hub')) { abort(403); }
        // return $request->all();
        $validation = Validator::make($request->all(), [
                'title' => 'required',
                'details' => 'required',
                'msisdn' => 'required|between:10,25',
                'alt_msisdn' => 'sometimes|between:10,25',
                // 'country_id' => 'required',
                // 'state_id' => 'required',
                // 'city_id' => 'required',
                // 'zone_id' => 'required',
                // 'address1' => 'required',
                'responsible_user_id' => 'required',
                'alt_responsible_user_id' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $zone = Zone::find($request->zone_id);
if(!$zone){
    return redirect()->back()->withErrors("Unknown zone area.");
}
        $hub = new Hub();
        $hub->fill($request->except('msisdn_country', 'alt_msisdn_country', 'city_id', 'state_id', 'country_id'));
        $hub->city_id = $zone->city->id;
        $hub->state_id = $zone->city->state->id;
        $hub->country_id = $zone->city->state->country->id;
        $hub->created_by = auth()->user()->id;
        $hub->updated_by = auth()->user()->id;
        $hub->save();

        Session::flash('message', "Hub saved successfully");
        return redirect('/hub');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!Entrust::can('view-hub')) { abort(403); }

        $hub = Hub::whereStatus(true)->findOrFail($id);
        return view('hubs.view', compact('hub'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Entrust::can('edit-hub')) { abort(403); }
        $hub = Hub::where('id', '=', $id)->first();

        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $hub->country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $hub->state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $hub->city_id)->lists('name', 'id')->toArray();
        $zone_genres = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();
        $users = User::select(array(
                                'users.id AS id',
                                DB::raw("CONCAT(users.name,' - ',roles.display_name) AS name")
                            ))
                            ->leftJoin('roles', 'users.user_type_id', '=', 'roles.id')
                            ->where('status', '=', '1')
                            ->where('user_type_id', '<=', '6')
                            ->lists("name", 'id')
                            ->toArray();
        return view('hubs.edit', compact('prefix', 'countries', 'hub', 'states', 'cities', 'zones', 'users', 'zone_genres'));
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
        if(!Entrust::can('edit-hub')) { abort(403); }

        $validation = Validator::make($request->all(), [
                'title' => 'required',
                'details' => 'required',
                'msisdn' => 'required|between:10,25',
                'alt_msisdn' => 'sometimes|between:10,25',
                // 'country_id' => 'required',
                // 'state_id' => 'required',
                // 'city_id' => 'required',
                // 'zone_id' => 'required',
                // 'address1' => 'required',
                'responsible_user_id' => 'required',
                'alt_responsible_user_id' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $zone = Zone::find($request->zone_id);
if(!$zone){
    return redirect()->back()->withErrors("Unknown zone area.");
}
        $hub = Hub::findOrFail($id);
        $hub->fill($request->except('msisdn_country', 'alt_msisdn_country', 'city_id', 'state_id', 'country_id'));
        $hub->city_id = $zone->city->id;
        $hub->state_id = $zone->city->state->id;
        $hub->country_id = $zone->city->state->country->id;
        $hub->updated_by = auth()->user()->id;
        $hub->save();

        Session::flash('message', "Hub updated successfully");
        return redirect('/hub');
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
