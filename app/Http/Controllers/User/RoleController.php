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

class RoleController extends Controller
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
    public function index()
    {

        $data['title'] = 'View Roles';
        $data['roles'] = Role::with('perms')->orderBy('id', 'asc')->get();

        // dd($data['roles']);

        return View::make('users.role.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data['title'] = 'Create New Role';
        $data['permissions'] = Permission::get();

        return View::make('users.role.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), 
                            [
                                'name' => 'required|unique:roles',
                                'display_name' => 'required'
                            ]
                        );

        if( $validation->fails() ) {        
            return Redirect::back()->withInput()->withErrors($validation);
        }

        $role = new Role;
        $role->name = $request->input('name');
        $role->display_name = $request->input('display_name');
        $role->save();

        $permission_ids = $request->input('permission_ids');
        if( count( $permission_ids ) ) {
            $role->perms()->attach( $permission_ids );
        }

        // // Flash::success('Role '.$role->name.' added successfully and selected (if any) permission(s) attached to that role.');
        Session::flash('message', 'Role '.$role->name.' added successfully and selected (if any) permission(s) attached to that role.');
        return Redirect('/role');
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

        $data['title'] = 'Update Role Details';
        $data['role'] = Role::with('perms')->where('roles.id', $id)->first(); 
        
        $currentPermissions = [];
        if( count( $data['role']->perms ) ) {
            foreach( $data['role']->perms as $currentPermission ) {
                array_push($currentPermissions, $currentPermission->id);
            }
        }

        $data['permissions'] = Permission::get();
        $data['currentPermissions'] = $currentPermissions;

        return View::make('users.role.edit', $data);
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
        
        $validation = Validator::make($request->all(), ['name' => 'required|alpha_num']);

        if( $validation->fails() ) {        
            return Redirect::back()->withInput()->withErrors($validation);
        }

        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->save();

        $permission_ids = $request->input('permission_ids');
        if( count( $permission_ids ) ) {
            $role->perms()->detach();
            $role->perms()->attach( $permission_ids );
        } else {
            $role->perms()->detach();
        }

        // Flash::success('Role '.$role->name.' updated successfully.');
        Session::flash('message', 'Role '.$role->name.' updated successfully.');
        return Redirect::route('role.index');
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
