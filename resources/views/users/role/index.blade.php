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
        <small>view & update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
  
  <div class="row">
    <!-- Start Panel -->
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body table-responsive">

            <table id="example0" class="table display">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Action</th>                        
                    </tr>
                </thead>      
             
                <tbody>
                    @if(count($roles))
                      @foreach($roles as $role)
                        <tr>
                          <td>{{ $role->display_name }}</td>
                          <td>
                            @if(count($role->perms))
                                @foreach($role->perms as $perm)
                                    <span class="btn btn-info btn-xs margin-bottom-5">{{ $perm->display_name }}</span>
                                @endforeach
                            @endif
                          </td>
                          <td>
                            <a href="{{ secure_url("role/$role->id/edit") }}">
                              <i class="fa falist fa-edit"></i> Edit
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    @endif
                </tbody>
            </table>


        </div>

      </div>
    </div>
    <!-- End Panel -->
  </div>

  <script type="text/javascript">
      $(document ).ready(function() {
          // Navigation Highlight
          highlight_nav('role-manage', 'roles');

          $('#example0').DataTable({
            "order": [],
          });
      });
  </script>
  
@endsection