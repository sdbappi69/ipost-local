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
                <span>Merchants</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Merchants
        <small> view & update</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('dashboard', 'dashboard');
        });
    </script>

@endsection
