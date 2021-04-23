<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;
use View;
use Validator;
use Input;
use Redirect;
use Session;

class PermissionController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
         $this->middleware('permission:view-permission');

         $req     = $request->all();
         $query   = Permission::where('name', '!=', null);

         ( $request->has('name') )          ? $query->where('name', 'like', trim($request->name)."%")                   : null;
         ( $request->has('display_name') )  ? $query->where('display_name', 'like', trim($request->display_name)."%")   : null;

         $data['title']       = 'View Permissions';
         $data['req']         = $req;
         $data['permissions'] = $query->get();

         return View::make('users.permission.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->middleware('permission:create-permission');

        $data['title'] = 'Create New Permission';
        $data['roles'] = Role::lists('display_name', 'id')->toArray();

        return View::make('users.permission.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->middleware('permission:create-permission');

        $validation = Validator::make($request->all(), [
                                            'name' => 'required|alpha_dash|unique:permissions',
                                            'display_name' => 'required'
                                        ]);

        if( $validation->fails() ) {
            return Redirect::back()->withInput()->withErrors($validation);
        }

        // dd($request->all());

        $permission = new Permission;
        $permission->name = $request->get('name');
        $permission->display_name = $request->get('display_name');
        $permission->save();

        $role_ids = $request->get('role_id');
        foreach( $role_ids as $rid ) {
            $role = Role::findOrFail($rid);
            $role->perms()->attach([$permission->id]);
        }

        // Flash::success('Permission '.$permission->name.' added successfully.');
        Session::flash('message', 'Permission '.$permission->name.' added successfully.');
        return Redirect('permission');
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
        $this->middleware('permission:edit-permission');

        $data['title'] = 'Update Permission Info';
        $data['roles'] = Role::lists('name', 'id');
        $data['permission'] = $permission = Permission::findOrFail($id);
        // $data['prevSelectedRoles'] = [];

        // foreach( $permission->roles as $role ) {
        //  array_push($data['prevSelectedRoles'], $role->id);
        // }

        return View::make('users.permission.edit', $data);
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
        $this->middleware('permission:edit-permission');

        $validation = Validator::make($request->all(), ['name' => 'required|alpha_dash']);

        if( $validation->fails() ) {
            return Redirect::back()->withInput()->withErrors($validation);
        }

        $permission = Permission::findOrFail($id);
        $permission->name = $request->get('name');
        $permission->display_name = $request->get('display_name');
        $permission->save();

        // $role_ids = $request->get('role_id');
        // $permission->roles()->detach();
        // foreach( $role_ids as $rid ) {
        //  $role = Role::findOrFail($rid);
        //  $role->perms()->sync([$permission->id]);
        // }

        // Flash::success('Permission '.$permission->name.' updated successfully.');
        Session::flash('message', 'Permission '.$permission->name.' updated successfully.');
        return Redirect('/permission');
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
}
