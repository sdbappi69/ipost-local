<?php

namespace App\Http\Controllers\Settings;

use App\Country;
use App\Zone;
use App\ZoneMap;
use App\City;
use App\ZoneGenre;
use App\Hub;
use Illuminate\Http\Request;
use Session;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class ZoneController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|operationmanager|operationalhead');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Zone List';

        $query = Zone::with('city.state.country');
        if($request->has('filter_name')){
            $query->where('name', 'like', '%'.$request->filter_name.'%');
        }
        if($request->has('filter_city_id')){
            $query->where('city_id', $request->filter_city_id);
        }
        if($request->has('filter_hub_id')){
            $query->where('hub_id', $request->filter_hub_id);
        }
        $zones = $query->orderBy('name', 'ASC')->paginate(20);

        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
        $cities = City::
                    select(DB::raw('CONCAT(cities.name, ", ", states.name) AS name'),
                    'cities.id'
                )
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->where('cities.status', 1)
                ->orderBy('cities.name', 'asc')
                ->lists('cities.name','cities.id')->toArray();

        return view('settings.zone.index', compact('title', 'zones', 'countries', 'hubs', 'cities'));
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
    public function store(Requests\Zone\Update $request)
    {
        try {
            $zone = Zone::firstOrCreate( $request->except('country_id', 'state_id', '_token', 'coordinates') );

            $zone->map()->create(['coordinates' => $request->coordinates]);
        } catch (\Exception $e) {
            Session::flash('message', "Something went wrong.");
            return redirect()->back();            
        }

        Session::flash('message', "Zone Information Added Successfully.");
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $zone = Zone::find($id);

        return view('settings.zone.show', compact('zone'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Update Zone Information';
        $zone = Zone::with('city.state.country')->findOrFail($id);
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $zone_genres = ZoneGenre::whereStatus(true)->lists('title', 'id')->toArray();
        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

        return view('settings.zone.edit', compact('title', 'zone', 'countries', 'zone_genres', 'hubs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Zone\Update $request, $id)
    {
        try {
            $zone = Zone::findOrFail($id);
            $zone->update($request->except('country_id', 'state_id', 'coordinates'));

            if(isset($zone->map)){
                $zone->map->coordinates = $request->coordinates;
                $zone->map->save();                
            } else {
                $zone->map()->create(['coordinates' => $request->coordinates]);
            }
        } catch (\Exception $e) {
            dd($e);
            Session::flash('message', "Something went wrong.");
            return redirect()->back();   
        }

        Session::flash('message', "Zone Information Updated Successfully.");
        return redirect()->back();
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
