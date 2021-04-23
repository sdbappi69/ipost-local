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
                <span>Countries</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Countries
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-flag"></i> {!! $title !!}
                </div>
                <div class="panel-body">

                    {!! Form::open(array('method' => 'get', 'id' => 'filter-form')) !!}

                        <?php if(!isset($_GET['filter_name'])){$_GET['filter_name'] = null;} ?>
                        <div class="col-md-12" style="margin-bottom:5px;">
                             <input type="text" value="{{$_GET['filter_name']}}" class="form-control focus_it" name="filter_name" id="filter_name" placeholder="Name">
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary filter-btn pull-right" style="width: 100%; margin-bottom:5px;"><i class="fa fa-search"></i> Filter</button>
                        </div>
                        <div class="clearfix"></div>

                    {!! Form::close() !!}

                    <table class="table table-bordered table-hover table-responsive table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Prefix</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($countries) > 0)
                            @foreach($countries as $country)
                                <tr>
                                    <td>{!! $country->name !!}</td>
                                    <td>{!! $country->code !!}</td>
                                    <td>{!! $country->prefix !!}</td>
                                    <td>
                                        {!! ($country->status) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' !!}
                                    </td>
                                    <td>
                                        <a href="{!! route('country.edit', $country->id) !!}" class="btn btn-sm btn-info">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pagination">
                        {!! $countries->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('country', 'locations');
        });
    </script>
@endsection