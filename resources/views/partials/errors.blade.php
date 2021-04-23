@if (count($errors) > 0)

    <!-- Form Error List -->
    <div id="prefix_123418344666" class="row custom-alerts alert alert-success fade in">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button"></button>
        <div class="col-md-1">
            <i class="fa-lg fa fa-warning"></i>
        </div>
        <div class="col-md-11">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    </div>

@endif