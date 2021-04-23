<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\HubVaultAccounts;
use App\Merchant;
use Validator;
use Session;
use App\Bank;
use App\BankAccounts;
use App\MerchantBankAccounts;

class MerchantBankAccountsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('role:superadministrator|systemadministrator|head_of_accounts');
    }

    public function index(Request $request) {

        $query = MerchantBankAccounts::whereStatus(true);
        ( $request->has('hub_id') ) ? $query->where('hub_id', trim($request->hub_id)) : null;
        ( $request->has('account_id') ) ? $query->where('account_id', trim($request->account_id)) : null;
        $merchant_bank_accounts = $query->orderBy('id')->get();
        $bank_accounts = BankAccounts::whereStatus(true)->pluck('name', 'id')->toArray();
        //$bank = Bank::whereStatus(true)->where()->pluck('name','id');
        $merchant = Merchant::whereStatus(true)->pluck('name', 'id')->toArray();
        return view('merchant-bank-accounts.index', compact('merchant_bank_accounts', 'merchant', 'bank_accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
        $bank_accounts = BankAccounts::whereStatus(true)->pluck('name', 'id')->toArray();
        $merchant = Merchant::whereStatus(true)->pluck('name', 'id')->toArray();
        return view('merchant-bank-accounts.insert', compact('merchant', 'bank_accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
                    'name' => 'required',
                    'account_id' => 'required|numeric',
                    'merchant_id' => 'required|numeric',
                    'store_id' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $merchant_bank_accounts = new MerchantBankAccounts();

        $merchant_bank_accounts->name = $request->name;
        $merchant_bank_accounts->account_id = $request->account_id;
        $merchant_bank_accounts->merchant_id = $request->merchant_id;
        $merchant_bank_accounts->store_id = $request->store_id;
        $merchant_bank_accounts->status = 1;
        $merchant_bank_accounts->created_by = auth()->user()->id;

        if ($merchant_bank_accounts->save()) {
            Session::flash('message', "Merchant Bank Account information saved successfully");
            return redirect('merchant-bank-accounts');
        } else {
            return redirect()->withErrors('Merchant Bank Account failed.');
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
        $merchant_bank_account = MerchantBankAccounts::findOrFail($id);
        $bank_accounts = BankAccounts::whereStatus(true)->pluck('name', 'id')->toArray();
        $merchant = Merchant::whereStatus(true)->pluck('name', 'id')->toArray();
        $store = \App\Store::whereStatus(true)->where('merchant_id', $merchant_bank_account->merchant_id)->pluck('store_id', 'id')->toArray();
        //dd($merchant_bank_account->toArray());
        return view('merchant-bank-accounts.edit', compact('store', 'merchant', 'bank_accounts', 'merchant_bank_account'));
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
                    'account_id' => 'required|numeric',
                    'merchant_id' => 'required|numeric',
                    'store_id' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }



        $merchant_bank_accounts = MerchantBankAccounts::findOrFail($id);
        $merchant_bank_accounts->name = $request->name;
        $merchant_bank_accounts->account_id = $request->account_id;
        $merchant_bank_accounts->merchant_id = $request->merchant_id;
        $merchant_bank_accounts->store_id = $request->store_id;
        $merchant_bank_accounts->status = 1;
        $merchant_bank_accounts->updated_by = auth()->user()->id;

        if ($merchant_bank_accounts->save()) {
            Session::flash('message', "Merchant Bank Account information update successfully");
            return redirect('merchant-bank-accounts');
        } else {
            return redirect()->withErrors('Merchant Bank Account update failed.');
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

    public function get_store_for_merchant_bank_accounts(Request $request) {

        $store = \App\Store::where('status', true)->where('merchant_id', $request->merchant_id)->get()->toArray();
        return $store;
    }

}
