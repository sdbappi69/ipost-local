<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Logistics</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="Logistics Project" />
        <meta content="" name="R&D, SSL Wireless" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{ secure_asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />

        <link href="{{ secure_asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

        <link href="{{ secure_asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{ secure_asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{ secure_asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{ secure_asset('assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('assets/layouts/layout/css/themes/darkblue.min.css') }}" rel="stylesheet" type="text/css" id="style_color" />
        <link href="{{ secure_asset('assets/layouts/layout/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Custom CSS -->
        <!-- <link href="{{ secure_asset('custom/css/theming_biddyut.css') }}" rel="stylesheet" type="text/css" /> -->
        <link href="{{ secure_asset('custom/css/animate.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('custom/css/common.css') }}" rel="stylesheet" type="text/css" />

        @yield('select2CSS')

        <!-- jQuery on Top -->
        <script src="{{ secure_asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
    </head>
    <!-- END HEAD -->
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
        <div class="page-wrapper">
            <div class="container"><h1 class="page-title" style="color: #fff;font-weight:600;"> SubOrder
                <small> {{ $suborders[0]->unique_suborder_id }}</small>
            </h1></div>

            <!-- END PAGE TITLE-->
            <!-- END PAGE HEADER-->
            <div>
                <div class="profile-content">
                    <div class="row">
                        <div class="container">
                            <div class="portlet light ">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__body">
                                        <div class="tab-content">
                                            @foreach($suborders as $i => $suborder)
                                            <div class="mt-element-step">
                                                <div class="row step-line">
                                                    <div class="col-md-3 mt-step-col first @if(iPostStatus($suborder->sub_order_status) >= 1) done @elseIf(iPostStatus($suborder->sub_order_status) == 1) active @endIf ">
                                                        <div class="mt-step-number bg-white">1</div>
                                                        <div class="mt-step-title uppercase fnt-grey-cascade">Order</div>
                                                        <div class="mt-step-content font-grey-cascade">Store: <a target="blank" href="{{ secure_url('store') }}/{{ $suborder->order->store->id }}">{{ $suborder->order->store->store_id }}</a></div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col @if(iPostStatus($suborder->sub_order_status) >= 2) done @elseIf(iPostStatus($suborder->sub_order_status) == 2) active @endIf ">
                                                        <div class="mt-step-number bg-white">2</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Assign Picker</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Rider is assigned
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col @if(iPostStatus($suborder->sub_order_status) >= 3) done @elseIf(iPostStatus($suborder->sub_order_status) == 3) active @endIf ">
                                                        <div class="mt-step-number bg-white">3</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Rider Confirmed</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Rider On The Way
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col last @if(iPostStatus($suborder->sub_order_status) >= 4) done @elseIf(iPostStatus($suborder->sub_order_status) == 4) active @endIf ">
                                                        <div class="mt-step-number bg-white">4</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Picked</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Product picked-up
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col first @if(iPostStatus($suborder->sub_order_status) >= 5) done @elseIf(iPostStatus($suborder->sub_order_status) == 5) active @endIf ">
                                                        <div class="mt-step-number bg-white">5</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Received</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Receive product at hub
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col @if(iPostStatus($suborder->sub_order_status) >= 6) done @elseIf(iPostStatus($suborder->sub_order_status) == 6) active @endIf ">
                                                        <div class="mt-step-number bg-white">6</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">In Transit</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Product on the way
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col @if(iPostStatus($suborder->sub_order_status) >= 7) done @elseIf(iPostStatus($suborder->sub_order_status) == 7) active @endIf ">
                                                        <div class="mt-step-number bg-white">7</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Destination</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Received at hub
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col last @if(iPostStatus($suborder->sub_order_status) >= 8) done @elseIf(iPostStatus($suborder->sub_order_status) == 8) active @endIf ">
                                                        <div class="mt-step-number bg-white">8</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Assign Delivery</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Delivery-man assigned
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col first @if(iPostStatus($suborder->sub_order_status) >= 9) done @elseIf(iPostStatus($suborder->sub_order_status) == 9) active @endIf ">
                                                        <div class="mt-step-number bg-white">9</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Rider Confirmed</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Rider On The Way
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col @if(iPostStatus($suborder->sub_order_status) >= 10) done @elseIf(iPostStatus($suborder->sub_order_status) == 10) active @endIf ">
                                                        <div class="mt-step-number bg-white">10</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Complete</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Process Complete
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-step-col last @if(iPostStatus($suborder->sub_order_status) >= 11) done @elseIf(iPostStatus($suborder->sub_order_status) == 11) active @endIf ">
                                                        <div class="mt-step-number bg-white">11</div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">Reconciliation</div>
                                                        <div class="mt-step-content font-grey-cascade">
                                                            Process Complete
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div>
                <div class="profile-content">
                    <div class="row">
                        <div class="container">
                            <div class="portlet light ">
                                <div class="portlet-title tabbable-line">
                                    <div class="caption caption-md">
                                        <i class="icon-globe theme-font hide"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">Details</span>
                                    </div>
                                    <ul class="nav nav-tabs">
                                        <!-- <li class="tab-btn info active">
                                            <a href="#history" data-toggle="tab">History</a>
                                        </li> -->
                                        <li class="tab-btn avatar active">
                                            <a href="#order_detail" data-toggle="tab">ORDER Detail</a>
                                        </li>
                                        <li class="tab-btn info ">
                                            <a href="#shiping_information" data-toggle="tab">SHIPPING Information</a>
                                        </li>
                                        <!-- <li class="tab-btn password">
                                            <a href="#payment_information" data-toggle="tab">PAYMENT Information</a>
                                        </li> -->
                                    </ul>
                                </div>
                                <div class="portlet-body">
                                    <div class="tab-content">
                                        <!-- PERSONAL INFO TAB -->

                                        <!-- CHANGE AVATAR TAB -->
                                        <div class="tab-pane active" id="order_detail">

                                            @if(isset($suborder->order->order_remarks))
                                            <div class="well">
                                                Remarks: {{ $suborder->order->order_remarks or '' }}
                                            </div>
                                            @endIf

                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead class="flip-content">
                                                    <th>AWB</th>
                                                    <th>Detail</th>
                                                    <th>Status</th>
                                                    <th>History</th>
                                                    <th>Picking</th>
                                                    <th>Delivery</th>
                                                    <th>Return</th>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($suborders as $row)

                                                        @if($row->sub_order_status != 0 && $row->parent_sub_order_id == 0)

                                                        <tr>
                                                            <td>{{ $row->unique_suborder_id }}</td>
                                                            <td>
                                                                @foreach($row->products as $row2)
                                                                {{ $row2->product_title }}<br>
                                                                Cat: {{ $row2->product_category->name }}<br>
                                                                Quantity: <b>{{ $row2->quantity }}</b><br>
                                                                Type: <b>@if($row->return == 1) Return
                                                                    @else Delivery @endIf</b>
                                                                <br>                                                                                                                    
                                                                @if($row->return == 0)
                                                                <h4><u>Pickup Location:</u></h4>
                                                                Name: {{ $row2->pickup_location->title }}
                                                                <br>
                                                                Phone: {{ $row2->pickup_location->msisdn }}, {{ $row2->pickup_location->alt_msisdn }}
                                                                <br>
                                                                Address: {{ $row2->pickup_location->address1 }}, {{ $row2->pickup_location->zone->name }}, {{ $row2->pickup_location->zone->city->name }}, {{ $row2->pickup_location->zone->city->state->name }}
                                                                @else
                                                                <h4><u>Pickup Location:</u></h4>
                                                                Name: {{ $row->order->delivery_name }}
                                                                <br>
                                                                Phone: {{ $row->order->delivery_msisdn }}, {{ $row->order->delivery_alt_msisdn }}
                                                                <br>
                                                                Address: {{ $row->order->delivery_address1 }}, {{ $row->order->delivery_zone->name }}, {{ $row->order->delivery_zone->city->name }}
                                                                @endif 
                                                                <br>
                                                                <h4><u>Payment:</u></h4>
                                                                Unit Price: {{ $row2->unit_price }}<br>
                                                                Total Product Charge: {{ $row2->sub_total }}<br>
{{--                                                                Unit Delivery Charge: {{ $row2->unit_deivery_charge }}<br>--}}
{{--                                                                Total Delivery Charge: {{ $row2->total_delivery_charge }}<br>--}}
                                                                Payable Amount: <b>{{ $row2->total_payable_amount }}</b><br>
                                                                Paid Amount: <b>{{ $row2->delivery_paid_amount }}</b>
                                                                <br>
{{--                                                                @if($row2->charge_details)--}}
                                                                @if(false)
                                                                <h4><u>Delivery Charge Details:</u></h4>
                                                                <?php
                                                                $charge = json_decode($row2->charge_details);
//                                                    dd($charge->trip_map);
                                                                ?>
                                                                Store: {{ $charge->store_id }}<br>
                                                                Charge Type: {{ $charge->charge_type }}<br>
                                                                Initial Charge: {{ $charge->initial_charge }}<br>
                                                                Hub Transfer Fee: {{ $charge->hub_transfer_charge }}<br>
                                                                Hub Transit No: {{ $charge->hub_transit }}<br>
                                                                Transit Hubs: <br>
                                                                @if(isset($charge->trip_map))
                                                                @foreach($charge->trip_map as $map)
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                {{ $map->start_hub or ''}} - {{ $map->end_hub or '' }}<br>
                                                                @endforeach
                                                                @endif

                                                                @if($charge->discount_id)
                                                                Discount Type: {{ $charge->discount_title }}<br>
                                                                Discount: {{ $charge->discount }}<br>
                                                                @endif
                                                                Total Quantity: {{ $charge->total_quantity }}<br>
                                                                Delivery Charge: {{ $charge->delivery_charge }}<br>
                                                                Total Delivery Charge: {{ $charge->final_delivery_charge }}<br>
                                                                @endif
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                <b>
                                                                    {{ $row->suborder_status->title }}
                                                                </b>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#history_{{ $row->unique_suborder_id }}" class="btn green">History</a>

                                                                <div class="modal fade" id="history_{{ $row->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content" style="padding: 15px;">
                                                                            <!-- <div class="table-responsive"> -->
                                                                            <table class="table table-striped table-bordered table-hover dt-responsive example0" width="100%">
                                                                                <thead>
                                                                                <th>History</th>
                                                                                <th>Date & Time</th>
                                                                                </thead>
                                                                                <tbody>

                                                                                    @if(count($row->child_sub_orders) > 0)

                                                                                    @foreach($row->child_sub_orders as $child_sub_order)

                                                                                    @foreach($child_sub_order->history as $val)

                                                                                    @if($val->type == 'child')

                                                                                    <tr>
                                                                                        <?php
                                                                                        $status_id = $row->sub_order_status;
                                                                                        ?>

                                                                                        <td>


                                                                                          {{$val->sub_order->unique_suborder_id}}: {{$val->text}} ({{$val->user->name}})
                                                                                            <?php
                                                                                              switch($val->sub_order_status){
                                                                                                  case 6:
                                                                                                      if($child_sub_order->picking_task && $child_sub_order->picking_task->reason){
                                                                                                          echo "<br><b>Reason:</b>" . $child_sub_order->picking_task->reason->reason;
                                                                                                      }
                                                                                                      break;
                                                                                                  case 33:
                                                                                                  case 40:
                                                                                                      if($child_sub_order->deliveryTask && $child_sub_order->deliveryTask->reason){
                                                                                                          echo "<br><b>Reason:</b>" . $child_sub_order->deliveryTask->reason->reason;
                                                                                                      }
                                                                                                      break;
                                                                                              }
                                                                                            ?>
                                                                                        </td>
                                                                                        <td>
                                                                                            {{ date("D, d M Y, h:i:s A", strtotime($val->created_at))}}
                                                                                        </td>
                                                                                    </tr>

                                                                                    @endIf

                                                                                    @endforeach

                                                                                    @endforeach

                                                                                    @endIf

                                                                                    @foreach($row->history as $val)

                                                                                    @if($val->type == 'parent')

                                                                                    <tr>
                                                                                        <?php
                                                                                        $status_id = $row->sub_order_status;
                                                                                        ?>

                                                                                        <td>
                                                                                          {{$val->sub_order->unique_suborder_id}}: {{$val->text}} ({{$val->user->name}})
                                                                                            <?php
                                                                                              switch($val->sub_order_status){
                                                                                                  case 6:
                                                                                                      if($row->picking_task && $row->picking_task->reason){
                                                                                                          echo "<br><b>Reason: </b>" . $row->picking_task->reason->reason;
                                                                                                      }
                                                                                                      break;
                                                                                                  case 33:
                                                                                                  case 40:
                                                                                                      if($row->deliveryTask && $row->deliveryTask->reason){
                                                                                                          echo "<br><b>Reason: </b>" . $row->deliveryTask->reason->reason;
                                                                                                      }
                                                                                                      break;
                                                                                              }
                                                                                            ?>
                                                                                        </td>
                                                                                        <td>
                                                                                            {{ date("D, d M Y, h:i:s A", strtotime($val->created_at))}}
                                                                                        </td>
                                                                                    </tr>

                                                                                    @endIf

                                                                                    @endforeach

                                                                                </tbody>

                                                                            </table>
                                                                            <!-- </div> -->
                                                                        </div>
                                                                    </div>
                                                            </td>
                                                            <td>

                                                                @foreach($suborders as $row)

                                                                @if(isset($row->product->pTask) && !is_null($row->product->pTask))

                                                                @if($row->product->pTask->type == 'Picking')

                                                                <a style="margin-bottom: 10px;" href="javascript:void(0)" data-toggle="modal" data-target="#picking_{{ $row->unique_suborder_id }}" class="btn yellow">{{ $row->unique_suborder_id }}</a>

                                                                <div class="modal fade" id="picking_{{ $row->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content" style="padding: 15px;">
                                                                            <table class="table table-striped table-bordered table-hover dt-responsive example0" width="100%">

                                                                                <tbody>

                                                                                    <tr>
                                                                                        <td>Status</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->status == 0)
                                                                                            Inactive
                                                                                            @elseIf($row->product->pTask->status == 1)
                                                                                            Pending
                                                                                            @elseIf($row->product->pTask->status == 2)
                                                                                            Success
                                                                                            @elseIf($row->product->pTask->status == 3)
                                                                                            Pending
                                                                                            @elseIf($row->product->pTask->status == 4)
                                                                                            Failed
                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Reason</td>
                                                                                        <td>{{ $row->product->pTask->reason->reason or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Remarks</td>
                                                                                        <td>{{ $row->product->pTask->remarks or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Picker</td>
                                                                                        <td>{{ $row->product->pTask->picker->name or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Consignment</td>
                                                                                        <td>{{ $row->product->pTask->consignment->consignment_unique_id or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Time</td>
                                                                                        <td>
                                                                                            {{ timeDifference($row->product->pTask->start_time, $row->product->pTask->end_time) }}
                                                                                            ({{ $row->product->pTask->start_time or '' }} to {{ $row->product->pTask->end_time or '' }})
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Location</td>
                                                                                        <td>
                                                                                            @if($row->product->pTask->start_lat != null && $row->product->pTask->start_long != null)
                                                                                            <a target="_blank" class="btn default" href="{{secure_url('maps/'.$row->product->pTask->start_lat.'/'.$row->product->pTask->start_long)}}">Here</a> to
                                                                                            @endIf
                                                                                            @if($row->product->pTask->end_lat != null && $row->product->pTask->end_long != null)
                                                                                            <a target="_blank" class="btn default" href="{{secure_url('maps/'.$row->product->pTask->end_lat.'/'.$row->product->pTask->end_long)}}">Here</a>
                                                                                            @endIf
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Reconcile with rider</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->reconcile == 0)
                                                                                            No
                                                                                            @elseIf($row->product->pTask->reconcile == 1)
                                                                                            Yes
                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Signature</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->signature != null)

                                                                                            <img height="100px;" src="{{ $row->product->pTask->signature }}">

                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Photo</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->image != null)

                                                                                            <img width="100%" src="{{ $row->product->pTask->image }}">

                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                </tbody>

                                                                            </table>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @endIf

                                                                @endIf

                                                                @endforeach

                                                            </td>
                                                            <td>

                                                                @foreach($suborders as $row)

                                                                @if($row->dTask)

                                                                <a style="margin-bottom: 10px;" href="javascript:void(0)" data-toggle="modal" data-target="#delivery_{{ $row->unique_suborder_id }}" class="btn yellow">{{ $row->unique_suborder_id }}</a>

                                                                <div class="modal fade" id="delivery_{{ $row->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content" style="padding: 15px;">
                                                                            <table class="table table-striped table-bordered table-hover dt-responsive example0" width="100%">

                                                                                <tbody>

                                                                                    <tr>
                                                                                        <td>Status</td>
                                                                                        <td>

                                                                                            @if($row->dTask->status == 0)
                                                                                            Inactive
                                                                                            @elseIf($row->dTask->status == 1)
                                                                                            Pending
                                                                                            @elseIf($row->dTask->status == 2)
                                                                                            Success
                                                                                            @elseIf($row->dTask->status == 3)
                                                                                            Pending
                                                                                            @elseIf($row->dTask->status == 4)
                                                                                            Failed
                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Reason</td>
                                                                                        <td>{{ $row->dTask->reason->reason or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Remarks</td>
                                                                                        <td>{{ $row->dTask->remarks or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Delivery man</td>
                                                                                        <td>{{ $row->dTask->deliveryman->name or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Consignment</td>
                                                                                        <td>{{ $row->dTask->consignment->consignment_unique_id or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Time</td>
                                                                                        <td>
                                                                                            {{ timeDifference($row->dTask->start_time, $row->dTask->end_time) }}
                                                                                            ({{ $row->dTask->start_time or '' }} to {{ $row->dTask->end_time or '' }})
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Location</td>
                                                                                        <td>
                                                                                            @if($row->dTask->start_lat != null && $row->dTask->start_long != null)
                                                                                            <a target="_blank" class="btn default" href="{{secure_url('maps/'.$row->dTask->start_lat.'/'.$row->dTask->start_long)}}">Here</a> to
                                                                                            @endIf
                                                                                            @if($row->dTask->end_lat != null && $row->dTask->end_long != null)
                                                                                            <a target="_blank" class="btn default" href="{{secure_url('maps/'.$row->dTask->end_lat.'/'.$row->dTask->end_long)}}">Here</a>
                                                                                            @endIf
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Reconcile with rider</td>
                                                                                        <td>

                                                                                            @if($row->dTask->reconcile == 0)
                                                                                            No
                                                                                            @elseIf($row->dTask->reconcile == 1)
                                                                                            Yes
                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Signature</td>
                                                                                        <td>

                                                                                            @if($row->dTask->signature != null)

                                                                                            <img height="100px;" src="{{ $row->dTask->signature }}">

                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Photo</td>
                                                                                        <td>

                                                                                            @if($row->dTask->image != null)

                                                                                            <img width="100%" src="{{ $row->dTask->image }}">

                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                </tbody>

                                                                            </table>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @endIf

                                                                @endforeach

                                                            </td>
                                                            <td>

                                                                @foreach($suborders as $row)

                                                                @if(isset($row->product->pTask) && count($row->product->pTask) > 0)

                                                                @if($row->product->pTask->type == 'Return')

                                                                <a style="margin-bottom: 10px;" href="javascript:void(0)" data-toggle="modal" data-target="#picking_{{ $row->unique_suborder_id }}" class="btn yellow">{{ $row->unique_suborder_id }}</a>

                                                                <div class="modal fade" id="picking_{{ $row->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content" style="padding: 15px;">
                                                                            <table class="table table-striped table-bordered table-hover dt-responsive example0" width="100%">

                                                                                <tbody>

                                                                                    <tr>
                                                                                        <td>Status</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->status == 0)
                                                                                            Inactive
                                                                                            @elseIf($row->product->pTask->status == 1)
                                                                                            Pending
                                                                                            @elseIf($row->product->pTask->status == 2)
                                                                                            Success
                                                                                            @elseIf($row->product->pTask->status == 3)
                                                                                            Pending
                                                                                            @elseIf($row->product->pTask->status == 4)
                                                                                            Failed
                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Reason</td>
                                                                                        <td>{{ $row->product->pTask->reason->reason or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Remarks</td>
                                                                                        <td>{{ $row->product->pTask->remarks or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Picker</td>
                                                                                        <td>{{ $row->product->pTask->picker->name or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Consignment</td>
                                                                                        <td>{{ $row->product->pTask->consignment->consignment_unique_id or '' }}</td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Time</td>
                                                                                        <td>
                                                                                            {{ timeDifference($row->product->pTask->start_time, $row->product->pTask->end_time) }}
                                                                                            ({{ $row->product->pTask->start_time or '' }} to {{ $row->product->pTask->end_time or '' }})
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Location</td>
                                                                                        <td>
                                                                                            @if($row->product->pTask->start_lat != null && $row->product->pTask->start_long != null)
                                                                                            <a target="_blank" class="btn default" href="{{secure_url('maps/'.$row->product->pTask->start_lat.'/'.$row->product->pTask->start_long)}}">Here</a> to
                                                                                            @endIf
                                                                                            @if($row->product->pTask->end_lat != null && $row->product->pTask->end_long != null)
                                                                                            <a target="_blank" class="btn default" href="{{secure_url('maps/'.$row->product->pTask->end_lat.'/'.$row->product->pTask->end_long)}}">Here</a>
                                                                                            @endIf
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Reconcile with rider</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->reconcile == 0)
                                                                                            No
                                                                                            @elseIf($row->product->pTask->reconcile == 1)
                                                                                            Yes
                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Signature</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->signature != null)

                                                                                            <img height="100px;" src="{{ $row->product->pTask->signature }}">

                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Photo</td>
                                                                                        <td>

                                                                                            @if($row->product->pTask->image != null)

                                                                                            <img width="100%" src="{{ $row->product->pTask->image }}">

                                                                                            @endIf

                                                                                        </td>
                                                                                    </tr>

                                                                                </tbody>

                                                                            </table>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @endIf

                                                                @endIf

                                                                @endforeach

                                                            </td>
                                                        </tr>

                                                        @endIf

                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <!-- END CHANGE AVATAR TAB -->

                                        <div class="tab-pane " id="shiping_information">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Name</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Mobile</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_msisdn }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Alt. Mobile</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_alt_msisdn }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Country</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_zone->city->state->country->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>State</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_zone->city->state->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>City</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_zone->city->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Zone</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_zone->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Address</th>
                                                        <td>:</td>
                                                        <td>{{ $suborder->order->delivery_address1 }}</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            @if(count($suborder->order->order_history) > 0)

                                            <h1>Previous shipping address</h1>

                                            @foreach($suborder->order->order_history AS $history)

                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Name</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Mobile</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_msisdn }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Alt. Mobile</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_alt_msisdn }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Country</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_zone->city->state->country->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>State</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_zone->city->state->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>City</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_zone->city->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Zone</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_zone->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Address</th>
                                                        <td>:</td>
                                                        <td>{{ $history->delivery_address1 }}</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            @endforeach

                                            @endIf

                                        </div>
                                        <!-- END PERSONAL INFO TAB -->


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PROFILE CONTENT -->
            </div>
            <div class="page-footer">
                <div class="page-footer-inner"> <?php echo date('Y'); ?> &copy; iPost System By
                    <a target="_blank" href="https://sslwireless.com">SSL Wireless</a>
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
        </div>
        <!--[if lt IE 9]>
<script src="{{ secure_asset('assets/global/plugins/respond.min.js') }}"></script>
<script src="{{ secure_asset('assets/global/plugins/excanvas.min.js') }}"></script>
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{ secure_asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ secure_asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/pages/scripts/table-datatables-responsive.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/global/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
        {{-- <script src="{{ secure_asset('assets/pages/scripts/dashboard.min.js') }}" type="text/javascript"></script> --}}
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="{{ secure_asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ secure_asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

        <script src="{{ secure_asset('assets/pages/scripts/ui-modals.min.js') }}" type="text/javascript"></script>

        <!-- Custom JS -->
        <script type="text/javascript">
        var site_path = "{{ secure_url('/') }}" + "/";
        </script>
        <script src="{{ secure_asset('custom/js/highlight-nav.js') }}" type="text/javascript"></script>
        <script src="{{ secure_asset('custom/js/location-list.js') }}" type="text/javascript"></script>
