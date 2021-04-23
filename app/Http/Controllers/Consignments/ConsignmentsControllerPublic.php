<?php

namespace App\Http\Controllers\Consignments;

use App\ConsignmentCommon;
use App\ConsignmentTask;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsTrait;

use App\User;
use DB;

use Illuminate\Support\Facades\Log;
use PDF;
use App\OrderProduct;
use App\Consignment;
use App\PickingTask;
use App\DeliveryTask;
use App\Order;
use Validator;
use App\Http\Traits\LogsTrait;
use Session;
use App\SubOrder;
use mPDF;


class ConsignmentsControllerPublic extends Controller
{
    use LogsTrait;
    use SmsTrait;

    //
    public function __construct()
    {
        // $this->middleware('role:superadministrator|systemadministrator|systemmoderator|hubmanager');
        // $this->middleware('role:hubmanager|inboundmanager|vehiclemanager');
    }

    public function common_awb_single_public($unique_suborder_id)
    {

        $sub_order = SubOrder::whereStatus(true)
            ->where('unique_suborder_id', $unique_suborder_id)
            ->first();
        $pdf = PDF::loadView('consignments.pdf.sub_order-common-single-awb', ['sub_order' => $sub_order])->setPaper([0, 0, 250, 750], 'portrait');

        return $pdf->stream($sub_order->unique_suborder_id . '_awb.pdf');

    }

}
