<!-- Modal -->
<div id="update_mail_group_{{$mail_group->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($mail_group,['url' => secure_url('') . '/mail-groups/'.$mail_group->id, 'method' => 'put'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Mail Group</h4>
      </div>
      <div class="modal-body">
       <div class="form-group">
        {{Form::label('team_title', 'Team Title :')}}
        {{Form::text('team_title',null,['class' => 'form-control','id' => 'team_title','placeholder' => 'Title','required' => ''])}}
      </div>
      <div class="form-group">
        {{Form::label('to', 'TO :')}}
        {{Form::email('to',null,['class' => 'form-control','id' => 'to','placeholder' => 'E-mail','required' => ''])}}
      </div>
      <div class="form-group">
        {{Form::label('cc', 'CC :')}}
        {{Form::textarea('cc',null,['class' => 'form-control','id' => 'cc','placeholder' => 'Please add multiple e-mail address with comma separate','required' => ''])}}
      </div>
      @include('customer-support.status')
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-success" >Submit</button>
    </div>
  </div>
  {{Form::close() }}
</div>
</div>