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
      <span>Consignments</span>
    </li>
  </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Consignments
  <small>Delivery</small>
</h1>
<div class="col-md-12">
  <div class="table-filtter">

    {!! Form::open(array('url' => 'consignments-delivery-submit', 'method' => 'post')) !!}
    

    <div class="col-md-2">
      <div class="row">
        {!! Form::select('deliveryman_id',['' => 'Select Delivery Man']+$deliveryman,old('deliveryman_id'), ['class' => 'form-control js-example-basic-single', 'id' => 'deliveryman_id']) !!}
      </div>
    </div>

    <div class="col-md-1">
      <div class="row">
        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>Assign</button>
      </div>
    </div>
    <div class="clearfix"></div>

  </div>
</div> <br><br>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->



<div class="col-md-12">
  <!-- BEGIN BUTTONS PORTLET-->
  <div class="portlet light tasks-widget bordered">
    <div class="portlet-body util-btn-margin-bottom-5">
      <table class="table table-bordered table-hover" id="example0">
        <thead class="flip-content">
          <th>{!!Form::checkbox('name', 'value', false,array('id'=>'select_all_chk')) !!}</th>
          <th>Unique ID</th>

          <th>Address</th>
          <th>Products</th>
          
          <!-- <th></th> -->

        </thead>
        <tbody>
          @if(count($sub_orders) > 0)
          @foreach($sub_orders as $sub_order)
          <tr>
            {!! Form::hidden('order_id[]', $sub_order->order->id, ['class' => 'form-control', 'required' => 'required']) !!}
            <td>{!!Form::checkbox('sub_order_id[]',$sub_order->id, false) !!}</td>
            <td> {{ $sub_order->unique_suborder_id }}</td>

            <td><h4 class="uppercase">Shipping</h4>
              Address: <b>{{ $sub_order->order->delivery_address1 }}, {{ $sub_order->order->delivery_zone->name }}, {{ $sub_order->order->delivery_zone->city->name }}, {{ $sub_order->order->delivery_zone->city->state->name }}
            </td>
            <td>
              <button type="button" data-toggle="collapse" data-target="#demo_{{$sub_order->unique_suborder_id}}">See All</button>

              <div id="demo_{{$sub_order->unique_suborder_id}}" class="collapse">
                @foreach($sub_order->products as $product)


                <b>{{ $product->product_title }}</b>
                <br>
                ID: {{ $product->product_unique_id }}
                <br>
                Cat: {{ $product->product_category->name }}
                <br>
                Qty :{{ $product->quantity }}

                <br><br>
                @endforeach
              </div>

              
            </td>

            <!-- <td><button type="button" value="{{ $sub_order->unique_suborder_id }}" class="print_modal"><i class="fa fa-folder-open-o" aria-hidden="true"></i></button></td> -->


          </tr>

          <!-- /.modal-dialog -->
        </div>
        @endforeach
        @endif
      </tbody>
    </table>
    {!! Form::close() !!}
  </div>
