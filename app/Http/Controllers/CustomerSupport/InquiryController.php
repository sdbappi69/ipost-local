<?php

namespace App\Http\Controllers\CustomerSupport;
use App\CustomerSupportModel\Inquiry;
use App\CustomerSupportModel\MailGroup;
use App\Http\Controllers\Controller;

use App\Services\PayUService\Exception;
use App\Http\Requests;
use App\SubOrder;
use DB;
use Excel;
use Illuminate\Http\Request;
use Log;
use Session;
use Validator;
use Mail;
class InquiryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage_inquiry');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = Inquiry::with(['sourceOfInformation','queryDetails','inquiryStatus','createdBy'])->orderBy('id','DSC');
        //Filter

        if($request->search_by_data){
            $query->where(function($q) use ($request) {
                $q->where('calling_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_name','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_alt_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_email','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_address','like','%'.$request->search_by_data.'%')
                ->orWhere('company_name','like','%'.$request->search_by_data.'%');
            });
        }
        ($request->query_id_s ? $query->whereIn('query_id',$request->query_id_s) : null);
        ($request->source_of_information_s ? $query->whereIn('source_of_information',$request->source_of_information_s) : null);
        ($request->status_s ? $query->whereIn('status',$request->status_s) : null);
        if(auth()->user()->can('head_of_customer_support')){
            ($request->created_by_s ? $query->whereCreatedBy($request->created_by_s) : null);
        }
        else{
            $query->whereCreatedBy(auth()->user()->id);
        }

        (($request->date_from and $request->date_to) ? $query->whereBetween('created_at',[$request->date_from.' 00:00:01',$request->date_to.' 23:59:59']) : null);

        // End Filter
        $inquirys = $query->paginate(20);
        $source_of_informations = __get_source_of_information_dropdown();
        $querys = __get_query_dropdown();
        $users = __get_inquiry_submitted_user_dropdown();
        $statues = __get_inquiry_status_dropdown();
        $mail_groups = __get_mail_gropus_dropdown();
        return view('customer-support.inquiry.index',compact('inquirys','querys','source_of_informations','users','statues','mail_groups'));
    }

    public function export_xls(Request $request){
        $query = Inquiry::with(['sourceOfInformation','queryDetails','inquiryStatus','createdBy'])->orderBy('id','DSC');
        //Filter
        if($request->search_by_data){
            $query->where(function($q) use ($request) {
                $q->where('customer_name','like','%'.$request->search_by_data.'%')
                ->orWhere('calling_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_alt_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_email','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_address','like','%'.$request->search_by_data.'%')
                ->orWhere('company_name','like','%'.$request->search_by_data.'%');
            });
        }
        ($request->query_id_s ? $query->whereIn('query_id',$request->query_id_s) : null);
        ($request->source_of_information_s ? $query->whereIn('source_of_information',$request->source_of_information_s) : null);
        ($request->status_s ? $query->whereIn('status',$request->status_s) : null);
        if(auth()->user()->can('head_of_customer_support')){
            ($request->created_by_s ? $query->whereCreatedBy($request->created_by_s) : null);
        }
        else{
            $query->whereCreatedBy(auth()->user()->id);
        }
        (($request->date_from and $request->date_to) ? $query->whereBetween('created_at',[$request->date_from.' 00:00:01',$request->date_to.' 23:59:59']) : null);
        // End Filter
        $inquirys = $query->get();
        if($inquirys){
            $data_array = null;
            $i = 0;
            foreach ($inquirys as $key => $inquiry) {
            # code...
                $data_array[$i]['Agent'] = $inquiry->createdBy->name;
                $data_array[$i]['Calling Number'] = $inquiry->calling_number;
                $data_array[$i]['Name'] = $inquiry->customer_name;
                $data_array[$i]['Number'] = $inquiry->customer_number;
                $data_array[$i]['Link / Company Name'] = $inquiry->company_name;
                $data_array[$i]['Alt. Number'] = $inquiry->company_alt_number;
                $data_array[$i]['E-mail'] = $inquiry->customer_email;
                $data_array[$i]['Location Details'] = $inquiry->customer_address;
                $data_array[$i]['Mode Selection'] = $inquiry->mode_selection;
                $data_array[$i]['Source of information'] = $inquiry->sourceOfInformation->title;
                $data_array[$i]['Query'] = $inquiry->queryDetails->title;
                $data_array[$i]['Complain'] = $inquiry->complain;
                $data_array[$i]['Agent Remarks'] = $inquiry->remarks;
                $data_array[$i]['Final Status'] = $inquiry->inquiryStatus->title;
                $data_array[$i]['Date'] = $inquiry->created_at;
                $i++;
            }
            return Excel::create('inquiry_'.time(), function($excel) use ($data_array) {
                $excel->sheet('orders', function($sheet) use ($data_array)
                {
                    $sheet->setOrientation('landscape');
                // Freeze first row
                    $sheet->freezeFirstRow();
                    $sheet->fromArray($data_array);
                });
            })->download('xls');
        }
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
        //
        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'calling_number' => 'required',
            'customer_name' => 'required|string',
            'company_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_address' => 'required|string',
            'query_id' => 'required|string|exists:queries,id,status,1',
            'source_of_information' => 'required|string|exists:source_of_informations,id,status,1',
            'complain' => 'required|string'
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        try {
            $inquiry = new Inquiry();
            $inquiry->calling_number = $request->calling_number;
            $inquiry->customer_name = $request->customer_name;
            $inquiry->company_name = $request->company_name;
            $inquiry->customer_alt_number = $request->customer_alt_number;
            $inquiry->customer_email = $request->customer_email;
            $inquiry->customer_address = $request->customer_address;
            $inquiry->mode_selection = "Inbound";
            //
            $inquiry->query_id = $request->query_id;
            $inquiry->source_of_information = $request->source_of_information;
            $inquiry->complain = $request->complain;
            $inquiry->remarks = $request->remarks;
            $inquiry->status = 1;
            $inquiry->created_by = auth()->user()->id;
            DB::beginTransaction();
            //dd($inquiry->toArray());
            $inquiry->save();
            DB::commit();
            Session::flash('message', "Inquiry submited successfully");
            return redirect()->back();
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Log::error($e->getMessage());
            // return $e->getMessage();
            return redirect()->back()->withErrors("Inquiry submited submission failed")->withInput();
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
        if(!auth()->user()->can('head_of_customer_support')){
            abort(403);
        }
        $validation = Validator::make($request->all(), [
            'calling_number' => 'required',
            'customer_name' => 'required|string',
            'company_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_address' => 'required|string',
            'query_id' => 'required|string|exists:queries,id,status,1',
            'source_of_information' => 'required|string|exists:source_of_informations,id,status,1',
            'complain' => 'required|string',
            'status' => 'required|string|exists:inquiry_statuses,id,status,1'
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        try {

            $inquiry = Inquiry::findOrFail($id);
            $inquiry->calling_number = $request->calling_number;
            $inquiry->customer_name = $request->customer_name;
            $inquiry->company_name = $request->company_name;
            $inquiry->customer_alt_number = $request->customer_alt_number;
            $inquiry->customer_email = $request->customer_email;
            $inquiry->customer_address = $request->customer_address;
            $inquiry->mode_selection = "Inbound";
            //
            $inquiry->query_id = $request->query_id;
            $inquiry->source_of_information = $request->source_of_information;
            $inquiry->complain = $request->complain;
            $inquiry->remarks = $request->remarks;
            $inquiry->status = $request->status;
            $inquiry->updated_by = auth()->user()->id;
            DB::beginTransaction();
            //dd($inquiry->toArray());
            $inquiry->save();
            DB::commit();
            Session::flash('message', "Inquiry updated successfully");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors("Inquiry updated submission failed")->withInput();
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
    }



    public function send_email(Request $request,$id){
        if(!auth()->user()->can('head_of_customer_support')){
            abort(403);
        }
        $inquiry = Inquiry::findOrFail($id);
        $this->validate($request,[
            'mail_groups' => 'required|array',
            'mail_groups.*' => 'required|integer|exists:mail_groups,id,status,1',
            'complain' => 'required|string'
        ]);
        try {
            $email_body = $request->extra_msg."\n\Complain : \n".$request->complain."\n\nRemarks :\n".$request->remarks;
            $mail_groups = $request->mail_groups;
            foreach ($mail_groups as $key => $value) {
                # code...
                $mail_group = MailGroup::whereId($value)->first();
                if($mail_group){
                    $cc_emails = explode(',',$mail_group->cc);
                    Mail::raw($email_body, function ($message) use ($mail_group,$cc_emails){
                    //
                        $message->to($mail_group->to);
                        $message->cc($cc_emails);
                    });
                }
            }
            DB::beginTransaction();
            $inquiry->status = 2 ;
            $inquiry->updated_by = auth()->user()->id;
            $inquiry->save();
            DB::commit();
            return redirect()->back()->with('message','E-mail send successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors('E-mail send failed')->withInput();
        }
    }

    public function mark_as_solved($id){
        if(!auth()->user()->can('head_of_customer_support')){
            abort(403);
        }
        $inquiry = Inquiry::where('status',1)->findOrFail($id);
        try {
            $inquiry->status = 2;
            $inquiry->updated_by = auth()->user()->id;
            DB::beginTransaction();
            $inquiry->save();
            DB::commit();
            return redirect()->back()->with('message','Inquiry update successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors('Inquiry update failed');
        }
    }
    public function get_caller_details(Request $request){
        if(!$request->calling_number){
            return [];
        }
        $caller_info = Inquiry::selectRaw("customer_name,customer_email,company_name,customer_address,customer_alt_number")->orderBy('id','DESC')->whereCallingNumber($request->calling_number)->first();
        return json_encode($caller_info);
    }
}
