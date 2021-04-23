<?php

namespace App\Http\Controllers\CustomerSupport;

use App\CustomerSupportModel\FeedBack;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Excel;
use Log;
use Session;
use Validator;
class FeedbackController extends Controller
{
    //
	function __construct()
	{
		$this->middleware('permission:manage_feedback');
	}
	public function index(Request $request){


		$query = FeedBack::with(['uniqueHead','reactionDetails','updatedBy','riderDetails'])->orderBy('id','DSC');
        //Filter
		if($request->search_by_data){
			$query->where(function($q) use ($request) {
				$q->where('customer_name','like','%'.$request->search_by_data.'%')
				->orWhere('customer_number','like','%'.$request->search_by_data.'%')
				->orWhere('customer_address','like','%'.$request->search_by_data.'%')
				->orWhere('company_name','like','%'.$request->search_by_data.'%');
			});
		}
		//dd($request->status_s);
		if($request->status_s){
			$query->where('status',$request->status_s);
		}
		else{
			$query->where('status',0);
		}
		($request->reaction_s ? $query->whereIn('reaction',$request->reaction_s) : null);
		($request->unique_head_s ? $query->whereIn('unique_head',$request->unique_head_s) : null);
		($request->unique_suborder_id_s ? $query->whereUniqueSubOrderId($request->unique_suborder_id_s) : null);
		($request->type_s ? $query->whereIn('type',$request->type_s) : null);
		($request->rider_s ? $query->whereIn('rider',$request->rider_s) : null);
		if(auth()->user()->can('head_of_customer_support')){
			($request->updated_by_s ? $query->whereUpdatedBy($request->updated_by_s) : null);
		}
		else{
			if($request->status_s and $request->status_s == 1){
				$query->whereUpdatedBy(auth()->user()->id);
			}
		}
		(($request->order_date_from and $request->order_date_to) ? $query->whereBetween('order_created_at',[$request->order_date_from.' 00:00:01',$request->order_date_to.' 23:59:59']) : null);
		(($request->date_from and $request->date_to) ? $query->whereBetween('created_at',[$request->date_from.' 00:00:01',$request->date_to.' 23:59:59']) : null);
		if($request->sub_order_unique_id){
			$query->whereHas('sub_order',function($q) use ($request){
				$q->where('unique_suborder_id',$request->sub_order_unique_id);
			});
		}
        // End Filter
		$feedbacks = $query->paginate(20);
		//dd($feedbacks->toArray());
		$unique_heads = __get_unique_heads_dropdown();
		$reactions = __get_reaction_dropdown();
		$users = __get_feedback_updated_user_dropdown();
		$statues = __get_feedback_status_dropdown();
		$ratings = __get_feedback_rating_dropdown();
		$riders = __get_rider_dropdown();
		return view('customer-support.feedback.index',compact('feedbacks','unique_heads','reactions','users','statues','ratings','riders'));
	}

	public function update(Request $request,$id){
		$validation = Validator::make($request->all(), [
			'unique_head' => 'required|string|exists:unique_heads,id,status,1',
			'reaction' => 'required|string|exists:reactions,id,status,1',
			'rating' => 'required|integer|min:1|max:5'
		]);
		if ($validation->fails()) {
			return redirect()->back()->withErrors($validation)->withInput();
		}
		try {
			$feedback = FeedBack::where('status',0)->findOrFail($id);
			$feedback->unique_head = $request->unique_head;
			$feedback->reaction = $request->reaction;
			$feedback->rating = $request->rating;
			$feedback->status = 1;
			$feedback->remarks = $request->remarks;
			$feedback->suggestion = $request->suggestion;
			$feedback->updated_by = auth()->user()->id;
			$feedback->call_date = date('Y-m-d H:i:s');
			DB::beginTransaction();
            //dd($feedback->toArray());
			$feedback->save();
			DB::commit();
			Session::flash('message', "Feedback updated successfully");
			return redirect('feedback');
		} catch (\Exception $e) {
			DB::rollback();
			Log::error($e->getMessage());
			return redirect()->back()->withErrors("Feedback updated failed")->withInput();
		}
	}

