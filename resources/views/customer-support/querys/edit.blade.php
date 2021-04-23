<!-- Modal -->
<div id="update_query_{{$query->id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::model($query,['url' => 'query/'.$query->id, 'method' => 'put'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Query</h4>
      </div>
      <div class="modal-body">
       <div class="form-group">
        {{Form::label('title', 'Title :')}}
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