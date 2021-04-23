<!-- Modal -->
<div id="edit_order_feedback_{{$feedback->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($feedback,['url' => 'feedback/'.$feedback->id, 'method' => 'put'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Set Feedback</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {{Form::label('unique_head_'.$feedback->id, 'Unique Head :')}}
          {!! Form::select('unique_head', $unique_heads, null, ['class' => 'form-control js-example-basic-single', 'id' => 'unique_head_'.$feedback->id,'placeholder' => 'Select One','required' => '']) !!}
        </div>
        <div class="form-group">
          {{Form::label('reaction_'.$feedback->id, 'Reaction :')}}
          {!! Form::select('reaction',$reactions, null, ['class' => 'form-control js-example-basic-single', 'id' => 'reaction_'.$feedback->id,'placeholder' => 'Select One','required' => '']) !!}
        </div>
        {{-- start --}}
        {{Form::label('', 'Rating :')}}
        <div class="form-control">
          @for($i=1;$i<=5;$i++)
          <label class="radio-inline">
            {{Form::radio('rating',$i,false)}} {{$i}}
          </label>
          @endfor
        </div>
        {{-- end --}}
        <div class="form-group">
          {{Form::label('suggestion_'.$feedback->id, 'Suggestion :')}}
          {{Form::textarea('suggestion',null,['class' => 'form-control','id' => 'suggestion_'.$feedback->id,'placeholder' => 'Input Suggestion (Optional)','rows' => '5'])}}
        </div>
        <div class="form-group">
          {{Form::label('remarks_'.$feedback->id, 'Remarks :')}}
          {{Form::textarea('remarks',null,['class' => 'form-control','id' => 'remarks_'.$feedback->id,'placeholder' => 'Input remarks (optional)','rows' => '5'])}}
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