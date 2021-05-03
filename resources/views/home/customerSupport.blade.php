@extends('layouts.appinside')

@section('content')

<style type="text/css">
.widget-thumb{
    text-align: center;
}
</style>

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <span>Dashboard</span>
        </li>
    </ul>
</div>

<br>

<div class="row">

    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Queries</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_query }}">0</span>
                </div>

                <br><br>
                @permission('manage_query')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('query') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Mail Gropup</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_mail_group }}">0</span>
                </div>

                <br><br>
                @permission('manage_mail_groups')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('mail-groups') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Source of information</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_source_of_information }}">0</span>
                </div>

                <br><br>
                @permission('manage_source_of_information')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('source-of-info') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Unique Head</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_unique_head }}">0</span>
                </div>

                <br><br>
                @permission('manage_unique_head')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('unique-head') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Reaction</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_reaction }}">0</span>
                </div>

                <br><br>
                @permission('manage_reaction')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('reaction') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>

    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Unsolved Complain</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_unsolved_complain}}">0</span>
                </div>

                <br><br>
                @permission('manage_complain')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('complain?status_s=0') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total In Process Complain</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_in_process_complain }}">0</span>
                </div>

                <br><br>
                @permission('manage_complain')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('complain?status_s=1') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Solved Complain</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_solved_complain}}">0</span>
                </div>

                <br><br>
                @permission('manage_complain')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('complain?status_s=2') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Non Collected Feedback</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_non_collected_feedback}}">0</span>
                </div>

                <br><br>
                @permission('manage_feedback')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('feedback?status_s=0') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Total Collected Feedback</h4>
            <div class="widget-thumb-wrap">
                <div class="widget-thumb-body">
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $count_collected_feedback}}">0</span>
                </div>

                <br><br>
                @permission('manage_feedback')
                <div class="col-md-12" style="padding-left: 0; padding-right: 0;">
                    <a class="form-control btn btn-primary pull-right" href="{{ secure_url('feedback?status_s=1') }}" >
                        Detail
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </a>
                </div>
                @endpermission
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
</div>

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('dashboard', 'dashboard');
    });
</script>

<script src="{{secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

<!-- <script src="{{secure_asset('assets/global/plugins/amcharts/amcharts/amcharts.js')}}" type="text/javascript"></script>
<script src="{{secure_asset('assets/global/plugins/amcharts/amcharts/pie.js')}}" type="text/javascript"></script> -->
<script src="{{secure_asset('assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js')}}" type="text/javascript"></script>

<script src="{{secure_asset('assets/global/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{secure_asset('assets/global/plugins/counterup/jquery.counterup.min.js')}}" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ secure_asset('assets/pages/scripts/dashboard.js') }}" type="text/javascript"></script> -->
<!-- END PAGE LEVEL SCRIPTS -->

@endsection