	public function export_xls(Request $request){

		$query = FeedBack::with(['uniqueHead','reactionDetails','updatedBy'])->orderBy('id','DSC');
        //Filter
		if($request->search_by_data){
			$query->where(function($q) use ($request) {
				$q->where('customer_name','like','%'.$request->search_by_data.'%')
				->orWhere('customer_number','like','%'.$request->search_by_data.'%')
				->orWhere('customer_address','like','%'.$request->search_by_data.'%')
				->orWhere('company_name','like','%'.$request->search_by_data.'%');
			});
		}
		//dd($request->status_s);
		if($request->status_s){
			$query->where('status',$request->status_s);
		}
		else{
			$query->where('status',0);
		}
		($request->reaction_s ? $query->whereIn('reaction',$request->reaction_s) : null);
		($request->unique_head_s ? $query->whereIn('unique_head',$request->unique_head_s) : null);
		($request->unique_suborder_id_s ? $query->whereUniqueSubOrderId($request->unique_suborder_id_s) : null);
		($request->type_s ? $query->whereIn('type',$request->type_s) : null);
		($request->rider_s ? $query->whereIn('rider',$request->rider_s) : null);
		if(auth()->user()->can('head_of_customer_support')){
			($request->updated_by_s ? $query->whereUpdatedBy($request->updated_by_s) : null);
		}
		else{
			$query->whereUpdatedBy(auth()->user()->id);
			//dd($query->get()->toArray());
		}
		(($request->order_date_from and $request->order_date_to) ? $query->whereBetween('order_created_at',[$request->order_date_from.' 00:00:01',$request->order_date_to.' 23:59:59']) : null);
		(($request->date_from and $request->date_to) ? $query->whereBetween('created_at',[$request->date_from.' 00:00:01',$request->date_to.' 23:59:59']) : null);
		if($request->sub_order_unique_id){
			$query->whereHas('sub_order',function($q) use ($request){
				$q->where('unique_suborder_id',$request->sub_order_unique_id);
			});
		}
        // End Filter
		$feedbacks = $query->paginate(20);
		$feedbacks = $query->get();
		if($feedbacks){
			$data_array = null;
			$i = 0;
			foreach ($feedbacks as $key => $feedback) {
				//dd($feedback->toArray());
            # code...
				$data_array[$i]['Agent'] = ($feedback->updatedBy ? $feedback->updatedBy->name : null);
				$data_array[$i]['Merchant'] = $feedback->company_name;
				$data_array[$i]['Hub'] = $feedback->hub;
				$data_array[$i]['Product'] = $feedback->product;
				$data_array[$i]['Order Create'] = $feedback->order_created_at;
				$data_array[$i]['Order ID'] = $feedback->unique_suborder_id;
				$data_array[$i]['Customer Name'] = $feedback->customer_name;
				$data_array[$i]['Address'] = $feedback->customer_address;
				$data_array[$i]['Contact Number'] = $feedback->customer_number;
				$data_array[$i]['Amount To Collect'] = $feedback->amount_to_collect;
				$data_array[$i]['Amount Collected'] = $feedback->amount_collected;
				$data_array[$i]['Mode Selection'] = $feedback->mode_selection;
				$data_array[$i]['Reaction'] = ($feedback->reactionDetails ? $feedback->reactionDetails->title : null);
				$data_array[$i]['Unique Head'] = ($feedback->uniqueHead ? $feedback->uniqueHead->title : null);
				$data_array[$i]['Remarks'] = $feedback->remarks;
				$data_array[$i]['Suggestion'] = $feedback->suggestion;
				$data_array[$i]['Rider'] = $feedback->rider;
				$data_array[$i]['Type'] = $feedback->type;
				$data_array[$i]['Final Status'] = $feedback->status;
				$data_array[$i]['Call Date'] = $feedback->call_date;
				$data_array[$i]['Date'] = $feedback->created_at;
				$i++;
			}
			//dd($data_array);
			return Excel::create('feedback_'.time(), function($excel) use ($data_array) {
				$excel->sheet('feedback', function($sheet) use ($data_array)
				{
					$sheet->setOrientation('landscape');
                // Freeze first row
					$sheet->freezeFirstRow();
					$sheet->fromArray($data_array);
				});
			})->download('xls');
		}
	}
}
