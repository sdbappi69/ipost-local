{!! Form::model($user, array('url' => '/user/'.$user->id.'?step=complete', 'method' => 'put')) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-6">
            
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
        <a href="{{ URL::to('user/'.$id.'/edit?step=3') }}" class="btn default"> Back </a>
        {!! Form::submit('Finish', ['class' => 'btn green pull-right']) !!}
    </div>

{!! Form::close() !!}

<script type="text/javascript">
    function check(input) {
        if (input.value != document.getElementById('new_password').value) {
            input.setCustomValidity('Password Must be Matching.');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }
</script>