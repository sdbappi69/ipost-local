<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;

use App\Http\Requests;
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
use App\Role;

use App\User;

class UserController extends Controller
{
   use LogsTrait;
   use MailsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

// Send Mail
$this->mailNewUser($user->id, $request->password);

return redirect('/merchant/'.$request->reference_id.'/edit?step=2');

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
    'password' => 'sometimes|confirmed|between:5,32',
    'password_confirmation' => 'sometimes'
    ]);

if($validation->fails()) {
    return Redirect::back()->withErrors($validation)->withInput();
}

$id = $request->id;

$user = User::findOrFail($id);
$user->fill($request->except('password','password_confirmation','old_photo','reference_id','step','password','password_confirmation','msisdn_country', 'alt_msisdn_country'));
//$user->password = bcrypt($request->input('password'));
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

//var_dump($request->password); die();

if($request->password != ''){
   // echo "a"; die();
    $user->password = bcrypt($request->input('password'));

            // activityLog('user_id', 'ref_id', 'ref_table', 'text')
    $this->activityLog(auth()->user()->id, $user->id, 'users', 'Updated password for the user with this email: '.$user->email);
}

$user->api_token = str_random(60);
$user->save();
if($request->user_type_id){
  $user->roles()->sync((array)$user->user_type_id);
}

$this->activityLog(auth()->user()->id, $user->id, 'users', 'Update user information with this email: '.$request->email);

Session::flash('message', "User Information Update successfully");

// Send Mail
if($request->has('password')){
  $this->mailEditUser($user->id, $request->password);
}

return redirect('/merchant/'.$request->reference_id.'/edit?step=2');

}

}
