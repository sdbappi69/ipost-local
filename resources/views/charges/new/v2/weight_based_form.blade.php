
<div class="row" style="display: none;" id="weight_based">

    <div class="col-md-12 fixed animated bounceInDown">

        <div id="accordion3" class="panel-group">

            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#charge_model_2">
                            <i class="fa fa-plus"></i> Weight Based Charges
                        </a>
                        <button id="addNewRange" type="button" class="btn purple" title="">Add Range</button>
                    </h4>
                </div>
                <div id="charge_model_2" class="panel-collapse collapse in">
                    <div class="panel-body">
                        
                        <div class="col-md-12 animated bounceIn" style="display: none;" id="form_1">
                            <div class="portlet light tasks-widget bordered" id="formContent">

                                <div class="portlet-title" id="13">
                                    <div class="caption">
                                        <i class="fa fa-circle" style="color:red"></i>

                                        <span class="caption-subject font-dark bold uppercase">Charges</span>
                                    </div>
                                </div>

                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Minimum Weight</label>
                                            <input type="number" name="min_weight[]" step="0.01" class="form-control input-group-lg number" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Maximum Weight</label>
                                            <input type="number" name="max_weight[]" step="0.01" class="form-control input-group-lg number" required>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">     

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Initial Charge</label>
                                            <input type="number" name="initial_charge[]" step="0.01" class="form-control input-group-lg number" required>
                                        </div>     

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Hub Transfer Charge</label>
                                            <input type="number" name="hub_transfer_charge[]" step="0.01" class="form-control input-group-lg number" required>
                                        </div> 

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Return Charge</label>
                                            <input type="number" name="return_charge[]" step="0.01" class="form-control input-group-lg number" required>
                                        </div>
                                        <button class="btn red removeRangeClass" style="float: left; margin-left: 2%" type="button" title="">Remove</button>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {!! Form::open(array('url' => secure_url('') . '/product-category-charge-update/v2', 'method' => 'post','id'=>'weightBasedForm')) !!}

                        <input type="hidden" name="product_category_id" value="{{ $product_category->id }}">
                        <input type="hidden" name="charge_type" class="chargeType">

                        @if(isset($_GET['store_id']) && $_GET['store_id'] != '')
                        <input type="hidden" name="store_id" value="{{ $_GET['store_id'] }}">
                        @endIf
                        
                        @foreach($charges as $charge)
                        <input type="hidden" name="ipost_charge_id[]" value="{{ $charge->id }}">
                        <div class="col-md-12 animated bounceIn" id="form_{{$i}}">
                            <div class="portlet light tasks-widget bordered" id="formContent">

                                <div class="portlet-title" id="13">
                                    <div class="caption">
                                        @if($charge->approved == 0)
                                        <i class="fa fa-circle" style="color:red"></i>
                                        @else
                                        <i class="fa fa-check-circle" style="color:green"></i>
                                        @endIf

                                        <span class="caption-subject font-dark bold uppercase">Charges</span>
                                    </div>
                                </div>

                                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Minimum Weight</label>
                                            <input type="number" name="min_weight[]" step="0.01" value="{{$charge->min_weight}}" class="form-control input-group-lg number" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Maximum Weight</label>
                                            <input type="number" name="max_weight[]" step="0.01" value="{{$charge->max_weight}}" class="form-control input-group-lg number" required>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">     

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Initial Charge</label>
                                            <input type="number" name="initial_charge[]" step="0.01" value="{{$charge->initial_charge}}" class="form-control input-group-lg number" required>
                                        </div>     

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Hub Transfer Charge</label>
                                            <input type="number" name="hub_transfer_charge[]" step="0.01" value="{{$charge->hub_transfer_charge}}" class="form-control input-group-lg number" required>
                                        </div> 

                                        <div class="form-group col-md-4">
                                            <label class="control-label">Return Charge</label>
                                            <input type="number" name="return_charge[]" step="0.01" value="{{$charge->return_charge}}" class="form-control input-group-lg number" required>
                                        </div>
                                        <a href="{{ secure_url('charge-remove/v2/'.$charge->id) }}" class="btn red" style="float: left; margin-left: 2%" data-toggle="confirmation" data-original-title="Are you sure ?" title="">Remove</a>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <?php $i++; ?>
                        @endforeach

                    </div>
                    <div class="panel-body">
                        {!! Form::submit('Save', ['class' => 'btn green col-md-2', 'style' => 'float:right']) !!}
                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>

    </div>

</div>