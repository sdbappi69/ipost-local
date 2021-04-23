<?php


namespace App\Http\Controllers\Rider;

use App\City;
use App\Country;
use App\Http\Controllers\Controller;
use App\Hub;
use App\RiderReference;
use App\Role;
use App\State;
use App\User;
use App\VehicleType;
use App\Zone;
use Illuminate\Http\Request;
use Entrust;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Validator;
use Session;

class RiderController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Entrust::can('user-list')) {
            abort(403);
        }

        $req = $request->all();

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

        if (Auth::user()->hasRole('merchantadmin')) {
            $query->where('users.reference_id', '=', auth()->user()->reference_id);
        }

        $query->where('users.user_type_id', 8)
            ->where('users.otp_verified', 1)
            ->where('users.status', 0);


        ($request->has('name')) ? $query->where('users.name', 'like', trim($request->name) . "%") : null;
        ($request->has('email')) ? $query->where('users.email', trim($request->email)) : null;
        ($request->has('msisdn')) ? $query->where('users.msisdn', trim($request->msisdn)) : null;
        ($request->has('alt_msisdn')) ? $query->where('users.alt_msisdn', trim($request->alt_msisdn)) : null;
        ($request->has('user_type_id')) ? $query->where('users.user_type_id', trim($request->user_type_id)) : null;
        ($request->has('status')) ? $query->where('users.status', trim($request->status)) : null;

//        $users = $query->orderBy('users.id', 'desc')->get();
        $users = $query->orderBy('users.id', 'desc')->paginate(5);
//        dd($users);
        return view('rider.index', compact('users', 'req'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $user = User::whereNotNull('tem_info')->findOrFail($id);

        $update = json_decode($user->tem_info);
//        dd($user, $update, $update->name, json_decode($update->rider_reference_id));
        $reference_list = Hub::whereStatus(true)->lists('title', 'id')->toArray();
        $transparentModes = VehicleType::whereStatus(true)->pluck('title', 'id')->toArray();
        $prefix = Country::whereStatus(true)->lists('prefix', 'id')->toArray();
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $user->country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $user->state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $user->city_id)->lists('name', 'id')->toArray();
        return view('rider.approve-profile', compact('user', 'update', 'reference_list', 'transparentModes', 'prefix', 'countries', 'states', 'cities', 'zones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'msisdn' => 'required|between:10,25|unique:users,msisdn,' . $id,
            'alt_msisdn' => 'sometimes|between:10,25|unique:users,msisdn,' . $id,
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'zone_id' => 'required',
            'address1' => 'required',
            'rider_reference_id' => 'required|array',
            'rider_reference_id.*' => 'exists:hubs,id',
            'photo' => 'sometimes|image|max:2000',
            'rider_type' => 'sometimes|boolean',
            'transparent_mode' => 'sometimes|exists:vehicle_types,id',
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $riderRequest = json_decode($user->tem_info);

            $user->fill($request->except('rider_reference_id', 'msisdn_country', 'alt_msisdn_country', 'photo'));

            RiderReference::where('user_id', $id)->delete();
            foreach ($request->rider_reference_id as $ref_id) {
                $riderRef = new RiderReference();
                $riderRef->user_id = $id;
                $riderRef->reference_id = $ref_id;
                $riderRef->save();
            }


            if ($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $fileName = $id . '.' . $extension;
                $url = 'uploads/users/';

                $img = Image::make($request->file('photo'))->resize(200, 200)->save($url . $fileName);
                // return env('APP_URL').$url.$fileName;
                $user->photo = env('APP_URL') . $url . $fileName;
            } elseif (!is_null($riderRequest->photo)) {
                Storage::disk('public-root')->delete($user->photo);
                Storage::disk('public-root')->move($riderRequest->photo, $user->photo);
            }
            $user->tem_info = null;
            $user->save();

            DB::commit();
            Session::flash('message', "Rider information saved successfully");
            return redirect('rider-profile-update-request');
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
    public function destroy($id)
    {
        abort(404);
    }

    public function updateRequests(Request $request)
    {
        if (!Entrust::can('user-list')) {
            abort(403);
        }

        $req = $request->all();

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

        if (Auth::user()->hasRole('merchantadmin')) {
            $query->where('users.reference_id', '=', auth()->user()->reference_id);
        }

        $query->where('users.user_type_id', 8)
            ->whereNotNull('users.tem_info')
            ->where('users.status', 1);


        ($request->has('name')) ? $query->where('users.name', 'like', trim($request->name) . "%") : null;
        ($request->has('email')) ? $query->where('users.email', trim($request->email)) : null;
        ($request->has('msisdn')) ? $query->where('users.msisdn', trim($request->msisdn)) : null;
        ($request->has('alt_msisdn')) ? $query->where('users.alt_msisdn', trim($request->alt_msisdn)) : null;
        ($request->has('user_type_id')) ? $query->where('users.user_type_id', trim($request->user_type_id)) : null;
        ($request->has('status')) ? $query->where('users.status', trim($request->status)) : null;

//        $users = $query->orderBy('users.id', 'desc')->get();
        $users = $query->orderBy('users.id', 'desc')->paginate(5);
//        dd($users);
        return view('rider.profile-update-request', compact('users', 'req'));
    }
}