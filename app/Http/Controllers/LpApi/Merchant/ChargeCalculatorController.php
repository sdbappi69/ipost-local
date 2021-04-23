<?php

namespace App\Http\Controllers\LpApi\Merchant;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use App\ProductCategory;
use App\OrderProduct;
use App\Charge;
use App\ChargeModel;
use App\Zone;
use Auth;
use Session;
use Redirect;
use Validator;
use DB;
use Log;

class ChargeCalculatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function charge_calculator(Request $request)
    {

        return (new ChargeClassController)->charge_calculator($request);

    }
}
