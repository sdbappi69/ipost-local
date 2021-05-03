@extends('layouts.appinside')

@section('content')

    <?php
        session_start();
        if(isset($_SESSION['bulk_msg'])){
            // $html = '';
            // foreach ($_SESSION['bulk_msg'] as $bulk_msg) {
            //     $html = $html.$bulk_msg.'\n';
            // }
    ?>

    <div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Import Log</h4>
                </div>
                <div class="modal-body">
                    <table style="width: 100%;">
                        <?php
                            foreach ($_SESSION['bulk_msg'] as $bulk_msg) {
                                echo "<tr><td>$bulk_msg</td></tr>";
                            }
                        ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            
            $('#basic').modal({
                backdrop: 'static',   // This disable for click outside event
                keyboard: true        // This for keyboard event
            })

        });

    </script>
           
    <?php } session_destroy(); ?>

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ secure_url('merchant-order') }}">Orders</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Insert</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Order
        <small> create bulk orders</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="mt-element-step">

        {!! Form::open(array('url' => secure_url('') . '/merchant-order-bulk-submit', 'method' => 'post', 'files' => true)) !!}

                <div class="row">

                    @include('partials.errors')

                    <div class="col-md-6">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputFile1">Bulk Upload</label>
                                <input type="file" id="exampleInputFile1" name="bulk_products" required="required">
                                {!! Form::submit('Bulk Upload', ['class' => 'btn green']) !!}
                            </div>
                        </div>
                        <div class="col-md-9">
                            <p class="help-block">Click here to download <a href="{{ secure_url('sample.xls') }}">sample.xls</a></p>
                        </div>

                    </div>

                </div>

            {!! Form::close() !!}

    </div>

    <script type="text/javascript">

        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('merchant-order-bulk', 'merchant-orders');

        });

    </script>

@endsection
