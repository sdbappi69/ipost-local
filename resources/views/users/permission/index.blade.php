@extends('layouts.appinside')

@section('content')

  <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
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
        <small>view & update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

  <div class="row">

     <div class="col-md-12">
        <div class="table-filtter">
           {!! Form::open(array('method' => 'get')) !!}
           <div class="col-md-5">
              <div class="row">
                 <input type="text" class="form-control" name="name" id="name" placeholder="Name">
              </div>
           </div>
           <div class="col-md-5">
              <div class="row">
                 <input type="text" class="form-control" name="display_name" id="display_name" placeholder="Display Name">
              </div>
           </div>

           <div class="col-md-2">
              <div class="row">
                 <button type="submit" class="btn btn-primary">Filter</button>
              </div>
           </div>
           <div class="clearfix"></div>
           {!! Form::close() !!}
        </div>
     </div>

    <!-- Start Panel -->
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body table-responsive">

            <table id="example0" class="table display">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Display Name</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @if(count($permissions))
                      @foreach($permissions as $permission)
                        <tr>
                          <td>{{ $permission->name }}</td>
                          <td>{{ $permission->display_name }}</td>
                          <td>
                            <a href="{{ URL::route('permission.edit', $permission->id) }}">
                              <i class="fa falist fa-edit"></i> Edit
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    @else
                       <p>
                          No Data Found
                       </p>
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
          highlight_nav('permission-manage', 'permissions');

          $('#example0').DataTable({
            "order": [],
          });

          {!! !empty($req['name'])          ? "document.getElementById('name').value = '".$req['name']."'" : "" !!}
          {!! !empty($req['display_name'])  ? "document.getElementById('display_name').value = '".$req['display_name']."'" : "" !!}
      });
  </script>
  <style media="screen">
  .table-filtter .btn{ width: 100%;}
  .table-filtter {
    margin: 20px 0;
  }
  </style>

@endsection
