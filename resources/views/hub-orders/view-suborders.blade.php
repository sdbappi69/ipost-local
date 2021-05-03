@extends('layouts.appinside')

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ secure_url('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ secure_url('hub-order') }}">Orders</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>View</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Order
    <small> {{ $order->unique_order_id }}</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
<div>
    <div class="profile-content">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="kt-portlet">
                        <div class="kt-portlet__body">
                            <ul class="nav nav-tabs  nav-tabs-line" role="tablist">
                                @foreach($order->suborders as $i => $suborder)
                                <li class="nav-item" id="tab_li_{{$i}}">
                                    <a class="nav-link" data-toggle="tab" href="#kt_tabs_{{$i}}" role="tab" {{ $i == 0 ? "aria-expanded=true" : '' }}>{{ $suborder->unique_suborder_id }}</a>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($order->suborders as $i => $suborder)
                                <div class="tab-pane" id="kt_tabs_{{$i}}" role="tabpanel">
                                    <div class="mt-element-step">
                                        <div class="row step-line">
                                            <div class="col-md-3 mt-step-col first @if(iPostStatus($suborder->sub_order_status) >= 1) done @elseIf(iPostStatus($suborder->sub_order_status) == 1) active @endIf ">
                                                <div class="mt-step-number bg-white">1</div>
                                                <div class="mt-step-title uppercase font-grey-cascade">Order</div>
                                                <div class="mt-step-content font-grey-cascade">Store: <a target="blank" href="{{ secure_url('store') }}/{{ $order->store->id }}">{{ $order->store->store_id }}</a></div>
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
                                            @php
                                            $reconcile = 0;
                                            if($suborder->deliveryTask){
                                                $suborder->deliveryTask->reconcile ? $reconcile = 1 : null;
                                            }
                                            @endphp
                                            <div class="col-md-3 mt-step-col last @if(iPostStatus($suborder->sub_order_status) >= 11 || $reconcile == 1) done @elseIf(iPostStatus($suborder->sub_order_status) == 11 || $reconcile == 1) active @endIf ">
                                                <div class="mt-step-number bg-white">11</div>
                                                <div class="mt-step-title uppercase font-grey-cascade">Reconciliation</div>
                                                <div class="mt-step-content font-grey-cascade">
                                                    Process Complete
                                                </div>
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
            <div class="col-md-12">
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

                                @if(isset($order->order_remarks))
                                <div class="well">
                                    Remarks: {{ $order->order_remarks or '' }}
                                </div>
                                @endIf

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="flip-content">
                                        <th>AWB</th>
                                        <th>Detail</th>
                                        <th>Status</th>
                                        <th>History</th>
                                        </thead>
                                        <tbody>

                                            <?php $sub_orders_desc = $order->suborders()->orderBy('id', 'desc')->get(); ?>

                                            @foreach($order->suborders as $row)

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
                                                    Unit Price: {{ number_format($row2->unit_price) }}<br>
                                                    Total Product Charge: {{ number_format($row2->sub_total) }}<br>
{{--                                                    Unit Delivery Charge: {{ $row2->unit_deivery_charge }}<br>--}}
{{--                                                    Total Delivery Charge: {{ $row2->total_delivery_charge }}<br>--}}
                                                        @if($row->post_delivery_return == 1 || $row->return == 1 || $order->payment_type_id == 2)
                                                            Payable Amount: <b>0</b><br>
                                                            Paid Amount: <b>0</b>
                                                        @else
                                                        Payable Amount: <b>{{ number_format($row2->total_payable_amount) }}</b><br>
                                                        Paid Amount: <b>{{ number_format($row2->delivery_paid_amount) }}</b>
                                                        @endif
                                                    <br>
{{--                                                    @if($row2->charge_details)--}}
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
                                            <td>{{ $order->delivery_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_msisdn }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alt. Mobile</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_alt_msisdn }}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_zone->city->state->country->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>State</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_zone->city->state->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>City</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_zone->city->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Zone</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_zone->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>:</td>
                                            <td>{{ $order->delivery_address1 }}</td>
                                        </tr>
                                    </table>
                                </div>

                                @if(count($order->order_history) > 0)

                                <h1>Previous shipping address</h1>

                                @foreach($order->order_history AS $history)

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

<script type="text/javascript">
    $(document).ready(function () {
        // Navigation Highlight
        highlight_nav('orders', 'orders');
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#tab_li_0").addClass("active");
        $("#kt_tabs_0").addClass("active");
    });
</script>

@endsection
