<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CustomerSupportModel\MailGroup;
use Validator;
use Log;
use DB;
use Session;
class MailGroupController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage_mail_groups');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = MailGroup::orderBy('id', 'asc');
        ( $request->has('team_title') ) ? $query->where('team_title', trim($request->team_title)) : null;
        $mail_groups = $query->get();
        return view('customer-support.mail_groups.index', compact('mail_groups'));
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
        'team_title' => 'required|string|unique:mail_groups',
        'to' => 'required|email',
        'cc' => 'required|string',
    ]);
       if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $mail_group = new MailGroup();
        $mail_group->team_title = $request->team_title;
        $mail_group->to = $request->to;
        $mail_group->cc = $request->cc;
        $mail_group->status = 1;
        $mail_group->created_by = auth()->user()->id;
        DB::beginTransaction();
        $mail_group->save();
        DB::commit();
        Session::flash('message', "Mail group created successfully");
        return redirect('mail-groups');
    } catch (\Exception $e) {
        DB::rollback();
        dd($e);
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Mail group creation failed")->withInput();
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
        'team_title' => 'required|string|unique:mail_groups,team_title,'.$id,
        'to' => 'required|email',
        'cc' => 'required|string',
    ]);
       if ($validation->fails()) {
        return redirect()->back()->withErrors($validation)->withInput();
    }
    try {
        $mail_group = MailGroup::findOrFail($id);
        $mail_group->team_title = $request->team_title;
        $mail_group->to = $request->to;
        $mail_group->cc = $request->cc;
        $mail_group->status = $request->status;
        $mail_group->updated_by = auth()->user()->id;
        DB::beginTransaction();
        $mail_group->save();
        DB::commit();
        Session::flash('message', "Mail group update successfully");
        return redirect('mail-groups');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());
        return redirect()->back()->withErrors("Mail group updation failed")->withInput();
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
