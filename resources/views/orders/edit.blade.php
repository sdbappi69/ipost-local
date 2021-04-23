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
                <a href="{{ URL::to('order') }}">Orders</a>
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
            <a href="{{ URL::to('order/'.$order->id.'/edit?step=1') }}">
                <div class="col-md-4 bg-grey-steel mt-step-col @if($step=='1') {{ 'active' }} @endif">
                    <div class="mt-step-number">1</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Basic</div>
                    <div class="mt-step-content font-grey-cascade">shipping information</div>
                </div>
            </a>
            <a href="{{ URL::to('order/'.$order->id.'/edit?step=2') }}">
                <div class="col-md-4 bg-grey-steel mt-step-col @if($step=='2') {{ 'active' }} @endif">
                    <div class="mt-step-number">2</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Product</div>
                    <div class="mt-step-content font-grey-cascade">products information</div>
                </div>
            </a>
            <a href="{{ URL::to('order/'.$order->id.'/edit?step=3') }}">
                <div class="col-md-4 bg-grey-steel mt-step-col @if($step=='3') {{ 'active' }} @endif">
                    <div class="mt-step-number">3</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Confirm</div>
                    <div class="mt-step-content font-grey-cascade">order confirm</div>
                </div>
            </a>
        </div>

            @include('orders.edit.'.$step)


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('orders', 'orders');
        });

    </script>

@endsection
