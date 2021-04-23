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
  <small>Pick-Up (EDIT)</small>
</h1>
<div class="col-md-12">
  <div class="table-filtter">

    {!! Form::open(array('url' => 'consignments-pick-up-submit', 'method' => 'post')) !!}
    {!! Form::hidden('status', '3', ['class' => 'form-control', 'required' => 'required']) !!}

    <div class="col-md-2">
      <div class="row">
        {!! Form::select('picker_id',['' => 'Select Picker']+$pickupman,$consignments->rider_id, ['class' => 'form-control js-example-basic-single', 'id' => 'picker_id']) !!}
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
          <th>Product Unique ID</th>
          <th>Picking Time</th>
          <th>Picking Address</th>
          <th>Productsp</th>
          <th></th>

        </thead>
        <tbody>
          @if(count($products) > 0)
          @foreach($products as $p)
          <tr>
          @if($selected_products->search($p->product_unique_id))
            <td>{!!Form::checkbox('selected_pick_up[]',$p->id, true) !!}</td>
            @else
            <td>{!!Form::checkbox('selected_pick_up[]',$p->id, false) !!}</td>
            @endif
            

            <td>{{ $p->product_unique_id }}</td>
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
            <td><button type="button" value="{{ $p->product_unique_id }}" class="print_modal"><i class="fa fa-folder-open-o" aria-hidden="true"></i></button></td>


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
@if(count($products) > 0)
@foreach($products as $product)
<div class="modal fade" id="invoice_{{ $product->product_unique_id }}" tabindex="-1" role="basic" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">View</h4>
      </div>
      <div class="modal-body">
        <div style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
        font-size: 12px; padding: 15px;width: 2.5in; margin: 0 auto;" id="{{ $product->product_unique_id }}">
        <div style='text-align: center;'>
          <img style="width:2in" src="{{URL::asset('assets/pages/img/login/login-invert.png')}}">
          <br>300/5/A Hatirpool, Dhaka
          <br>Website: www.biddyut.com
          <br>
          <?php
                                                        // echo DNS1D::getBarcodeHTML('4', "C128B",1.5,33);
          echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($product->product_unique_id, "C128B",1,33) . '" alt="barcode"   /><br>';
          ?>
          {{ $product->product_unique_id }}
        </div>
        <p><b>Pick list printed on: {{ date('Y-m-d') }}</b></p>

        <table cellpadding="10"  border="1" style='width : 100%; font-size: 10px;' >

          <tr>
            <td style='padding-left:10px'><b>Pickup req. Date</b></td>
            <td style='padding-left:10px' >{{ $product->picking_date }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>Package Tracking ID</b></td>
            <td style='padding-left:10px' >{{ $product->product_unique_id }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>Merchant Name</b></td>
            <td style='padding-left:10px' >{{ $product->merchant_name }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>Merchant Phone No</b></td>
            <td style='padding-left:10px' >{{ $product->merchant_msisdn }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>Store Name</b></td>
            <td style='padding-left:10px' >{{ $product->title }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>Store Phone No</b></td>
            <td style='padding-left:10px' >{{ $product->msisdn }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>Pickup location</b></td>
            <td style='padding-left:10px' >{{ $product->address1 }}, {{ $product->zone_name }}</td>

          </tr>
          <tr>
            <td style='padding-left:10px' ><b>City</b></td>
            <td style='padding-left:10px' >{{ $product->city_name }}</td>

          </tr>
                                                    <!-- <tr>
                                                      <td style='padding-left:10px' ><b>Postcode</b></td>
                                                      <td style='padding-left:10px' >1205</td>
                                                      
                                                    </tr> -->
                                                    <!-- <tr>
                                                      <td style='padding-left:10px' ><b>Customer Name</b></td>
                                                      <td style='padding-left:10px' >Asima Begum</td>                          
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Address</b></td>
                                                      <td style='padding-left:10px' >600/2, Moghbazar</td>
                                                      
                                                    </tr> -->
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Order Number(Mer)</b></td>
                                                      <td style='padding-left:10px' >20345678</td>
                                                    </tr>

                                                  </table>

                                                  <br>
                                                  <table border="1" style="width : 100%; font-size: 10px;" >

                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Name</b></td>
                                                      <td style='padding-left:10px' >{{ $product->cus_name }}</td>
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Email</b></td>
                                                      <td style='padding-left:10px' >{{ $product->cus_email }}</td>
                                                    </tr>
                                                    <tr>
                                                      <td style='padding-left:10px' ><b>Customer Phone No</b></td>
                                                      <td style='padding-left:10px' >{{ $product->cus_msisdn }}, {{ $product->cus_alt_msisdn }}</td>
                                                    </tr>

                                                  </table>

                                                  <br>
                                                  <table border="1" style="width : 100%; font-size: 10px;" >
                                                    <tr>
                                                     <th style='padding-left:10px' >Product details</th>
                                                     <th style='padding-left:10px' >Product SKUs</th>
                                                     <th style='padding-left:10px' >Quantity</th>
                                                   </tr>
                                                   <tr>
                                                     <td style='padding-left:10px' >{{ $product->product_title }}</td>
                                                     <td style='padding-left:10px' >{{ $product->product_category }}</td>
                                                     <td style='padding-left:10px' >{{ $product->quantity }}</td>

                                                   </table>

                                                   <br>
                                                   <table border="1" style="width : 100%; font-size: 10px;" >
                                                    <tr>
                                                     <th style='padding-left:10px' >Product Details</th>
                                                     <th style='padding-left:10px' >Total of Packages</th>
                                                     <th style='padding-left:10px' >Price</th>
                                                   </tr>
                                                   <tr>
                                                     <td style='padding-left:10px' >{{ $product->product_title }}</td>
                                                     <td style='padding-left:10px' ></td>
                                                     <td style='padding-left:10px' >{{ $product->sub_total }}</td>

                                                   </table>
                                                   <br><br>
                                                   <p><b>Merchant Signature</b></p>
                                                   <br><br><br>
                                                 </div>

                                                 <button type="button" class="btn dark btn-outline print_it" product_id = "{{ $product->product_unique_id }}">Print</button>

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
            highlight_nav('pick_up_consignments', 'consignments');

            // $('#example0').DataTable({
            //     "order": [],
            // });
          });
        </script>

        <script type="text/javascript">
         $(".print_it").click(function(){
          var product_id = $(this).attr("product_id");
            //alert(product_id);
            $("#"+product_id).print({
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
