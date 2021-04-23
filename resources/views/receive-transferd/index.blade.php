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
                <a href="{{ URL::to('hub-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Receive Product</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Receive
        <small> product</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <th>Product ID</th>
                            <th>Received Detail</th>
                            <th>Products</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(count($products) > 0)
                                @foreach($products as $p)
                                    <tr>
                                        <td>{{ $p->product_unique_id }}</td>
                                        <td>
                                            Trip ID: {{ $p->unique_trip_id }}
                                            <br>
                                            Transferd from: <b>{{ $p->hub_title }}</b>
                                            
                                            <h4 class="uppercase">Products</h4>
                                            Title: <strong>{{ $p->product_title }}</strong>
                                            <br>
                                            Category: {{ $p->product_category }}
                                            <br>
                                            Quantity: {{ $p->quantity }}
                                        </td>
                                        <td>
                                            Title: <strong>{{ $p->product_title }}</strong>
                                            <br>
                                            Category: {{ $p->product_category }}
                                            <br>
                                            Quantity: {{ $p->quantity }}
                                        </td>
                                        <td>
                                            <a href="{{ URL::to('receive-transferd').'/'.$p->id.'/edit' }}" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12">
                                                <i class="fa fa-check"></i>Receive
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- /.modal-dialog -->
                            
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('receive-transferd', 'pickup');
        });
    </script>

@endsection
