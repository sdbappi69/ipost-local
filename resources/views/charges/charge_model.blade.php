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
                <span>Charge Models</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Charge Models
        <small> view</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    @if(count($charge_models) > 0)

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Title</th>
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach($charge_models as $charge_model)
                                <tr>
                                    <td>{{ $charge_model->title }}</td>
                                    <td>{{ $charge_model->description }}</td>
                                    <td>{{ $charge_model->unit }}</td>
                                    <td>{!! ($charge_model->status) ? 'Active' : 'Inactive' !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination pull-right">
                        {{ $charge_models->appends($_REQUEST)->render() }}
                    </div>
                </div>
            </div>
        </div>
    @endIf

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('charge-model', 'charge-model');

            $('#example0').DataTable({
                "order": [],
            });
        });
    </script>

@endsection
