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
                <span>Queued Shipping</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Sub-Orders
        <small> queued</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">

        @if(count($sub_orders) > 0)

            @foreach($sub_orders as $sub_order)

                <div class="col-md-6 small">

                    <div class="mt-element-ribbon bg-grey-steel" style="overflow: hidden;">

                    <a href="javascript:void(0)" class="ribbon ribbon-right ribbon-vertical-right ribbon-shadow ribbon-border-dash-vert ribbon-color-default uppercase print_it" suborder_id = "{{ $sub_order->unique_suborder_id }}">
                            <div class="ribbon-sub ribbon-bookmark"></div>
                            <i class="fa fa-print"></i>
                        </a>

                        <div class="ribbon ribbon-shadow ribbon-color-warning uppercase">{{ $sub_order->unique_suborder_id }}</div>
                        <div class="ribbon-content" style="overflow:hidden;">

                            <div id="{{ $sub_order->unique_suborder_id }}">
                                <?php
                                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B",1.5,33) . '" alt="barcode"   /><br>';
                                ?>
                                {{ $sub_order->unique_suborder_id }}

                                <h4 class="uppercase">Destination</h4>
                                Destination Hub: <b>{{ $sub_order->destination_hub->title }}</b>
                                <h4 class="uppercase">Products</h4>
                                <div class="product-summery-tbl">
                                    <table style="width:100%">
                                        <thead>
                                            <th>Product</th>
                                            <th style="padding-left:1px solid #FFFFFF;padding-right:1px solid #FFFFFF;">Qty</th>
                                        </thead>
                                        <tbody>
                                            @foreach($sub_order->products as $product)
                                                <tr style="padding-bottom:1px solid #FFFFFF; border-bottom: 1px solid #666666; border-top: 1px solid #666666;">
                                                    <td>
                                                        <b>{{ $product->product_title }}</b>
                                                        <br>
                                                        ID: {{ $product->product_unique_id }}
                                                        <br>
                                                        Cat: {{ $product->product_category->name }}
                                                    </td>
                                                    <td class="numeric" style="padding-left:1px solid #FFFFFF;padding-right:1px solid #FFFFFF;">{{ $product->quantity }}</td>
                                                </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

            @endforeach

        @endIf

    </div>

    <div class="pagination pull-right">
        {{ $sub_orders->render() }}
    </div>

    <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('transfer-suborder', 'tasks');
        });

        $(".print_it").click(function(){
            var suborder_id = $(this).attr("suborder_id");
            $("#"+suborder_id).print({
                globalStyles: true,
                mediaPrint: true,
                stylesheet: null,
                noPrintSelector: ".no-print",
                iframe: true,
                append: null,
                prepend: null,
                manuallyCopyFormValues: true,
                deferred: $.Deferred(),
                timeout: 750,
                title: null,
                doctype: '<!doctype html>'
            });
        });
    </script>

@endsection
