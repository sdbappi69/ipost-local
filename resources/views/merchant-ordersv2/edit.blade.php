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
                <a href="{{ secure_url('merchant-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Update</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Order
        <small>update order</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        <div class="row step-background-thin">
            <a href="{{ secure_url('merchant-orderv2/'.$order->id.'/edit?step=1') }}">
                <div class="col-md-6 bg-grey-steel mt-step-col @if($step=='1') {{ 'active' }} @endif">
                    <div class="mt-step-number">1</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Order</div>
                    <div class="mt-step-content font-grey-cascade">Order information</div>
                </div>
            </a>
            <a href="{{ secure_url('merchant-orderv2/'.$order->id.'/edit?step=3') }}">
                <div class="col-md-6 bg-grey-steel mt-step-col @if($step=='3') {{ 'active' }} @endif">
                    <div class="mt-step-number">2</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Confirmation</div>
                    <div class="mt-step-content font-grey-cascade">Order confirm</div>
                </div>
            </a>
        </div>

        <div class="portlet light tasks-widget bordered">
            <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                @include('merchant-ordersv2.edit.'.$step)

            </div>
        </div>

    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-order-manage', 'merchant-orders');
        });

    </script>

@endsection
