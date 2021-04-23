<div class="row">
    @include('partials.errors')
    <br>
    <div id="accordion3" class="panel-group">

        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_new">
                        <i class="fa fa-plus"></i> Add User
                    </a>
                </h4>
            </div>
            <div id="accordion3_new" class="panel-collapse collapse">
                <div class="panel-body">

                    {!! Form::open(array('url' => '/merchant/create-user', 'method' => 'post','enctype' => 'multipart/form-data' )) !!}
                    <input type="hidden" name="reference_id" value="{{$merchant->id}}">
                    <input type="hidden" name="step" value="2">
                    <div class="row">



                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Name</label>
                                {!! Form::text('name',$merchant->name, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email</label>
                                {!! Form::email('email',$merchant->email, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-12 np-lr">Mobile Number</label>
                                <div class="col-md-2 np-lr">
                                    {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                                <div class="col-md-10 np-lr">
                                    {!! Form::text('msisdn',$merchant->msisdn, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                                </div>
                            </div>

                            <div class="form-group">

                                <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
                                <div class="col-md-2 np-lr">
                                    {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-10 np-lr">
                                    {!! Form::text('alt_msisdn',$merchant->alt_msisdn, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="control-label">Select Status</label>
                                {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Select Country</label>
                                {!! Form::select('country_id', $countries,$merchant->country_id, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'country_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Select State</label>
                                {!! Form::select('state_id',$states,$merchant->state_id, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'state_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Select City</label>
                                {!! Form::select('city_id',$cities,$merchant->city_id, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'city_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Select Zone</label>
                                {!! Form::select('zone_id',$zones,$merchant->zone_id, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'zone_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Address</label>
                                {!! Form::text('address1',$merchant->address1, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
                            </div>

                        </div>






                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                                        <img src="{{url('/uploads/merchants/no_image.jpg')}}" alt=""  id="img-thumb" />
                                    </div>
                                    <div>
                                        <span class="btn default btn-file">
                                            <span class="fileinput-new"> Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" id="photo" name="photo"> </span>
                                            <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                                        </div>
                                    </div>
                                    <div class="clearfix margin-top-10">
                                        <span class="label label-danger">NOTE! </span>
                                        <span>Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Select Type</label>
                                    {!! Form::select('user_type_id', $user_types, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'user_type_id']) !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Password</label>
                                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password', 'id' => 'new_password']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Confirm Password</label>
                                    {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Re-type New Password', 'oninput' => 'check(this)']) !!}
                                </div>

                                <div class="form-group reference-area"></div>

                            </div>

                        </div>


                        &nbsp;
                        <div class="row padding-top-10">
                            <!-- <a href="javascript:history.back()" class="btn default"> Cancel </a> -->
                            {!! Form::reset('Reset', ['class' => 'btn default']) !!}
                            {!! Form::submit('Add', ['class' => 'btn green pull-right']) !!}
                        </div>

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>

            @foreach($user as $u)

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="icon-btn icon-btn-customized accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_{{ $u->id }}">
                            {{ $u->name }} ({{ $u->title }})
                            {{-- <span class="badge badge-success"> {{ $u->id }} </span> --}}
                        </a>



                    </h4>
                </div>
                <div id="accordion3_{{ $u->id }}" class="panel-collapse collapse">
                    <div class="panel-body">

                        {!! Form::model($u, array('url' => '/merchant/edit-user', 'method' => 'put')) !!}

                        <input type="hidden" name="reference_id" value="{{$merchant->id}}">
                        <input type="hidden" name="id" value="{{$u->id}}">
                        <input type="hidden" name="old_photo" value="{{$u->photo}}">
                        <input type="hidden" name="step" value="2">
                        <div class="row">



                            <div class="col-md-6">

                                <div class="form-group">
                                    <label class="controul-label">Name</label>
                                    {!! Form::text('name',$u->name, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    {!! Form::email('email',$u->email, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-12 np-lr">Mobile Number</label>
                                    <div class="col-md-2 np-lr">
                                        {!! Form::select('msisdn_country', $prefix, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    </div>
                                    <div class="col-md-10 np-lr">
                                        {!! Form::text('msisdn',$u->msisdn, ['class' => 'form-control', 'placeholder' => 'Mobile Number',  'required' => 'required']) !!}
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="control-label col-md-12 np-lr">Alt. Mobile Number</label>
                                    <div class="col-md-2 np-lr">
                                        {!! Form::select('alt_msisdn_country', $prefix, null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-md-10 np-lr">
                                        {!! Form::text('alt_msisdn',$u->alt_msisdn, ['class' => 'form-control', 'placeholder' => 'Alt. Mobile Number']) !!}
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="control-label">Select Status</label>
                                    {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label class="control-label">Select Country</label>
                                    {!! Form::select('country_id', $countries,$u->country_id, ['class' => 'form-control js-example-basic-single js-country country_id', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Select State</label>
                                    {!! Form::select('state_id',$states,$u->state_id, ['class' => 'form-control js-example-basic-single js-country state_id', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Select City</label>
                                    {!! Form::select('city_id',$cities,$u->city_id, ['class' => 'form-control js-example-basic-single js-country city_id', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Select Zone</label>
                                    {!! Form::select('zone_id',$zones,$u->zone_id, ['class' => 'form-control js-example-basic-single js-country zone_id', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    {!! Form::text('address1',$u->address1, ['class' => 'form-control', 'placeholder' => 'Address', 'required' => 'required']) !!}
                                </div>

                            </div>






                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                                            <img src="{{$u->photo}}" alt=""  id="img-thumb" />
                                        </div>
                                        <div>
                                            <span class="btn default btn-file">
                                                <span class="fileinput-new"> Select image </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" id="photo" name="photo"> </span>
                                                <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                                            </div>
                                        </div>
                                        <div class="clearfix margin-top-10">
                                            <span class="label label-danger">NOTE! </span>
                                            <span>Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Select Type</label>
                                        {!! Form::select('user_type_id', $user_types,$u->user_types_id, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'user_type_id']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Password</label>
                                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password', 'id' => 'new_password']) !!}
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Confirm Password</label>
                                        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Re-type New Password', 'oninput' => 'check(this)']) !!}
                                    </div>

                                    <div class="form-group reference-area"></div>

                                </div>

                            </div>

                            &nbsp;
                            <div class="row padding-top-10">
                                {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>

                @endforeach


            </div>

            <a href="{{ URL::to('merchant') }}/{{ $merchant->id }}/edit" class="btn default"> Back </a>
            <a href="{{ URL::to('merchant') }}/{{ $id }}/edit?step=3" class="btn green pull-right">Next</a>

        </div>

        <script type="text/javascript">
            document.getElementById("photo").onchange = function () {
                var reader = new FileReader();

                reader.onload = function (e) {
            // get loaded data and render thumbnail.
            document.getElementById("img-thumb").src = e.target.result;
        };

        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    };
</script>
<script type="text/javascript">

    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-add', 'merchants');

            // Get State list
            var country_id = $('#country_id').val();
            get_states(country_id);
        });

        // Get State list On Country Change
        $('#country_id').on('change', function() {
            get_states($(this).val());
        });

        // Get City list On State Change
        $('#state_id').on('change', function() {
            get_cities($(this).val());
        });

        // Get Zone list On City Change
        $('#city_id').on('change', function() {
            get_zones($(this).val());
        });

    </script>
   