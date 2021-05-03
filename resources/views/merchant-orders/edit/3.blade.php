<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<div class="row">
    <br>

    <div class="col-md-12">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green-haze bold uppercase">Order</span>
                    <span class="caption-helper">{{ $order->unique_order_id }}</span>
                </div>
                @if(count($order->cart_products) > 0)
                <div class="caption pull-right" style="float: right">
                    @if($order->as_package == 0)
                    {!! Form::checkbox('as_package', '1', false, ['id' => 'as_package_control']) !!} Create as package
                    @elseIf($order->as_package == 1)
                    {!! Form::checkbox('as_package', '1', true, ['id' => 'as_package_control']) !!} Create as package
                    @endIf
                </div>
                @endif
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                <div class="table-responsive cart_products">
                    @if(count($order->cart_products) == 0)
                    No Product Added Yet.
                    @endif
                    {!! Form::model($order, array('url' => secure_url('') . '/merchant-order/'.$order->id.'?step=complete', 'method' => 'put')) !!}

                        {!! Form::hidden('as_package', null, ['class' => 'as_package']) !!}

                        <table class="table" @if(count($order->cart_products) == 0) style="display: none;" @endif >
                            <thead class="flip-content">
                                <th>Product</th>
                                <th>Category</th>
                                <th>Unit Price</th>
                                <th>Unit Delivery Cost</th>
                                <th>Discount</th>
                                <th>Final Delivery Cost</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Total Delivery Cost</th>
                            </thead>
                            <tbody>
                                {{-- */ $total_product_price = 0; /* --}}
                                {{-- */ $total_delivery_charge = 0; /* --}}
                                {{-- */ $total_width = 0; /* --}}
                                {{-- */ $total_height = 0; /* --}}
                                {{-- */ $total_length = 0; /* --}}
                                {{-- */ $total_weight = 0; /* --}}
                                @foreach($order->cart_products as $row)
                                    <tr>
                                        <td>{{ $row->product_title }}</td>
                                        <td>{{ $row->product_category->name }}</td>
                                        <td>{{ $row->unit_price }}</td>
                                        <td>{{ $row->discount_log->unit_actual_charge or $row->unit_deivery_charge }}</td>
                                        <td>
                                            {{ $row->discount_log->unit_discount or 0 }}
                                            <br>
                                            <span style="font-size: 10px; color: green">
                                                {{ $row->discount_log->discount->discount_title or '' }}
                                            </span>
                                        </td>
                                        <td>{{ $row->discount_log->unit_payable_charge or $row->unit_deivery_charge }}</td>
                                        <td>{{ $row->quantity }}</td>
                                        <td>{{ $row->sub_total }}</td>
                                        <td>{{ $row->total_delivery_charge }}</td>
                                    </tr>
                                    {{-- */ $total_product_price = $total_product_price + $row->sub_total; /* --}}
                                    {{-- */ $total_delivery_charge = $total_delivery_charge + $row->total_delivery_charge; /* --}}
                                    {{-- */ $total_width = $total_width + $row->width; /* --}}
                                    {{-- */ $total_height = $total_height + $row->height; /* --}}
                                    {{-- */ $total_length = $total_length + $row->length; /* --}}
                                    {{-- */ $total_weight = $total_weight + ($row->weight * $row->quantity); /* --}}
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Total</td>
                                    <td style="padding:8px;">{{ $total_product_price }}</td>
                                    <td style="padding:8px;">{{ $total_delivery_charge }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Collectable amount</td>
                                    <td style="padding:8px;">
                                        {!! Form::text('amount', $total_product_price, ['class' => 'form-control', 'placeholder' => 'BDT', 'required' => 'required', 'id' => 'amount']) !!}
                                        {!! Form::hidden('delivery_payment_amount', $total_delivery_charge, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'delivery_payment_amount']) !!}
                                        {!! Form::hidden('amount_hidden', $total_product_price, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'order_product_charge']) !!}
                                    </td>
                                    <td style="padding:8px;">
                                        @if($order->delivery_pay_by_cus == '1')
                                            {!! Form::checkbox('include_delivery', '1', true, ['id' => 'include_delivery']) !!}
                                        @else
                                            {!! Form::checkbox('include_delivery', '1', false, ['id' => 'include_delivery']) !!}
                                        @endif
                                        Customer will pay the delivery charge
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{!! Form::hidden('total_amount', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'total_amount']) !!}</td>
                                    <td>Total Collectable amount</td>
                                    <td style="padding:8px;" id="total_collectable_amount"></td>
                                    <td>BDT</td>
                                </tr>
                            </tfoot>
                        </table>

                        &nbsp;
                        <div class="row padding-top-10">
                            <a href="{{ secure_url('merchant-order/'.$id.'/edit?step=2') }}" class="btn default"> Back </a>
                             @if(count($order->cart_products) > 0)
                            {!! Form::submit('Confirm', ['class' => 'btn green pull-right']) !!}
                            @endif
                        </div>

                    {!! Form::close() !!}

                </div>

                <div class="table-responsive order_products">

                    {!! Form::model($order, array('url' => secure_url('') . '/merchant-order/'.$order->id.'?step=complete', 'method' => 'put')) !!}

                        {!! Form::hidden('as_package', null, ['class' => 'as_package']) !!}
                        @if(count($order->cart_products) > 0)
                        <table class="table">
                            <thead class="flip-content">
                                <!-- <th>Width (CM)</th>
                                <th>Height (CM)</th>
                                <th>Length (CM)</th> -->
                                <th>Weight (KG)</th>
                                <th>Price (BDT)</th>
                                <th>Unit Delivery Cost</th>
                                <th>Discount</th>
                                <th>Final Delivery Cost</th>
                                <th>Pickup detail</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- <td>{{ $total_width }}</td>
                                    <td>{{ $total_height }}</td>
                                    <td>{{ $total_length }}</td> -->
                                    <td>{{ $total_weight }}</td>
                                    <td>{{ $total_product_price }}</td>
                                    <td class="actual_delivery_charge">Select Warehouse</td>
                                    <td class="discount_delivery_charge">Select Warehouse</td>
                                    <td class="total_delivery_charge">Select Warehouse</td>
                                    <td>
                                        <label class="control-label">Warehouse</label>
                                        {!! Form::select('pickup_location_id', array(''=>'Select Warehouse')+$warehouse, $pickup_location_id, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'pickup_location_id']) !!}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <!-- <td></td> -->
                                    <td></td>
                                    <td></td>
                                    <td>Collectable amount</td>
                                    <td style="padding:8px;">
                                        {!! Form::text('amount', $total_product_price, ['class' => 'form-control', 'required' => 'required', 'id' => 'amount_package']) !!}
                                        {!! Form::hidden('delivery_payment_amount', $total_delivery_charge, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'delivery_payment_amount_package']) !!}
                                        {!! Form::hidden('amount_hidden', $total_product_price, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'order_product_charge_package']) !!}

                                        {!! Form::hidden('weight', $total_weight, ['class' => 'form-control', 'placeholder' => 'weight', 'required' => 'required']) !!}
                                        {!! Form::hidden('width', $total_width, ['class' => 'form-control', 'placeholder' => 'width', 'required' => 'required']) !!}
                                        {!! Form::hidden('height', $total_height, ['class' => 'form-control', 'placeholder' => 'height', 'required' => 'required']) !!}
                                        {!! Form::hidden('length', $total_length, ['class' => 'form-control', 'placeholder' => 'length', 'required' => 'required']) !!}

                                        {!! Form::hidden('delivery_discount_id', null, ['class' => 'form-control', 'id' => 'delivery_discount_id', 'required' => 'required']) !!}
                                        {!! Form::hidden('product_actual_unit_delivery_charge', null, ['class' => 'form-control', 'id' => 'product_actual_unit_delivery_charge', 'required' => 'required']) !!}
                                        {!! Form::hidden('product_unit_discount', null, ['class' => 'form-control', 'id' => 'product_unit_discount', 'required' => 'required']) !!}
                                        {!! Form::hidden('product_unit_delivery_charge', null, ['class' => 'form-control', 'id' => 'product_unit_delivery_charge', 'required' => 'required']) !!}
                                        {!! Form::hidden('product_actual_delivery_charge', null, ['class' => 'form-control', 'id' => 'product_actual_delivery_charge', 'required' => 'required']) !!}
                                        {!! Form::hidden('product_discount', null, ['class' => 'form-control', 'id' => 'product_discount', 'required' => 'required']) !!}
                                        {!! Form::hidden('product_delivery_charge', null, ['class' => 'form-control', 'id' => 'product_delivery_charge', 'required' => 'required']) !!}
                                    </td>
                                    <td style="padding:8px;">
                                        {!! Form::checkbox('include_delivery', '1', false, ['id' => 'include_delivery_package']) !!}
                                        Customer will pay the delivery charge
                                    </td>
                                    <td>
                                        <label class="control-label">Pick Date</label>

                                        <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" data-date-start-date="+0d">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                            {!! Form::text('picking_date', $picking_date, ['class' => 'form-control picking_date', 'required' => 'required', 'readonly' => 'true', 'id' => 'picking_date']) !!}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- <td></td> -->
                                    <td></td>
                                    <td></td>
                                    <td>
                                        Total Collectable amount
                                        {!! Form::hidden('total_amount', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'total_amount_package']) !!}
                                    </td>
                                    <td style="padding:8px;" id="total_collectable_amount_package"></td>
                                    <td></td>
                                    <td>
                                        <label class="control-label">Pick Time</label>
                                        {!! Form::select('picking_time_slot_id', array(''=>'Select Pick Time')+$picking_time_slot, $picking_time_slot_id, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'picking_time_slot_id']) !!}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        @endif
                        &nbsp;
                        <div class="row padding-top-10">
                            <a href="{{ secure_url('merchant-order/'.$id.'/edit?step=2') }}" class="btn default"> Back </a>
                            @if(count($order->cart_products) > 0)
                            {!! Form::submit('Confirm', ['class' => 'btn green pull-right']) !!}
                            @endif
                        </div>

                    {!! Form::close() !!}

                </div>

            </div>
        </div>
    </div>

    <script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

    <script src="{{ secure_asset('custom/js/date-time.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('custom/js/charge.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

        function collectableAmount(amount, delivery_payment_amount, include_delivery){
            if(include_delivery == 1){
                var newAmount = Number(amount) + Number(delivery_payment_amount);
            }else{
                var newAmount = amount;
            }
            $('#total_collectable_amount').html(newAmount);
            $('#total_amount').val(newAmount);
        }

        function collectableAmount_package(amount, delivery_payment_amount, include_delivery){
            if(include_delivery == 1){
                var newAmount = Number(amount) + Number(delivery_payment_amount);
            }else{
                var newAmount = amount;
            }
            $('#total_collectable_amount_package').html(newAmount);
            $('#total_amount_package').val(newAmount);
        }

        function asPackageAction(){
            var as_package_control = $('#as_package_control:checkbox:checked').val();
            // alert(as_package_control);
            if(as_package_control == 1){
                var as_package = 1;
                $('.cart_products').hide(5);
                $('.order_products').show(5);
            }else{
                var as_package = 0;
                $('.order_products').hide(5);
                $('.cart_products').show(5);
            }
            $('.as_package').val(as_package);
        }

        $("#as_package_control").change(function(){
            asPackageAction();
        });

        $(document ).ready(function() {
            collectableAmount($('#order_product_charge').val(), $('#delivery_payment_amount').val(), $('#include_delivery:checkbox:checked').val());

            if($('#pickup_location_id').val() != ''){
                collectableAmount_package($('#order_product_charge_package').val(), $('#delivery_payment_amount_package').val(), $('#include_delivery_package:checkbox:checked').val());
            }else{
                collectableAmount_package($('#order_product_charge_package').val(), 0, $('#include_delivery_package:checkbox:checked').val());
            }

            asPackageAction();
        });

        $("#include_delivery").change(function(){
            collectableAmount($('#amount').val(), $('#delivery_payment_amount').val(), $('#include_delivery:checkbox:checked').val());
        });

        $("#amount").keyup(function(){
            var delivery_payment_amount = $('#delivery_payment_amount').val();
            var include_delivery = $('#include_delivery:checkbox:checked').val();

            if(include_delivery == 1){
                var max = Number($('#order_product_charge').val()) + Number(delivery_payment_amount);
            }else{
                var max = $('#order_product_charge').val();
            }

            if(Number($('#amount').val()) > max){
                $('#amount').val(max);
                var amount = max;
            }else{
                var amount = $('#amount').val();
            }

            collectableAmount(amount, delivery_payment_amount, include_delivery);
        });

        $("#include_delivery_package").change(function(){
            collectableAmount_package($('#amount_package').val(), $('#delivery_payment_amount_package').val(), $('#include_delivery_package:checkbox:checked').val());
        });

        $("#amount_package").keyup(function(){
            var delivery_payment_amount = $('#delivery_payment_amount_package').val();
            var include_delivery = $('#include_delivery_package:checkbox:checked').val();

            if(include_delivery == 1){
                var max = Number($('#order_product_charge_package').val()) + Number(delivery_payment_amount);
            }else{
                var max = $('#order_product_charge_package').val();
            }

            if(Number($('#amount_package').val()) > max){
                $('#amount_package').val(max);
                var amount = max;
            }else{
                var amount = $('#amount_package').val();
            }

            collectableAmount_package(amount, delivery_payment_amount, include_delivery);
        });

        $('#pickup_location_id').on('change', function() {
            if($('#pickup_location_id').val() != ''){
                var pickup_location_id = $('#pickup_location_id').val();

                var charge = calculate_bulk_charge({{ $order->id }},{{ $total_width }},{{ $total_height }},{{ $total_length }},{{ $total_weight }},{{ $total_product_price }},pickup_location_id);
            }
        });

        // Get Pick-up time On date Change
        $('#picking_date').on('change', function() {
            var date = $(this).val();
            var day = dayOfWeek(date);

            pick_up_slot(day);
        });

        // Get Pick-up time On date Change
        $('.picking_date').on('change', function() {
            var id = $(this).attr("id");
            var date = $(this).val();
            var day = dayOfWeek(date);

            pick_up_slot_by_id(id,day);
        });

    </script>

</div>