<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CustomerSupportModel\UniqueHead;
use Validator;
use Log;
use DB;
use Session;
class UniqueHeadController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage_unique_head');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = UniqueHead::orderBy('id', 'asc');
        ( $request->has('team_title') ) ? $query->where('team_title', trim($request->team_title)) : null;
        $unique_heads = $query->get();
        return view('customer-support.unique-head.index', compact('unique_heads'));
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
        'title' => 'required|string|unique:unique_heads',
    ]);
     if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $unique_head = new UniqueHead();
        $unique_head->title = $request->title;
        $unique_head->status = 1;
        $unique_head->created_by = auth()->user()->id;
        DB::beginTransaction();
        $unique_head->save();
        DB::commit();
        Session::flash('message', "Unique Head created successfully");
        return redirect('unique-head');
    } catch (\Exception $e) {
        DB::rollback();
        dd($e);
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Unique Head creation failed")->withInput();
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
        'title' => 'required|string|unique:unique_heads,title,'.$id,
    ]);
     if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $unique_head = UniqueHead::findOrFail($id);
        $unique_head->title = $request->title;
        $unique_head->status = $request->status;
        $unique_head->updated_by = auth()->user()->id;
        DB::beginTransaction();
        $unique_head->save();
        DB::commit();
        Session::flash('message', "Unique Head update successfully");
        return redirect('unique-head');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Unique Head updation failed")->withInput();
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
