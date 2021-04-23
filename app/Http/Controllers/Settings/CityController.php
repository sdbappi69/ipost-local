<?php

namespace App\Http\Controllers\Settings;

use App\City;
use App\Country;
use App\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class CityController extends Controller
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
        $title = 'City List';

        $query = City::with('state.country');
        if($request->has('filter_name')){
            $query->where('name', 'like', '%'.$request->filter_name.'%');
        }
        if($request->has('filter_state_id')){
            $query->where('state_id', $request->filter_state_id);
        }
        $cities = $query->orderBy('name', 'ASC')->paginate(20);

        $states = State::
                    select(DB::raw('CONCAT(states.name, ", ", countries.name) AS name'),
                    'states.id'
                )
                ->join('countries', 'countries.id', '=', 'states.country_id')
                ->where('states.status', 1)
                ->orderBy('states.name', 'asc')
                ->lists('states.name','states.id')->toArray();

        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        return view('settings.city.index', compact('title', 'cities', 'countries', 'states'));
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
        City::firstOrCreate( $request->only('name', 'state_id', 'status') );

        flash()->overlay('City Information Added Successfully.', 'Added!');
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
        $title = 'Update City / District Information';
        $city = City::with('state.country')->findOrFail($id);
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        return view('settings.city.edit', compact('title', 'city', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\City\Update $request, $id)
    {
        City::findOrFail($id)->update( $request->only('name', 'state_id', 'status') );

        flash()->overlay('City Information Updated Successfully.', 'Updated!');
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
