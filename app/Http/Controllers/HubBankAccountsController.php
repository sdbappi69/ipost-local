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
use App\HubBankAccounts;

class HubBankAccountsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('role:superadministrator|systemadministrator|head_of_accounts');
    }

    public function index(Request $request) {

        $query = HubBankAccounts::whereStatus(true);
        ( $request->has('hub_id') ) ? $query->where('hub_id', trim($request->hub_id)) : null;
        ( $request->has('account_id') ) ? $query->where('account_id', trim($request->account_id)) : null;
        $hub_bank_accounts = $query->orderBy('id')->get();
        $bank_accounts = BankAccounts::whereStatus(true)->pluck('name', 'id')->toArray();
        $hub = Hub::whereStatus(true)->pluck('title', 'id')->toArray();
        return view('hub-bank-accounts.index', compact('hub_bank_accounts', 'hub', 'bank_accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
        $bank_accounts = BankAccounts::whereStatus(true)->pluck('name', 'id')->toArray();
        $hub = Hub::whereStatus(true)->pluck('title', 'id')->toArray();
        return view('hub-bank-accounts.insert', compact('hub', 'bank_accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validation = Validator::make($request->all(), [
                    'name' => 'required',
                    'hub_id' => 'required|numeric',
                    'account_id' => 'required',
                    'notification_time' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $hub_bank_accounts = new HubBankAccounts();

        $hub_bank_accounts->name = $request->name;
        $hub_bank_accounts->hub_id = $request->hub_id;
        $hub_bank_accounts->account_id = $request->account_id;
        $hub_bank_accounts->notification_time = $request->notification_time;
        $hub_bank_accounts->status = 1;
        $hub_bank_accounts->created_by = auth()->user()->id;

        if ($hub_bank_accounts->save()) {
            Session::flash('message', "Hub Bank Account information saved successfully");
            return redirect('hub-bank-accounts');
        } else {
            return redirect()->withErrors('Hub Bank Account failed.');
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
        $hub_bank_accounts = HubBankAccounts::findOrFail($id);
        $bank_accounts = BankAccounts::whereStatus(true)->pluck('name', 'id')->toArray();
        $hub = Hub::whereStatus(true)->pluck('title', 'id')->toArray();
        return view('hub-bank-accounts.edit', compact('hub', 'bank_accounts', 'hub_bank_accounts'));
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
                    'hub_id' => 'required|numeric',
                    'account_id' => 'required',
                    'notification_time' => 'required',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $hub_bank_accounts = HubBankAccounts::findOrFail($id);

        $hub_bank_accounts->name = $request->name;
        $hub_bank_accounts->hub_id = $request->hub_id;
        $hub_bank_accounts->account_id = $request->account_id;
        $hub_bank_accounts->notification_time = $request->notification_time;
        $hub_bank_accounts->status = 1;
        $hub_bank_accounts->updated_by = auth()->user()->id;

        if ($hub_bank_accounts->save()) {
            Session::flash('message', "Hub Bank Account information Update successfully");
            return redirect('hub-bank-accounts');
        } else {
            return redirect()->withErrors('Hub Bank Account update failed.');
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
