<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Storetype;
use App\Store;
use App\Merchant;
use App\Charge;
use App\ProductCategory;
use Session;
use Redirect;
use Validator;
use Datatables;
use Entrust;
use Auth;

class StoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|merchantadmin|merchantsupport|hubmanager|salesteam|coo|kam');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        if(!Entrust::can('store-list')) { abort(403); }

        $req  = $request->all();
        $query = Store::select(array(
         'stores.id',
         'merchants.name AS merchant_name',
         'stores.store_id',
         'stores.store_password',
         'stores.store_url',
         'store_types.title AS store_type',
         'stores.status',
         ));

        $query->leftJoin('merchants', 'merchants.id', '=', 'stores.merchant_id');
        $query->leftJoin('store_types', 'store_types.id', '=', 'stores.store_type_id');

        ( $request->has('merchant_id') )      ? $query->where('stores.merchant_id', trim($request->merchant_id))      : null;
        ( $request->has('store_id') )         ? $query->where('stores.store_id', trim($request->store_id))            : null;
        ( $request->has('store_url') )        ? $query->where('stores.store_url', trim($request->store_url))          : null;
        ( $request->has('store_type_id') )    ? $query->where('stores.store_type_id', trim($request->store_type_id))  : null;
        ( $request->has('status') )           ? $query->where('stores.status', trim($request->status))                : null;

        if (Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('merchantsupport')) {
         $query->where('stores.merchant_id', '=', auth()->user()->reference_id);
     }

     $stores = $query->orderBy('stores.id', 'desc')->paginate(10);
     $merchants = Merchant::whereStatus(true)->orderBy('name', 'ASC')->lists('name', 'id')->toArray();
     $storeType = Storetype::whereStatus(true)->orderBy('title', 'ASC')->lists('title', 'id')->toArray();
     return view('stores.index', compact('stores', 'req', 'merchants', 'storeType'));
 }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Entrust::can('create-store')) { abort(403); }

        $storetypes = Storetype::whereStatus(true)->lists('title', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        return view('stores.insert', compact('storetypes', 'merchants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if (!Merchant::where('id', '=',$request->merchant_id)->exists()) {
          return Redirect::back()->withErrors('Merchant id invalid.')->withInput();
   // Merchant not found
      }
      if(!Entrust::can('create-store')) { abort(403); }

      $validation = Validator::make($request->all(), [
        'store_id' => 'required|unique:stores',
        'store_password' => 'required',
        'store_url' => 'required',
        'merchant_id' => 'required',
        'store_type_id' => 'required',
        'status' => 'required',
        'billing_address' => 'required',
        'account_synq_cod' => 'sometimes',
        'account_synq_dc' => 'sometimes',
        'vat_include' => 'sometimes',
        'vat_percentage' => 'sometimes',
        ]);

      if($validation->fails()) {
        return Redirect::back()->withErrors($validation)->withInput();
    }

    $store = new Store();
    $store->fill($request->except('merchant_end','step'));
    $store->created_by = auth()->user()->id;
    $store->updated_by = auth()->user()->id;

    if (!$request->has('account_synq_cod')) { $store->account_synq_cod = 0; }
    if (!$request->has('account_synq_dc')) { $store->account_synq_dc = 0; }
    if (!$request->has('vat_include')) { $store->vat_include = 0; }
    
    $store->save();

    Session::flash('message', "Store information saved successfully");

    if ($request->has('merchant_end')) {
      if($request->merchant_end == 'merchant'){
        return redirect('/merchant/'.$request->merchant_id.'/edit?step=3');
    }
    else{
        return redirect('/store/'.$store->id.'/edit?step=2');
    }
}

else{
 return redirect('/store/'.$store->id.'/edit?step=2');
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
        if(!Entrust::can('view-store')) { abort(403); }

        $store = Store::whereStatus(true)->findOrFail($id);
        // return $store->merchant->zone->city->state->country->name;
        // return $store->merchant->name;
        return view('stores.view', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(!Entrust::can('edit-store')) { abort(403); }

        $store = Store::where('id', '=', $id)->first();
        if (Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('merchantsupport')) {
            if($store->merchant_id != auth()->user()->reference_id){
                abort(403);
            }
        }

        $storetypes = Storetype::whereStatus(true)->lists('title', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        $req  = $request->all();
        $query = ProductCategory::select('*');

        ( $request->has('name') )               ? $query->where('name', 'like', trim($request->name)."%")                  : null;
        ( $request->has('category_type') )      ? $query->where('category_type', trim($request->category_type))            : null;
        ( $request->has('parent_category_id') ) ? $query->where('parent_category_id', trim($request->parent_category_id))  : null;
        ( $request->has('status') )             ? $query->where('status', trim($request->status))                          : null;

        $product_category = $query->orderBy('category_type', 'desc')->paginate(30);
        $parent_cat = ProductCategory::whereStatus(true)->where('category_type', 'parent')->lists('name', 'id')->toArray();
        $all_cat = ProductCategory::select('name')->get();

        $count = Charge::select()
        ->whereStatus(true)
        ->where('charge_model_id', '=', '1')
        ->where('store_id', '=', $id)
        ->count();
        if($count == 0){
            $cod = Charge::select()->whereStatus(true)->where('id', '=', '1')->orderBy('id', 'desc')->first();
        }else{
            $cod = Charge::select()->whereStatus(true)->where('store_id', '=', $id)->orderBy('id', 'desc')->first();
        }

        if($request->step){
            $step = $request->step;
        }else{
            $step = 1;
        }

        return view('stores.edit', compact('step', 'id', 'store', 'storetypes', 'merchants', 'req', 'product_category', 'parent_cat', 'all_cat', 'cod'));
    }

    private function processData($mainArray,$singleValue)
    {
//        echo "call me <br/>";
        $returnData = $singleValue;
        foreach($mainArray as $row)
        {
            if(!is_null($row->store_id) and ($row->charge_model_id == $singleValue->charge_model_id) and ($row->zone_genre_id == $singleValue->zone_genre_id))
            {
//                echo "match found 2 <br/>";
                // if($row->store_id == $id){
                    $returnData = $row;
                    break;
                // }
            }
        }
        
        return $returnData;
    }

    public function msort($array, $key, $sort_flags = SORT_REGULAR) {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
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
       // dd($request->all());
        if(!Entrust::can('edit-store')) { abort(403); }

        $validation = Validator::make($request->all(), [
            'store_id' => 'sometimes|unique:stores,store_id,'.$id,
            'store_password' => 'sometimes',
            'store_url' => 'sometimes',
            'merchant_id' => 'sometimes',
            'store_type_id' => 'sometimes',
            'status' => 'sometimes',
            'billing_address' => 'sometimes',
            'account_synq_cod' => 'sometimes',
            'account_synq_dc' => 'sometimes',
            'vat_include' => 'sometimes',
            'vat_percentage' => 'sometimes',
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $store = Store::findOrFail($id);
        $store->fill($request->except('step','merchant_end'));
        $store->created_by = auth()->user()->id;
        $store->updated_by = auth()->user()->id;

        if (!$request->has('account_synq_cod')) { $store->account_synq_cod = 0; }
        if (!$request->has('account_synq_dc')) { $store->account_synq_dc = 0; }
        if (!$request->has('vat_include')) { $store->vat_include = 0; }

        $store->save();

        if($request->step){
            if($request->step == 'complete'){
                // return $request->step;
                Session::flash('message', "Store information updated successfully");
                if ($request->has('merchant_end')) {
                  if($request->merchant_end == 'merchant'){
                    return redirect('/merchant/'.$request->merchant_id.'/edit?step=3');
                }
                else{
                    return redirect('/store');
                }
            }
            return redirect('/store');
        }else{
            $step = $request->step;
        }
    }else{
        $step = 1;
    }

    Session::flash('message', "Store information saved successfully");
    return redirect('/store/'.$id.'/edit?step='.$step);
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

    public function storeList(){

        if(!Entrust::can('view-store')) { abort(403); }

        $query = Store::select(array(
            'stores.id',
            'merchants.name AS merchant_name',
            'stores.store_id',
            'stores.store_password',
            'stores.store_url',
            'store_types.title AS store_type',
            'stores.status',
            ))
        ->leftJoin('merchants', 'merchants.id', '=', 'stores.merchant_id')
        ->leftJoin('store_types', 'store_types.id', '=', 'stores.store_type_id');

        if (Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('merchantsupport')) {
            $query->where('stores.merchant_id', '=', auth()->user()->reference_id);
        }

        $query->orderBy('stores.id', 'desc');

        $requesters = $query->get();

        return Datatables::of($requesters)
        ->remove_column('id')
        ->editColumn('status', '@if($status=="1")
            <span class="label label-success"> Active </span>
            @else
            <span class="label label-danger"> Inactive </span>
            @endif')
        ->add_column('action',
            '<div class="btn-group pull-right">
            <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                <i class="fa fa-angle-down"></i>
            </button>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="store/{{ $id }}/edit">
                        <i class="fa fa-pencil"></i> Update </a>
                    </li>
                </ul>
            </div>'
            )
        ->make();

    }
}
