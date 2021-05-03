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

<div class="mt-element-step">
    <div class="row step-line">
        <div class="col-md-3 mt-step-col first @if($order->order_status >= 1) done @elseIf($order->order_status == 1) active @endIf ">
            <div class="mt-step-number bg-white">1</div>
            <div class="mt-step-title uppercase font-grey-cascade">Order</div>
            <div class="mt-step-content font-grey-cascade">Store: <a target="blank" href="{{ secure_url('store') }}/{{ $order->store->id }}">{{ $order->store->store_id }}</a></div>
        </div>
        <div class="col-md-3 mt-step-col @if($order->order_status >= 2) done @elseIf($order->order_status == 2) active @endIf ">
            <div class="mt-step-number bg-white">2</div>
            <div class="mt-step-title uppercase font-grey-cascade">Assign Picker</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->hub_id == null)                
                A rider is assigned
                @else
                Responsible hub: <a target="blank" href="{{ secure_url('hub') }}/{{ $order->hub->id }}">{{ $order->hub->title }}</a>
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col @if($order->order_status >= 3) done @elseIf($order->order_status == 3) active @endIf ">
            <div class="mt-step-number bg-white">3</div>
            <div class="mt-step-title uppercase font-grey-cascade">Rider Confirmed</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->hub_id == null)
                Rider On The Way
                @else
                Responsible hub: <a target="blank" href="{{ secure_url('hub') }}/{{ $order->hub->id }}">{{ $order->hub->title }}</a>
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col last @if($order->order_status >= 4) done @elseIf($order->order_status == 4) active @endIf ">
            <div class="mt-step-number bg-white">4</div>
            <div class="mt-step-title uppercase font-grey-cascade">Picked</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 4)
                Product picked-up
                @else
                Product picked-up
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col first @if($order->order_status >= 5) done @elseIf($order->order_status == 5) active @endIf ">
            <div class="mt-step-number bg-white">5</div>
            <div class="mt-step-title uppercase font-grey-cascade">Received</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 5)
                Product received from rider
                @else
                Receive product at hub
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col @if($order->order_status >= 6) done @elseIf($order->order_status == 6) active @endIf ">
            <div class="mt-step-number bg-white">6</div>
            <div class="mt-step-title uppercase font-grey-cascade">In Transit</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 6)
                Product on the way
                @else
                Product on the way
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col @if($order->order_status >= 7) done @elseIf($order->order_status == 7) active @endIf ">
            <div class="mt-step-number bg-white">7</div>
            <div class="mt-step-title uppercase font-grey-cascade">Destination</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 7)
                Responsible hub: <a target="blank" href="{{ secure_url('hub') }}/{{ $order->delivery_zone->hub->id }}">{{ $order->delivery_zone->hub->title }}</a>
                @else
                Received at destination hub
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col last @if($order->order_status >= 8) done @elseIf($order->order_status == 8) active @endIf ">
            <div class="mt-step-number bg-white">8</div>
            <div class="mt-step-title uppercase font-grey-cascade">Assign Delivery</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 8)
                Responsible hub: <a target="blank" href="{{ secure_url('hub') }}/{{ $order->delivery_zone->hub->id }}">{{ $order->delivery_zone->hub->title }}</a>
                @else
                Delivery-man assigned
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col first @if($order->order_status >= 9) done @elseIf($order->order_status == 9) active @endIf ">
            <div class="mt-step-number bg-white">9</div>
            <div class="mt-step-title uppercase font-grey-cascade">Rider Confirmed</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 9)
                Responsible hub: <a target="blank" href="{{ secure_url('hub') }}/{{ $order->delivery_zone->hub->id }}">{{ $order->delivery_zone->hub->title }}</a>
                @else
                Rider On The Way
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col first @if($order->order_status >= 9) done @elseIf($order->order_status == 9) active @endIf ">
            <div class="mt-step-number bg-white">10</div>
            <div class="mt-step-title uppercase font-grey-cascade">Complete</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 9)
                Responsible hub: <a target="blank" href="{{ secure_url('hub') }}/{{ $order->delivery_zone->hub->id }}">{{ $order->delivery_zone->hub->title }}</a>
                @else
                Process Complete
                @endIf
            </div>
        </div>
        <div class="col-md-3 mt-step-col last @if($order->order_status >= 10) done @elseIf($order->order_status == 10) active @endIf ">
            <div class="mt-step-number bg-white">11</div>
            <div class="mt-step-title uppercase font-grey-cascade">Reconciliation</div>
            <div class="mt-step-content font-grey-cascade">
                @if($order->order_status >= 9)
                <!-- <a href="#">Delivery documents</a> -->
                    Process Complete
                @else
                    Process Pending
                @endIf
            </div>
        </div>
    </div>
