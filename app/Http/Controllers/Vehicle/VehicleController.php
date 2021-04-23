<?php

namespace App\Http\Controllers\Vehicle;

use App\Vehicle;
use App\VehicleType;
use Illuminate\Http\Request;
use Image;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Redirect;


class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager|vehiclemanager|coo|operationalhead');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Vehicle Listing';
        $vehicleTypes = VehicleType::whereStatus(true)->lists('title', 'id')->toArray();

        $req  = $request->all();
        $query = Vehicle::with('type');

        ( $request->has('name') )            ? $query->where('name', 'like', trim($request->name)."%")            : null;
        ( $request->has('vehicle_type_id') ) ? $query->where('vehicle_type_id', trim($request->vehicle_type_id))  : null;
        ( $request->has('license_no') )      ? $query->where('license_no', trim($request->license_no))            : null;
        ( $request->has('brand') )           ? $query->where('brand', trim($request->brand))                      : null;
        ( $request->has('model') )           ? $query->where('model', trim($request->model))                      : null;
        ( $request->has('status') )          ? $query->where('status', trim($request->status))                    : null;

        $vehicles = $query->orderBy('name', 'ASC')->paginate(5);

        return view('vehicle.vehicle.index', compact('title', 'vehicles', 'vehicleTypes', 'req'));
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
    public function store(Requests\Vehicle\Vehicle $request)
    {
        $vehicle = new Vehicle();
        $vehicle->fill( $request->except('photo') );

        if( $request->hasFile('photo') ) {

            $image = Image::make( $request->file('photo') );
            $fileName = uniqid().'_'.str_random(5).'.'.$request->file('photo')->getClientOriginalExtension();
            $image->resize(480, 320)->save('images/vehicle/'.$fileName);

            $vehicle->photo = env('APP_URL').'/images/vehicle/'.$fileName;
        }

        $vehicle->save();

        // flash()->overlay('Vehicle Added Successfully.', 'Added!');
        // return redirect()->back();

        Session::flash('message', "Vehicle Added Successfully.");
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
        $title = 'Update Vehicle Information';
        $vehicle = Vehicle::findOrFail($id);
        $vehicleTypes = VehicleType::whereStatus(true)->lists('title', 'id')->toArray();

        return view('vehicle.vehicle.edit', compact('title', 'vehicle', 'vehicleTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Vehicle\Vehicle $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->fill( $request->except('photo') );

        if( $request->hasFile('photo') ) {

            $image = Image::make( $request->file('photo') );
            $fileName = uniqid().'_'.str_random(5).'.'.$request->file('photo')->getClientOriginalExtension();
            $image->resize(480, 320)->save('images/vehicle/'.$fileName);

            $vehicle->photo = env('APP_URL').'/images/vehicle/'.$fileName;
        }

        $vehicle->save();

        // flash()->overlay('Vehicle Information Updated Successfully.', 'Updated!');
        Session::flash('message', "Vehicle Information Updated Successfully.");
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

    public function vehicles($vehicle_type_id){

        $vehicles = Vehicle::whereStatus(true)->where('vehicle_type_id', '=', $vehicle_type_id)->addSelect('id','name')->get();

        return $_GET['callback']."(".json_encode($vehicles).")";
    }
}
