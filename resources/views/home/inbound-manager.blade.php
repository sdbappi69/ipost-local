@extends('layouts.appinside')

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        {{-- <li>
            <a href="{{ secure_url('home') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li> --}}
        <li>
            <span>Dashboard</span>
        </li>
    </ul>
    <!-- <div class="page-toolbar">
        <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
            <i class="icon-calendar"></i>&nbsp;
            <span class="thin uppercase hidden-xs"></span>&nbsp;
            <i class="fa fa-angle-down"></i>
        </div>
    </div> -->
</div>
<div class="row widget-row">
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Number of pickup request</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-green fa fa-truck"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{$number_of_pickup_request}}"></span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Pending orders with number of pickup attempt</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-red icon-drawer"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{$Pending_orders_with_number_of_pickup_attempt}}">

                    </span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <div class="col-md-3">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Average number of picking attempt</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-yellow icon-screen-desktop"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value=" {{round($avg_number_of_attempt,0)}}">

                    </span>
                    
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
   
</div>
<div class="row widget-row">

    <div class="col-md-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
            <h4 class="widget-thumb-heading">Tenor wise rate (Pickup)</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-blue icon-bar-chart"></i>
                <div class="widget-thumb-body">
                    <table class="table" >
                        <thead>
                          <tr>
                              <th >
                                @if($total_rate_pickup != 0)
                                    Success ({{round(($success_failure_partial_rate->success_rate*100)/$total_rate_pickup,0)}}%)
                                @else
                                    0%
                                @endIf
                              </th>
                              <th >
                                @if($total_rate_pickup != 0)
                                    Failure ({{round(($success_failure_partial_rate->failure_rate*100)/$total_rate_pickup,0)}}%)
                                @else
                                    0%
                                @endIf
                              </th>
                              <th >
                                @if($total_rate_pickup != 0)
                                    Partial ({{round(($success_failure_partial_rate->partial_rate*100)/$total_rate_pickup,0)}}%)
                                @else
                                    0%
                                @endIf
                              </th>
                          </tr>
                      </thead>
                      <tbody>
                         <tr >
                             <td style="text-align: center; font-size: 30px; font-weight: 600; color: #3e4f5e;"   data-counter="counterup" data-value="{{$success_failure_partial_rate->success_rate}}"></td>
                             <td style="text-align: center; font-size: 30px; font-weight: 600; color: #3e4f5e;"   data-counter="counterup" data-value="{{$success_failure_partial_rate->failure_rate}}"></td>
                             <td  style="text-align: center; font-size: 30px; font-weight: 600; color: #3e4f5e;"  data-counter="counterup" data-value="{{$success_failure_partial_rate->partial_rate}}"></td>
                         </tr>

                     </tbody>
                 </table>



             </div>
         </div>
     </div>
     <!-- END WIDGET THUMB -->
 </div>


</div>


<script src="{{secure_asset('assets/global/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{secure_asset('assets/global/plugins/counterup/jquery.counterup.min.js')}}" type="text/javascript"></script>


<script type="text/javascript">
    $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('dashboard', 'dashboard');
        });
    </script>

    @endsection
