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
            <span>Cash Collection</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Cash Collection
    <small>All</small>
</h1>
@include('partials.errors')
<div class="col-md-12">

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered animated flipInX">

        <div class="portlet-title">
            <div class="caption">
                <i class="icon-edit font-dark"></i>
                <span class="caption-subject font-dark bold uppercase">Filter</span>
            </div>
        </div>

        <div class="portlet-body util-btn-margin-bottom-5">
            {!! Form::open(array('method' => 'get')) !!}
            <?php if(!isset($_GET['sub_unique_id'])){$_GET['sub_unique_id'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Sub-Order ID</label>
                <input type="text" value="{{$_GET['sub_unique_id']}}" class="form-control" name="sub_unique_id" id="sub_unique_id" placeholder="Sub-Order ID">
            </div>


            <?php if(!isset($_GET['order_unique_id'])){$_GET['order_unique_id'] = null;} ?>
            <div class="col-md-4">
                <label class="control-label">Order ID</label>
                <input type="text" value="{{$_GET['order_unique_id']}}" class="form-control" name="order_unique_id" id="order_unique_id" placeholder="Order ID">
            </div>


            <div class="col-md-2">
                <label class="control-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Filter</button>
            </div>
            <div class="clearfix"></div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

<div class="col-md-12">
    {!! Form::open(array('url' => 'cash-collection-submit', 'method' => 'post')) !!}

    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
        <div class="portlet-body util-btn-margin-bottom-5">
            <table class="table table-bordered table-hover" id="example0">
                <thead class="flip-content">
                    <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
                    <th>Sub-Order ID</th>
                    <th>Order ID</th>
                    <th></th>
                    <th>Delivery Amount</th>
                    <th>Bill Amount</th>
                </thead>
                <tbody>
                    @if(count($cash_collection) > 0 )
                    <?php $total_list_delivery_paid = 0 ;?>
                    <?php $total_list_cod = 0 ;?>
                    <?php $total_list_cod_charge = 0 ;?>
                    @foreach($cash_collection as $c)
                    <input type="hidden" name="order_id_{{$c->id}}" value="{{$c->order->id}}">
                    <input type="hidden" name="merchant_id_{{$c->id}}" value="{{$c->order->store->merchant_id}}">
                    <input type="hidden" name="store_id_{{$c->id}}" value="{{$c->order->store_id}}">
                    <input type="hidden" name="sub_unique_id_{{$c->id}}" value="{{$c->unique_suborder_id}}">
                    <tr>
                        <td>{!!Form::checkbox('sub_order_id[]',$c->id, false) !!}</td>
                        <td>{{$c->unique_suborder_id}}</td>
                        <td>{{$c->order->unique_order_id}}</td>
                        <td>
                            <button type="button" data-toggle="collapse" data-target="#demo_{{$c->unique_suborder_id}}"><i class="fa fa-plus"></i></button>

                            <div id="demo_{{$c->unique_suborder_id}}" class="collapse">
                                <?php $total_product_delivery_paid = 0 ;?>
                                <?php $total_cod_ammount = 0 ;?>
                                <?php $total_cod_charge = 0 ;?>
                                <?php $total_delivery_charge = 0 ;?>
                                <?php $total_paid_amount = 0 ;?>
                                @foreach($c->products as $product)

                                <?php $total_delivery_charge += $product->total_delivery_charge;?>

                                <b>{{ $product->product_title }} 
                                    <br>
                                    ID: {{ $product->product_unique_id }}
                                    <br>
                                    Cat: {{ $product->product_category->name }}
                                    <br>
                                    Qty :{{ $product->quantity }}
                                    <br>
                                    Collected :{{ $product->delivery_paid_amount }}
                                    <br>

                                    <?php
                                    if($product->delivery_pay_by_cus == 1){

                                        $temp_cod =  $product->delivery_paid_amount - $product->total_delivery_charge;
                                        $total_paid_amount += $product->total_delivery_charge;
                                    }
                                    else {

                                        $temp_cod = $product->delivery_paid_amount;
                                    }
                                    ?>

                                    COD : {{$temp_cod}}
                                    <br>
                                    <?php
                                    $temp_cod_charge = calculate_cod_charge($c->order->store_id,$temp_cod);
                                    $temp_cod_json = null;
                                    $temp_cod_json =  $temp_cod_charge['charge_model'];
                                    ?>
                                    COD Charge : {{$temp_cod_charge['cod_charge']}}
                                    <br><br>
                                    <?php $total_product_delivery_paid += $product->delivery_paid_amount; ?>
                                    <?php $total_cod_ammount += $temp_cod; ?>
                                    <?php $total_cod_charge += $temp_cod_charge['cod_charge']; ?>
                                    <input type="hidden" name="cod_charge_info_{{$c->id}}" value="{{$temp_cod_json}}">
                                    @endforeach

                                </div>


                            </td>
                            <td>
                                {{$total_product_delivery_paid}}
                                <input type="hidden" name="collected_amount_{{$c->id}}" value="{{$total_product_delivery_paid}}">
                                <input type="hidden" name="cod_amount_{{$c->id}}" value="{{$total_cod_ammount}}">
                                <input type="hidden" name="cod_charge_{{$c->id}}" value="{{$total_cod_charge}}">
                            </td>
                            <td>{{$total_delivery_charge}}</td>
                            <input type="hidden" name="bill_amount_{{$c->id}}" value="{{$total_delivery_charge}}">
                            <input type="hidden" name="total_bill_amount_{{$c->id}}" value="{{$total_delivery_charge+$total_cod_charge}}">
                            <input type="hidden" name="paid_amount_{{$c->id}}" value="{{$total_paid_amount}}">

                        </tr>
                        <?php $total_list_delivery_paid +=  $total_product_delivery_paid; ?>
                        <?php $total_list_cod +=  $total_cod_ammount; ?>
                        <?php $total_list_cod_charge +=  $total_cod_charge; ?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total Delivery Amount</b></td>
                            <td><b>{{ $total_list_delivery_paid }}</b></td>
                            <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @if(count($cash_collection) > 0 )
                <center><div >
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
                {{$cash_collection->appends($_REQUEST)->render()}}
            </center>
            @endif

        </div>
    </div>
</div>
{!! Form::close() !!}
<script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('cash_collection', 'accounts_bills');

        // $('#example0').DataTable({
        //     "order": [],
        // });
    });
</script>


<script type="text/javascript">
    $("#select_all_chk").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
</script>



@endsection
