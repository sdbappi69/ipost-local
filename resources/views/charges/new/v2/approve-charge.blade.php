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
            <a href="{{ URL::to('product-category') }}">Category</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Approve Charges</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Approve Charges
    {{-- <small></small> --}}
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
@include('errors.validation_error')
<div class="row">

    <div class="col-md-12 animated flipInX">
        <!-- BEGIN BUTTONS PORTLET-->
        <div class="portlet light tasks-widget bordered">

            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-edit font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">Category Charge Detail</span>

                    <button class="btn purple" id="approveChecked">Approve Checked</button>
                </div>
            </div>

            {{Form::open(array('url'=>'category-charge-approve-all/v2/','method'=>'post','id'=>'checkedForm'))}}
            {{Form::hidden('charge_ids',null,['id'=>'charge_ids'])}}
            {{Form::close()}}
            <div class="portlet-body util-btn-margin-bottom-5">

                <table class="table table-striped table-bordered table-hover dt-responsive example0" style="width: 100%">
                    <thead>
                        <tr>
                            <td><input type="checkbox" id="selectAll">Select All</td>
                            <td>Product Category</td>
                            <td>Store</td>
                            <td>Created By</td>
                            <td>Charge Type</td>
                            <td>Min Range</td>
                            <td>Max Range</td>
                            <td>Initial Charge</td>
                            <td>Hub Transfer Charge</td>
                            <td>Return Charge</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($charges) > 0)
                        @foreach($charges as $charge)
                        <tr>
                            <td><input type="checkbox" name="charge_id" value="{{$charge->id}}"></td>
                            <td>{{$charge->product_category->name ?? ''}}</td>
                            <td>{{$charge->store->store_id ?? ''}}</td>
                            <td>{{$charge->createdBy->name ?? ''}}</td>
                            <td>{{$charge->charge_type ?? ''}}</td>
                            @if($charge->charge_type == 'Fixed')
                            <td></td>
                            <td></td>
                            @else
                            <td>{{$charge->min_weight}}</td>
                            <td>{{$charge->max_weight}}</td>
                            @endif
                            <td>{{$charge->initial_charge}}</td>
                            <td>{{$charge->hub_transfer_charge}}</td>
                            <td>{{$charge->return_charge}}</td>
                            <td>
                                <a href="{{url("category-charge-approved/v2").'/'.$charge->id}}" class="btn green">Approve</a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="11" style="color:red; text-align: center"> Not Data found</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>

<script type="text/javascript">

    $(document).ready(function () {
        highlight_nav('product-category-charge-approve', 'product-category');

        $("#selectAll").click(function () {
            $("input[type=checkbox]").prop('checked', $(this).prop('checked'));

        });
        $("#approveChecked").click(function () {
            var favorite = [];
            $.each($("input[name='charge_id']:checked"), function () {
                favorite.push($(this).val());
            });
            if (favorite.length === 0) {
                alert("Nothing Selected");
            } else {
                $("#charge_ids").val(favorite.join(","));
                $("#checkedForm").submit();
            }
        });
    });

</script>

@endsection
