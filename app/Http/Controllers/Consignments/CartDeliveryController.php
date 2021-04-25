<?php

namespace App\Http\Controllers\Consignments;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Session;
use Redirect;
use App\SubOrder;

class CartDeliveryController extends Controller
{
    public function add_delivery_cart(Request $request) {

    	$validation = Validator::make($request->all(), [
	      'unique_suborder_id' => 'required'
	     ]);

		if($validation->fails()) {
			Session::flash('message', "Sub-Order Id required");
		    return Redirect::back()->withErrors($validation)->withInput();
		}

		$sub_order = $this->check_sub_order($request->unique_suborder_id);

		if(count($sub_order) == 1){
			if (Session::has('delivery_cart')) {

			    $delivery_cart = Session::get('delivery_cart');
			    $exist = 0;
			    foreach ($delivery_cart as $cart) {
			    	if($cart == $sub_order->unique_suborder_id){
			    		$exist = 1;
			    		break;
			    	}
			    }
			    if($exist == 0){
			    	Session::push('delivery_cart', $sub_order->unique_suborder_id);
			    	Session::flash('message', "Added to cart");
			    }else{
			    	Session::flash('message', "Already in cart");
			    }

			}else{

				Session::push('delivery_cart', $sub_order->unique_suborder_id);
				Session::flash('message', "Added to cart");

			}
		}else{
			Session::flash('message', "Invalid Sub-Order Id");
		}

		return Redirect::back();

	}

	public function add_bulk_delivery_cart(Request $request) {

    	$validation = Validator::make($request->all(), [
	      'unique_suborder_ids' => 'required'
	     ]);

		if($validation->fails()) {
			Session::flash('message', "Invalid Request");
		    return Redirect::back()->withErrors($validation)->withInput();
		}

		$msg = array();

		foreach ($request->unique_suborder_ids as $unique_suborder_id) {
			$sub_order = $this->check_sub_order($unique_suborder_id);

			if($sub_order){
				if (Session::has('delivery_cart')) {

				    $delivery_cart = Session::get('delivery_cart');
				    $exist = 0;
				    foreach ($delivery_cart as $cart) {
				    	if($cart == $sub_order->unique_suborder_id){
				    		$exist = 1;
				    		break;
				    	}
				    }
				    if($exist == 0){
				    	Session::push('delivery_cart', $sub_order->unique_suborder_id);
				    	$msg[] = "$unique_suborder_id: Added to cart";
				    }else{
				    	$msg[] = "$unique_suborder_id: Already in cart";
				    }

				}else{

					Session::push('delivery_cart', $sub_order->unique_suborder_id);
					$msg[] = "$unique_suborder_id: Added to cart";

				}
			}else{
				$msg[] = "$unique_suborder_id: Invalid Sub-Order Id";
			}
		}

		$message = '';
		foreach ($msg as $m) {
			$message = $message.$m.'\n';
		}

		Session::flash('message', $message);
		return Redirect::back();

	}

	public function remove_delivery_cart($unique_suborder_id) {

		if(count(Session::get('delivery_cart')) > 0){

			$delivery_cart = Session::get('delivery_cart');
			$new_cart = array();

			foreach ($delivery_cart as $cart) {
				if($cart != $unique_suborder_id){
					$new_cart[] = $cart;
				}
			}

			if(count($new_cart) == 0){
				Session::forget('delivery_cart');
				Session::flash('message', "Cart removed");
			}else{
				Session::forget('delivery_cart');
				Session::put('delivery_cart', $new_cart);
				Session::flash('message', "Removed from cart");
			}

		}else{
			Session::flash('message', "Cart is empty");
		}

		return Redirect::back();
	}

	public function check_sub_order($unique_suborder_id){
		$sub_order = SubOrder::select('unique_suborder_id')
								->where('status', 1)
								->whereIn('sub_order_status', [26, 34])
								->whereRaw("IF (`sub_orders`.`post_delivery_return` = 0, `sub_orders`.`destination_hub_id`,`sub_orders`.`source_hub_id`) = " . auth()->user()->reference_id)
								->where('return', '=', '0')
								->where('unique_suborder_id', '=', $unique_suborder_id)
								->first();
		return $sub_order;
	}
}
