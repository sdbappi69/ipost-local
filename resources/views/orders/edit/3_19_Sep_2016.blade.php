<div class="row">
    <br>
    <div class="col-md-4">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green-haze bold uppercase">Shipping</span>
                    <span class="caption-helper">Information</span>
                </div>
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
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
                            <td>{{ $shipping_loc->country_title }}</td>
                        </tr>
                        <tr>
                            <th>State</th>
                            <td>:</td>
                            <td>{{ $shipping_loc->state_title }}</td>
                        </tr>
                        <tr>
                            <th>City</th>
                            <td>:</td>
                            <td>{{ $shipping_loc->city_title }}</td>
                        </tr>
                        <tr>
                            <th>Zone</th>
                            <td>:</td>
                            <td>{{ $shipping_loc->zone_title }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>:</td>
                            <td>{{ $order->delivery_address1 }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green-haze bold uppercase">Order</span>
                    <span class="caption-helper">{{ $charges->order_id }}</span>
                </div>
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                <div class="table-responsive">

                    {!! Form::model($order, array('url' => '/order/'.$order->id.'?step=complete', 'method' => 'put')) !!}

                        <table class="table">
                            <thead class="flip-content">
                                <th>Product</th>
                                <th class="numeric">Unit Price</th>
                                <th class="numeric">Quantity</th>
                                <th class="numeric">Total Price</th>
                                <th class="numeric">Delivery Cost</th>
                            </thead>
                            <tbody>
                                @foreach($charges->order_detail as $row)
                                    <tr>
                                        <td>{{ $row->product_title }}</td>
                                        <td class="numeric" style="text-align:right;">{{ $row->product_unit_price }}</td>
                                        <td class="numeric" style="text-align:right;">{{ $row->product_quantity }}</td>
                                        <td class="numeric" style="text-align:right;">{{ $row->product_total_price }}</td>
                                        <td class="numeric" style="text-align:right;">{{ $row->product_delivery_charge }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>Total</td>
                                    <td class="numeric" style="text-align:right; padding-right:5px;">{{ $charges->order_product_charge }}</td>
                                    <td class="numeric" style="text-align:right; padding-right:5px;">{{ $charges->order_delivery_charge }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>COD</td>
                                    <td style="padding-right:5px;">
                                        {!! Form::text('amount', $charges->order_product_charge, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                                        {!! Form::hidden('delivery_payment_amount', $charges->order_delivery_charge, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                                    </td>
                                    <td class="numeric" style="padding-right:5px;">
                                        {!! Form::checkbox('include_delivery', '1', true) !!}
                                        Include Delivery Charge
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        &nbsp;
                        <div class="row padding-top-10">
                            <a href="{{ URL::to('order/'.$id.'/edit?step=2') }}" class="btn default"> Back </a>
                            {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

</div>