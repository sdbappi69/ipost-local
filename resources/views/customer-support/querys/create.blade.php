<!-- Modal -->
<div id="create_query" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    {{Form::open(['url' => 'query', 'method' => 'post'])}}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Create Query</h4>
      </div>
      <div class="modal-body">
       <div class="form-group">
        {{Form::label('title', 'Title :')}}
        {{Form::text('title',null,['class' => 'form-control','id' => 'title','placeholder' => 'Title','required' => ''])}}
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