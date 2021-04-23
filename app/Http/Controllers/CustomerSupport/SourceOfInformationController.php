<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CustomerSupportModel\SourceOfInformation;
use Validator;
use Log;
use DB;
use Session;
class SourceOfInformationController extends Controller
{
 function __construct()
 {
    $this->middleware('permission:manage_source_of_information');
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SourceOfInformation::orderBy('id', 'asc');
        ( $request->has('team_title') ) ? $query->where('team_title', trim($request->team_title)) : null;
        $src_of_infos = $query->get();
        return view('customer-support.source-of-infomation.index', compact('src_of_infos'));
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
        'title' => 'required|string|unique:source_of_informations',
    ]);
     if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $src_of_info = new SourceOfInformation();
        $src_of_info->title = $request->title;
        $src_of_info->status = 1;
        $src_of_info->created_by = auth()->user()->id;
        DB::beginTransaction();
        $src_of_info->save();
        DB::commit();
        Session::flash('message', "Source Of Information created successfully");
        return redirect('source-of-info');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Source Of Information creation failed")->withInput();
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
        'title' => 'required|string|unique:source_of_informations,title,'.$id,
    ]);
     if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $src_of_info = SourceOfInformation::findOrFail($id);
        $src_of_info->title = $request->title;
        $src_of_info->status = $request->status;
        $src_of_info->updated_by = auth()->user()->id;
        DB::beginTransaction();
        $src_of_info->save();
        DB::commit();
        Session::flash('message', "Source Of Information update successfully");
        return redirect('source-of-info');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Source Of Information updation failed")->withInput();
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

    }}
