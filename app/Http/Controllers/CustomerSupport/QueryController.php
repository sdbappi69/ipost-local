<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CustomerSupportModel\Query;
use Validator;
use Log;
use DB;
use Session;
class QueryController extends Controller
{
   function __construct()
   {
    $this->middleware('permission:manage_query');
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = Query::orderBy('id', 'asc');
        ( $request->has('team_title') ) ? $query->where('team_title', trim($request->team_title)) : null;
        $querys = $query->get();
        return view('customer-support.querys.index', compact('querys'));
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
        'title' => 'required|string|unique:queries',
    ]);
       if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $query = new Query();
        $query->title = $request->title;
        $query->status = 1;
        $query->created_by = auth()->user()->id;
        DB::beginTransaction();
        $query->save();
        DB::commit();
        Session::flash('message', "Query created successfully");
        return redirect('query');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Query creation failed")->withInput();
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
        'title' => 'required|string|unique:queries,title,'.$id,
    ]);
       if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $query = Query::findOrFail($id);
        $query->title = $request->title;
        $query->status = $request->status;
        $query->updated_by = auth()->user()->id;
        DB::beginTransaction();
        $query->save();
        DB::commit();
        Session::flash('message', "Query update successfully");
        return redirect('query');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Query updation failed")->withInput();
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
