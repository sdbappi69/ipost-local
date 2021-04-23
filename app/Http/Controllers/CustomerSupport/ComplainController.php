<?php

namespace App\Http\Controllers\CustomerSupport;
use App\CustomerSupportModel\Complain;
use App\CustomerSupportModel\MailGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\SubOrder;
use DB;
use Excel;
use Illuminate\Http\Request;
use Log;
use Session;
use Validator;
use Mail;
class ComplainController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage_complain');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = Complain::with(['sourceOfInformation','queryDetails','sub_order'])->orderBy('id','DSC');
        //Filter

        if($request->search_by_data){
            $query->where(function($q) use ($request) {
                $q->where('customer_name','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_alt_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_email','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_address','like','%'.$request->search_by_data.'%')
                ->orWhere('company_name','like','%'.$request->search_by_data.'%');
            });
        }
        ($request->query_id_s ? $query->whereIn('query_id',$request->query_id_s) : null);
        ($request->source_of_information_s ? $query->whereIn('source_of_information',$request->source_of_information_s) : null);
        ($request->sub_order_id_s ? $query->whereSubOrderId($request->sub_order_id_s) : null);
        ($request->status_s ? $query->whereIn('status',$request->status_s) : null);
        if(auth()->user()->can('head_of_customer_support')){
            ($request->created_by_s ? $query->whereCreatedBy($request->created_by_s) : null);
        }
        else{
            $query->whereCreatedBy(auth()->user()->id);
        }

        (($request->date_from and $request->date_to) ? $query->whereBetween('created_at',[$request->date_from.' 00:00:01',$request->date_to.' 23:59:59']) : null);

        if($request->sub_order_unique_id){
            $query->whereHas('sub_order',function($q) use ($request){
                $q->where('unique_suborder_id',$request->sub_order_unique_id);
            });
        }
        // End Filter
        $complains = $query->paginate(20);
        $source_of_informations = __get_source_of_information_dropdown();
        $querys = __get_query_dropdown();
        $users = __get_complain_submitted_user_dropdown();
        $statues = __get_complain_status_dropdown();
        $mail_groups = __get_mail_gropus_dropdown();
        return view('customer-support.complain.index',compact('complains','querys','source_of_informations','users','statues','mail_groups'));
    }

    public function export_xls(Request $request){
        $query = Complain::with(['sourceOfInformation','queryDetails','sub_order'])->orderBy('id','DSC');
        //Filter
        if($request->search_by_data){
            $query->where(function($q) use ($request) {
                $q->where('customer_name','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_alt_number','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_email','like','%'.$request->search_by_data.'%')
                ->orWhere('customer_address','like','%'.$request->search_by_data.'%')
                ->orWhere('company_name','like','%'.$request->search_by_data.'%');
            });
        }
        ($request->query_id_s ? $query->whereIn('query_id',$request->query_id_s) : null);
        ($request->source_of_information_s ? $query->whereIn('source_of_information',$request->source_of_information_s) : null);
        ($request->sub_order_id_s ? $query->whereSubOrderId($request->sub_order_id_s) : null);
        ($request->status_s ? $query->whereIn('status',$request->status_s) : null);
        if(auth()->user()->can('head_of_customer_support')){
            ($request->created_by_s ? $query->whereCreatedBy($request->created_by_s) : null);
        }
        else{
            $query->whereCreatedBy(auth()->user()->id);
        }
        (($request->date_from and $request->date_to) ? $query->whereBetween('created_at',[$request->date_from.' 00:00:01',$request->date_to.' 23:59:59']) : null);
        if($request->sub_order_unique_id){
            $query->whereHas('sub_order',function($q) use ($request){
                $q->where('unique_suborder_id',$request->sub_order_unique_id);
            });
        }
        // End Filter
        $complains = $query->get();
        if($complains){
            $data_array = null;
            $i = 0;
            foreach ($complains as $key => $complain) {
            # code...
                $data_array[$i]['Agent'] = $complain->createdBy->name;
                $data_array[$i]['Order ID'] = $complain->sub_order->unique_suborder_id;
                $data_array[$i]['Calling Number'] = $complain->customer_number;
                $data_array[$i]['Name'] = $complain->customer_name;
                $data_array[$i]['Link / Company Name'] = $complain->company_name;
                $data_array[$i]['Alt. Number'] = $complain->company_alt_number;
                $data_array[$i]['E-mail'] = $complain->customer_email;
                $data_array[$i]['Location Details'] = $complain->customer_address;
                $data_array[$i]['Mode Selection'] = $complain->mode_selection;
                $data_array[$i]['Source of information'] = $complain->sourceOfInformation->title;
                $data_array[$i]['Query'] = $complain->queryDetails->title;
                $data_array[$i]['Complain'] = $complain->complain;
                $data_array[$i]['Agent Remarks'] = $complain->remarks;
                $data_array[$i]['Final Status'] = $complain->status;
                $data_array[$i]['Date'] = $complain->created_at;
                $i++;
            }
            return Excel::create('complain_'.time(), function($excel) use ($data_array) {
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
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'sub_order_id' => 'required|integer|exists:sub_orders,id',
            'query_id' => 'required|string|exists:queries,id,status,1',
            'source_of_information' => 'required|string|exists:source_of_informations,id,status,1',
            'complain' => 'required|string'
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        try {
            $sub_order = SubOrder::with(['order.store.merchant','order.delivery_zone','order.delivery_city','order.delivery_state','order.delivery_country'])->findOrFail($request->sub_order_id);
            //dd($sub_order->toArray());
            //
            $complain = new Complain();
            $complain->sub_order_id = $request->sub_order_id;
            $complain->unique_suborder_id = $sub_order->unique_suborder_id;
            $complain->order_id = $sub_order->order->id;
            $complain->customer_name = $sub_order->order->delivery_name;
            $complain->company_name = $sub_order->order->store->store_id." - ".$sub_order->order->store->merchant->name;
            $complain->customer_number = $sub_order->order->delivery_msisdn;
            $complain->customer_alt_number = $sub_order->order->delivery_alt_msisdn;
            $complain->customer_email = $sub_order->order->delivery_email;
            $complain->customer_address = $this->__create_address_by_concat_all($sub_order);
            $complain->mode_selection = "Inbound";
            //
            $complain->query_id = $request->query_id;
            $complain->source_of_information = $request->source_of_information;
            $complain->complain = $request->complain;
            $complain->remarks = $request->remarks;
            $complain->status = 0;
            $complain->created_by = auth()->user()->id;
            DB::beginTransaction();
            //dd($complain->toArray());
            $complain->save();
            DB::commit();
            Session::flash('message', "Complain submited successfully");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors("Complain submited submission failed")->withInput();
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
            'query_id' => 'required|string|exists:queries,id,status,1',
            'source_of_information' => 'required|string|exists:source_of_informations,id,status,1',
            'complain' => 'required|string'
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        try {
            $complain = Complain::findOrFail($id);
            $complain->query_id = $request->query_id;
            $complain->source_of_information = $request->source_of_information;
            $complain->complain = $request->complain;
            $complain->remarks = $request->remarks;
            $complain->updated_by = auth()->user()->id;
            DB::beginTransaction();
            //dd($complain->toArray());
            $complain->save();
            DB::commit();
            Session::flash('message', "Complain updated successfully");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors("Complain updated failed")->withInput();
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

    public function __create_address_by_concat_all($sub_order){
        $address = null;
        $address = $sub_order->order->delivery_address1.", ".$sub_order->order->delivery_zone->name.", ".$sub_order->order->delivery_city->name.", ".$sub_order->order->delivery_state->name;
        return $address;
    }

    public function send_email(Request $request,$id){
        if(!auth()->user()->can('head_of_customer_support')){
            abort(403);
        }
        $complain = Complain::findOrFail($id);
        $this->validate($request,[
            'mail_groups' => 'required|array',
            'mail_groups.*' => 'required|integer|exists:mail_groups,id,status,1',
            'complain' => 'required|string'
        ]);
        try {
            $email_body = $request->extra_msg."\n\nComplain : \n".$request->complain."\n\nRemarks :\n".$request->remarks;
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
            $complain->status = 1 ;
            $complain->updated_by = auth()->user()->id;
            $complain->save();
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
        $complain = Complain::where('status',1)->findOrFail($id);
        try {
            $complain->status = 2;
            $complain->updated_by = auth()->user()->id;
            DB::beginTransaction();
            $complain->save();
            DB::commit();
            return redirect()->back()->with('message','Complain update successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors('Complain update failed');
        }
    }
}
