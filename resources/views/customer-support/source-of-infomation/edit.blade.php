<!-- Modal -->
<div id="update_src_of_info_{{$src_of_info->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($src_of_info,['url' => 'source-of-info/'.$src_of_info->id, 'method' => 'put'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Source of infomation</h4>
      </div>
      <div class="modal-body">
       <div class="form-group">
        {{Form::label('title', 'Team Title :')}}
        {{Form::text('title',null,['class' => 'form-control','id' => 'title','placeholder' => 'Title','required' => ''])}}
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