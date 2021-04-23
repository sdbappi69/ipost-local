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
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|merchantadmin|merchantsupport|hubmanager|salesteam');
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
        ]);

      if($validation->fails()) {
        return Redirect::back()->withErrors($validation)->withInput();
    }

    $store = new Store();
    $store->fill($request->except('merchant_end','step'));
    $store->created_by = auth()->user()->id;
    $store->updated_by = auth()->user()->id;
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

        // $charges = Charge::select(array(
        //             'charges.id',
        //             'charges.store_id',
        //             'charges.percentage_range_start',
        //             'charges.percentage_range_end',
        //             'charges.percentage_value',
        //             'charges.additional_range_per_slot',
        //             'charges.additional_charge_per_slot',
        //             'charges.additional_charge_type',
        //             'charges.fixed_charge',
        //             'charges.product_category_id',

        //             'charge_models.id AS charge_model_id',
        //             'charge_models.title AS charge_model_title',
        //             'charge_models.description AS charge_model_description',
        //             'charge_models.unit AS charge_model_unit',

        //             'zone_genres.id AS zone_genre_id',
        //             'zone_genres.title AS zone_genre_title',
        //             'zone_genres.description AS zone_genre_description',
        //             ))
        //         ->leftJoin('charge_models', 'charge_models.id', '=', 'charges.charge_model_id')
        //         ->leftJoin('zone_genres', 'zone_genres.id', '=', 'charges.zone_genre_id')
        //         ->where('charges.product_category_id', '=', 5)
        //                         // ->where('charges.store_id', '=', $id)
        //         ->where('charges.status', '=', '1')
        //         ->where('charge_models.status', '=', '1')
        //         ->where('zone_genres.status', '=', '1')
        //         ->orderBy('charges.id', 'desc')
        //         ->get();

        //     // $resultArray = json_decode(json_encode($charges), true);
        //     // print_r($resultArray); exit;

        //     $charge = array();
        //     $defined_charge_model_ids = array();
        //     $defined_zone_genre_ids = array();

        //     foreach ($charges as $row5) {

        //         if($row5->store_id != null){
        //             $charge[] = array(
        //                     'id' => $row5->id,
        //                     'store_id' => $row5->store_id,
        //                     'percentage_range_start' => $row5->percentage_range_start,
        //                     'percentage_range_end' => $row5->percentage_range_end,
        //                     'percentage_value' => $row5->percentage_value,
        //                     'additional_range_per_slot' => $row5->additional_range_per_slot,
        //                     'additional_charge_per_slot' => $row5->additional_charge_per_slot,
        //                     'additional_charge_type' => $row5->additional_charge_type,
        //                     'fixed_charge' => $row5->fixed_charge,
        //                     'charge_model_id' => $row5->charge_model_id,
        //                     'charge_model_title' => $row5->charge_model_title,
        //                     'charge_model_description' => $row5->charge_model_description,
        //                     'charge_model_unit' => $row5->charge_model_unit,
        //                     'zone_genre_id' => $row5->zone_genre_id,
        //                     'zone_genre_title' => $row5->zone_genre_title,
        //                     'zone_genre_description' => $row5->zone_genre_description,
        //                 );

        //             $defined_charge_model_ids[] = $row5->charge_model_id;
        //             $defined_zone_genre_ids[] = $row5->zone_genre_id;
        //         }
        //     }

        //     foreach ($charges as $row5) {

        //         if(count($defined_charge_model_ids) > 0){
        //             for($i=0;$i<sizeof($defined_charge_model_ids);$i++)
        //             {
        //                 if($defined_charge_model_ids[$i] == $row5->charge_model_id and $defined_zone_genre_ids[$i] == $row5->zone_genre_id ){
        //                 }
        //                 else
        //                 {
        //                     $charge[] = array(
        //                         'id' => $row5->id,
        //                         'store_id' => $row5->store_id,
        //                         'percentage_range_start' => $row5->percentage_range_start,
        //                         'percentage_range_end' => $row5->percentage_range_end,
        //                         'percentage_value' => $row5->percentage_value,
        //                         'additional_range_per_slot' => $row5->additional_range_per_slot,
        //                         'additional_charge_per_slot' => $row5->additional_charge_per_slot,
        //                         'additional_charge_type' => $row5->additional_charge_type,
        //                         'fixed_charge' => $row5->fixed_charge,
        //                         'charge_model_id' => $row5->charge_model_id,
        //                         'charge_model_title' => $row5->charge_model_title,
        //                         'charge_model_description' => $row5->charge_model_description,
        //                         'charge_model_unit' => $row5->charge_model_unit,
        //                         'zone_genre_id' => $row5->zone_genre_id,
        //                         'zone_genre_title' => $row5->zone_genre_title,
        //                         'zone_genre_description' => $row5->zone_genre_description,
        //                     );
        //                 }
        //             }
        //         }else{
        //             $charge[] = array(
        //                         'id' => $row5->id,
        //                         'store_id' => $row5->store_id,
        //                         'percentage_range_start' => $row5->percentage_range_start,
        //                         'percentage_range_end' => $row5->percentage_range_end,
        //                         'percentage_value' => $row5->percentage_value,
        //                         'additional_range_per_slot' => $row5->additional_range_per_slot,
        //                         'additional_charge_per_slot' => $row5->additional_charge_per_slot,
        //                         'additional_charge_type' => $row5->additional_charge_type,
        //                         'fixed_charge' => $row5->fixed_charge,
        //                         'charge_model_id' => $row5->charge_model_id,
        //                         'charge_model_title' => $row5->charge_model_title,
        //                         'charge_model_description' => $row5->charge_model_description,
        //                         'charge_model_unit' => $row5->charge_model_unit,
        //                         'zone_genre_id' => $row5->zone_genre_id,
        //                         'zone_genre_title' => $row5->zone_genre_title,
        //                         'zone_genre_description' => $row5->zone_genre_description,
        //                     );
        //         }
        //     }

        //     echo '<pre>';
        //     print_r($charge);
        //     echo '</pre>';
        //     // echo '<pre>';
        //     // print_r($defined_zone_genre_ids);
        //     // echo '</pre>';
        //     // echo '<pre>';
        //     // print_r($defined_product_category_ids);
        //     // echo '</pre>';
        //     exit;


        $store = Store::where('id', '=', $id)->first();
        if (Auth::user()->hasRole('merchantadmin')||Auth::user()->hasRole('merchantsupport')) {
            if($store->merchant_id != auth()->user()->reference_id){
                abort(403);
            }
        }

        $storetypes = Storetype::whereStatus(true)->lists('title', 'id')->toArray();
        $merchants = Merchant::whereStatus(true)->lists('name', 'id')->toArray();

        // $data = ProductCategory::with('sub_cats.sub_cat_charge')
        //                 ->where('id', '<=', '6')
        //                 ->whereHas('sub_cats.sub_cat_charge', function($q){
        //                     $q->where('store_id', '=', null);
        //                 })
        //                 ->get();

        // COD
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

        // dd($cod);

        $categories = ProductCategory::select(array(
            'id', 'name',
            ))
        ->whereStatus(true)
        ->where('category_type', '=', 'parent')
        ->get();

        $data = array();

        foreach ($categories as $row) {

            $allcategories = array();

            $count_subcategory = ProductCategory::select()
            ->whereStatus(true)
            ->where('parent_category_id', '=', $row->id)
            ->count();

            if($count_subcategory == 0){
                $allcategories[] = array(
                    'id' => $row->id,
                    'name' => $row->name,
                    );;
            }else{
                $subcategories = ProductCategory::select(array(
                    'id', 'name',
                    ))
                ->whereStatus(true)
                ->where('parent_category_id', '=', $row->id)
                ->get();

                foreach ($subcategories as $row2) {
                 $allcategories[] = array(
                    'id' => $row2->id,
                    'name' => $row2->name,
                    );;
             }
         }

         $subcategory = array();

         foreach ($allcategories as $row3) {
            $count = Charge::select()
            ->whereStatus(true)
            ->where('product_category_id', '=', $row3['id'])
            ->where('store_id', '=', $id)
            ->count();

            if($count == 0){
                // return 0;
                $charges = Charge::select(array(
                    'charges.id',
                    'charges.store_id',
                    'charges.percentage_range_start',
                    'charges.percentage_range_end',
                    'charges.percentage_value',
                    'charges.additional_range_per_slot',
                    'charges.additional_charge_per_slot',
                    'charges.additional_charge_type',
                    'charges.fixed_charge',

                    'charge_models.id AS charge_model_id',
                    'charge_models.title AS charge_model_title',
                    'charge_models.description AS charge_model_description',
                    'charge_models.unit AS charge_model_unit',

                    'zone_genres.id AS zone_genre_id',
                    'zone_genres.title AS zone_genre_title',
                    'zone_genres.description AS zone_genre_description',
                    ))
                ->leftJoin('charge_models', 'charge_models.id', '=', 'charges.charge_model_id')
                ->leftJoin('zone_genres', 'zone_genres.id', '=', 'charges.zone_genre_id')
                ->where('charges.product_category_id', '=', $row3['id'])
                ->where('charges.store_id', '=', null)
                ->where('charges.status', '=', '1')
                ->where('charge_models.status', '=', '1')
                ->where('zone_genres.status', '=', '1')
                ->get();
                // $charges = $charges->sortByDesc(['zone_genre_title']);
            }else{
                // return 1;
                $charges = Charge::select(array(
                    'charges.id',
                    'charges.store_id',
                    'charges.percentage_range_start',
                    'charges.percentage_range_end',
                    'charges.percentage_value',
                    'charges.additional_range_per_slot',
                    'charges.additional_charge_per_slot',
                    'charges.additional_charge_type',
                    'charges.fixed_charge',

                    'charge_models.id AS charge_model_id',
                    'charge_models.title AS charge_model_title',
                    'charge_models.description AS charge_model_description',
                    'charge_models.unit AS charge_model_unit',

                    'zone_genres.id AS zone_genre_id',
                    'zone_genres.title AS zone_genre_title',
                    'zone_genres.description AS zone_genre_description',
                    ))
                ->leftJoin('charge_models', 'charge_models.id', '=', 'charges.charge_model_id')
                ->leftJoin('zone_genres', 'zone_genres.id', '=', 'charges.zone_genre_id')
                ->where('charges.product_category_id', '=', $row3['id'])
                // ->whereIn('charges.store_id', [$id, ''])
                // ->orWhere('charges.store_id', '=', null)
                ->where('charges.status', '=', '1')
                ->where('charge_models.status', '=', '1')
                ->where('zone_genres.status', '=', '1')
                ->orderBy('charges.id', 'desc')
                ->get();

                // $charges = $charges->unique(['zone_genre_id']);
                // $charges = $charges->sortByDesc(['zone_genre_title']);
            }

            $charge = array();

            foreach($charges as $cg)
            {
                $flag = 0;
                //           echo "<br/>".$row->id . "=>".$row->charge_model_id." ".$row->zone_genre_id."<br/>";
               $return = $this->processData($charges,$cg);
                //            echo $return->id . "=>".$return->store_id." ".$return->charge_model_id." ".$return->zone_genre_id."<br/>";
                foreach($charge as $final){

                   if($final->store_id == $return->store_id and ($final->charge_model_id == $return->charge_model_id) and ($final->zone_genre_id == $return->zone_genre_id))
                   {
                        //                   echo "match found <br/>";
                       $flag = 1;
                       break;
                   }
                }
               
                if(!$flag){

                    $charge[] = $return;
                       
                }
               
            }

            
            
            // $defined_charge_model_ids = array();
            // $defined_zone_genre_ids = array();

            // foreach ($charges as $row5) {

            //     if($row5->store_id != null){
            //         if($row5->store_id == $id){

            //             $charge[] = array(
            //                 'id' => $row5->id,
            //                 'store_id' => $row5->store_id,
            //                 'percentage_range_start' => $row5->percentage_range_start,
            //                 'percentage_range_end' => $row5->percentage_range_end,
            //                 'percentage_value' => $row5->percentage_value,
            //                 'additional_range_per_slot' => $row5->additional_range_per_slot,
            //                 'additional_charge_per_slot' => $row5->additional_charge_per_slot,
            //                 'additional_charge_type' => $row5->additional_charge_type,
            //                 'fixed_charge' => $row5->fixed_charge,
            //                 'charge_model_id' => $row5->charge_model_id,
            //                 'charge_model_title' => $row5->charge_model_title,
            //                 'charge_model_description' => $row5->charge_model_description,
            //                 'charge_model_unit' => $row5->charge_model_unit,
            //                 'zone_genre_id' => $row5->zone_genre_id,
            //                 'zone_genre_title' => $row5->zone_genre_title,
            //                 'zone_genre_description' => $row5->zone_genre_description,
            //             );

            //             $defined_charge_model_ids[] = $row5->charge_model_id;
            //             $defined_zone_genre_ids[] = $row5->zone_genre_id;

            //         }
            //     }
            // }

            // foreach ($charges as $row5) {

            //     if(count($defined_charge_model_ids) > 0){

            //         for($i=0;$i<sizeof($defined_charge_model_ids);$i++)
            //         {
            //             if($defined_charge_model_ids[$i] == $row5->charge_model_id and $defined_zone_genre_ids[$i] == $row5->zone_genre_id ){
            //             }
            //             else
            //             {
            //                 if($row5->store_id == null){
            //                     $charge[] = array(
            //                         'id' => $row5->id,
            //                         'store_id' => $row5->store_id,
            //                         'percentage_range_start' => $row5->percentage_range_start,
            //                         'percentage_range_end' => $row5->percentage_range_end,
            //                         'percentage_value' => $row5->percentage_value,
            //                         'additional_range_per_slot' => $row5->additional_range_per_slot,
            //                         'additional_charge_per_slot' => $row5->additional_charge_per_slot,
            //                         'additional_charge_type' => $row5->additional_charge_type,
            //                         'fixed_charge' => $row5->fixed_charge,
            //                         'charge_model_id' => $row5->charge_model_id,
            //                         'charge_model_title' => $row5->charge_model_title,
            //                         'charge_model_description' => $row5->charge_model_description,
            //                         'charge_model_unit' => $row5->charge_model_unit,
            //                         'zone_genre_id' => $row5->zone_genre_id,
            //                         'zone_genre_title' => $row5->zone_genre_title,
            //                         'zone_genre_description' => $row5->zone_genre_description,
            //                     );
            //                 }
            //             }
            //         }

            //     }
            //     // else{

            //     //     if($row5->store_id == null){
            //     //         $charge[] = array(
            //     //                     'id' => $row5->id,
            //     //                     'store_id' => $row5->store_id,
            //     //                     'percentage_range_start' => $row5->percentage_range_start,
            //     //                     'percentage_range_end' => $row5->percentage_range_end,
            //     //                     'percentage_value' => $row5->percentage_value,
            //     //                     'additional_range_per_slot' => $row5->additional_range_per_slot,
            //     //                     'additional_charge_per_slot' => $row5->additional_charge_per_slot,
            //     //                     'additional_charge_type' => $row5->additional_charge_type,
            //     //                     'fixed_charge' => $row5->fixed_charge,
            //     //                     'charge_model_id' => $row5->charge_model_id,
            //     //                     'charge_model_title' => $row5->charge_model_title,
            //     //                     'charge_model_description' => $row5->charge_model_description,
            //     //                     'charge_model_unit' => $row5->charge_model_unit,
            //     //                     'zone_genre_id' => $row5->zone_genre_id,
            //     //                     'zone_genre_title' => $row5->zone_genre_title,
            //     //                     'zone_genre_description' => $row5->zone_genre_description,
            //     //                 );
            //     //     }

            //     // }

            // }

            $sortedcharge = $this->msort($charge, array('zone_genre_title', 'charge_model_title'));
            // $this->sendRequest($uri)

            $subcategory[] = array(
                'sub_category_id' => $row3['id'],
                'sub_category_name' => $row3['name'],
                'charge' => $sortedcharge,
                );
        }

        $data[] = array(
            'category_id' => $row->id,
            'category' => $row->name,
            'subcategory' => $subcategory,
            );
    }

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // exit;

    if($request->step){
        $step = $request->step;
    }else{
        $step = 1;
    }

    return view('stores.edit', compact('step', 'id', 'store', 'storetypes', 'merchants', 'data','cod'));
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
            ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $store = Store::findOrFail($id);
        $store->fill($request->except('step','merchant_end'));
        $store->created_by = auth()->user()->id;
        $store->updated_by = auth()->user()->id;
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
