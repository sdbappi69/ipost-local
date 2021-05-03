<!-- Modal -->
<div id="submit_order_complain_{{$sub_order->suborder_id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::open(['url' => secure_url('') . '/complain', 'method' => 'post'])}}
    <input type="hidden" name="sub_order_id" value="{{$sub_order->suborder_id}}">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Submit Compalin (ID: {{$sub_order->unique_suborder_id}})</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {{Form::label('query_'.$sub_order->id, 'Query :')}}
          {!! Form::select('query_id', $querys, null, ['class' => 'form-control js-example-basic-single', 'id' => 'query_'.$sub_order->suborder_id,'placeholder' => 'Select One','required' => '']) !!}
        </div>
        <div class="form-group">
          {{Form::label('source_of_infomartion_'.$sub_order->suborder_id, 'Source of information :')}}
          {!! Form::select('source_of_information',$source_of_infomartions, null, ['class' => 'form-control js-example-basic-single', 'id' => 'source_of_infomartion_'.$sub_order->suborder_id,'placeholder' => 'Select One','required' => '']) !!}
        </div>
        <div class="form-group">
          {{Form::label('complain_'.$sub_order->suborder_id, 'Complain :')}}
          {{Form::textarea('complain',null,['class' => 'form-control','id' => 'complain_'.$sub_order->suborder_id,'placeholder' => 'Input complain','required' => ''])}}
        </div>
        <div class="form-group">
          {{Form::label('remarks_'.$sub_order->suborder_id, 'Remarks :')}}
          {{Form::textarea('remarks',null,['class' => 'form-control','id' => 'remarks_'.$sub_order->suborder_id,'placeholder' => 'Input remarks (optional)','rows' => '5'])}}
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