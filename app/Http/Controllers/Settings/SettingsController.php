<?php

namespace App\Http\Controllers\Settings;

use App\City;
use App\Country;
use App\Setting;
use App\State;
use App\Zone;
use Illuminate\Http\Request;
use Image;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Application Settings';

        $settings = Setting::findOrFail(1);

        return view('settings.settings.index', compact('title', 'settings'));
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
        //
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
        $title = 'Update Settings Information';
        $settings = Setting::first();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->lists('name', 'id')->toArray();

        return view('settings.settings.edit', compact('title', 'settings', 'countries', 'states', 'cities', 'zones'));
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
        $settings = Setting::findOrFail($id);
        $settings->update( $request->except('logo') );

        if( $request->hasFile('logo') ) {

            $image = Image::make( $request->file('logo') );
            $fileName = uniqid().'_'.str_random(5).'.'.$request->file('logo')->getClientOriginalExtension();
            $image->save('images/'.$fileName);

            $settings->logo = env('APP_URL').'/images/'.$fileName;
        }

        $settings->save();

        flash()->overlay('Settings Information Updated Successfully.', 'Success!!');
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
