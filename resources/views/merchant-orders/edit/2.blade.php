<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

<div class="row">
    <br>
    {!! Form::open(array('url' => secure_url('') . '/bulkproduct', 'method' => 'post', 'files' => true)) !!}

        {!! Form::hidden('order_id', $id, ['class' => 'form-control', 'required' => 'required']) !!}

        <!-- <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputFile1">Bulk Upload</label>
                <input type="file" id="exampleInputFile1" name="bulk_products" required="required">
                {!! Form::submit('Bulk Upload', ['class' => 'btn green']) !!}
            </div>
        </div>
        <div class="col-md-9">
            <p class="help-block">Click here to download <a href="{{ secure_url('sample.xls') }}">sample.xls</a></p>
        </div> -->

    {!! Form::close() !!}
</div>

<div class="row">
    
    <div id="accordion3" class="panel-group">

        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_new">
                        <i class="fa fa-plus"></i> Add Product
                    </a>
                </h4>
            </div>
            <div id="accordion3_new" class="panel-collapse collapse">
                <div class="panel-body">
                    
                {!! Form::open(array('url' => secure_url('') . '/product', 'method' => 'post')) !!}

                    <div class="row">

                        @include('partials.errors')

                        <div class="col-md-4">

                            {!! Form::hidden('order_id', $id, ['class' => 'form-control', 'required' => 'required']) !!}

                            <div class="form-group">
                                <label class="control-label">Product Title</label>
                                {!! Form::text('product_title', null, ['class' => 'form-control', 'placeholder' => 'Product name', 'required' => 'required']) !!}
                            </div>

                            <!-- <div class="form-group"> -->
                                <!-- <label class="control-label">Product URL</label> -->
                                {!! Form::hidden('url', null, ['class' => 'form-control', 'placeholder' => 'https://your-product-link']) !!}
                            <!-- </div> -->

                            <div class="form-group">
                                <label class="control-label">Product Category</label>
                                {!! Form::select('product_category_id', array(''=>'Select Category')+$categories, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'product_category_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Unit Price (BDT)</label>
                                {!! Form::text('unit_price', null, ['class' => 'form-control', 'placeholder' => '00.00', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Quantity</label>
                                {!! Form::text('quantity', '1', ['class' => 'form-control input-group-lg', 'id' => 'quantity', 'required' => 'required', 'onkeydown' => 'return false;']) !!}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label class="control-label">Weight (KG)</label>
                                {!! Form::text('weight', '0.1', ['class' => 'form-control input-group-lg weight', 'required' => 'required', 'min' => '0.1']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Width (CM)</label>
                                {!! Form::text('width', '0', ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Height (CM)</label>
                                {!! Form::text('height', '0', ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Length (CM)</label>
                                {!! Form::text('length', '0', ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label class="control-label">Warehouse</label>
                                {!! Form::select('pickup_location_id', array(''=>'Select Warehouse')+$warehouse, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'pickup_location_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Pick Date</label>

                                <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" data-date-start-date="+0d">
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                    {!! Form::text('picking_date', null, ['class' => 'form-control picking_date', 'required' => 'required', 'readonly' => 'true', 'id' => 'picking_date']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Pick Time</label>
                                {!! Form::select('picking_time_slot_id', array(''=>'Select Pick Time'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'picking_time_slot_id']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Select Status</label>
                                {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                            </div>

                        </div>

                    </div>

                    &nbsp;
                    <div class="row padding-top-10">
                        <!-- <a href="javascript:history.back()" class="btn default"> Cancel </a> -->
                        {!! Form::reset('Reset', ['class' => 'btn default']) !!}
                        {!! Form::submit('Add', ['class' => 'btn green pull-right']) !!}
                    </div>

                {!! Form::close() !!}

                </div>
            </div>
        </div>

        @foreach($products as $row)

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="icon-btn icon-btn-customized accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_{{ $row->id }}">
                            {{ $row->product_title }}
                            <span class="badge badge-success"> {{ $row->quantity }} </span>
                        </a>

                        <!-- Delete -->
                        {!! Form::open(array('url' => secure_url('') . '/product/'.$row->id, 'method' => 'post')) !!}
                            {{ method_field('DELETE') }}
                            <button type="submit" id="delete-task-{{ $row->id }}" class="btn btn-danger btn-xs pull-right" style="margin-top:-20px;">
                                <i class="fa fa-btn fa-trash"></i> Remove
                            </button>
                        {!! Form::close() !!}

                    </h4>
                </div>
                <div id="accordion3_{{ $row->id }}" class="panel-collapse collapse">
                    <div class="panel-body">
                        
                    {!! Form::model($row, array('url' => secure_url('') . '/product/'.$row->id, 'method' => 'put')) !!}
                    {!! Form::hidden('order_id', $id, ['class' => 'form-control', 'required' => 'required']) !!}
                        <div class="row">

                            @include('partials.errors')

                            <div class="col-md-4">

                                <div class="form-group">
                                    <label class="control-label">Product Title</label>
                                    {!! Form::text('product_title', null, ['class' => 'form-control', 'placeholder' => 'Product name', 'required' => 'required']) !!}
                                </div>

                                <!-- <div class="form-group"> -->
                                    <!-- <label class="control-label">Product URL</label> -->
                                    {!! Form::hidden('url', null, ['class' => 'form-control', 'placeholder' => 'https://your-product-link']) !!}
                                <!-- </div> -->

                                <div class="form-group">
                                    <label class="control-label">Product Category</label>
                                    {!! Form::select('product_category_id', array(''=>'Select Category')+$categories, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'product_category_id']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Unit Price (BDT)</label>
                                    {!! Form::text('unit_price', null, ['class' => 'form-control', 'placeholder' => '00.00', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Quantity</label>
                                    {!! Form::text('quantity', null, ['class' => 'form-control input-group-lg quantity', 'required' => 'required', 'onkeydown' => 'return false;']) !!}
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group">
                                    <label class="control-label">Weight (KG)</label>
                                    {!! Form::text('weight', null, ['class' => 'form-control input-group-lg weight', 'required' => 'required', 'min' => '0.1']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Width (CM)</label>
                                    {!! Form::text('width', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Height (CM)</label>
                                    {!! Form::text('height', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Length (CM)</label>
                                    {!! Form::text('length', null, ['class' => 'form-control input-group-lg number', 'required' => 'required']) !!}
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group">
                                    <label class="control-label">Warehouse</label>
                                    {!! Form::select('pickup_location_id', array(''=>'Select Warehouse')+$warehouse, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'pickup_location_id']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Pick Date</label>

                                    <div class="input-group input-medium date date-picker input-full" data-date-format="yyyy-mm-dd" data-date-start-date="+0d">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                        {!! Form::text('picking_date', null, ['class' => 'form-control picking_date', 'required' => 'required', 'readonly' => 'true', 'id' => $row->id]) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Pick Time</label>
                                    {!! Form::select('picking_time_slot_id', array(''=>'Select Pick Time')+$picking_time_slot, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'picking_time_slot_id_'.$row->id]) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Select Status</label>
                                    {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                                </div>

                            </div>

                        </div>

                        &nbsp;
                        <div class="row padding-top-10">
                            {!! Form::submit('Update', ['class' => 'btn green pull-right']) !!}
                        </div>

                    {!! Form::close() !!}

                    </div>
                </div>
            </div>

        @endforeach

    </div>

    <a href="{{ secure_url('merchant-order') }}/{{ $id }}/edit?step=1" class="btn default"> Back </a>
    <a href="{{ secure_url('merchant-order') }}/{{ $id }}/edit?step=3" class="btn green pull-right">Next</a>

</div>

<script src="{{ secure_asset('assets/global/plugins/fuelux/js/spinner.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/components-bootstrap-touchspin.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('custom/js/date-time.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    // $("#quantity").TouchSpin();

    $('#quantity').TouchSpin({
                min: 1,
                max: 10000,
            });
    $('.quantity').TouchSpin({
                min: 1,
                max: 10000,
            });

    $('.number').TouchSpin({
                min: 0,
                max: 999999,
                step: 0.1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
            });

    $('.weight').TouchSpin({
                min: 0.1,
                max: 999999,
                step: 0.1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
            });

    // Get Pick-up time On date Change
    $('#picking_date').on('change', function() {
        var date = $(this).val();
        var day = dayOfWeek(date);

        pick_up_slot(day);
    });

    // Get Pick-up time On date Change
    $('.picking_date').on('change', function() {
        var id = $(this).attr("id");
        var date = $(this).val();
        var day = dayOfWeek(date);

        pick_up_slot_by_id(id,day);
    });

</script>