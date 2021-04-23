<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\SubOrder;

/**
 * Description of SubOrderDetailsController
 *
 * @author johnny
 */
class SubOrderDetailsController extends Controller {

    public function subOrderDetails($suborderUniqueId) {
        $suborders = SubOrder::whereUniqueSuborderId($suborderUniqueId)->get(); // for using pre-created view file
        if ($suborders->count() < 1) {
            abort(404);
        }
//        dd($suborders);
        return view('orders.view-suborders', compact("suborders"));
    }

}
