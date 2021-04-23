<link href="{{ URL::asset('assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css') }}" rel="stylesheet" type="text/css" />

{!! Form::model($merchant, array('url' => '/merchant/'.$merchant->id.'?step=complete', 'method' => 'put')) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-6">
        
            <div class="form-group">
                <label class="control-label">Billing date</label>
                {!! Form::text('billing_date', null, ['class' => 'form-control input-group-lg', 'id' => 'billing_date', 'required' => 'required', 'onkeydown' => 'return false;']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Due date</label>
                {!! Form::text('due_date', null, ['class' => 'form-control input-group-lg', 'id' => 'due_date', 'required' => 'required', 'onkeydown' => 'return false;']) !!}
            </div>

            <div class="form-group reference-area"></div>

        </div>

    </div>

    &nbsp;
    <div class="row padding-top-10">
        <a href="{{ URL::to('merchant/'.$id.'/edit?step=2') }}" class="btn default"> Back </a>
        {!! Form::submit('Finish', ['class' => 'btn green pull-right']) !!}
    </div>

{!! Form::close() !!}

<script src="{{ URL::asset('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/pages/scripts/components-bootstrap-touchspin.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $("#billing_date").TouchSpin();
    $("#due_date").TouchSpin();
</script>