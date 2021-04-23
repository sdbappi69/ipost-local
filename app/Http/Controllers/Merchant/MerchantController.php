<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\MailsTrait;
use App\Country;
use App\Merchant;
use App\State;
use App\City;
use App\Zone;
use Validator;
use Session;
use Redirect;
use Image;
use Datatables;
use Entrust;
use Excel;
use App\Role;

use App\User;

use App\Storetype;

use App\Store;

use App\ExtractLog;
use Auth;

class MerchantController extends Controller
{
    use LogsTrait;
    use MailsTrait;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
    //echo "a"; die();
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|salesteam|coo|saleshead|operationmanager|operationalhead|kam');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index( Request $request )
    {
        $req  = $request->all();
        
        $query = Merchant::select('merchants.id AS merchant_id',
            'merchants.name AS merchant_name',
            'merchants.email AS merchant_email',
            'merchants.msisdn AS merchant_msisdn',
            'merchants.alt_msisdn',
            'merchants.website AS merchant_website',
            'merchants.billing_date',
            'merchants.billing_type',
            'merchants.status AS merchant_status',
            'merchants.photo AS merchant_photo',
            'A.name AS creator_name',
            'B.name AS responsible_name')
        ->leftjoin('users as A', 'A.id', '=', 'merchants.created_by')
        ->leftjoin('users as B', 'B.id', '=', 'merchants.responsible_user_id')
        ->orderBy('merchant_id', 'desc');

        ( $request->has('name') )           ? $query->where('merchants.name', 'like', "%".trim($request->name)."%")     : null;
        ( $request->has('email') )          ? $query->where('merchants.email', trim($request->email))               : null;
        ( $request->has('msisdn') )         ? $query->where('merchants.msisdn', trim($request->msisdn))             : null;
        ( $request->has('alt_msisdn') )     ? $query->where('merchants.alt_msisdn', trim($request->alt_msisdn))     : null;
        ( $request->has('website') )        ? $query->where('merchants.website', trim($request->website))           : null;
        ( $request->has('billing_date') )   ? $query->where('merchants.billing_date', trim($request->billing_date)) : null;
        ( $request->has('due_date') )       ? $query->where('merchants.due_date', trim($request->due_date))         : null;
        ( $request->has('status') )         ? $query->where('merchants.status', trim($request->status))             : null;
        
        if($request->has('responsible_user_id')){
            $query->whereIn('merchants.responsible_user_id', $request->responsible_user_id);
        }

        $merchant = $query->paginate(10);

        $salesteam = User::where('user_type_id', '19')->lists('name', 'id')->toArray();

        return view('merchants.index', compact('merchant', 'req', 'salesteam'));
    }

