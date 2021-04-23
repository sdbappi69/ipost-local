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
      <span>Banks</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Banks
  <small> view</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

@include('partials.errors')

@if(count($banks) > 0)

<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">
                <!-- <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green-haze bold uppercase">Hubs</span>
                        <span class="caption-helper">list</span>
                    </div>
                  </div> -->
                  <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                      <thead class="flip-content">
                        <th>Name</th>
                        
                        <th>Action</th>
                      </thead>
                      <tbody>
                        @foreach($banks as $bank)
                        <tr>
                          <td>{{ $bank->name }}</td>
                          
                          <td>
                            <a href="{{url('/bank/'.$bank->id.'/edit')}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>
                          </td>

                        </tr>
                        @endforeach
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
              @else
              <div class="col-md-12">
                <div class="portlet light tasks-widget bordered">
                 <p>
                  No Data Found
                </p>
              </div>
            </div>
            @endIf

            <script type="text/javascript">
              $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('all-bank', 'bank');

            $('#example0').DataTable({
              "order": [],
            });


          });
        </script>
        <style media="screen">
          .table-filtter .btn{ width: 100%;}
          .table-filtter {
            margin: 20px 0;
          }
        </style>

        @endsection
