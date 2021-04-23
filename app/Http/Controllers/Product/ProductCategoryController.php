<?php

namespace App\Http\Controllers\Product;

use App\ProductCategoryVehicleType;
use App\VehicleType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Datatables;
use App\Role;
use App\ProductCategory;
use App\Charge;
use Illuminate\Support\Facades\Log;
use Validator;
use Session;
use Redirect;
use Image;
use DB;
use Auth;
use Entrust;

class ProductCategoryController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadministrator|systemadministrator|systemmoderator|salesteam|coo|saleshead|operationmanager|operationalhead|kam');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Entrust::can('view-product-category')) {
            abort(403);
        }

        $req = $request->all();
        $query = ProductCategory::select('*');

        ($request->has('name')) ? $query->where('name', 'like', trim($request->name) . "%") : null;
        ($request->has('category_type')) ? $query->where('category_type', trim($request->category_type)) : null;
        ($request->has('parent_category_id')) ? $query->where('parent_category_id', trim($request->parent_category_id)) : null;
        ($request->has('status')) ? $query->where('status', trim($request->status)) : null;

        $product_category = $query->orderBy('category_type', 'desc')->paginate(30);
        $parent_cat = ProductCategory::whereStatus(true)->where('category_type', 'parent')->lists('name', 'id')->toArray();
        $all_cat = ProductCategory::select('name')->get();

        return view('product-category.index', compact('product_category', 'req', 'parent_cat', 'all_cat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Entrust::can('create-product-category')) {
            abort(403);
        }

        $categories = ProductCategory::whereStatus(true)->where('category_type', '=', 'parent')->lists('name', 'id')->toArray();
        // $categories = ProductCategory::whereStatus(true)->where('category_type', '=', 'parent')->where('parent_category_id', '=', null)->lists('name', 'id')->toArray();
        $vehicleTypes = VehicleType::select('title', 'id')->whereStatus(1)->pluck('title', 'id')->toArray();

        return view('product-category.insert', compact('categories', 'vehicleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Entrust::can('create-product-category')) {
            abort(403);
        }
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'category_type' => 'required',
            'parent_category_id' => 'sometimes',
            'vehicle_type_id' => 'sometimes|array',
            'vehicle_type_id.*' => 'exists:vehicle_types,id',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();
            // Insert product on rack
            $product_category = new ProductCategory();
            $product_category->name = $request->name;
            $product_category->status = '1';
            $product_category->created_by = auth()->user()->id;
            $product_category->updated_by = auth()->user()->id;
            $product_category->save();

            $category = ProductCategory::findOrFail($product_category->id);

            if ($request->category_type == 'individual') {
                $category->category_type = 'parent';
                $category->parent_category_id = $product_category->id;
            } else if ($request->category_type == 'parent') {
                $category->category_type = 'parent';
            } else {
                $category->category_type = $request->category_type;
                $category->parent_category_id = $request->parent_category_id;
            }
            $category->save();

            if (count($request->vehicle_type_id) > 0) {
                foreach ($request->vehicle_type_id as $vehichle_type_id) {
                    $productCategoryVehicleType = new ProductCategoryVehicleType();
                    $productCategoryVehicleType->product_category_id = $category->id;
                    $productCategoryVehicleType->vehicle_type_id = $vehichle_type_id;
                    $productCategoryVehicleType->save();
                }
            }
            DB::commit();
            $message = "Product category created successfully.";
            Session::flash('message', $message);
            return redirect('/product-category');
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return redirect()->back()->withErrors('Something went wrong, please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Entrust::can('view-product-category')) {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // return 0;
        if (!Entrust::can('edit-product-category')) {
            abort(403);
        }

        $detail = ProductCategory::where('id', '=', $id)->first();
        $categoryVehicles = $detail->vehicles->map(function ($q) {
            return $q->vehicle_type_id;
        })->toArray();
        // $categories = ProductCategory::whereStatus(true)->where('category_type', '=', 'parent')->where('parent_category_id', '=', null)->lists('name', 'id')->toArray();
        $categories = ProductCategory::whereStatus(true)->where('category_type', '=', 'parent')->lists('name', 'id')->toArray();
        $vehicleTypes = VehicleType::select('title', 'id')->whereStatus(1)->pluck('title', 'id')->toArray();

        return view('product-category.edit', compact('categories', 'detail', 'vehicleTypes', 'categoryVehicles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Entrust::can('edit-product-category')) {
            abort(403);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'category_type' => 'required',
            'parent_category_id' => 'sometimes',
            'vehicle_type_id' => 'sometimes|array',
            'vehicle_type_id.*' => 'exists:vehicle_types,id',
        ]);

        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        }
        try {
            DB::beginTransaction();
            $category = ProductCategory::findOrFail($id);
            $category->fill($request->except(['vehicle_type_id']));
            $category->updated_by = auth()->user()->id;
            $category->save();

            if (count($request->vehicle_type_id) > 0) {
                foreach ($request->vehicle_type_id as $vehichle_type_id) {
                    $productCategoryVehicleType = new ProductCategoryVehicleType();
                    $productCategoryVehicleType->product_category_id = $category->id;
                    $productCategoryVehicleType->vehicle_type_id = $vehichle_type_id;
                    $productCategoryVehicleType->save();
                }
            }
            DB::commit();
            Session::flash('message', "Product Category updated successfully");
            return redirect('/product-category');
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception);
            return redirect()->back()->withErrors('Something went wrong, please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
