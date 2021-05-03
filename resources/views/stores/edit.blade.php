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
                <a href="{{ secure_url('store') }}">Stores</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Update</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Stores
        <small>update store</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        @if(!Auth::user()->hasRole('kam'))
        <div class="row step-background-thin">
            <a href="{{ secure_url('store/'.$store->id.'/edit?step=1') }}">
                <div class="col-md-6 bg-grey-steel mt-step-col @if($step=='1') {{ 'active' }} @endif">
                    <div class="mt-step-number">1</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Basic</div>
                <div class="mt-step-content font-grey-cascade">Store information</div>
                </div>
            </a>
            <a href="{{ secure_url('store/'.$store->id.'/edit?step=2') }}">
                <div class="col-md-6 bg-grey-steel mt-step-col @if($step=='2') {{ 'active' }} @endif">
                    <div class="mt-step-number">2</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Charge</div>
                <div class="mt-step-content font-grey-cascade">Set charge</div>
                </div>
            </a>
        </div>
        @endIf
        
        @include('stores.edit.'.$step)


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('store-manage', 'stores');
        });

    </script>

@endsection
