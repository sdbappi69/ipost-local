<!-- Modal -->
<div id="edit_inquiry_{{$inquiry->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($inquiry,['url' => secure_url('') . '/inquiry/'.$inquiry->id, 'method' => 'PUT'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Inquiry</h4>
      </div>
      <div class="modal-body">
       <div class="form-group">
        <div class="col-sm-6">
          {{Form::label('calling_number', 'Calling Number :')}}
          {{Form::text('calling_number',null,['class' => 'form-control','id' => 'calling_number_'.$inquiry->id,'placeholder' => 'Number','required' => ''])}}
        </div>
        <div class="col-sm-6">
          {{Form::label('customer_name', 'Customer Name :')}}
          {{Form::text('customer_name',null,['class' => 'form-control','id' => 'customer_name_'.$inquiry->id,'placeholder' => 'Name','required' => ''])}}
        </div>
      </div>
      <div class="form-group">

       <div class="col-sm-6">
        {{Form::label('company_name', 'Company/Link Name :')}}
        {{Form::text('company_name',null,['class' => 'form-control','id' => 'company_name_'.$inquiry->id,'placeholder' => 'Name','required' => ''])}}
      </div>
    </div>
    <div class="form-group">

     <div class="col-sm-6">
      {{Form::label('customer_alt_number', 'Cutomer Alt. Number :')}}
      {{Form::text('customer_alt_number',null,['class' => 'form-control','id' => 'customer_alt_number_'.$inquiry->id,'placeholder' => 'Number (Optional)','required' => ''])}}
    </div>
    <div class="col-sm-6">
      {{Form::label('customer_email', 'Customer E-mail :')}}
      {{Form::email('customer_email',null,['class' => 'form-control','id' => 'customer_email_'.$inquiry->id,'placeholder' => 'E-mail','required' => ''])}}
    </div>
  </div>
  <div class="form-group">

   <div class="col-sm-6">
    {{Form::label('customer_address', 'Customer Address :')}}
    {{Form::textarea('customer_address',null,['class' => 'form-control','id' => 'customer_address_'.$inquiry->id,'placeholder' => 'Address (Optional)','required' => '','rows' => '4'])}}
  </div>
  <div class="col-sm-6">
    {{Form::label('query', 'Query :')}}
    {!! Form::select('query_id',$querys, null, ['class' => 'form-control js-example-basic-single', 'id' => 'query_'.$inquiry->id,'placeholder' => 'Select One','required' => '']) !!}
  </div>
</div>
<div class="form-group">
  <div class="col-sm-6">
    {{Form::label('source_of_infomartion_', 'Source of information :')}}
    {!! Form::select('source_of_information',$source_of_informations, null, ['class' => 'form-control js-example-basic-single', 'id' => 'source_of_infomartion_'.$inquiry->id,'placeholder' => 'Select One','required' => '']) !!}
  </div>
</div>

<div class="form-group">
  <div class="col-sm-12">
    {{Form::label('complain_', 'Complain :')}}
    {{Form::textarea('complain',null,['class' => 'form-control','id' => 'complain_'.$inquiry->id,'placeholder' => 'Input complain','required' => '','rows' => '5'])}}
  </div>
</div>
<div class="form-group">
  <div class="col-sm-12">
    {{Form::label('remarks_', 'Remarks :')}}
    {{Form::textarea('remarks',null,['class' => 'form-control','id' => 'remarks_'.$inquiry->id,'placeholder' => 'Input remarks (optional)','rows' => '5'])}}
  </div>
</div>
<div class="form-group">
  <div class="col-sm-12">
    {{Form::label('status', 'Final Status :')}}
    {!! Form::select('status',$statues, null, ['class' => 'form-control js-example-basic-single', 'id' => 'status_'.$inquiry->id,'placeholder' => 'Select One','required' => '']) !!}
  </div>
</div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="submit" class="btn btn-success" >Update</button>
</div>
</div>
{{Form::close() }}
</div>
</div>