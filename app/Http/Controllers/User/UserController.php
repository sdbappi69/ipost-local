<?php

namespace App\Http\Controllers\User;

use App\RiderReference;
use App\VehicleType;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogsTrait;
use App\Http\Traits\MailsTrait;
use App\User;
use Datatables;
use App\Country;
use App\State;
use App\City;
use App\Zone;
use App\Role;
use App\Hub;
use App\Merchant;
use App\Store;
use App\UserType;
use Illuminate\Support\Facades\Log;
use Validator;
use Session;
use Redirect;
use Image;
use DB;
use Auth;
use Entrust;

class UserController extends Controller {

    use LogsTrait;
    use MailsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|merchantadmin|hubmanager|salesteam|saleshead|operationmanager|operationalhead');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if (!Entrust::can('user-list')) {
            abort(403);
        }

        $user_type_title = Role::where('id', '=', auth()->user()->user_type_id)->first()->name;
        switch ($user_type_title) {
            case "hubmanager":
            case "vehiclemanager":
            case "delivery-pickupman":
            case "inboundmanager":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'vehiclemanager')->orWhere('name', 'delivery-pickupman')->orWhere('name', 'inboundmanager')->lists('display_name', 'id')->toArray();
                break;
            case "merchantadmin":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'merchantaccounts')->orWhere('name', 'merchantsupport')->orWhere('name', 'storeadmin')->lists('display_name', 'id')->toArray();
                break;
            case "merchantaccounts":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'merchantsupport')->orWhere('name', 'storeadmin')->lists('display_name', 'id')->toArray();
                break;
            case "merchantsupport":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'storeadmin')->lists('display_name', 'id')->toArray();
                break;
            case "storeadmin":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'storeadmin')->lists('display_name', 'id')->toArray();
                break;
            case "operationmanager":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'delivery-pickupman')->lists('display_name', 'id')->toArray();
                break;
            case "operationalhead":
                $userTypes = Role::orderBy('display_name', 'ASC')->where('name', 'delivery-pickupman')->lists('display_name', 'id')->toArray();
                break;
            default:
                $userTypes = Role::orderBy('display_name', 'ASC')->lists('display_name', 'id')->toArray();
        }

        $req = $request->all();
        /**
         * If user role is merchantadmin
         */
        if (Auth::user()->hasRole('merchantadmin')) {
            // return auth()->user()->id;
            $merchant_user_type_ids = [9, 10, 11];
            $query = User::select(array(
                        'users.id',
                        'users.photo',
                        'users.name',
                        'users.email',
                        'users.msisdn',
                        'users.rider_type',
                        'roles.display_name',
                        'users.status',
            ));

            $query->leftJoin('roles', 'roles.id', '=', 'users.user_type_id');
            $query->whereIn('users.user_type_id', $merchant_user_type_ids);
            $query->where([
                    ['users.id', '!=', auth()->user()->id],
                    ['users.reference_id', '=', auth()->user()->reference_id],
            ]);
            // $query->leftJoin('company_saved_cv', function($leftJoin)use($company_id)
            // $query->leftJoin('stores', 'stores.merchant_id', '=', 'users.reference_id')->where('stores.id', '=', '');
            // $query->orWhere([
            //                ['stores.merchant_id', '=', auth()->user()->reference_id],
            //              ]);
            // $query->where('users.reference_id', '=', auth()->user()->reference_id)->orWhere('stores.merchant_id', '=', auth()->user()->reference_id);
            // $query->where('users.user_type_id', '=', '12');
        } else if (Auth::user()->hasRole('salesteam')) {
            $query = User::select(array(
                        'users.id',
                        'users.photo',
                        'users.name',
                        'users.email',
                        'users.msisdn',
                        'users.alt_msisdn',
                        'roles.display_name',
                        'users.status',
                    ))
                    ->leftJoin('roles', 'roles.id', '=', 'users.user_type_id')
                    ->where('users.user_type_id', '13');
        } else if (Auth::user()->hasRole('operationmanager')) {
            $query = User::select(array(
                        'users.id',
                        'users.photo',
                        'users.name',
                        'users.email',
                        'users.msisdn',
                        'users.alt_msisdn',
                        'roles.display_name',
                        'users.status',
                    ))
                    ->leftJoin('roles', 'roles.id', '=', 'users.user_type_id')
                    ->where('users.user_type_id', '8');
        } else if (Auth::user()->hasRole('operationalhead')) {
            $query = User::select(array(
                        'users.id',
                        'users.photo',
                        'users.name',
                        'users.email',
                        'users.msisdn',
                        'users.alt_msisdn',
                        'roles.display_name',
                        'users.status',
                    ))
                    ->leftJoin('roles', 'roles.id', '=', 'users.user_type_id')
                    ->where('users.user_type_id', '8');
        } else {
            $query = User::select(array(
                        'users.id',
                        'users.photo',
                        'users.name',
                        'users.email',
                        'users.msisdn',
                        'users.alt_msisdn',
                        'roles.display_name',
                        'users.status',
            ));
            $query->leftJoin('roles', 'roles.id', '=', 'users.user_type_id');

            if (Auth::user()->hasRole('merchantadmin')) {
                $query->where('users.reference_id', '=', auth()->user()->reference_id);
            }

            $query->where('users.user_type_id', '>', auth()->user()->user_type_id);
        }

        ($request->has('name')) ? $query->where('users.name', 'like', trim($request->name) . "%") : null;
        ($request->has('email')) ? $query->where('users.email', trim($request->email)) : null;
        ($request->has('msisdn')) ? $query->where('users.msisdn', trim($request->msisdn)) : null;
        ($request->has('alt_msisdn')) ? $query->where('users.alt_msisdn', trim($request->alt_msisdn)) : null;
        ($request->has('user_type_id')) ? $query->where('users.user_type_id', trim($request->user_type_id)) : null;
        ($request->has('status')) ? $query->where('users.status', trim($request->status)) : null;

        $users = $query->orderBy('users.id', 'desc')->paginate(5);

        return view('users.index', compact('users', 'req', 'userTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!Entrust::can('create-user')) {
            abort(403);
        }
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();

        return view('users.insert', compact('prefix', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (!Entrust::can('create-user')) {
            abort(403);
        }
        $validation = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'msisdn' => 'required|between:10,25|unique:users,msisdn',
                    'alt_msisdn' => 'sometimes|between:10,25',
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'zone_id' => 'required',
                    'address1' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $user = new User();
        $user->fill($request->except('msisdn_country', 'alt_msisdn_country'));
        $user->created_by = auth()->user()->id;
        $user->updated_by = auth()->user()->id;
        // $user->user_type_id = \App\UserType::whereTitle("Guest")->first()->id;
        $user->user_type_id = Role::whereName("guest")->first()->id;
        $url = 'uploads/users/';
        $user->photo = env('APP_URL') . $url . 'no_image.jpg';
        $user->api_token = str_random(60);
        $user->save();

        // return $request->all();
        // activityLog('user_id', 'ref_id', 'ref_table', 'text')
        $this->activityLog(auth()->user()->id, $user->id, 'users', 'Created a new user with this email: ' . $request->email);

        Session::flash('message', "Basic information saved successfully");
        return redirect('/user/' . $user->id . '/edit?step=2');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (!Entrust::can('view-user')) {
            abort(403);
        }
        $user = User::select(array(
                    'users.id',
                    'users.name',
                    'users.photo',
                    'users.email',
                    'users.user_type_id',
                    'users.reference_id',
                    'users.msisdn',
                    'users.alt_msisdn',
                    'users.address1',
                    'users.address2',
                    'users.status',
                    'countries.name AS country',
                    'states.name AS state',
                    'cities.name AS city',
                    'zones.name AS zone',
                    'user_types.title AS user_type'
                ))
                ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
                ->leftJoin('states', 'states.id', '=', 'users.state_id')
                ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
                ->leftJoin('zones', 'zones.id', '=', 'users.zone_id')
                ->leftJoin('user_types', 'user_types.id', '=', 'users.user_type_id')
                ->where('users.id', '=', $id)
                ->first();

        if (Auth::user()->hasRole('merchantadmin')) {
            if ($user->user_type_id == 12) {
                $merchant_id = Store::whereStatus(true)->where('id', '=', $user->reference_id)->first()->merchant_id;
                if ($merchant_id != auth()->user()->reference_id) {
                    abort(403);
                }
            } else if ($user->user_type_id < auth()->user()->user_type_id) {
                if ($user->reference_id != auth()->user()->reference_id) {
                    abort(403);
                }
            }
        }

        return view('users.view', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {

        if (!Entrust::can('edit-user')) {
            abort(403);
        }
        $user = User::where('id', '=', $id)->first();
        if(!$user){
            return redirect()->back()->withErrors("Invalid User.");
        }
        if (Auth::user()->hasRole('merchantadmin')) {
            if ($user->user_type_id == 12) {
                $merchant_id = Store::whereStatus(true)->where('id', '=', $user->reference_id)->first()->merchant_id;
                if ($merchant_id != auth()->user()->reference_id) {
                    abort(403);
                }
            } else if ($user->user_type_id < auth()->user()->user_type_id) {
                if ($user->reference_id != auth()->user()->reference_id) {
                    abort(403);
                }
            }
            $user_types = Role::where('id', '=', 9)->orWhere('id', '=', 10)->orWhere('id', '=', 11)->orWhere('id', '=', 12)->lists('display_name', 'id')->toArray();
        } else if (Auth::user()->hasRole('salesteam')) {
            $user_types = Role::whereIn('id', ['9', '10', '11'])->lists('display_name', 'id')->toArray();
        } else if (Auth::user()->hasRole('operationmanager')) {
            $user_types = Role::where('id', '8')->lists('display_name', 'id')->toArray();
        } else if (Auth::user()->hasRole('operationalhead')) {
            $user_types = Role::where('id', '8')->lists('display_name', 'id')->toArray();
        } else {
            // $user_types = UserType::whereStatus(true)->lists('title', 'id')->toArray();
            $user_types = Role::where('id', '>', auth()->user()->user_type_id)->lists('display_name', 'id')->toArray();
        }

        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $user->country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $user->state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $user->city_id)->lists('name', 'id')->toArray();

        // dd($user_types);
        // $user_type_title = UserType::whereStatus(true)->where('id', '=', $user->user_type_id)->pluck('title');
        $user_type_title = Role::where('id', '=', $user->user_type_id)->pluck('name');

        $transparentModes = VehicleType::whereStatus(true)->pluck('title', 'id')->toArray();
        $userReferences = array();
        if (count($user->userReference)) {
            foreach ($user->userReference as $ref) {
                $userReferences[] = $ref->reference_id;
            }
        }
//dd($user_type_title);
        switch ($user_type_title) {
            case "hubmanager":
            case "vehiclemanager":
            case "delivery-pickupman":
                $reference_list = Hub::whereStatus(true)->lists('title', 'id')->toArray();
                break;
            case "merchantadmin":
            case "merchantaccounts":
            case "merchantsupport":
                $reference_list = Merchant::whereStatus(true)->lists('name', 'id')->toArray();
                break;
            case "storeadmin":
                $reference_list = Store::whereStatus(true)->lists('store_id', 'id')->toArray();
                break;
            default:
                $reference_list = null;
        }

        if ($request->step) {
            $step = $request->step;
        } else {
            $step = 1;
        }
//dd($reference_list);
        return view('users.edit', compact('prefix', 'countries', 'step', 'id', 'user_types', 'user', 'reference_list', 'states', 'cities', 'zones', 'transparentModes', 'userReferences'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (!Entrust::can('edit-user')) {
            abort(403);
        }
        if ($request->user_type_id == 8) {
            $validation = Validator::make($request->all(), [
                        'name' => 'sometimes',
                        'email' => 'sometimes|email|unique:users,email,' . $id,
                        'msisdn' => 'sometimes|between:10,25|unique:users,msisdn,' . $id,
                        'alt_msisdn' => 'sometimes|between:10,25|unique:users,msisdn,' . $id,
                        'country_id' => 'sometimes',
                        'state_id' => 'sometimes',
                        'city_id' => 'sometimes',
                        'zone_id' => 'sometimes',
                        'address1' => 'sometimes',
                        'user_type_id' => 'sometimes',
                        'rider_reference_id' => 'sometimes|array',
                        'rider_reference_id.*' => 'exists:hubs,id',
                        'photo' => 'sometimes|image|max:2000',
                        'password' => 'sometimes|confirmed|between:5,32',
                        'password_confirmation' => 'sometimes',
                        'rider_type' => 'sometimes|boolean',
                        'transparent_mode' => 'sometimes|exists:vehicle_types,id',
            ]);
        } else {
            $validation = Validator::make($request->all(), [
                        'name' => 'sometimes',
                        'email' => 'sometimes|email|unique:users,email,' . $id,
                        'msisdn' => 'sometimes|between:10,25',
                        'alt_msisdn' => 'sometimes|between:10,25|unique:users,msisdn,' . $id,
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
            ]);
        }


        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            $user->fill($request->except('rider_reference_id', 'rider_type', 'transparent_mode', 'msisdn_country', 'alt_msisdn_country', 'step', 'photo', 'password', 'password_confirmation'));

            /**
             * reference type handle
             * for rider(user_type_id == 8) reference type is multiple & rider_type, transparent_mode used
             * use different table
             * */
            if ($request->user_type_id == 8) {
                $user->rider_type = $request->rider_type;
                $user->transparent_mode = $request->transparent_mode;

                /*                 * multiple hub assign not allowed* */
//                if (count($request->rider_reference_id) > 1) {
//                    return redirect()->back()->withErrors('Multiple Reference not allowed.');
//                }
                RiderReference::where('user_id', $id)->delete();
                foreach ($request->rider_reference_id as $ref_id) {
                    $riderRef = new RiderReference();
                    $riderRef->user_id = $id;
                    $riderRef->reference_id = $ref_id;
                    $riderRef->save();
                }
            }


            if ($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $fileName = $id . '.' . $extension;
                $url = 'uploads/users/';

                $img = Image::make($request->file('photo'))->resize(200, 200)->save($url . $fileName);
                // return env('APP_URL').$url.$fileName;
                $user->photo = env('APP_URL') . $url . $fileName;

                // activityLog('user_id', 'ref_id', 'ref_table', 'text')
                $this->activityLog(auth()->user()->id, $user->id, 'users', 'Updated profile image for the user with this email: ' . $user->email);
            } else if ($request->password != '') {
                $user->password = bcrypt($request->input('password'));

                // activityLog('user_id', 'ref_id', 'ref_table', 'text')
                $this->activityLog(auth()->user()->id, $user->id, 'users', 'Updated password for the user with this email: ' . $user->email);
            } else {
                // activityLog('user_id', 'ref_id', 'ref_table', 'text')
                $this->activityLog(auth()->user()->id, $user->id, 'users', 'Updated user role for the user with this email: ' . $user->email);
            }

            $user->save();

            if ($request->user_type_id) {
                $user->roles()->sync((array) $request->user_type_id);
            }
            // User::findOrFail($id)->update($request->except('msisdn_country','alt_msisdn_country','step'));
            DB::commit();
            if ($request->step) {
                if ($request->step == 'complete') {
                    // return $request->step;

                    $mail_body = '<h1>BIDDYUT LIMITED</h1><p>Email: ' . $user->email . '</p><p>Password: ' . $request->password . '</p>';
                    // setMail('user_id', 'source', 'mail_type', 'ref_id', 'ref_table', 'to', 'subject', 'body', 'cc', 'bcc')
                    $this->setMail(auth()->user()->id, 'system', 'User Credintials', $user->id, 'users', $user->email, 'Biddyut User Credintials', $mail_body, '', '');

                    Session::flash('message', "User information updated successfully");
                    return redirect('/user');
                } else {
                    $step = $request->step;
                }
            } else {
                $step = 1;
            }
            
            Session::flash('message', "User information saved successfully");
            return redirect('/user/' . $id . '/edit?step=' . $step);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return redirect()->back()->withErrors('Something went wrong, please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function userList() {
        if (!Entrust::can('view-user')) {
            abort(403);
        }
        $query = User::select(array(
                    'users.id',
                    'users.photo',
                    'users.name',
                    'users.email',
                    'users.msisdn',
                    'users.alt_msisdn',
                    'roles.display_name',
                    'users.status',
        ));

        $query->leftJoin('roles', 'roles.id', '=', 'users.user_type_id');

        if (Auth::user()->hasRole('merchantadmin')) {
            $query->where('users.reference_id', '=', auth()->user()->reference_id);
        }

        $query->where('users.user_type_id', '>', auth()->user()->user_type_id);
        $query->orderBy('users.id', 'desc');

        $requesters = $query->get();

        return Datatables::of($requesters)
                        ->remove_column('id')
                        ->editColumn('photo', '<img class="table-thumb" class="img-circle" src="{{ $photo }}" alt="">'
                        )
                        ->editColumn('status', '@if($status=="1")
                                <span class="label label-success"> Active </span>
                             @else
                                <span class="label label-danger"> Inactive </span>
                             @endif')
                        ->add_column('action', '<div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="user/{{ $id }}">
                                                    <i class="fa fa-file-o"></i> View </a>
                                            </li>
                                            <li>
                                                <a href="user/{{ $id }}/edit">
                                                    <i class="fa fa-pencil"></i> Update </a>
                                            </li>
                                        </ul>
                                    </div>'
                        )
                        ->make();
    }

    public function storeUserList() {
        if (!Entrust::can('view-user')) {
            abort(403);
        }
        $query = User::select(array(
                    'users.id',
                    'users.photo',
                    'users.name',
                    'users.email',
                    'users.msisdn',
                    'users.alt_msisdn',
                    'roles.display_name',
                    'users.status',
        ));

        $query->leftJoin('roles', 'roles.id', '=', 'users.user_type_id');
        $query->leftJoin('stores', 'stores.id', '=', 'users.reference_id');
        if (Auth::user()->hasRole('merchantadmin')) {
            $query->where('stores.merchant_id', '=', auth()->user()->reference_id)->where('users.user_type_id', '=', '12');
        } else {
            $query->where('users.user_type_id', '=', '12');
        }

        $query->orderBy('users.id', 'desc');

        $requesters = $query->get();

        return Datatables::of($requesters)
                        ->remove_column('id')
                        ->editColumn('photo', '<img class="table-thumb" class="img-circle" src="{{ $photo }}" alt="">'
                        )
                        ->editColumn('status', '@if($status=="1")
                                <span class="label label-success"> Active </span>
                             @else
                                <span class="label label-danger"> Inactive </span>
                             @endif')
                        ->add_column('action', '<div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="user/{{ $id }}">
                                                    <i class="fa fa-file-o"></i> View </a>
                                            </li>
                                            <li>
                               z                 <a href="user/{{ $id }}/edit">
                                                    <i class="fa fa-pencil"></i> Update </a>
                                            </li>
                                        </ul>
                                    </div>'
                        )
                        ->make();
    }

    public function references($user_type_id) {

        // $user_type_title = UserType::whereStatus(true)->where('id', '=', $user_type_id)->first()->title;
        $user_type_title = Role::where('id', '=', $user_type_id)->first()->name;

        switch ($user_type_title) {
            case "hubmanager":
            case "vehiclemanager":
            case "delivery-pickupman":
            case "inboundmanager":
            case "head_of_accounts":
            case "inventoryoperator":
                $reference_list = Hub::whereStatus(true)->addSelect('title AS title', 'id')->get();
                break;
            case "merchantadmin":
            case "merchantaccounts":
            case "merchantsupport":
                $query = Merchant::whereStatus(true)->addSelect('name AS title', 'id');
                if (Auth::user()->hasRole('merchantadmin')) {
                    $query->where('id', '=', auth()->user()->reference_id);
                }
                $reference_list = $query->get();
                break;
            case "storeadmin":
                $query = Store::where('stores.status', '=', '1')
                        ->select(array(
                            'stores.id AS id',
                            DB::raw("CONCAT(merchants.name,' - ',stores.store_id) AS title")
                        ))
                        ->leftJoin('merchants', 'merchants.id', '=', 'stores.merchant_id');
                // ->addSelect('store_id AS title', 'id')
                if (Auth::user()->hasRole('merchantadmin')) {
                    $query->where('stores.merchant_id', '=', auth()->user()->reference_id);
                }
                $reference_list = $query->get();
                break;
            default:
                $reference_list = null;
        }

        return $_GET['callback'] . "(" . json_encode($reference_list) . ")";
    }

}
