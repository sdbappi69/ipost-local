<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\HubVaultAccounts;
use App\Hub;
use Validator;
use Session;
use App\Bank;

class BankController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('role:superadministrator|systemadministrator|head_of_accounts');
    }

    public function index(Request $request) {
        $banks = Bank::whereStatus(true)->get();

        return view('bank.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //


        return view('bank.insert');
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
                    'name' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $bank = new Bank();

        $bank->name = $request->name;
        $bank->status = 1;
        $bank->created_by = auth()->user()->id;

        if ($bank->save()) {
            Session::flash('message', "Bank information saved successfully");
            return redirect('bank');
        } else {
            return redirect()->withErrors('Bank added failed.');
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
        //echo $id; die();
        $bank = Bank::findOrFail($id);

        return view('bank.edit', compact('bank'));
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
                    'name' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        //
        $bank = Bank::findOrFail($id);
        $bank->name = $request->name;

        $bank->status = 1;
        $bank->updated_by = auth()->user()->id;

        if ($bank->save()) {
            Session::flash('message', "Bank updated successfully");
            return redirect('bank');
        } else {
            return redirect()->withErrors('Bank update failed.');
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
