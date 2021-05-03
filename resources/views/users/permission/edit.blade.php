@extends('layouts.appinside')

@section('content')

  <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Permission</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Permission
        <small>update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
  
    <div class="row">
      <div class="col-md-12 col-lg-6">
        <div class="panel panel-default">

              <div class="panel-body">
                {{ Form::model($permission, ['url' => secure_url('') . "/permission/$permission->id", 'method' => 'put']) }}
                  <div class="form-group">                  
                    {{Form::label('inputPermissionName', 'Name', ['class' => 'form-label'])}}                  
                    {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'inputPermissionName', 'required' => 'required']) }}
                  </div>
                  <div class="form-group">                  
                    {{Form::label('inputPermissionDisplayName', 'Display Name', ['class' => 'form-label'])}}                  
                    {{ Form::text('display_name', null, ['class' => 'form-control', 'id' => 'inputPermissionDisplayName', 'required' => 'required']) }}
                  </div>
                  
                  <button type="submit" class="btn btn-default">Update</button>
                {{ Form::close() }}
              </div>

        </div>
      </div>
    </div>
  
<script type="text/javascript">
      $(document ).ready(function() {
          // Navigation Highlight
          highlight_nav('permission-manage', 'permissions');

          $('#example0').DataTable({
            "order": [],
          });
      });
  </script>
  
@endsection