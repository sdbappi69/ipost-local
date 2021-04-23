{!! Form::model($user, array('url' => '/user/'.$user->id.'?step=2', 'method' => 'put')) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-6">

            <div class="form-group">
                <label class="control-label">Name</label>
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Email</label>
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label col-md-12 np-lr">Mobile Number</label>
                <!-- <div class="col-md-2 np-lr">
                    {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div> -->
                <!-- <div class="col-md-12 np-lr"> -->
                    {!! Form::text('msisdn', null, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                <!-- </div> -->
            </div>

            <div class="form-group">

                <!-- <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
                <div class="col-md-2 np-lr">
                    {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-10 np-lr"> -->
                    {!! Form::text('alt_msisdn', null, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                <!-- </div> -->
                
            </div>

            <div class="form-group">
                <label class="control-label">Select Status</label>
                {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
            </div>

        </div>

        <div class="col-md-6">
            
            <div class="form-group">
                <label class="control-label">Select Country</label>
                {!! Form::select('country_id', $countries, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'country_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select State</label>
                {!! Form::select('state_id', $states, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'state_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select City</label>
                {!! Form::select('city_id', $cities, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'city_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Zone</label>
                {!! Form::select('zone_id', $zones, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'zone_id']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Address</label>
                {!! Form::text('address1', null, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
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
        if($(this).val() != {{ $user->country_id }}){
            get_states($(this).val());
        }
    });

    // Get City list On State Change
    $('#state_id').on('change', function() {
        if($(this).val() != {{ $user->state_id }}){
            get_cities($(this).val());
        }
    });

    // Get Zone list On City Change
    $('#city_id').on('change', function() {
        if($(this).val() != {{ $user->city_id }}){
            get_zones($(this).val());
        }
    });
</script>