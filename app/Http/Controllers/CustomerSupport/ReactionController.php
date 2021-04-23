<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CustomerSupportModel\Reaction;
use Validator;
use Log;
use DB;
use Session;
class ReactionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage_reaction');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = Reaction::orderBy('id', 'asc');
        ( $request->has('team_title') ) ? $query->where('team_title', trim($request->team_title)) : null;
        $reactions = $query->get();
        return view('customer-support.reaction.index', compact('reactions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(403);
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
     $validation = Validator::make($request->all(), [
        'title' => 'required|string|unique:reactions',
    ]);
     if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $reaction = new Reaction();
        $reaction->title = $request->title;
        $reaction->status = 1;
        $reaction->created_by = auth()->user()->id;
        DB::beginTransaction();
        $reaction->save();
        DB::commit();
        Session::flash('message', "Reaction created successfully");
        return redirect('reaction');
    } catch (\Exception $e) {
        DB::rollback();
        dd($e);
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Reaction creation failed")->withInput();
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
        abort(403);
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
        abort(403);
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
     $validation = Validator::make($request->all(), [
        'title' => 'required|string|unique:reactions,title,'.$id,
    ]);
     if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $reaction = Reaction::findOrFail($id);
        $reaction->title = $request->title;
        $reaction->status = $request->status;
        $reaction->updated_by = auth()->user()->id;
        DB::beginTransaction();
        $reaction->save();
        DB::commit();
        Session::flash('message', "Reaction update successfully");
        return redirect('reaction');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Reaction updation failed")->withInput();
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
        //
        abort(403);
    }
}
