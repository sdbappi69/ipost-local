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
                <a href="{{ secure_url('user') }}">User</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Update</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Users
        <small>update user</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">
        <div class="row step-background-thin">
            <a href="{{ secure_url('user/'.$user->id.'/edit?step=1') }}">
                <div class="col-md-3 bg-grey-steel mt-step-col @if($step=='1') {{ 'active' }} @endif">
                    <div class="mt-step-number">1</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Basic</div>
                    <div class="mt-step-content font-grey-cascade">Personal information</div>
                </div>
            </a>
            <a href="{{ secure_url('user/'.$user->id.'/edit?step=2') }}">
                <div class="col-md-3 bg-grey-steel mt-step-col @if($step=='2') {{ 'active' }} @endif">
                    <div class="mt-step-number">2</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Reference</div>
                    <div class="mt-step-content font-grey-cascade">Choose user type</div>
                </div>
            </a>
            <a href="{{ secure_url('user/'.$user->id.'/edit?step=3') }}">
                <div class="col-md-3 bg-grey-steel mt-step-col @if($step=='3') {{ 'active' }} @endif">
                    <div class="mt-step-number">3</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Photo</div>
                    <div class="mt-step-content font-grey-cascade">Upload profile photo</div>
                </div>
            </a>
            <a href="{{ secure_url('user/'.$user->id.'/edit?step=4') }}">
                <div class="col-md-3 bg-grey-steel mt-step-col @if($step=='4') {{ 'active' }} @endif">
                    <div class="mt-step-number">4</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Password</div>
                    <div class="mt-step-content font-grey-cascade">Set a Password</div>
                </div>
            </a>
        </div>

            @include('users.edit.'.$step)


    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('user-manage', 'users');
        });

    </script>

@endsection
