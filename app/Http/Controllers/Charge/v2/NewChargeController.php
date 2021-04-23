<?php

namespace App\Http\Controllers\Charge\v2;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Charge;
use App\IpostCharge;
use App\ProductCategory;
use App\ChargeModel;
use App\ZoneGenre;
use Validator;
use Redirect;
use Session;
use Entrust;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewChargeController extends Controller {

    public function view($category_id, Request $request) {

        if (!Entrust::can('view-charge')) {
            abort(403);
        }

        $product_category = ProductCategory::findOrFail($category_id);
        $charge_types = array('Fixed' => 'Fixed', 'Weight Based' => 'Weight Based');

        if ($request->has('store_id')) {
            $charges = IpostCharge::where('product_category_id', $category_id)
                    ->where(function($query) use ($request) {
                        $query->where('ipost_charges.store_id', $request->store_id);
//                        $query->orWhere('ipost_charges.store_id', null);
                    })
//                    ->where('approved', 1)
                    ->where('status', 1)
                    ->orderBy('ipost_charges.store_id', 'desc')
                    ->get();
        } else {
            $charges = IpostCharge::where('product_category_id', $category_id)->where('status', 1)->where('approved', 1)->where('store_id', null)->get();
        }

        if (Auth::user()->hasRole('kam')) {
            return view('charges.new.v2.viewcharges', compact('product_category', 'charge_types', 'charges'));
        } else {
            return view('charges.new.v2.view', compact('product_category', 'charge_types', 'charges'));
        }
    }

    public function save(Request $request) {

        if (!Entrust::can('create-charge')) {
            abort(403);
        }

        if (!Entrust::can('edit-charge')) {
            abort(403);
        }

        $this->validate($request, [
            'product_category_id' => 'required',
            'charge_type' => 'required|in:Fixed,Weight Based',
            'store_id' => 'sometimes',
        ]);

        if ($request->charge_type == 'Fixed') {
            $this->validate($request, [
                'initial_charge' => 'required',
                'hub_transfer_charge' => 'required',
                'return_charge' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'min_weight' => 'array',
                'max_weight' => 'array',
                'initial_charge' => 'array',
                'hub_transfer_charge' => 'array',
                'return_charge' => 'array',
                'min_weight.*' => 'sometimes|numeric',
                'max_weight.*' => 'sometimes|numeric',
                'initial_charge.*' => 'sometimes|numeric',
                'hub_transfer_charge.*' => 'sometimes|numeric',
                'return_charge.*' => 'sometimes|numeric',
            ]);
        }
        try {
            DB::beginTransaction();
            $preCharge = IpostCharge::where('product_category_id', $request->product_category_id)->whereStatus(1)->first();
            if ($preCharge && !$request->has("store_id")) {
                IpostCharge::where('product_category_id', $request->product_category_id)->whereStatus(1)
                        ->update(['status' => 0]);
            }
            if ($request->charge_type == 'Fixed') {
                $charge = new IpostCharge();
                $charge->fill($request->all());

                if (Auth::user()->hasRole('salesteam')) {
                    $charge->status = '1';
                    $charge->approved = '0';
                } else {
                    $charge->approved_by = auth()->user()->id;
                }

                $charge->created_by = auth()->user()->id;
                $charge->updated_by = auth()->user()->id;
                $charge->created_at = date("Y-m-d H:i:s");
                $charge->save();
            } else {
                $min_weight = $request->min_weight;
                $max_weight = $request->max_weight;
                $initial_charge = $request->initial_charge;
                $hub_transfer_charge = $request->hub_transfer_charge;
                $return_charge = $request->return_charge;
                foreach ($min_weight as $index => $value) {
                    if (empty($value) || empty($max_weight[$index])) {
                        continue;
                    }
                    if ($value > $max_weight[$index]) {
                        return redirect()->back()->withErrors("Minimum value can't be greater than manximum.");
                    }
                    $ranges[$index]['min'] = $value;
                    $ranges[$index]['max'] = $max_weight[$index];
                }
                foreach ($min_weight as $index => $value) {
                    $duplicate = 0;
                    foreach ($ranges as $range) {
//                        echo $value . ' >= ' . $range['min'] . ' && ' . $value . ' <= ' . $range['max'] . '<br/>';
                        if ($value >= $range['min'] && $value <= $range['max']) {
                            $duplicate++;
//                            echo $duplicate . '<br/>';
                        }
                        if ($duplicate > 1) {
                            return redirect()->back()->withErrors('Range should not be conflicted.');
                        }
                    }
                    if (empty($value) || empty($max_weight[$index])) {
                        continue;
                    }
                    $charge = new IpostCharge();
                    $charge->product_category_id = $request->product_category_id;
                    $charge->charge_type = $request->charge_type;
                    if ($request->has("store_id")) {
                        $charge->store_id = $request->store_id;
                    }
                    $charge->min_weight = $value;
                    $charge->max_weight = $max_weight[$index];
                    $charge->initial_charge = $initial_charge[$index];
                    $charge->hub_transfer_charge = $hub_transfer_charge[$index];
                    $charge->return_charge = $return_charge[$index];

                    if (Auth::user()->hasRole('salesteam')) {
                        $charge->status = '1';
                        $charge->approved = '0';
                    } else {
                        $charge->approved_by = auth()->user()->id;
                    }

                    $charge->created_by = auth()->user()->id;
                    $charge->updated_by = auth()->user()->id;
                    $charge->created_at = date("Y-m-d H:i:s");
                    $charge->save();
                }
            }
            DB::commit();
            Session::flash('message', "Charge modified successfully");
            return Redirect::back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors("Something went wrong, please try again.");
        }
    }

    public function update(Request $request) {
        if (!Entrust::can('create-charge')) {
            abort(403);
        }

        if (!Entrust::can('edit-charge')) {
            abort(403);
        }

        $this->validate($request, [
            'product_category_id' => 'required',
            'charge_type' => 'required|in:Fixed,Weight Based',
            'store_id' => 'sometimes',
        ]);

        if ($request->charge_type == 'Fixed') {
            $this->validate($request, [
                'initial_charge' => 'required',
                'hub_transfer_charge' => 'required',
                'return_charge' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'ipost_charge_id' => 'sometimes|array',
                'min_weight' => 'array',
                'max_weight' => 'array',
                'initial_charge' => 'array',
                'hub_transfer_charge' => 'array',
                'return_charge' => 'array',
                'ipost_charge_id.*' => 'sometimes|exists:ipost_charges,id',
                'min_weight.*' => 'sometimes|numeric',
                'max_weight.*' => 'sometimes|numeric',
                'initial_charge.*' => 'sometimes|numeric',
                'hub_transfer_charge.*' => 'sometimes|numeric',
                'return_charge.*' => 'sometimes|numeric',
            ]);
        }
        try {
            DB::beginTransaction();

            if ($request->charge_type == 'Fixed') {
                $charge = IpostCharge::where('product_category_id', $request->product_category_id)->whereStatus(1)->first();

                if (Auth::user()->hasRole('salesteam')) {
                    if ($charge->initial_charge != $request->initial_charge || $charge->hub_transfer_charge != $request->hub_transfer_charge || $charge->return_charge != $request->return_charge) {
                        $charge->status = '1';
                        $charge->approved = '0';
                    }
                } else {
                    $charge->approved_by = auth()->user()->id;
                }
                $charge->fill($request->except('ipost_charge_id'));

                $charge->updated_by = auth()->user()->id;
                $charge->save();
            } else {
                $id = $request->ipost_charge_id;
                $min_weight = $request->min_weight;
                $max_weight = $request->max_weight;
                $initial_charge = $request->initial_charge;
                $hub_transfer_charge = $request->hub_transfer_charge;
                $return_charge = $request->return_charge;
                foreach ($min_weight as $index => $value) {
                    if (empty($value) || empty($max_weight[$index])) {
                        continue;
                    }
                    if ($value > $max_weight[$index]) {
                        return redirect()->back()->withErrors("Minimum value can't be greater than manximum.");
                    }
                    $ranges[$index]['min'] = $value;
                    $ranges[$index]['max'] = $max_weight[$index];
                }
                foreach ($min_weight as $index => $value) {
                    $duplicate = 0;
                    foreach ($ranges as $range) {
//                        echo $value . ' >= ' . $range['min'] . ' && ' . $value . ' <= ' . $range['max'] . '<br/>';
                        if ($value >= $range['min'] && $value <= $range['max']) {
                            $duplicate++;
//                            echo $duplicate . '<br/>';
                        }
                        if ($duplicate > 1) {
                            return redirect()->back()->withErrors('Range should not be conflicted.');
                        }
                    }
                    if (empty($value) || empty($max_weight[$index])) {
                        continue;
                    }

                    if (isset($id[$index])) {
                        $charge = IpostCharge::where('product_category_id', $request->product_category_id)->whereStatus(1)->find($id[$index]);
//                        dd($charge->min_weight . " != " . $value . "<br/>" . $charge->max_weight . " != " . $max_weight[$index] . "<br/>" . $charge->initial_charge . " != " . $initial_charge[$index] . "<br/>" . $charge->hub_transfer_charge . " != " . $hub_transfer_charge[$index] . "<br/>" . $charge->return_charge . " != " . $return_charge[$index]);
                        /** checking for changed value* */
                        if (Auth::user()->hasRole('salesteam')) {
                            if ($charge->min_weight != $value || $charge->max_weight != $max_weight[$index] || $charge->initial_charge != $initial_charge[$index] || $charge->hub_transfer_charge != $hub_transfer_charge[$index] || $charge->return_charge != $return_charge[$index]) {
                                $charge->status = '1';
                                $charge->approved = '0';
                            }
                        }
                    } else {
                        $charge = new IpostCharge();
                        if (Auth::user()->hasRole('salesteam')) {
                            $charge->status = '1';
                            $charge->approved = '0';
                        } else {
                            $charge->approved_by = auth()->user()->id;
                        }
                        $charge->created_at = date("Y-m-d H:i:s");
                    }

                    $charge->product_category_id = $request->product_category_id;
                    $charge->charge_type = $request->charge_type;
                    if ($request->has("store_id")) {
                        $charge->store_id = $request->store_id;
                    }

                    $charge->min_weight = $value;
                    $charge->max_weight = $max_weight[$index];
                    $charge->initial_charge = $initial_charge[$index];
                    $charge->hub_transfer_charge = $hub_transfer_charge[$index];
                    $charge->return_charge = $return_charge[$index];

                    $charge->created_by = auth()->user()->id;
                    $charge->updated_by = auth()->user()->id;
                    $charge->updated_at = date("Y-m-d H:i:s");
                    $charge->save();
                }
            }
            DB::commit();
            Session::flash('message', "Charge modified successfully");
            return Redirect::back();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors("Something went wrong, please try again.");
        }
    }

    public function remove($id) {
        if (!Entrust::can('view-charge')) {
            abort(403);
        }

        $charge = IpostCharge::findOrFail($id);
        $charge->status = '0';
        $charge->approved = '0';
        $charge->updated_by = auth()->user()->id;
        $charge->save();

        Session::flash('message', "Charge successfully removed.");
        return Redirect::back();
    }

    public function approveCharge() {
        $charges = IpostCharge::whereStatus(1)->whereApproved(0)->get();
        return view('charges.new.v2.approve-charge', compact('charges'));
    }

    public function approved($id) {
        if (auth()->user()->hasRole('superadministrator') || auth()->user()->hasRole('systemadministrator') || auth()->user()->hasRole('systemmoderator')) {
            $charge = IpostCharge::findOrFail($id);
            $charge->status = 1;
            $charge->approved = 1;
            $charge->approved_by = auth()->user()->id;
            $charge->save();

            Session::flash('message', "Charge approved successfully");
            return Redirect::back();
        } else {
            abort(403);
        }
    }

    public function approvedAll(Request $request) {
        if (auth()->user()->hasRole('superadministrator') || auth()->user()->hasRole('systemadministrator') || auth()->user()->hasRole('systemmoderator')) {

            $ids = explode(",", $request->charge_ids);
            if (count($ids) < 1) {
                return redirect()->back()->withErrors("Nothig to approved.");
            }
            try {
                DB::beginTransaction();
                foreach ($ids as $id) {
                    $charge = IpostCharge::find($id);
                    if (!$charge) {
                        return redirect()->back()->withErrors("Invalid Charge id: $id.");
                    }
                    $charge->status = 1;
                    $charge->approved = 1;
                    $charge->approved_by = auth()->user()->id;
                    $charge->save();
                }
                DB::commit();
                Session::flash('message', "Charges approved successfully");
                return Redirect::back();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->withErrors("Something went wrong, please try again.");
            }
        } else {
            abort(403);
        }
    }

}
