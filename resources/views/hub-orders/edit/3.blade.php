<div class="row">
    <br>
    <div class="col-md-12">
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

                    {!! Form::model($order, array('url' => '/hub-order/'.$order->id.'?step=complete', 'method' => 'put')) !!}

                        <table class="table">
                            <thead class="flip-content">
                                <th>Product</th>
                                <th>Category</th>
                                <th class="numeric">Unit Price</th>
                                <th class="numeric">Unit Delivery Cost</th>
                                <th class="numeric">Quantity</th>
                                <th class="numeric">Total Price</th>
                                <th class="numeric">Total Delivery Cost</th>
                            </thead>
                            <tbody>
                                @foreach($charges->order_detail as $row)
                                    <tr>
                                        <td>{{ $row->product_title }}</td>
                                        <td>{{ $row->product_category }}</td>
                                        <td class="numeric" style="text-align:right;">{{ $row->product_unit_price }}</td>
                                        <td class="numeric" style="text-align:right;">{{ $row->product_unit_delivery_charge }}</td>
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
                                    <td></td>
                                    <td></td>
                                    <td>Total</td>
                                    <td class="numeric" style="text-align:right; padding-right:5px;">{{ $charges->order_product_charge }}</td>
                                    <td class="numeric" style="text-align:right; padding-right:5px;">{{ $charges->order_delivery_charge }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Collectable amount</td>
                                    <td style="padding-right:5px;">
                                        {!! Form::text('amount', $charges->order_product_charge, ['class' => 'form-control', 'placeholder' => 'BDT', 'required' => 'required', 'id' => 'amount']) !!}
                                        {!! Form::hidden('delivery_payment_amount', $charges->order_delivery_charge, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'delivery_payment_amount']) !!}
                                        {!! Form::hidden('amount_hidden', $charges->order_product_charge, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'order_product_charge']) !!}
                                    </td>
                                    <td class="numeric" style="padding-right:5px;">
                                        {!! Form::checkbox('include_delivery', '1', true, ['id' => 'include_delivery']) !!}
                                        Customer will pay the delivery charge
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{!! Form::hidden('total_amount', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required', 'id' => 'total_amount']) !!}</td>
                                    <td>Total Collectable amount</td>
                                    <td style="padding-right:5px;" id="total_collectable_amount"></td>
                                    <td>BDT</td>
                                </tr>
                            </tfoot>
                        </table>

                        &nbsp;
                        <div class="row padding-top-10">
                            <a href="{{ URL::to('hub-order/'.$id.'/edit?step=2') }}" class="btn default"> Back </a>
                            {!! Form::submit('Confirm', ['class' => 'btn green pull-right']) !!}
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

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

        $(document ).ready(function() {
            collectableAmount($('#order_product_charge').val(), $('#delivery_payment_amount').val(), $('#include_delivery:checkbox:checked').val());
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

    </script>

</div>