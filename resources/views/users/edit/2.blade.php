<script src="{{ URL::asset('custom/js/reference-list.js') }}" type="text/javascript"></script>

{!! Form::model($user, array('url' => '/user/'.$user->id.'?step=3', 'method' => 'put')) !!}

<div class="row">

    @include('partials.errors')

    <div class="col-md-6">

        <div class="form-group">
            <label class="control-label">Select Type</label>
            {!! Form::select('user_type_id', $user_types, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'user_type_id']) !!}
        </div>


        @if($reference_list != null)
            <div class="form-group reference-area">
                <label class="control-label">Select Reference</label>
                {!! Form::select('reference_id', $reference_list, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'reference_id']) !!}
            </div>
            <div class="form-group rider-reference-area">
                <label class="control-label">Select Reference</label>
                {!! Form::select('rider_reference_id[]', $reference_list, $userReferences, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'rider_reference_id','multiple']) !!}
            </div>
        @else
            <div class="form-group reference-area">
                <label class="control-label">Select Reference</label>
                <select name="reference_id" class="form-control js-example-basic-single js-country"
                        id="reference_id" style="width:100%">
                    <option value="">Select Reference</option>
                </select>
            </div>
            <div class="form-group rider-reference-area">
                <label class="control-label">Select Reference</label>
                <select name="rider_reference_id[]" class="form-control js-example-basic-single js-country"
                        id="rider_reference_id" multiple style="width:100%">
                    <option value="">Select Reference</option>
                </select>
            </div>
        @endif

    </div>
    <div class="col-md-6">
        @if($user->user_type_id == 8)
            <div class="form-group rider-type">
                <label class="control-label">Select Rider Type</label>
                {!! Form::select('rider_type', ['Freelancer','Permanent'], null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'rider_type','placeholder'=>'Select One']) !!}
            </div>
            <div class="form-group transparent-mode">
                <label class="control-label">Select Transparent Mode</label>
                {!! Form::select('transparent_mode', $transparentModes, null, ['class' => 'form-control js-example-basic-single js-country', 'required' => 'required', 'id' => 'transparent_mode','placeholder'=>'Select One']) !!}
            </div>
        @else
            <div class="form-group rider-type">
                <label class="control-label">Select Rider Type</label>
                {!! Form::select('rider_type', ['Freelancer','Permanent'], null, ['class' => 'form-control js-example-basic-single js-country', 'id' => 'rider_type','placeholder'=>'Select One']) !!}
            </div>
            <div class="form-group transparent-mode">
                <label class="control-label">Select Transparent Mode</label>
                {!! Form::select('transparent_mode', $transparentModes, null, ['class' => 'form-control js-example-basic-single js-country', 'id' => 'transparent_mode','placeholder'=>'Select One']) !!}
            </div>
        @endif
    </div>

</div>

&nbsp;
<div class="row padding-top-10">
    <a href="{{ URL::to('user/'.$id.'/edit?step=1') }}" class="btn default"> Back </a>
    {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
</div>

{!! Form::close() !!}

<script type="text/javascript">
    // Get Reference list On UserType Change
    $('#user_type_id').on('change', function () {

        if ($(this).val() == 8) {
            var refs = [];
            @foreach($userReferences as $ref)
            refs.push({{$ref}});
            @endforeach
            get_rider_reference($(this).val(), refs);

            $("#rider_type").attr('required', true);
            $(".rider-type").show();
            $("#transparent_mode").attr('required', true);
            $(".transparent-mode").show();
        } else {
            get_reference($(this).val(), '{{ $user->reference_id }}');

            $("#rider_type").attr('required', false);
            $(".rider-type").hide();
            $("#transparent_mode").attr('required', false);
            $(".transparent-mode").hide();
        }
    });
</script>