</div>
{{-- aaa --}}
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
                                <th>Picking</th>
                                <th>Delivery</th>
                                <th>Return</th>
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
                                                    <h4><u>Pickup Location:</u></h4>
                                                    Name: {{ $row2->pickup_location->title }}
                                                    <br>
                                                    Phone: {{ $row2->pickup_location->msisdn }}, {{ $row2->pickup_location->alt_msisdn }}
                                                    <br>
                                                    Address: {{ $row2->pickup_location->address1 }}, {{ $row2->pickup_location->zone->name }}, {{ $row2->pickup_location->zone->city->name }}, {{ $row2->pickup_location->zone->city->state->name }}
                                                    <br>
                                                    <h4><u>Payment:</u></h4>
                                                    Unit Price: {{ $row2->unit_price }}<br>
                                                    Total Product Charge: {{ $row2->sub_total }}<br>
                                                    Unit Delivery Charge: {{ $row2->unit_deivery_charge }}<br>
                                                    Total Delivery Charge: {{ $row2->total_delivery_charge }}<br>
                                                    Payable Amount: <b>{{ $row2->total_payable_amount }}</b><br>
                                                    Paid Amount: <b>{{ $row2->delivery_paid_amount }}</b>
                                                    <br>
                                                    @if($row2->charge_details)
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
                                                    @if($row->sub_order_last_status === NULL)
                                                        {{ hubGetStatus($row->sub_order_status) }}
                                                    @else
                                                        {{ hubGetStatus($row->sub_order_last_status) }}
                                                    @endIf
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
                                                                                            $status_id = hubGetRealStatusId($val->text);
                                                                                            $pickingArray = array(3,4,5,6,7,8);
                                                                                            $deliveryArray = array(28,29,30,31,32,33);
                                                                                        ?>

                                                                                        <td>
                                                                                            @if(in_array($status_id, $pickingArray))

                                                                                                {{$val->sub_order->unique_suborder_id}}: {{$val->text}}

                                                                                                @if($val->text == 'Pick up failed' || $val->text == 'Products Delivery Failed' || $val->text == 'Product return failed')

                                                                                                    @if(isset($val->sub_order->product->pTask->reason->reason))
                                                                                                        <br><b>Reason:</b> {{ $val->sub_order->product->pTask->reason->reason }}
                                                                                                    @endIf

                                                                                                @endIf

                                                                                            @elseIf(in_array($status_id, $deliveryArray))

                                                                                                {{$val->sub_order->unique_suborder_id}}: {{$val->text}}

                                                                                                @if($val->text == 'Pick up failed' || $val->text == 'Products Delivery Failed' || $val->text == 'Product return failed')

                                                                                                    @if(isset($val->sub_order->dTask->reason->reason))
                                                                                                        <br><b>Reason:</b> {{ $val->sub_order->dTask->reason->reason }}
                                                                                                    @endIf

                                                                                                @endIf

                                                                                            @else
                                                                                                {{$val->sub_order->unique_suborder_id}}: {{$val->text}} ({{$val->user->name}})
                                                                                            @endIf
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
                                                                                    $status_id = hubGetRealStatusId($val->text);
                                                                                    $pickingArray = array(3,4,5,6,7,8);
                                                                                    $deliveryArray = array(28,29,30,31,32,33);
                                                                                ?>

                                                                                <td>
                                                                                    @if(in_array($status_id, $pickingArray))

                                                                                        {{$val->sub_order->unique_suborder_id}}: {{$val->text}}

                                                                                        @if($val->text == 'Pick up failed' || $val->text == 'Products Delivery Failed' || $val->text == 'Product return failed')

                                                                                            @if(isset($val->sub_order->product->pTask->reason->reason))
                                                                                                <br><b>Reason:</b> {{ $val->sub_order->product->pTask->reason->reason }}
                                                                                            @endIf

                                                                                        @endIf

                                                                                    @elseIf(in_array($status_id, $deliveryArray))

                                                                                        {{$val->sub_order->unique_suborder_id}}: {{$val->text}}

                                                                                        @if($val->text == 'Pick up failed' || $val->text == 'Products Delivery Failed' || $val->text == 'Product return failed')

                                                                                            @if(isset($val->sub_order->dTask->reason->reason))
                                                                                                <br><b>Reason:</b> {{ $val->sub_order->dTask->reason->reason }}
                                                                                            @endIf

                                                                                        @endIf

                                                                                    @else
                                                                                        {{$val->sub_order->unique_suborder_id}}: {{$val->text}} ({{$val->user->name}})
                                                                                    @endIf
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

                                                @foreach($sub_orders_desc as $row)

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

                                                @foreach($sub_orders_desc as $row)

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

                                                @foreach($sub_orders_desc as $row)

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
                
                <!-- CHANGE PASSWORD TAB -->
                <!-- <div class="tab-pane" id="payment_information">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="flip-content">
                                <th>Amount</th>
                                <th>Delivery Charge</th>
                                <th>COD</th>
                            </thead>
                            <tr>
                                <td>{{ $order->total_product_price }}</td>
                                <td>{{ $order->delivery_payment_amount }}</td>
                                <td>
                                    {{ $order->total_amount }}
                                    @if($order->delivery_pay_by_cus == 1)
                                    <p>Including delivery charge</p>
                                    @else
                                    <p>Excluding delivery charge</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div> -->

                <!-- END CHANGE PASSWORD TAB -->
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!-- END PROFILE CONTENT -->
</div>

<script type="text/javascript">
    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('orders', 'orders');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            // $('.example0').dataTable( {
            //     "bPaginate": true,
            //     "bFilter": true,
            //     "bSort": false,
            //     "bInfo": true
            // } );
        } );
    </script>

    @endsection
