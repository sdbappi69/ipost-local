<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Country;
use App\Order;
use App\Store;
use App\State;
use App\City;
use App\Zone;
use App\ProductCategory;
use App\PickingLocations;
use App\OrderProduct;
use App\PickingTimeSlot;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Entrust;

use App\SubOrder;

use App\Merchant;
use App\User;

use App\Status;

use Excel;

class OrderDraftController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|head_of_accounts|customerservice|salesteam|coo|saleshead|operationmanager|operationalhead');
    }

    public function draft(Request $request)
    {
        $query = Order::where('order_status', '=', '1');

        ($request->has('customer_mobile_no'))      ? $query->where('orders.delivery_msisdn', 'like', '%' . $request->customer_mobile_no)->orWhere('orders.delivery_alt_msisdn', 'like', '%' . $request->customer_mobile_no) : null;
        ($request->has('store_id'))  ? $query->where('orders.store_id',$request->store_id) : null;
        ($request->has('merchant_order_id'))  ? $query->where('orders.merchant_order_id','like','%'.$request->merchant_order_id) : null;

        ($request->has('search_date') )  ? $query->whereDate('orders.created_at','=',$request->search_date) : null;

        $orders = $query->orderBy('id', 'desc')->get();

        $stores = Store::select(DB::raw('CONCAT(stores.store_id, " - ", merchants.name) AS name'),'stores.id')
                        ->leftJoin('merchants','merchants.id','=','stores.merchant_id')
                        ->where('stores.status', 1)
                        ->where('merchants.status', 1)
                        ->lists('name', 'id')
                        ->toArray();

        return view('orders.draft',compact('orders', 'stores'));
    }
}
