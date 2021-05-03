@extends('layouts.appinside')

@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ secure_url('home') }}">Home</a>
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
    <small>view & update</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

<div class="portlet light tasks-widget bordered animated flipInX">

    <div class="portlet-title">
        <div class="caption">
            <i class="icon-edit font-dark"></i>
            <span class="caption-subject font-dark bold uppercase">Filter</span>
        </div>
    </div>
    <div class="portlet-body util-btn-margin-bottom-5">
        {!! Form::open(array('url' => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'method' => 'get', 'id' => 'filter-form')) !!}
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="Email">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Primary Contact</label>
            <input type="text" class="form-control" name="msisdn" id="msisdn" placeholder="Primary Contact">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Secondary Contact</label>
            <input type="text" class="form-control" name="alt_msisdn" id="alt_msisdn" placeholder="Secondary Contact">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Website</label>
            <input type="text" class="form-control" name="website" id="website" placeholder="Website">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Billing</label>
            <input type="text" class="form-control" name="billing_date" id="billing_date" placeholder="Billing">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Due Date</label>
            <input type="text" class="form-control" name="due_date" id="due_date" placeholder="Due Date">
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Status</label>
            {!! Form::select('status', ['' => 'Status', '1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'id' => 'status']) !!}
        </div>
        <div class="col-md-4" style="margin-bottom:5px;">
            <label class="control-label">Responsible</label>
            {!! Form::select('responsible_user_id[]', $salesteam, null, ['class' => 'form-control js-example-basic-single','id' => 'responsible_user_id', 'multiple' => '']) !!}
        </div>
        <div class="col-md-12">
            <button type="button" class="btn btn-primary filter-btn pull-right"><i class="fa fa-search"></i> Filter</button>
        </div>
        <div class="clearfix"></div>
        {!! Form::close() !!}

    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">Merchants</span>
                </div>
                <div class="tools">
                    <button type="button" class="btn btn-primary export-btn"><i class="fa fa-file-excel-o"></i></button>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" width="100%" id="example0">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Alt. Mobile</th>
                            <th>Website</th>
                            <th>Billing Date</th>
                            <th>Billing Type</th>
                            {{-- <th>Due Date</th> --}}
                            <th>Created By</th>
                            <th>Responsible</th>
                            <th>Status</th>
                            <th>View</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($merchant) > 0)
                            @foreach($merchant as $mcnt)
                                <tr>
                                    <td>
                                        <img class="table-thumb" class="img-circle" src="{{ secure_url('') . $mcnt->merchant_photo }}" alt="thumb">
                                    </td>
                                    <td>{{ $mcnt->merchant_name }}</td>
                                    <td>{{ $mcnt->merchant_email }}</td>
                                    <td>{{ $mcnt->merchant_msisdn }}</td>
                                    <td>{{ $mcnt->alt_msisdn }}</td>
                                    <td>{{ $mcnt->merchant_website }}</td>
                                    <td>{{ $mcnt->billing_date }}</td>
                                    <td>{{ $mcnt->billing_type }}</td>
                                    {{-- <td>{{ $mcnt->due_date }}</td> --}}
                                    <td>{{ $mcnt->creator_name }}</td>
                                    <td>{{ $mcnt->responsible_name }}</td>
                                    <td>{!! $mcnt->merchant_status == 1 ? '<span class="label label-success"> Active </span>' : '<span class="label label-danger"> Inactive </span>' !!}</td>
                                    <td>
                                        <a href="merchant/{{ $mcnt->merchant_id }}"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                    <td>
                                        <a href="merchant/{{ $mcnt->merchant_id }}/edit"><i class="fa fa-pencil"></i> Update </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="pagination pull-right">
                    {!! $merchant->appends($req)->render() !!}
                </div>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('merchant-manage', 'merchants');

        <?php if(!isset($_GET['responsible_user_id'])){$_GET['responsible_user_id'] = array();} ?>
        $('#responsible_user_id').select2().val([{!! implode(",", $_GET['responsible_user_id']) !!}]).trigger("change");

        $('#example0').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "bSort": false
        });
    });

    $(".filter-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('merchant') }}").submit();
    });

    $(".export-btn").click(function(e){
        e.preventDefault();
        $('#filter-form').attr('action', "{{ secure_url('merchantexport/xls') }}").submit();
    });

</script>
<style media="screen">
.table-filtter .btn{ width: 100%;}
.table-filtter {
    margin: 20px 0;
}
</style>

@endsection
