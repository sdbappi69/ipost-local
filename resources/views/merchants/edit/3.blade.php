<div class="row">
    @include('partials.errors')
    <br>
    <div id="accordion3" class="panel-group">

        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_new">
                        <i class="fa fa-plus"></i> Add Store
                    </a>
                </h4>
            </div>
            <div id="accordion3_new" class="panel-collapse collapse">
                <div class="panel-body">

                    {!! Form::open(array('url' => '/store', 'method' => 'post')) !!}
                    <input type="hidden" name="merchant_id" value="{{$merchant->id}}">
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="merchant_end" value="merchant">

                    <div class="row">



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

                            <div class="form-group">
                                <label class="control-label">Billing Address</label>
                                {!! Form::textarea('billing_address', null, ['class' => 'form-control', 'placeholder' => 'Type billing address', 'required' => 'required']) !!}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Store Url</label>
                                {!! Form::text('store_url', null, ['class' => 'form-control', 'placeholder' => 'Store Url', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Store Type</label>
                                {!! Form::select('store_type_id',$storetypes, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">

                                {!! Form::checkbox('account_synq_cod', '1', false, ['id' => 'account_synq_cod']) !!} Synq COD Charge with billing

                            </div>

                            <div class="form-group">

                                {!! Form::checkbox('account_synq_dc', '1', false, ['id' => 'account_synq_dc']) !!} Synq Delivery Charge with billing

                            </div>

                            <div class="form-group">

                                {!! Form::checkbox('vat_include', '1', true, ['id' => 'vat_include_new', 'name' => 'vat_include']) !!} VAT Include

                            </div>

                            <div class="form-group">

                                <label class="control-label">VAT Percentage</label>
                                {!! Form::text('vat_percentage', 15, ['class' => 'form-control', 'placeholder' => 'VAT Percentage', 'required' => 'required', 'id' => 'vat_percentage_new']) !!}

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

            @foreach($store as $s)

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="icon-btn icon-btn-customized accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_{{ $s->id }}">
                            Store Id : {{ $s->store_id }} ({{ $s->title }}) <a target="_blank" class="btn btn-xs btn-success" href="{{url('/store/'.$s->id.'/edit?step=2')}}">Change Cost</a>
                            {{-- <span class="badge badge-success"> {{ $u->id }} </span> --}}
                        </a>



                    </h4>
                </div>
                <div id="accordion3_{{ $s->id }}" class="panel-collapse collapse">
                    <div class="panel-body">

                       
                        {!! Form::model($s, array('url' => '/store/'.$s->id.'?step=2', 'method' => 'put')) !!}

                        <input type="hidden" name="merchant_id" value="{{$merchant->id}}">
                    <input type="hidden" name="step" value="complete">
                    <input type="hidden" name="merchant_end" value="merchant">

                    <div class="row">


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

                            <div class="form-group">
                                <label class="control-label">Billing Address</label>
                                {!! Form::textarea('billing_address', null, ['class' => 'form-control', 'placeholder' => 'Type billing address', 'required' => 'required']) !!}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Store Url</label>
                                {!! Form::text('store_url', null, ['class' => 'form-control', 'placeholder' => 'Store Url', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Store Type</label>
                                {!! Form::select('store_type_id',$storetypes, null, ['class' => 'form-control js-example-basic-single', 'required' => 'required']) !!}
                            </div>

                            <div class="form-group">

                                @if($s->account_synq_cod == 0)
                                    {!! Form::checkbox('account_synq_cod', '1', false, ['id' => 'account_synq_cod']) !!} Synq COD Charge with billing
                                @elseIf($s->account_synq_cod == 1)
                                    {!! Form::checkbox('account_synq_cod', '1', true, ['id' => 'account_synq_cod']) !!} Synq COD Charge with billing
                                @endIf

                            </div>

                            <div class="form-group">

                                @if($s->account_synq_dc == 0)
                                    {!! Form::checkbox('account_synq_dc', '1', false, ['id' => 'account_synq_dc']) !!} Synq Delivery Charge with billing
                                @elseIf($s->account_synq_dc == 1)
                                    {!! Form::checkbox('account_synq_dc', '1', true, ['id' => 'account_synq_dc']) !!} Synq Delivery Charge with billing
                                @endIf

                            </div>

                            <div class="form-group">

                                @if($s->vat_include == 0)
                                    {!! Form::checkbox('vat_include', '1', false, ['id' => 'vat_include', 'name' => 'vat_include']) !!} VAT Include
                                @elseIf($s->vat_include == 1)
                                    {!! Form::checkbox('vat_include', '1', true, ['id' => 'vat_include', 'name' => 'vat_include']) !!} VAT Include
                                @endIf

                            </div>

                            <div class="form-group">

                                <label class="control-label">VAT Percentage</label>
                                {!! Form::text('vat_percentage', null, ['class' => 'form-control', 'placeholder' => 'VAT Percentage', 'required' => 'required', 'disabled', 'id' => 'vat_percentage']) !!}

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

            <a href="{{ URL::to('merchant') }}/{{ $merchant->id }}/edit" class="btn default"> Back </a>
            <a href="{{ URL::to('merchant') }}" class="btn green pull-right">Complete</a>

        </div>

        <script type="text/javascript">
            // document.getElementById("photo").onchange = function () {
            //     var reader = new FileReader();

            //     reader.onload = function (e) {
            //         // get loaded data and render thumbnail.
            //         document.getElementById("img-thumb").src = e.target.result;
            //     };

            //     // read the image file as a data URL.
            //     reader.readAsDataURL(this.files[0]);
            // };

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

            function vatActionNew(vat_include){
                if(vat_include == 1){
                    $('#vat_percentage_new').removeAttr("disabled");
                }else{
                    $('#vat_percentage_new').attr('disabled', true);
                }
            }

            // document.getElementById("vat_include").onchange = function () {
            $("#vat_include").change(function(){
                var vat_include = $('#vat_include:checkbox:checked').val();
                vatAction(vat_include);
            });

            $("#vat_include_new").change(function(){
                var vat_include = $('#vat_include_new:checkbox:checked').val();
                vatActionNew(vat_include);
            });

        </script>