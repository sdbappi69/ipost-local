<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Country;
use Auth;
use Validator;
use Session;
use Redirect;
use Image;
use Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::whereStatus(true)->lists('prefix', 'id')->toArray();

        $user = User::where('id', '=', Auth::user()->id)->first();
        return view('profile.editprofile', compact('user', 'countries'));
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
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'msisdn' => 'required|between:10,25',
            'alt_msisdn' => 'sometimes|between:10,25',
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        // User::findOrFail($id)->update($request->all());
        $user = User::findOrFail($id);
        $user->fill($request->except('msisdn_country','alt_msisdn_country'));
        $user->updated_by = auth()->user()->id;
        $user->save();

        Session::flash('message', "Profile updated successfully");
        return redirect('/profile');
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

    public function updatePhoto(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'photo' => 'required|image|max:2000'
        ]);

        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $user = User::findOrFail($id);

        if($request->hasFile('photo')) {
            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = $id.'.'.$extension;
            $url = 'uploads/users/';

            $img = Image::make($request->file('photo'))->resize(200, 200)->save($url.$fileName);

            $user->photo = env('APP_URL').$url.$fileName;
        }

        $user->save();

        Session::flash('message', "Profile photo updated successfully");
        return redirect('/profile#avatar');
    }

    public function updatePass(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|between:5,32',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::findOrFail(auth()->user()->id);

        if(! Hash::check($request->input('current_password'), $user->password)) {
            return redirect('/profile#password')->withErrors(['Sorry! Current password value mismatch'])->withInput();
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        Session::flash('message', 'Password updated successfully');
        return redirect('/profile#password');
    }
}
