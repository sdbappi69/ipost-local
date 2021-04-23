<?php

namespace App\Http\Controllers\Trip;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator;
use Session;
use Redirect;
use Image;
use DB;
use Auth;
use Entrust;
use PDF;

use App\TripMap;
use App\Hub;

class TripMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();

        $query = TripMap::whereStatus(true)->groupBy('start_hub_id', 'end_hub_id');

        if($request->has('start_hub_id')){
            $query->whereIn('start_hub_id', $request->start_hub_id);
        }

        if($request->has('end_hub_id')){
            $query->whereIn('end_hub_id', $request->end_hub_id);
        }

        $trip_maps = $query->paginate(10);

        return view('trip-map.index', compact('hubs', 'trip_maps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hubs = Hub::whereStatus(true)->lists('title', 'id')->toArray();
        return view('trip-map.insert', compact('hubs'));
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
            'start_hub_id' => 'required',
            'end_hub_id' => 'required',
            'hub_id' => 'required'
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        if($request->start_hub_id == $request->end_hub_id){
            Session::flash('message', "Start Hub & End Hub can't be same");
            return Redirect::back();
        }else if($request->start_hub_id == $request->hub_id){
            Session::flash('message', "Start Hub & Transit Hub can't be same");
            return Redirect::back();
        }else if($request->end_hub_id == $request->hub_id){
            Session::flash('message', "End Hub & Transit Hub can't be same");
            return Redirect::back();
        }

        $trip_map = TripMap::where('start_hub_id', $request->start_hub_id)
                                ->where('end_hub_id', $request->end_hub_id)
                                ->where('hub_id', $request->hub_id)
                                ->first();
        if($trip_map){
            Session::flash('message', "This map already exist");
            return Redirect::back();
        }else{

            try {
                DB::beginTransaction();

                    $old_map = TripMap::where('start_hub_id', $request->start_hub_id)
                                ->where('end_hub_id', $request->end_hub_id)
                                ->orderBy('priority', 'desc')
                                ->first();
                    if($old_map){
                        $priority = $old_map->priority + 1;
                    }else{
                        $priority = 1;
                    }

                    $map = new TripMap();
                    $map->start_hub_id = $request->start_hub_id;
                    $map->end_hub_id = $request->end_hub_id;
                    $map->hub_id = $request->hub_id;
                    $map->priority = $priority;
                    $map->created_by = auth()->user()->id;
                    $map->updated_by = auth()->user()->id;
                    $map->save();

                DB::commit();

                Session::flash('message', "New Map created successfully");
                return redirect('/trip-map/'.$map->start_hub_id.'/'.$map->end_hub_id);

            } catch (Exception $e) {
                DB::rollback();

                Session::flash('message', "Failed to create new Map");
                return Redirect::back();
            }

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
    public function edit($start_hub_id, $end_hub_id)
    {
        $trip_maps = TripMap::whereStatus(true)
                                ->where('start_hub_id', $start_hub_id)
                                ->where('end_hub_id', $end_hub_id)
                                ->orderBy('priority', 'asc')
                                ->get();

        $exist_hubs = array((int)$start_hub_id, (int)$end_hub_id);
        foreach ($trip_maps as $trip_map) {
            $exist_hubs[] = $trip_map->hub_id;
        }

        $hubs = Hub::whereStatus(true)->whereNotIn('id', $exist_hubs)->lists('title', 'id')->toArray();

        $start_hub = Hub::findOrFail($start_hub_id);
        $end_hub = Hub::findOrFail($end_hub_id);

        return view('trip-map.edit', compact('hubs', 'trip_maps', 'start_hub', 'end_hub'));
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
            'hub_priority' => 'required'
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        try {
            DB::beginTransaction();

                $trip_map = TripMap::findOrFail($id);

                switch ($request->hub_priority) {
                    case 'up':
                        
                        $target_trip_map = TripMap::where('start_hub_id', $trip_map->start_hub_id)
                                        ->where('end_hub_id', $trip_map->end_hub_id)
                                        ->where('priority', '<', $trip_map->priority)
                                        ->orderBy('priority', 'desc')
                                        ->first();

                        $new_priority = $target_trip_map->priority;

                        break;
                    
                    default:
                        
                        $target_trip_map = TripMap::where('start_hub_id', $trip_map->start_hub_id)
                                        ->where('end_hub_id', $trip_map->end_hub_id)
                                        ->where('priority', '>', $trip_map->priority)
                                        ->orderBy('priority', 'asc')
                                        ->first();

                        $new_priority = $target_trip_map->priority;

                        break;
                }

                $target_trip_map->priority = $trip_map->priority;
                $target_trip_map->save();

                $trip_map->priority = $new_priority;
                $trip_map->save();

            DB::commit();

            Session::flash('message', 'Map updated successfully');
            return Redirect::back();

        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('message', 'Failed to update the Map');
            return Redirect::back();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

                $trip_map = TripMap::where('id', $id)->delete();

            DB::commit();

            Session::flash('message', 'Hub removed successfully');
            return Redirect::back();

        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('message', 'Failed to remove the Hub');
            return Redirect::back();
        }
    }
}