    public function merchantexport(Request $request, $type){
        $query = Merchant::whereStatus(true);

        ( $request->has('name') )           ? $query->where('name', 'like', "%".trim($request->name)."%")     : null;
        ( $request->has('email') )          ? $query->where('email', trim($request->email))               : null;
        ( $request->has('msisdn') )         ? $query->where('msisdn', trim($request->msisdn))             : null;
        ( $request->has('alt_msisdn') )     ? $query->where('alt_msisdn', trim($request->alt_msisdn))     : null;
        ( $request->has('website') )        ? $query->where('website', trim($request->website))           : null;
        ( $request->has('billing_date') )   ? $query->where('billing_date', trim($request->billing_date)) : null;
        ( $request->has('due_date') )       ? $query->where('due_date', trim($request->due_date))         : null;
        ( $request->has('status') )         ? $query->where('status', trim($request->status))             : null;

        $merchants = $query->orderBy('id', 'desc')->get()->toArray();

        $ExtractLog = new ExtractLog();
        $ExtractLog->user_id = Auth::user()->id;
        $ExtractLog->extract_type = 'Merchants';
        $ExtractLog->download_date = date('Y-m-d H:i:s');
        $ExtractLog->save();

        return Excel::create('merchants_'.time(), function($excel) use ($merchants) {
            $excel->sheet('merchants', function($sheet) use ($merchants)
            {

                $datasheet = array();
                $datasheet[0]  =   array('S/N','Merchant Name','Email','Mobile','Alt. Mobile','Website','Address');
                $i=1;
                foreach($merchants as $merchant){

                    $datasheet[$i] = array(
                        $i,
                        $merchant['name'],
                        $merchant['email'],
                        $merchant['msisdn'],
                        $merchant['alt_msisdn'],
                        $merchant['website'],
                        $merchant['address1']
                    );

                    $i++;
                }

                $sheet->setOrientation('landscape');

                // Freeze first row
                $sheet->freezeFirstRow();

                $sheet->fromArray($datasheet);
            });
        })->download($type);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if(!Entrust::can('create-merchant')) { abort(403); }
            $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
            $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

            return view('merchants.insert', compact('prefix', 'countries'));
        }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        //dd($request->all());
        if(!Entrust::can('create-merchant')) { abort(403); }

            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:merchants',
                'msisdn' => 'required|between:10,25',
                'alt_msisdn' => 'sometimes|between:10,25',
                'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'zone_id' => 'required',
                'address1' => 'required',
                'website' => 'required',
                // 'billing_date' => 'required',
                // 'billing_type' => 'required',
        //'due_date' => 'required',
            ]);

            if($validation->fails()) {
                return Redirect::back()->withErrors($validation)->withInput();
            }

            $merchant = new Merchant();
            $last_merchant_id = Merchant::select('id')->orderBy('id','desc')->first();
            if(!count($last_merchant_id) > 0){
                $id = 1;
            }
            else{
                $id =  $last_merchant_id->id + 1;
            }
            $merchant->fill($request->except('due_date','photo','msisdn_country', 'alt_msisdn_country'));
            if($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $fileName = $id.'.'.$extension;
                $url = 'uploads/merchants/';

                $img = Image::make($request->file('photo'))->resize(200, 200)->save($url.$fileName);
                // return env('APP_URL').$url.$fileName;
                $merchant->photo = env('APP_URL').$url.$fileName;
            }
            else{
                $url = 'uploads/merchants/';
                $merchant->photo = env('APP_URL').$url.'no_image.jpg';
            }
            $merchant->created_by = auth()->user()->id;
            $merchant->updated_by = auth()->user()->id;

            $merchant->responsible_user_id = auth()->user()->id;

            // if(Auth::user()->hasRole('salesteam')) {
            //     $merchant->status = '0';
            //     $merchant->is_approved = '0';
            // }

            $merchant->save();

            Session::flash('message', "Merchant added successfully");
            return redirect('/merchant/'.$merchant->id.'/edit?step=2');
        }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $merchant = Merchant::select(array(
            'merchants.id',
            'merchants.name',
            'merchants.photo',
            'merchants.email',
            'merchants.msisdn',
            'merchants.alt_msisdn',
            'merchants.address1',
            'merchants.address2',
            'merchants.status',
            'merchants.website',
            'merchants.billing_date',
            'merchants.due_date',
            'countries.name AS country',
            'states.name AS state',
            'cities.name AS city',
            'zones.name AS zone',
        ))
        ->leftJoin('countries', 'countries.id', '=', 'merchants.country_id')
        ->leftJoin('states', 'states.id', '=', 'merchants.state_id')
        ->leftJoin('cities', 'cities.id', '=', 'merchants.city_id')
        ->leftJoin('zones', 'zones.id', '=', 'merchants.zone_id')
        ->where('merchants.id', '=', $id)
        ->first();
        return view('merchants.view', compact('merchant'));
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit(Request $request, $id)
    {
        //echo \Request::route()->getName(); die();

        //  \Artisan::call('route:list');
        // echo '<pre>'; print_r(\Artisan::output());
        //echo Route::currentRouteName(); die();
        // echo app('router')->getRoutes()->match(app('request')->create('/qqq/posts/68/u1'))->getName(); die();
        if(!Entrust::can('edit-merchant')) { abort(403); }
            $merchant = Merchant::where('id', '=', $id)->first();
            //dd($merchant->toArray());
            $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
            $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
            $states = State::whereStatus(true)->where('country_id', '=', $merchant->country_id)->lists('name', 'id')->toArray();
            $cities = City::whereStatus(true)->where('state_id', '=', $merchant->state_id)->lists('name', 'id')->toArray();
            $zones = Zone::whereStatus(true)->where('city_id', '=', $merchant->city_id)->lists('name', 'id')->toArray();

            if($request->step){
                $step = $request->step;
            }else{
                $step = 1;
            }
            $user_types = Role::where('id', '=', 9)->orWhere('id', '=', 10)->orWhere('id', '=', 11)->orWhere('id', '=', 12)->lists('display_name', 'id')->toArray();

            $merchant_user_type_ids = [9,10,11];
            $user = User::select('users.*','user_types.title')
            ->leftJoin('user_types', 'user_types.id', '=', 'users.user_type_id')
            ->whereIn('users.user_type_id', $merchant_user_type_ids)
            ->where('users.reference_id', '=', $merchant->id)
            ->get();

            $storetypes = Storetype::whereStatus(true)->lists('title', 'id')->toArray();

            $store = Store::select('stores.*','store_types.title')
            ->leftJoin('store_types', 'store_types.id', '=', 'stores.store_type_id')
            ->where('merchant_id',$merchant->id)
            ->get();
            //dd($store->toArray());
            // dd($user->toArray());

            return view('merchants.edit', compact('store','storetypes','user','user_types','prefix', 'countries', 'step', 'id', 'merchant', 'states', 'cities', 'zones'));
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
    //dd($request->all());
        if(!Entrust::can('edit-merchant')) { abort(403); }

            $validation = Validator::make($request->all(), [
                'name' => 'sometimes',
                'email' => 'sometimes|email',
                'msisdn' => 'sometimes|between:10,25',
                'alt_msisdn' => 'sometimes|between:10,25',
                'country_id' => 'sometimes',
                'state_id' => 'sometimes',
                'city_id' => 'sometimes',
                'zone_id' => 'sometimes',
                'address1' => 'sometimes',
                'user_type_id' => 'sometimes',
                'reference_id' => 'sometimes',
                'photo' => 'sometimes|image|max:2000',
                'password' => 'sometimes|confirmed|between:5,32',
                'password_confirmation' => 'sometimes',
                'website' => 'sometimes',
                // 'billing_date' => 'sometimes',
            //'due_date' => 'sometimes',
            ]);

            if($validation->fails()) {
                return Redirect::back()->withErrors($validation)->withInput();
            }

            $merchant = Merchant::findOrFail($id);

            $merchant->fill($request->except('msisdn_country','alt_msisdn_country','step','photo'));

            if($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $fileName = $id.'.'.$extension;
                $url = 'uploads/merchants/';

                $img = Image::make($request->file('photo'))->resize(200, 200)->save($url.$fileName);
            // return env('APP_URL').$url.$fileName;
                $merchant->photo = env('APP_URL').$url.$fileName;
            }

            $merchant->save();

        // Merchant::findOrFail($id)->update($request->except('msisdn_country','alt_msisdn_country','step'));

            if($request->step){
                if($request->step == 'complete'){
                // return $request->step;
                    Session::flash('message', "Merchant information updated successfully");
                    return redirect('/merchant');
                }else{
                    $step = $request->step;
                }
            }else{
                $step = 1;
            }

            Session::flash('message', "Merchant information saved successfully");
            return redirect('/merchant/'.$id.'/edit?step='.$step);
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

    public function merchantList(){

        $query = Merchant::select(array(
            'merchants.id',
            'merchants.photo',
            'merchants.name',
            'merchants.email',
            'merchants.msisdn',
            'merchants.alt_msisdn',
            'merchants.website',
            'merchants.billing_date',
            'merchants.due_date',
            'merchants.status',
        ))
        ->orderBy('merchants.id', 'desc');

        $requesters = $query->get();

        return Datatables::of($requesters)
        ->remove_column('id')
        ->editColumn('photo',
            '<img class="table-thumb" class="img-circle" src="{{ $photo }}" alt="">'
        )
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
            <a href="merchant/{{ $id }}">
            <i class="fa fa-file-o"></i> View </a>
            </li>
            <li>
            <a href="merchant/{{ $id }}/edit">
            <i class="fa fa-pencil"></i> Update </a>
            </li>
            </ul>
            </div>'
        )
        ->make();

    }

    public function create_user(Request $request){
        //dd($request->all());
        //if(!Entrust::can('create-user')) { abort(403); }
        if (!Merchant::where('id', '=',$request->reference_id)->exists()) {
            return Redirect::back()->withErrors('Merchant id invalid.')->withInput();
            // Merchant not found
        }
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'msisdn' => 'required|between:10,25',
            'alt_msisdn' => 'sometimes|between:10,25',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'zone_id' => 'required',
            'user_type_id' => 'sometimes',
            'reference_id' => 'sometimes',
            'photo' => 'sometimes|image|max:2000',
            'password' => 'sometimes|confirmed|between:5,32',
            'password_confirmation' => 'sometimes'
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        $last_user_id  = User::select('id')->orderBy('id','desc')->first();
        if(!count($last_user_id)>0){
            $id = 1 ;
        }
        else{
            $id = $last_user_id->id + 1 ;
        }

        $user = new User();
        $user->fill($request->except('step','password','password_confirmation','msisdn_country', 'alt_msisdn_country'));
        $user->password = bcrypt($request->input('password'));
        $user->created_by = auth()->user()->id;
        $user->updated_by = auth()->user()->id;
        if($request->hasFile('photo')) {
            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = $id.'.'.$extension;
            $url = 'uploads/users/';

            $img = Image::make($request->file('photo'))->resize(200, 200)->save($url.$fileName);
            // return env('APP_URL').$url.$fileName;
            $user->photo = env('APP_URL').$url.$fileName;
        }
        else{
            $url = 'uploads/users/';
            $user->photo = env('APP_URL').$url.'no_image.jpg';
        }

        $user->api_token = str_random(60);
        $user->save();
        if($request->user_type_id){
            $user->roles()->sync((array)$user->user_type_id);
        }

        $this->activityLog(auth()->user()->id, $user->id, 'users', 'Created a new user with this email: '.$request->email);

        Session::flash('message', "User added successfully");

        return redirect('/merchant2/'.$request->reference_id.'/edit?step=2');
        //return redirect('/user/'.$user->id.'/edit?step=2');
    }
    
    public function edit_user(Request $request){
        //dd($request->all());
        //if(!Entrust::can('create-user')) { abort(403); }
        if (!Merchant::where('id', '=',$request->reference_id)->exists()) {
            return Redirect::back()->withErrors('Merchant id invalid.')->withInput();
            // Merchant not found
        }
        if (!User::where('id', '=',$request->id)->where('reference_id',$request->reference_id)->exists()) {
            return Redirect::back()->withErrors('User id invalid.')->withInput();
            // Merchant not found
        }
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,id,'.$request->id,
            'msisdn' => 'required|between:10,25',
            'alt_msisdn' => 'sometimes|between:10,25',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'zone_id' => 'required',
            'user_type_id' => 'sometimes',
            'reference_id' => 'sometimes',
            'photo' => 'sometimes|image|max:2000',
            //'password' => 'sometimes|confirmed|between:5,32',
            ///'password_confirmation' => 'sometimes'
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $id = $request->id;

        $user = User::findOrFail($id);
        $user->fill($request->except('old_photo','reference_id','step','password','password_confirmation','msisdn_country', 'alt_msisdn_country'));
        $user->password = bcrypt($request->input('password'));
        $user->created_by = auth()->user()->id;
        $user->updated_by = auth()->user()->id;
        if($request->hasFile('photo')) {
            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = $id.'.'.$extension;
            $url = 'uploads/users/';

            $img = Image::make($request->file('photo'))->resize(200, 200)->save($url.$fileName);
            // return env('APP_URL').$url.$fileName;
            $user->photo = env('APP_URL').$url.$fileName;
            $this->activityLog(auth()->user()->id, $user->id, 'users', 'Updated profile image for the user with this email: '.$user->email);
        }
        else{
            $user->photo=$request->old_photo;
        }
        $user->api_token = str_random(60);
        $user->save();
        if($request->user_type_id){
            $user->roles()->sync((array)$user->user_type_id);
        }

        $this->activityLog(auth()->user()->id, $user->id, 'users', 'Update user information with this email: '.$request->email);

        Session::flash('message', "User Information Update successfully");

        return redirect('/merchant2/'.$request->reference_id.'/edit?step=2');
        //return redirect('/user/'.$user->id.'/edit?step=2');
    }
}
