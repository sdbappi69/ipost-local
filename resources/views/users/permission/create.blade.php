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
                <a href="{{ secure_url('permission') }}">Permission</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Permission
        <small>Insert</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
  
  <div class="row">
    <div class="col-md-12 col-lg-6">
      <div class="panel panel-default">

            <div class="panel-body">
              {{ Form::open(['url' => secure_url('') . '/permission', 'method' => 'post']) }}
                <div class="form-group">                  
                  {{Form::label('selectRoleName', 'Role', ['class' => 'form-label'])}}                  
                  {{ Form::select('role_id[]', ['' => 'SELECT ROLE']+$roles, null, ['class' => 'form-control', 'id' => 'selectRoleName', 'required' => 'required', 'multiple' => 'true']) }}
                </div>            
                <div class="form-group">                  
                  {{Form::label('inputPermissionName', 'Name', ['class' => 'form-label'])}}                  
                  {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'inputPermissionName', 'required' => 'required']) }}
                </div>
                <div class="form-group">                  
                  {{Form::label('inputPermissionDisplayName', 'Display Name', ['class' => 'form-label'])}}                  
                  {{ Form::text('display_name', null, ['class' => 'form-control', 'id' => 'inputPermissionDisplayName', 'required' => 'required']) }}
                </div>
                
                <button type="submit" class="btn btn-default">Submit</button>
              {{ Form::close() }}
            </div>

      </div>
    </div>
  </div>
  
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('permission-add', 'permissions');
    });
</script>
  
@endsection