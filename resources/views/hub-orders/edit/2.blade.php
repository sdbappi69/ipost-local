<link href="{{ secure_asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

<div class="row">
    <br>
    <div id="accordion3" class="panel-group">

        @foreach($products as $row)

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="icon-btn icon-btn-customized accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_{{ $row->id }}">
                            {{ $row->title }}
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

                        <div class="row">

                            @include('partials.errors')

                            <div class="col-md-4">

                                <div class="form-group">
                                    <label class="control-label">Product Title</label>
                                    {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Product name', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Product URL</label>
                                    {!! Form::text('url', null, ['class' => 'form-control', 'placeholder' => 'https://your-product-link']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Product Category</label>
                                    {!! Form::select('product_category_id', array(''=>'Select Category')+$categories, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required', 'id' => 'product_category_id']) !!}
                                </div>

                            </div>

                            <div class="col-md-4">
                                
                                <div class="form-group">
                                    <label class="control-label">Quantity</label>
                                    {!! Form::text('quantity', null, ['class' => 'form-control input-group-lg quantity', 'required' => 'required', 'onkeydown' => 'return false;']) !!}
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

                        {!! Form::hidden('unit_price', null, ['class' => 'form-control', 'placeholder' => '00.00', 'required' => 'required']) !!}

                        {!! Form::hidden('picking_time_slot_id', null, ['required' => 'required']) !!}

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

    <a href="{{ secure_url('receive-picked') }}" class="btn default"> Back </a>
    <a href="{{ secure_url('hub-order') }}/{{ $id }}/edit?step=3" class="btn green pull-right">Next</a>

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