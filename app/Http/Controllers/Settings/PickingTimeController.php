<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Session;
use Redirect;
use Entrust;
use App\PickingTimeSlot;

class PickingTimeController extends Controller
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
      $req  = $request->all();
      $query = PickingTimeSlot::whereStatus(true);

      ( $request->has('day') )        ? $query->where('day', ($request->day))                     : null;
      ( $request->has('start_time') ) ? $query->where('start_time', ($request->start_time).":00") : null;
      ( $request->has('end_time') )   ? $query->where('end_time', ($request->end_time).":00")     : null;

      $picking_times = $query->orderBy('id', 'desc')->paginate(10);

      // $picking_times = PickingTimeSlot::orderBy('id', 'desc')->paginate(10);

      return view('picking-time.index', compact('picking_times', 'req'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('picking-time.insert', compact('picking_times'));
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
                'day' => 'required',
                'status' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $picking_time = new PickingTimeSlot();
        $picking_time->fill($request->all());
        $picking_time->save();

        Session::flash('message', "Picking Time saved successfully");
        return redirect('/picking-time');
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
        $picking_time = PickingTimeSlot::where('id', '=', $id)->first();
        return view('picking-time.edit', compact('picking_time'));
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
                'day' => 'required',
                'status' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $picking_time = PickingTimeSlot::findOrFail($id);
        $picking_time->fill($request->all());
        $picking_time->save();

        Session::flash('message', "Picking Time updated successfully");
        return redirect('/picking-time');
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
