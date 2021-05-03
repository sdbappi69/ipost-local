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
              <span>Roles</span>
          </li>
      </ul>
  </div>
  <!-- END PAGE BAR -->
  <!-- BEGIN PAGE TITLE-->
  <h1 class="page-title"> Roles
      <small>update</small>
  </h1>
  <!-- END PAGE TITLE-->
  <!-- END PAGE HEADER-->
  
  {{ Form::model($role, ['url' => secure_url('') . "/role/$role->id", 'method' => 'put']) }}

    <div class="row">
        <div class="col-md-12 col-lg-6">
          <div class="panel panel-default">

                <div class="panel-body">
                  
                    <div class="form-group">                  
                      {{Form::label('inputRoleName', 'Role Name', ['class' => 'form-label'])}}                  
                      {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'inputRoleName', 'required' => 'required']) }}
                    </div>

                    <div class="form-group">                  
                      {{Form::label('inputRoleName', 'Display Name', ['class' => 'form-label'])}}                  
                      {{ Form::text('display_name', null, ['class' => 'form-control', 'id' => 'inputRoleName', 'required' => 'required']) }}
                    </div>
                  
                </div>

          </div>
        </div>

        <div class="col-md-12 col-lg-6">
          <div class="panel panel-default">

                <div class="panel-body">

                    <div class="form-group">
                      {{ Form::label('selectPermissions', 'Attach Permissions', ['class' => 'form-label']) }}

                      <div class="checkbox checkbox-primary col-md-offset-2">
                        @foreach($permissions as $permission)
                          <p>
                            {{ Form::checkbox('permission_ids[]', $permission->id, in_array($permission->id, $currentPermissions), ['id' => 'checkbox'.$permission->id]) }}

                            <label for="checkbox{{ $permission->id }}">
                              {{ $permission->display_name }}
                            </label>
                          </p>
                        @endforeach                    
                      </div>
                    </div>
                  
                </div>

          </div>
        </div>

    </div>

    &nbsp;
    <div class="row padding-top-10">
        <a href="javascript:history.back()" class="btn default"> Cancel </a>
        {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
    </div>
    &nbsp;

  {{ Form::close() }}

  <script type="text/javascript">
      $(document ).ready(function() {
          // Navigation Highlight
          highlight_nav('role-manage', 'roles');
      });
  </script>
  
@endsection