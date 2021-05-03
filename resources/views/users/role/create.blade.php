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
              <a href="{{ secure_url('role') }}">Role</a>
              <i class="fa fa-circle"></i>
          </li>
          <li>
              <span>Insert</span>
          </li>
      </ul>
  </div>
  <!-- END PAGE BAR -->
  <!-- BEGIN PAGE TITLE-->
  <h1 class="page-title"> Roles
      <small>insert</small>
  </h1>
  <!-- END PAGE TITLE-->
  <!-- END PAGE HEADER-->
  
  {{ Form::open(['url' => secure_url('') . '/role','method'=>'post']) }}

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
                            {{ Form::checkbox('permission_ids[]', $permission->id, false, ['id' => 'checkbox'.$permission->id]) }}

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
        <a href="{{ secure_url('role') }}" class="btn default"> Cancel </a>
        {!! Form::submit('Save', ['class' => 'btn green pull-right']) !!}
    </div>
    &nbsp;

  {{ Form::close() }}

  <script type="text/javascript">
      $(document ).ready(function() {
          // Navigation Highlight
          highlight_nav('role-add', 'roles');
      });
  </script>
  
@endsection