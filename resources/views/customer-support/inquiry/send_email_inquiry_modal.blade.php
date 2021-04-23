<!-- Modal -->
<div id="send_email_inquiry_{{$inquiry->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($inquiry,['url' => 'inquiry-send-email/'.$inquiry->id, 'method' => 'post'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Send E-mail to mail groups</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {{Form::label('mail_groups_'.$inquiry->id, 'Mail Groups :')}}
          {!! Form::select('mail_groups[]', $mail_groups, null, ['class' => 'form-control js-example-basic-single', 'id' => 'mail_groups_'.$inquiry->id,'required' => '','multiple' => '']) !!}
        </div>
        <div class="form-group">
          {{Form::label('extra_msg_'.$inquiry->id, 'Extra Msg. :')}}
          {{Form::textarea('extra_msg',null,['class' => 'form-control','id' => 'extra_msg_'.$inquiry->id,'placeholder' => 'Input extra message (optional - it will add in e-mail body)','rows' => '4'])}}
        </div>
        <div class="form-group">
          {{Form::label('complain_'.$inquiry->id, 'Complain :')}}
          {{Form::textarea('complain',null,['class' => 'form-control','id' => 'complain_'.$inquiry->id,'placeholder' => 'Input complain','required' => ''])}}
        </div>
        <div class="form-group">
          {{Form::label('remarks_'.$inquiry->id, 'Remarks :')}}
          {{Form::textarea('remarks',null,['class' => 'form-control','id' => 'remarks_'.$inquiry->id,'placeholder' => 'Input remarks (optional)','rows' => '5'])}}
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