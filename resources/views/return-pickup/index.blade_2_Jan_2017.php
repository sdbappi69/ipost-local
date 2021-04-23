@extends('layouts.appinside')

@section('content')

    <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

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
                <span>Return Pickup</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Return
        <small> pickup</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        <div class="col-md-12">
            {!! Form::open(array('url' => '/return-pickup/', 'method' => 'post')) !!}

                <div class="form-group col-md-10">

                    {!! Form::text('product_unique_id', null, ['class' => 'form-control', 'id' => 'remarks']) !!}

                </div>

                <div class="form-group col-md-2">
                    
                    <span class="form-group-btn">
                        <button class="btn blue" type="submit">Find</button>
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
                            <th>Order ID</th>
                            <th>Product ID</th>
                            <th>Picking Address</th>
                            <th>Product</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(count($products) > 0)
                            @foreach($products as $p)
                            <tr>
                                <td>
                                    <a target="_blank" class="label label-success" href="hub-order/{{ $p->order_id }}">
                                        {{ $p->unique_order_id }}
                                    </a>
                                </td>
                                <td>{{ $p->product_unique_id }}</td>
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
                                <td>{{ $p->reason }}</td>
                                <td>
                                    {!! Form::open(array('url' => '/return-pickup/'.$p->id, 'method' => 'put')) !!}

                                        {!! Form::hidden('task_id', $p->task_id, ['class' => 'form-control', 'required' => 'required']) !!}

                                        {!! Form::hidden('picking_quantity', $p->picking_quantity, ['class' => 'form-control', 'required' => 'required']) !!}

                                        <div class="form-group">
                                            <label class="control-label">Pick Date</label>

                                            <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" data-date-start-date="+0d">
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                                {!! Form::text('picking_date', $p->picking_date, ['class' => 'form-control picking_date', 'required' => 'required', 'readonly' => 'true', 'id' => 'picking_date']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group">

                                            <label class="control-label">Select Picker</label>

                                            <select name="picker_id" class="form-control js-example-basic-single js-country" required="required">
                                                <option value="">Select one</option>
                                                @foreach($pickupman as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                            <i class="fa fa-check"></i>
                                            Re-Assign
                                        </button>

                                    {!! Form::close() !!}
                                    <a href="{{ URL::to('return-pickup').'/'.$p->task_id }}" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                        <i class="fa fa-close"></i>
                                        Cancel
                                    </a>

                                    @if($p->task_status == 3)
                                    <a href="{{ URL::to('return-pickup').'/'.$p->task_id }}/edit" class="btn green-sharp btn-md col-md-12 col-lg-12 col-xs-12" data-toggle="confirmation" data-original-title="Are you sure ?" title="">
                                        <i class="fa fa-pencil"></i>
                                        Update
                                    </a>

                                    @endIf
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

    <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

    <script src="{{ URL::asset('custom/js/date-time.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('return-pickup', 'tasks');

            // Datatable
            $('#example0').DataTable({
                "order": [],
            });
        });
    </script>

@endsection
