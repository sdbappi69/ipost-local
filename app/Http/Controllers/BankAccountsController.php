<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\HubVaultAccounts;
use App\Hub;
use Validator;
use Session;
use App\Bank;
use App\BankAccounts;

class BankAccountsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('role:superadministrator|systemadministrator|head_of_accounts');
    }

    public function index(Request $request) {
        $query = BankAccounts::whereStatus(true);
        ( $request->has('bank_id') ) ? $query->where('bank_id', trim($request->bank_id)) : null;
        $bank_accounts = $query->orderBy('id', 'desc')->get();
        $bank = Bank::whereStatus(true)->pluck('name', 'id')->toArray();
        return view('bank-accounts.index', compact('bank_accounts', 'bank'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //

        $bank = Bank::whereStatus(true)->pluck('name', 'id')->toArray();

        return view('bank-accounts.insert', compact('bank'));
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
                    'bank_id' => 'required',
                    'account_no' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $bank_accounts = new BankAccounts();

        $bank_accounts->name = $request->name;
        $bank_accounts->bank_id = $request->bank_id;
        $bank_accounts->account_no = $request->account_no;
        $bank_accounts->account_of = $request->account_of;
        $bank_accounts->status = 1;
        $bank_accounts->created_by = auth()->user()->id;

        if ($bank_accounts->save()) {
            Session::flash('message', "Bank account information saved successfully");
            return redirect('bank-accounts');
        } else {
            return redirect()->withErrors('Bank account added failed.');
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
        $bank_account = BankAccounts::findOrFail($id);
        $bank = Bank::whereStatus(true)->pluck('name', 'id')->toArray();
        return view('bank-accounts.edit', compact('bank_account', 'bank'));
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
                    'bank_id' => 'required',
                    'account_no' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }

        $bank_accounts = BankAccounts::findOrFail($id);
        $bank_accounts->name = $request->name;
        $bank_accounts->bank_id = $request->bank_id;
        $bank_accounts->account_no = $request->account_no;
        $bank_accounts->account_of = $request->account_of;
        $bank_accounts->status = 1;
        $bank_accounts->updated_by = auth()->user()->id;

        if ($bank_accounts->save()) {
            Session::flash('message', "Bank account information updated successfully");
            return redirect('bank-accounts');
        } else {
            return redirect()->withErrors('Bank account updated failed.');
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
