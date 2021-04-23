<?php

namespace App\Http\Controllers\Vehicle;

use App\Driver;
use App\Vehicle;
use Illuminate\Http\Request;
use Image;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;

class DriverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|coo');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $title = 'Driver Listing';
      $vehicles = Vehicle::whereStatus(true)->lists('name', 'id')->toArray();

      $req  = $request->all();
      $query = Driver::select('id', 'name', 'photo', 'contact_msisdn', 'date_of_birth', 'status','job_type');

      ( $request->has('name') )            ? $query->where('name', 'like', trim($request->name)."%")         : null;
      ( $request->has('contact_msisdn') )  ? $query->where('contact_msisdn', trim($request->contact_msisdn)) : null;
      ( $request->has('job_type') )        ? $query->where('job_type', trim($request->job_type))             : null;
      ( $request->has('status') )          ? $query->where('status', trim($request->status))                 : null;

      $drivers = $query->orderBy('name', 'ASC')->paginate(5);

      return view('vehicle.driver.index', compact('title', 'vehicles', 'drivers', 'req'));
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
    public function store(Requests\Vehicle\Driver $request)
    {
        $driver = new Driver();
        $driver->fill( $request->except('photo') );

        if( $request->hasFile('photo') ) {

            $image = Image::make( $request->file('photo') );
            $fileName = uniqid().'_'.str_random(5).'.'.$request->file('photo')->getClientOriginalExtension();
            $image->resize(480, 320)->save('images/driver/'.$fileName);

            $driver->photo = env('APP_URL').'/images/driver/'.$fileName;
        }

        $driver->save();

        (count( $request->input('vehicle_id') > 0 )) ? $driver->vehicles()->attach( $request->input('vehicle_id') ) : null;

        flash()->overlay('Driver Added Successfully.', 'Added!');
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
        $title  = 'Driver Information';
        $driver = Driver::with('vehicles')->findOrFail($id);

        return view('vehicle.driver.show', compact('title', 'driver'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Update Driver Information';
        $driver = Driver::with('vehicles')->findOrFail($id);
        $vehicles = Vehicle::whereStatus(true)->lists('name', 'id')->toArray();

        return view('vehicle.driver.edit', compact('title', 'driver', 'vehicles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Vehicle\Driver $request, $id)
    {
        // dd($request->all());
        $driver = Driver::findOrFail($id);
        $driver->fill( $request->except('photo') );

        if( $request->hasFile('photo') ) {

            $image = Image::make( $request->file('photo') );
            $fileName = uniqid().'_'.str_random(5).'.'.$request->file('photo')->getClientOriginalExtension();
            $image->resize(480, 320)->save('images/driver/'.$fileName);

            $driver->photo = env('APP_URL').'/images/driver/'.$fileName;
        }

        $driver->save();

        // (count( $request->input('vehicle_id') > 0 )) ? $driver->vehicles()->sync( $request->input('vehicle_id') ) : null;

        Session::flash('message', "Driver Information Updated Successfully.");
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
