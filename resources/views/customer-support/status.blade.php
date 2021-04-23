<div class="form-group">
	{{Form::label('status', 'Status :')}}
	{{Form::select('status',['1' => 'Active','0' => 'Inactive'],null,['class' => 'form-control','id' => 'status','placeholder' => 'Select One','required' => ''])}}
</div>