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
                <span>Receive Pickup</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Product
        <small> receive</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        
        <div class="col-md-12">
            {!! Form::open(array('url' => '/accept-picked/', 'method' => 'post')) !!}

                <div class="form-group col-md-10">

                    {!! Form::text('unique_suborder_id', null, ['class' => 'form-control', 'id' => 'remarks']) !!}

                </div>

                <div class="form-group col-md-2">
                    
                    <span class="form-group-btn">
                        <button class="btn blue" type="submit">Accept</button>
                    </span>

                </div>

            {!! Form::close() !!}

        </div>

        <div class="col-md-12">
            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered">
                <div class="portlet-body util-btn-margin-bottom-5">
                    <table class="table table-bordered table-hover" id="example0">
                        <thead class="flip-content">
                            <!-- <th>Order ID</th> -->
                            <th>Unique ID</th>
                            <th>Picker</th>
                            <th>Picking Time</th>
                            <th>Picking Address</th>
                            <th>Product</th>
                        </thead>
                        <tbody>
                            @if(count($products) > 0)
                            @foreach($products as $p)
                            <tr>
                                <!-- <td>
                                    <a target="_blank" class="label label-success" href="hub-order/{{ $p->order_id }}">
                                        {{ $p->unique_order_id }}
                                    </a>
                                </td> -->
                                <td>{{ $p->unique_suborder_id }}</td>
                                <td>{{ $p->picker_name }}</td>
                                <td>{{ $p->picking_date }} ({{ $p->start_time }}-{{ $p->end_time }})</td>
                                <td> Warehouse: <strong>{{ $p->title }}</strong>
                                    <br>
                                    Phone: {{ $p->msisdn }}, {{ $p->alt_msisdn }}
                                    <br>
                                    Address: {{ $p->address1 }}, {{ $p->zone_name }}, {{ $p->city_name }}, {{ $p->state_name }}
                                </td>
                                <td>
                                    Title: <strong>{{ $p->product_title }}</strong>
                                    <br>
                                    Category: {{ $p->product_category }}
                                    <br>
                                    Quantity: {{ $p->quantity }}
                                </td>
                            </tr>
                            
                            <!-- /.modal-dialog -->
                        
                        @endforeach
                        @endif
                    </tbody>
                </table>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('accept-picked', 'pickup');

            // Datatable
            $('#example0').DataTable({
                "order": [],
            });

            // Checkbox
            $("#select_all_chk").change(function () {
                $("input:checkbox").prop('checked', $(this).prop("checked"));
            });
        });
        
    </script>

@endsection
