<?php

namespace App\Http\Controllers\Rider;

use App\RiderReference;
use App\VehicleType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Country;
use App\State;
use App\City;
use App\Zone;
use Illuminate\Support\Facades\Log;
use Validator;
use Session;
use Redirect;
use Image;
use DB;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('role:hubmanager');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $query = User::join('rider_references', 'users.id', '=', 'rider_references.user_id')
                ->select('users.id', 'users.photo', 'users.name', 'users.email', 'users.msisdn', 'users.alt_msisdn', 'users.status', 'users.rider_type')
                ->where('users.status', 1)->where('user_type_id', '=', '8')
                ->where('rider_references.reference_id', '=', auth()->user()->reference_id);

        ($request->has('name')) ? $query->where('users.name', 'like', trim($request->name) . "%") : null;
        ($request->has('email')) ? $query->where('users.email', trim($request->email)) : null;
        ($request->has('msisdn')) ? $query->where('users.msisdn', trim($request->msisdn)) : null;
        ($request->has('alt_msisdn')) ? $query->where('users.alt_msisdn', trim($request->alt_msisdn)) : null;
        ($request->has('user_type_id')) ? $query->where('users.user_type_id', trim($request->user_type_id)) : null;
        ($request->has('status')) ? $query->where('users.status', trim($request->status)) : null;

        $users = $query->orderBy('users.id', 'desc')->paginate(10);

        return view('rider.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $transparentModes = VehicleType::whereStatus(true)->pluck('title', 'id')->toArray();
        return view('rider.user.insert', compact('transparentModes', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validation = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'msisdn' => 'required|between:10,25|unique:users,msisdn',
                    'alt_msisdn' => 'sometimes|between:10,25',
                    'country_id' => 'required|exists:countries,id',
                    'state_id' => 'required|exists:states,id',
                    'city_id' => 'required|exists:cities,id',
                    'zone_id' => 'required|exists:zones,id',
                    'address1' => 'required',
                    'password' => 'required',
        ]);
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        try {
            DB::beginTransaction();
            $user = new User();
            $user->fill($request->except('photo', 'password', 'password_confirmation'));
            $user->created_by = auth()->user()->id;
            $user->updated_by = auth()->user()->id;
            $user->user_type_id = 8; // rider
            $user->rider_type = $request->rider_type;
            $user->transparent_mode = $request->transparent_mode;
            $user->api_token = str_random(60);
            $user->password = bcrypt($request->input('password'));
            $user->save();

            $riderRef = new RiderReference();
            $riderRef->user_id = $user->id;
            $riderRef->reference_id = auth()->user()->reference_id;
            $riderRef->save();

            if ($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $fileName = $user->id . '.' . $extension;
                $url = 'uploads/users/';

                Image::make($request->file('photo'))->resize(200, 200)->save($url . $fileName);

                $user->photo = $url . $fileName;
            } else {
                $url = 'uploads/users/';
                $user->photo = $url . 'no_image.jpg';
            }

            $user->save();

            DB::commit();
            Session::flash('message', "Rider saved successfully");
            return redirect('/rider-user');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            Session::flash('message', "Something went wrong, Please try again.");
            return redirect('/rider-user');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $user = User::findOrFail($id);
        $countries = Country::whereStatus(true)->lists('name', 'id')->toArray();
        $states = State::whereStatus(true)->where('country_id', '=', $user->country_id)->lists('name', 'id')->toArray();
        $cities = City::whereStatus(true)->where('state_id', '=', $user->state_id)->lists('name', 'id')->toArray();
        $zones = Zone::whereStatus(true)->where('city_id', '=', $user->city_id)->lists('name', 'id')->toArray();
        $transparentModes = VehicleType::whereStatus(true)->pluck('title', 'id')->toArray();
        $userReferences = array();
        if (count($user->userReference)) {
            foreach ($user->userReference as $ref) {
                $userReferences[] = $ref->reference_id;
            }
        }
        return view('rider.user.edit', compact('countries', 'user', 'reference_list', 'states', 'cities', 'zones', 'transparentModes', 'userReferences'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validation = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'msisdn' => 'required|between:10,25|unique:users,msisdn,' . $id,
                    'alt_msisdn' => 'sometimes|between:10,25',
                    'country_id' => 'required|exists:countries,id',
                    'state_id' => 'required|exists:states,id',
                    'city_id' => 'required|exists:cities,id',
                    'zone_id' => 'required|exists:zones,id',
                    'address1' => 'required',
        ]);
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        $user = User::findOrFail($id);

        try {
            DB::beginTransaction();
            $user->fill($request->except('photo'));
            $user->updated_by = auth()->user()->id;
            $user->rider_type = $request->rider_type;
            $user->transparent_mode = $request->transparent_mode;
            $user->save();

            if ($request->hasFile('photo')) {
                $extension = $request->file('photo')->getClientOriginalExtension();
                $fileName = $user->id . '.' . $extension;
                $url = 'uploads/users/';

                Image::make($request->file('photo'))->resize(200, 200)->save($url . $fileName);

                $user->photo = $url . $fileName;
            }

            $user->save();

            DB::commit();
            Session::flash('message', "Rider saved successfully");
            return redirect('/rider-user');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            Session::flash('message', "Something went wrong, Please try again.");
            return redirect('/rider-user');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        abort(404);
    }

}
