<?php

namespace App\Http\Controllers\Api;

use App\RiderLocation;
use App\User;
use App\State;
use App\City;
use App\Zone;
use Illuminate\Http\Request;
use DB;
use Log;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {

            $validator = Validator::make($request->all(), [
                'rider_id'  =>  'required|exists:users,id',
                'latitude'  =>  'required',
                'longitude' =>  'required'
            ]);

            if($validator->fails()) {
                return $this->sendResponse('error', 422, $validator->errors()->all(), []);
            }

            DB::beginTransaction();

                RiderLocation::create( $request->only('rider_id', 'latitude', 'longitude') );
                User::findOrFail( $request->input('rider_id') )->update( $request->only('latitude', 'longitude') );

            DB::commit();

            return $this->sendResponse('success', 200, ['Locations updated successfully.'], []);

        } catch(\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());
            return $this->sendResponse('error', 500, ['Something went wrong. Please try again.'], []);
        }
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
        //
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
        //
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

    public function states($country_id){

        $states = State::whereStatus(true)->where('country_id', '=', $country_id)->addSelect('id','name')->get();

        return response($states);
    }

    public function cities($state_id){

        $cities = City::whereStatus(true)->where('state_id', '=', $state_id)->addSelect('id','name')->get();

        return response($cities);
    }

    public function zones($city_id){

        $zones = Zone::whereStatus(true)->where('city_id', '=', $city_id)->addSelect('id','name')->get();

        return response($zones);
    }
}