</div>
</div>
@if(count($sub_orders) > 0)
@foreach($sub_orders as $sub_order)
<div class="modal fade" id="invoice_{{ $sub_order->unique_suborder_id }}" tabindex="-1" role="basic" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Invoice</h4>
      </div>
      <div class="modal-body">
        <div style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
      font-size: 12px; padding: 15px;width: 2.5in; margin: 0 auto;" id="{{ $sub_order->unique_suborder_id }}">
          <div style='text-align: center;'>
            <img src="{{URL::asset('assets/pages/img/login/login-invert.png')}}">
            <br>300/5/A Hatirpool, Dhaka
            <br>Website: www.biddyut.com
            <br><br>
            <?php
            echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($sub_order->unique_suborder_id, "C128B") . '" alt="barcode"   /><br>';
            ?>
            {{ $sub_order->unique_suborder_id }}
          </div>

          <br>
          <table cellpadding="10"  border="1" style='width : 100%; font-size: 10px;' >

            <tr>
              <td style='padding-left:10px'><b>Package Tracking ID</b></td>
              <td style='padding-left:10px' >{{ $sub_order->unique_suborder_id }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Order Date</b></td>
              <td style='padding-left:10px' >{{ $sub_order->created_at }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Merchant Name</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->store->merchant->name }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Merchant Phone No</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->store->merchant->msisdn }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Merchant Order Id</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->merchant_order_id }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Order Date</b></td>
              <td style='padding-left:10px' >{{ $sub_order->created_at }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Customer Name</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->delivery_name }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Customer Phone</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->delivery_msisdn }}</td>

            </tr>
            <tr>
              <td style='padding-left:10px' ><b>Customer address</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->delivery_address1 }}</td>

            </tr>

            <tr>
              <td style='padding-left:10px' ><b>Zone</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->delivery_zone->name }}</td>

            </tr>

            <tr>
              <td style='padding-left:10px' ><b>City</b></td>
              <td style='padding-left:10px' >{{ $sub_order->order->delivery_zone->city->name }}</td>

            </tr>

                                    <!-- <tr>
                                      <td style='padding-left:10px' ><b>Postcode</b></td>
                                      <td style='padding-left:10px' >1217</td>
                                      
                                    </tr> -->
                                    <!-- <tr>
                                      <td style='padding-left:10px' ><b>Purchase order No :</b></td>
                                      <td style='padding-left:10px' >20345678</td>
                                      
                                    </tr> -->
                                    <!-- <tr>
                                      <td style='padding-left:10px' ><b>Payment Type</b></td>
                                      <td style='padding-left:10px' >Cash On Delivery</td>
                                      
                                    </tr> -->
                                    <tr>
                                      <td style='padding-left:10px' ><b>Shipping Charge</b></td>
                                      <?php 
                                      $delivery_charge = 0;
                                      $product_price = 0;
                                      $collectable_amount = 0;
                                      ?>
                                      @foreach($sub_order->products as $product)
                                      <?php
                                      $delivery = $product->unit_deivery_charge * $product->quantity;
                                      $delivery_charge = $delivery_charge + $delivery;
                                      $price = $product->unit_price * $product->quantity;
                                      $product_price = $product_price + $price;

                                      $collectable_amount = $collectable_amount + $product->total_payable_amount;
                                      ?>
                                      @endforeach
                                      <td style='padding-left:10px' >{{ $delivery_charge }}</td>
                                      
                                    </tr>
                                    <tr>
                                      <td style='padding-left:10px' ><b>Product Price</b></td>
                                      <td style='padding-left:10px' >{{ $product_price }}</td>
                                      
                                    </tr>
                                    
                                  </table>
                                  <br>
                                  <table border="1" style="width : 100%; font-size: 10px;" >
                                    <tr>
                                     <th style='padding-left:10px' >Item Details</th>
                                     <th style='padding-left:10px' >Price</th>
                                     <th style='padding-left:10px' >Qty</th>
                                   </tr>

                                   @foreach($sub_order->products as $row)
                                   <tr>
                                     <td style='padding-left:10px' >{{ $row->product_title }}</td>
                                     <td style='padding-left:10px' >{{ $row->unit_price }}</td>
                                     <td style='padding-left:10px' >{{ $row->quantity }}</td>
                                   </tr>
                                   @endforeach

                                 </table>

                                 <br>
                                 <table border="1" style="width : 100%; font-size: 10px;" >
                                  <tr>
                                   <th style='padding-left:10px' >Product price</th>
                                   <th style='padding-left:10px' >Shipping charge</th>
                                   <th style='padding-left:10px' >Amount to be collected</th>
                                 </tr>
                                 <tr>
                                   <td style='padding-left:10px' >{{ $product_price }}</td>
                                   <td style='padding-left:10px' >{{ $delivery_charge }}</td>
                                   <td style='padding-left:10px' >{{ $collectable_amount }}</td>

                                 </table>
                                 <br><br>
                                 <p><b>Customer Signature</b></p>
                                 <br><br><br>
                               </div>

                               <button type="button" class="btn dark btn-outline print_it" suborder_id = "{{ $sub_order->unique_suborder_id }}">Print</button>

                             </div>
                           </div>
                         </div>
                         <!-- /.modal-content -->
                       </div>
                       <!-- /.modal-dialog -->
                     </div>

                     @endforeach
                     @endif
                     <script src="{{ URL::asset('custom/js/jQuery.print.js') }}" type="text/javascript"></script>
                     <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
                     <script type="text/javascript">
                      $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('delivery_consignments', 'consignments');

            // $('#example0').DataTable({
            //     "order": [],
            // });
          });
        </script>

        <script type="text/javascript">
          $(".print_it").click(function(){
            var suborder_id = $(this).attr("suborder_id");
            // alert(suborder_id);
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

        <script type="text/javascript">

          $(".print_modal").click(function(){
            var product_id = $(this).val();

            $('#invoice_'+product_id).modal('show');
          }); 
        </script>

        <script type="text/javascript">
          $("#select_all_chk").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
          });
        </script>

        @endsection
