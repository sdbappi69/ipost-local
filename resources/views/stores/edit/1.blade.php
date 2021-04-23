{!! Form::model($store, array('url' => '/store/'.$store->id.'?step=2', 'method' => 'put')) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-6">

            <div class="form-group">
                <label class="control-label">Store Id</label>
                {!! Form::text('store_id', null, ['class' => 'form-control', 'placeholder' => 'Store id', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Store Password</label>
                {!! Form::text('store_password', null, ['class' => 'form-control', 'placeholder' => 'Store password', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Status</label>
                {!! Form::select('status', array('1' => 'Active','0' => 'Inactive'), null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
            </div>

        </div>

        <div class="col-md-6">

            <div class="form-group">
                <label class="control-label">Store Url</label>
                {!! Form::text('store_url', null, ['class' => 'form-control', 'placeholder' => 'Store Url', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Select Merchant</label>
                {!! Form::select('merchant_id', array(''=>'Select Merchant')+$merchants, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                <label class="control-label">Store Type</label>
                {!! Form::select('store_type_id', $storetypes, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-md-12">

            <!-- BEGIN BUTTONS PORTLET-->
            <div class="portlet light tasks-widget bordered animated flipInX">

                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-edit font-dark"></i>
                        <span class="caption-subject font-dark bold uppercase">Billing</span>
                    </div>
                </div>

                <div class="portlet-body util-btn-margin-bottom-5" style="overflow: hidden;">

                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="control-label">Billing Address</label>
                            {!! Form::textarea('billing_address', null, ['class' => 'form-control', 'placeholder' => 'Type billing address', 'required' => 'required']) !!}
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            @if($store->account_synq_cod == 0)
                                {!! Form::checkbox('account_synq_cod', '1', false, ['id' => 'account_synq_cod']) !!} Synq COD Charge with billing
                            @elseIf($store->account_synq_cod == 1)
                                {!! Form::checkbox('account_synq_cod', '1', true, ['id' => 'account_synq_cod']) !!} Synq COD Charge with billing
                            @endIf

                        </div>

                        <div class="form-group">

                            @if($store->account_synq_dc == 0)
                                {!! Form::checkbox('account_synq_dc', '1', false, ['id' => 'account_synq_dc']) !!} Synq Delivery Charge with billing
                            @elseIf($store->account_synq_dc == 1)
                                {!! Form::checkbox('account_synq_dc', '1', true, ['id' => 'account_synq_dc']) !!} Synq Delivery Charge with billing
                            @endIf

                        </div>

                        <div class="form-group">

                            @if($store->vat_include == 0)
                                {!! Form::checkbox('vat_include', '1', false, ['id' => 'vat_include']) !!} VAT Include
                            @elseIf($store->vat_include == 1)
                                {!! Form::checkbox('vat_include', '1', true, ['id' => 'vat_include']) !!} VAT Include
                            @endIf

                        </div>

                        <div class="form-group">

                            <label class="control-label">VAT Percentage</label>
                            {!! Form::text('vat_percentage', null, ['class' => 'form-control', 'placeholder' => 'VAT Percentage', 'required' => 'required', 'disabled', 'id' => 'vat_percentage']) !!}

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    &nbsp;
    <div class="row padding-top-10">
        <a href="javascript:history.back()" class="btn default"> Cancel </a>
        {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
    </div>

{!! Form::close() !!}

<script type="text/javascript">
    $(document).ready(function() {

        var vat_include = $('#vat_include:checkbox:checked').val();
        vatAction(vat_include);

    });

    function vatAction(vat_include){
        if(vat_include == 1){
            $('#vat_percentage').removeAttr("disabled");
        }else{
            $('#vat_percentage').attr('disabled', true);
        }
    }

    $("#vat_include").change(function(){
        var vat_include = $('#vat_include:checkbox:checked').val();
        vatAction(vat_include);
    });

</script>