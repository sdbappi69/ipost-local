<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CustomerSupportModel\InquiryStatus;
use Validator;
use Log;
use DB;
use Session;
class InquiryStatusController extends Controller
{
   function __construct()
   {
    $this->middleware('permission:manage_inquiry_status');
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $inquiry_status = InquiryStatus::orderBy('id', 'asc');
        ( $request->has('team_title') ) ? $inquiry_status->where('team_title', trim($request->team_title)) : null;
        $inquiry_statuss = $inquiry_status->get();
        return view('customer-support.inquiry-status.index', compact('inquiry_statuss'));
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
        $inquiry_status = new InquiryStatus();
        $inquiry_status->title = $request->title;
        $inquiry_status->status = 1;
        $inquiry_status->created_by = auth()->user()->id;
        DB::beginTransaction();
        $inquiry_status->save();
        DB::commit();
        Session::flash('message', "Inquiry Status created successfully");
        return redirect('inquiry-status');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Inquiry Status creation failed")->withInput();
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
        $inquiry_status = InquiryStatus::findOrFail($id);
        $inquiry_status->title = $request->title;
        $inquiry_status->status = $request->status;
        $inquiry_status->updated_by = auth()->user()->id;
        DB::beginTransaction();
        $inquiry_status->save();
        DB::commit();
        Session::flash('message', "Inquiry Status update successfully");
        return redirect('inquiry-status');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Inquiry Status updation failed")->withInput();
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
