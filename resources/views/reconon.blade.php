{!! Form::open(array('url' => '/recon-on', 'method' => 'post')) !!}

    <div class="form-group">
        <label class="control-label">Consignment Id</label>
        {!! Form::text('consignment_unique_id', null, ['class' => 'form-control', 'placeholder' => 'Consignment Id', 'required' => 'required']) !!}
    </div>

    <div class="row padding-top-10">
        {!! Form::submit('Reconciliation On', ['class' => 'btn green pull-right']) !!}
    </div>

{!! Form::close() !!}