<?php

namespace App\Http\Controllers\Settings;

use App\Country;
use App\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class StateController extends Controller
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
        $title = 'State List';

        $query = State::orderBy('name', 'ASC');
        if($request->has('filter_name')){
            $query->where('name', 'like', '%'.$request->filter_name.'%');
        }
        if($request->has('filter_country_id')){
            $query->where('country_id', $request->filter_country_id);
        }
        $states = $query->paginate(20);

        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        return view('settings.state.index', compact('title', 'states', 'countries'));
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
        State::firstOrCreate( $request->only('name', 'country_id', 'status') );

        flash()->overlay('Information Added Successfully.', 'Success!');
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
        $title = 'Update State Information';
        $state = State::findOrFail($id);
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        return view('settings.state.edit', compact('title', 'state', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\State\Update $request, $id)
    {
        State::findOrFail($id)->update( $request->all() );

        flash()->overlay('State Information Updated Successfully.', 'Success!');
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
