{!! Form::model($order, array('url' => '/order/'.$order->id.'?step=2', 'method' => 'put')) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-6">

            <div class="form-group">
                <label class="control-label">Select Store</label>
                {!! Form::select('store_id', $stores, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'store_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Customer Name</label>
                {!! Form::text('delivery_name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Customer Email</label>
                {!! Form::email('delivery_email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label col-md-12 np-lr">Customer Mobile Number</label>
                <div class="col-md-2 np-lr">
                    {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="col-md-10 np-lr">
                    {!! Form::text('delivery_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                </div>
            </div>

            <div class="form-group">

                <label class="control-label col-md-12 np-lr">Customer Alt. Mobile Number</label>
                <div class="col-md-2 np-lr">
                    {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-10 np-lr">
                    {!! Form::text('delivery_alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                </div>
                
            </div>

            <div class="form-group">
                <label class="control-label">Select Status</label>
                {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
            </div>

        </div>

        <div class="col-md-6">
            
            <div class="form-group">
                <label class="control-label">Select Customer Country</label>
                {!! Form::select('delivery_country_id', $countries, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'country_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Customer State</label>
                {!! Form::select('delivery_state_id', $states, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'state_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Customer City</label>
                {!! Form::select('delivery_city_id', $cities, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'city_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Customer Zone</label>
                {!! Form::select('delivery_zone_id', $zones, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'zone_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Customer Address</label>
                {!! Form::text('delivery_address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
            </div>

        </div>

    </div>

    &nbsp;
    <div class="row padding-top-10">
        <a href="javascript:history.back()" class="btn default"> Cancel </a>
        {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
    </div>

{!! Form::close() !!}

<script type="text/javascript">
    $(document ).ready(function() {
        // Get State list
        // var country_id = $('#country_id').val();
    });

    // Get State list On Country Change
    $('#country_id').on('change', function() {
        if($(this).val() != {{ $order->delivery_country_id }}){
            get_states($(this).val());
        }
    });

    // Get City list On State Change
    $('#state_id').on('change', function() {
        if($(this).val() != {{ $order->delivery_state_id }}){
            get_cities($(this).val());
        }
    });

    // Get Zone list On City Change
    $('#city_id').on('change', function() {
        if($(this).val() != {{ $order->delivery_city_id }}){
            get_zones($(this).val());
        }
    });
</script>