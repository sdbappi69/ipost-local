<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\HubVaultAccounts;
use App\Hub;
use Validator;
use Session;

class VaultController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('role:superadministrator|systemadministrator|head_of_accounts');
    }

    public function index(Request $request) {
        $query = HubVaultAccounts::whereStatus(true);
        ( $request->has('hub_id') ) ? $query->where('hub_id', trim($request->hub_id)) : null;
        $vaults = $query->orderBy('id')->get();
        $hub = Hub::whereStatus(true)->pluck('title', 'id')->toArray();
        return view('vault.index', compact('vaults', 'hub'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
        $hub = Hub::whereStatus(true)->pluck('title', 'id')->toArray();

        return view('vault.insert', compact('hub'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //

        $validation = Validator::make($request->all(), [
                    'hub_id' => 'required|numeric',
                    'title' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $hub_insert = new HubVaultAccounts();

        $hub_insert->title = $request->title;
        $hub_insert->hub_id = $request->hub_id;
        $hub_insert->amount = $request->amount;
        $hub_insert->status = 1;
        $hub_insert->created_by = auth()->user()->id;

        if ($hub_insert->save()) {
            Session::flash('message', "vault information saved successfully");
            return redirect('vault');
        } else {
            return redirect()->withErrors('Vault added failed.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $vault = HubVaultAccounts::findOrFail($id);
        $hub = Hub::whereStatus(true)->pluck('title', 'id')->toArray();
        return view('vault.edit', compact('vault', 'hub'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validation = Validator::make($request->all(), [
                    'hub_id' => 'required|numeric',
                    'title' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        //
        $vault = HubVaultAccounts::findOrFail($id);
        $vault->title = $request->title;
        $vault->hub_id = $request->hub_id;
        $vault->amount = $request->amount;
        $vault->status = 1;
        $vault->updated_by = auth()->user()->id;

        if ($vault->save()) {
            Session::flash('message', "vault updated successfully");
            return redirect('vault');
        } else {
            return redirect()->withErrors('Vault update failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
