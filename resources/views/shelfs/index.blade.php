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
                <span>Shelfs</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Shelfs
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

   <div class="col-md-12">
      <div class="table-filtter">
         {!! Form::open(array('method' => 'get')) !!}
         <div class="col-md-2">
            <div class="row">
               {!! Form::select('hub_id', array(''=>'Select Hub') + $hubs, null, ['class' => 'form-control', 'id' => 'hub_id']) !!}
            </div>
         </div>
         <div class="col-md-2">
            <div class="row">
               {!! Form::select('assignd_hub_id', array(''=>'Assigned Hub') + $hubs, null, ['class' => 'form-control', 'id' => 'assignd_hub_id']) !!}
            </div>
         </div>
         <div class="col-md-2">
            <div class="row">
               <input type="text" class="form-control" name="shelf_title" id="shelf_title" placeholder="Shelf Title">
            </div>
         </div>
         <div class="col-md-2">
            <div class="row">
               <input type="text" class="form-control" name="width" id="width" placeholder="Width">
            </div>
         </div>
         <div class="col-md-2">
            <div class="row">
               <input type="text" class="form-control" name="height" id="height" placeholder="Height">
            </div>
         </div>
         <div class="col-md-1">
            <div class="row">
               <input type="text" class="form-control" name="length" id="length" placeholder="Length">
            </div>
         </div>
         <div class="col-md-1">
            <div class="row">
               <button type="submit" class="btn btn-primary">Filter</button>
            </div>
         </div>
         <div class="clearfix"></div>
         {!! Form::close() !!}
      </div>
   </div>

    @if(count($shelfs) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">

                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Hub</th>
                            <th>Shelf Title</th>
                            <th>Assigned Hub</th>
                            <th>Width</th>
                            <th>Height</th>
                            <th>Length</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach($shelfs as $shelf)
                                <tr>
                                    <td>{{ $shelf->get_hub->title }}</td>
                                    <td><a data-toggle="modal" data-target="#large" href="{{ route('shelf_products', ['id' => $shelf->id]) }}">{{ $shelf->shelf_title }}</a></td>
                                    <td>{{ $shelf->assigndhub['title'] }}</td>
                                    <td>{{ $shelf->width }}</td>
                                    <td>{{ $shelf->height }}</td>
                                    <td>{{ $shelf->length }}</td>
                                    <td>
                                        <a class="label label-success" href="shelf/{{ $shelf->id }}/edit">
                                            <i class="fa fa-pencil"></i> Update
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $shelfs->appends($req)->render() }}
                    </div>

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


    <div class="modal fade bs-modal-lg" id="large" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">

          </div>
      </div>
    </div>


   <script type="text/javascript">
        $(document ).ready(function() {
            /**
            * calback filter after modal show
            */
            $("#large").on("show.bs.modal", function(e) {
               setTimeout(function(){
                  $('#modalTable').DataTable({
                     "order": [],
                  });
               }, 1000);
            });
            // End

            // Navigation Highlight
            highlight_nav('warehouse-manage', 'shelfs');

            $('#example0').DataTable({
                "order": [],
            });


            @php
               if( !empty($req) )
               {
                  foreach($req as $key => $val)
                  {
                     echo "document.getElementById('".$key."').value = '".$val."';";
                  }
               }
            @endphp
        });
    </script>

    <style media="screen">
    .table-filtter .btn{ width: 100%;}
    .table-filtter {
      margin: 20px 0;
    }
    </style>

@endsection
