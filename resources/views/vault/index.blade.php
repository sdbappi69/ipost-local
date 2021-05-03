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
            <span>Vaults</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Vaults
    <small> view</small>
</h1>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->

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
            {!! Form::model($_GET,array('method' => 'get', 'id' => 'filter-form')) !!}
            <div class="col-md-4">
                <label class="control-label">Hubs</label>
                {!! Form::select('hub_id', array(''=>'Select Hub') + $hub, null, ['class' => 'form-control js-example-basic-single', 'id' => 'id']) !!}
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

@if(count($vaults) > 0)

<div class="col-md-12">
    <!-- BEGIN BUTTONS PORTLET-->
    <div class="portlet light tasks-widget bordered">
<!-- <div class="portlet-title">
<div class="caption">
<span class="caption-subject font-green-haze bold uppercase">Hubs</span>
<span class="caption-helper">list</span>
</div>
</div> -->
<div class="portlet-body util-btn-margin-bottom-5">
    <table class="table table-bordered table-hover" id="example0">
        <thead class="flip-content">
            <th>Title</th>
            <th>Hub</th>
            <th>Amount</th>
            <th>Action</th>
        </thead>
        <tbody>
            @foreach($vaults as $vault)
            <tr>
                <td>{{ $vault->title }}</td>
                <td>{{ $vault->hub->title or "N/A"}}</td>
                <td>{{ $vault->amount }}</td>
                <td>
                    <a href="{{secure_url('/vault/'.$vault->id.'/edit')}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</div>
</div>
@else
<div class="col-md-12">
    <div class="portlet light tasks-widget bordered">
        <p>
            No Data Found
        </p>
    </div>
</div>
@endIf

<script type="text/javascript">
    $(document ).ready(function() {
        // Navigation Highlight
        highlight_nav('all-vault', 'vault');

        $('#example0').DataTable({
            "order": [],
        });
    });
</script>
<style media="screen">
.table-filtter .btn{ width: 100%;}
.table-filtter {
    margin: 20px 0;
}
</style>

@endsection
