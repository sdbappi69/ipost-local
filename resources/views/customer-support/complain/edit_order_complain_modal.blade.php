<!-- Modal -->
<div id="edit_order_complain_{{$complain->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($complain,['url' => 'complain/'.$complain->id, 'method' => 'put'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Compalin </h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {{Form::label('query_'.$complain->id, 'Query :')}}
          {!! Form::select('query_id', $querys, null, ['class' => 'form-control js-example-basic-single', 'id' => 'query_'.$complain->id,'placeholder' => 'Select One','required' => '']) !!}
        </div>
        <div class="form-group">
          {{Form::label('source_of_infomartion_'.$complain->id, 'Source of information :')}}
          {!! Form::select('source_of_information',$source_of_informations, null, ['class' => 'form-control js-example-basic-single', 'id' => 'source_of_infomartion_'.$complain->id,'placeholder' => 'Select One','required' => '']) !!}
        </div>
        <div class="form-group">
          {{Form::label('complain_'.$complain->id, 'Complain :')}}
          {{Form::textarea('complain',null,['class' => 'form-control','id' => 'complain_'.$complain->id,'placeholder' => 'Input complain','required' => ''])}}
        </div>
        <div class="form-group">
          {{Form::label('remarks_'.$complain->id, 'Remarks :')}}
          {{Form::textarea('remarks',null,['class' => 'form-control','id' => 'remarks_'.$complain->id,'placeholder' => 'Input remarks (optional)','rows' => '5'])}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" >Submit</button>
      </div>
    </div>
    {{Form::close() }}
  </div>
</div>