@extends('layouts.appinside')

@section('content')

<link href="{{ URL::asset('assets/pages/css/error.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL::to('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Error</span>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12 page-404">
            <div class="number font-green" style="top:10px"> 404 </div>
            <div class="details">
                <h3>Oops! You're lost.</h3>
                <p> We can not find the page you're looking for.
                    <br/>
                    <a href="{{ URL::to('home') }}"> Return home </a></p>
            </div>
        </div>
    </div>

@endsection
