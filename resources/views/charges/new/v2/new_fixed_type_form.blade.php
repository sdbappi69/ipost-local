<div class="row"  style="display: none;" id="fixed">

    <div class="col-md-12 fixed animated bounceInDown">

        <div id="accordion3" class="panel-group">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#charge_model_1">
                            <i class="fa fa-plus"></i> Fixed Charges
                        </a>
                    </h4>
                </div>
                <div id="charge_model_1" class="panel-collapse collapse in">
                    <div class="panel-body">

                        {!! Form::open(array('url' => secure_url('') . '/product-category-charge-submit/v2', 'method' => 'post')) !!}
                        <input type="hidden" name="product_category_id" value="{{ $product_category->id }}">
                        <input type="hidden" name="charge_type" class="chargeType">

                        @if(isset($_GET['store_id']) && $_GET['store_id'] != '')
                        <input type="hidden" name="store_id" value="{{ $_GET['store_id'] }}">
                        @endIf
                        <div class="col-md-12 animated bounceIn">
                            <!-- BEGIN BUTTONS PORTLET-->
                            <div class="portlet light tasks-widget bordered">

                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-circle" style="color:red"></i>

                                        <span class="caption-subject font-dark bold uppercase">Charge</span>
                                    </div>
                                </div>

                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Initial Charge</label>
                                        {!! Form::number('initial_charge', null, ['class' => 'form-control input-group-lg number', 'step'=>"0.01", 'required' => 'required']) !!}
                                    </div>                               

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Hub Transfer Charge</label>
                                        {!! Form::number('hub_transfer_charge', null, ['class' => 'form-control input-group-lg number', 'step'=>"0.01", 'required' => 'required']) !!}
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Return Charge</label>
                                        {!! Form::number('return_charge', null, ['class' => 'form-control input-group-lg number', 'step'=>"0.01", 'required' => 'required']) !!}
                                    </div>

                                </div>


                            </div>
                            {!! Form::submit('Save', ['class' => 'btn green col-md-2', 'style' => 'float:right']) !!}
                        </div>

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>