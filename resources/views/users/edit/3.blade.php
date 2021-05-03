{!! Form::model($user, array('url' => secure_url('') . '/user/'.$user->id.'?step=4', 'method' => 'put', 'files' => true)) !!}

    <div class="row">

        @include('partials.errors')

        <div class="col-md-6 padding-top-10">
            
            <div class="form-group">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                        <img src="{{ $user->photo }}" alt=""  id="img-thumb" />
                    </div>
                    <div>
                        <span class="btn default btn-file">
                            <span class="fileinput-new"> Select image </span>
                            <span class="fileinput-exists"> Change </span>
                            <input type="file" id="photo" name="photo"> </span>
                        <!-- <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a> -->
                    </div>
                </div>
                <div class="clearfix margin-top-10">
                    <span class="label label-danger">NOTE! </span>
                    <span>Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
                </div>
            </div>

        </div>

    </div>

    &nbsp;
    <div class="row padding-top-10">
        <a href="{{ secure_url('user/'.$id.'/edit?step=2') }}" class="btn default"> Back </a>
        {!! Form::submit('Next', ['class' => 'btn green pull-right']) !!}
    </div>

{!! Form::close() !!}

<script type="text/javascript">
    document.getElementById("photo").onchange = function () {
        var reader = new FileReader();

        reader.onload = function (e) {
            // get loaded data and render thumbnail.
            document.getElementById("img-thumb").src = e.target.result;
        };

        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    };
</script>