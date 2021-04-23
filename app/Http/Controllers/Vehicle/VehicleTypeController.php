<?php

namespace App\Http\Controllers\Vehicle;

use App\VehicleType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Redirect;

class VehicleTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Vehicle Type Listing';
        $vehicleTypes = VehicleType::orderBy('title', 'ASC')->get();

        return view('vehicle.type.index', compact('title', 'vehicleTypes'));
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
    public function store(Requests\Vehicle\Type $request)
    {
        VehicleType::create( $request->only('title', 'details', 'status') );

        // flash()->overlay('Vehicle Type Added Successfully.', 'Added!');
        Session::flash('message', "Vehicle Type Added Successfully");
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
        $title = 'Update Vehicle Type';
        $vehicleType = VehicleType::findOrFail($id);

        return view('vehicle.type.edit', compact('title', 'vehicleType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Vehicle\Type $request, $id)
    {
        VehicleType::findOrFail($id)->update( $request->only('title', 'details', 'status') );

        // flash()->overlay('Vehicle Type Updated Successfully.', 'Updated!');
        Session::flash('message', "Vehicle Type Updated Successfully");
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
