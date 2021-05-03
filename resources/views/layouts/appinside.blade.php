<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>Logistics</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="Logistics Project" />
    <meta content="" name="R&D, SSL Wireless" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ secure_asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ secure_asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ secure_asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ secure_asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ secure_asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ secure_asset('assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('assets/layouts/layout/css/themes/darkblue.min.css') }}" rel="stylesheet" type="text/css" id="style_color" />
    <link href="{{ secure_asset('assets/layouts/layout/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />

    <!-- Custom CSS -->
    <!-- <link href="{{ secure_asset('custom/css/theming_biddyut.css') }}" rel="stylesheet" type="text/css" /> -->
    <link href="{{ secure_asset('custom/css/animate.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('custom/css/common.css') }}" rel="stylesheet" type="text/css" />

    @yield('select2CSS')

    <!-- jQuery on Top -->
    <script src="{{ secure_asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
    <div class="page-wrapper">
        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="{{ secure_url('home') }}">
                        <img src="{{secure_asset('assets/layouts/layout/img/logo.png')}}" alt="logo" class="logo-default" /> </a>
                        <div class="menu-toggler sidebar-toggler">
                            <span></span>
                        </div>
                    </div>
                    <!-- END LOGO -->
                    <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
                    <!-- END RESPONSIVE MENU TOGGLER -->
                    <!-- BEGIN TOP NAVIGATION MENU -->

                    @include('layouts.topmenu')

                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END HEADER INNER -->
            </div>
            <!-- END HEADER -->
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->

            <div class="page-container">
                <!-- BEGIN SIDEBAR -->

                @if(Auth::user()->hasRole('superadministrator'))
                @include('layouts.nav.superadministrator')
                @elseIf(Auth::user()->hasRole('systemadministrator'))
                @include('layouts.nav.systemadministrator')
                @elseIf(Auth::user()->hasRole('systemmoderator'))
                @include('layouts.nav.systemmoderator')
                @elseIf(Auth::user()->hasRole('hubmanager'))
                @include('layouts.nav.hubmanager')
                @elseIf(Auth::user()->hasRole('merchantadmin'))
                @include('layouts.nav.merchantadmin')
                @elseIf(Auth::user()->hasRole('merchantsupport'))
                @include('layouts.nav.merchantsupport')
                @elseIf(Auth::user()->hasRole('storeadmin'))
                @include('layouts.nav.storeadmin')
                @elseIf(Auth::user()->hasRole('vehiclemanager'))
                @include('layouts.nav.vehiclemanager')
                @elseIf(Auth::user()->hasRole('inboundmanager'))
                @include('layouts.nav.inboundmanager')
                @elseIf(Auth::user()->hasRole('head_of_accounts'))
                @include('layouts.nav.headOfAccounts')
                @elseIf(Auth::user()->hasRole('inventoryoperator'))
                @include('layouts.nav.inventoryoperator')
                @elseIf(Auth::user()->hasRole('customerservice'))
                @include('layouts.nav.customerservice')
                @elseIf(Auth::user()->hasRole('salesteam'))
                @include('layouts.nav.salesteam')
                @elseIf(Auth::user()->hasRole('coo'))
                @include('layouts.nav.coo')
                @elseIf(Auth::user()->hasRole('saleshead'))
                @include('layouts.nav.saleshead')
                @elseIf(Auth::user()->hasRole('operationmanager'))
                @include('layouts.nav.operationmanager')
                @elseIf(Auth::user()->hasRole('operationalhead'))
                @include('layouts.nav.operationalhead')
                @elseIf(Auth::user()->hasRole('kam'))
                @include('layouts.nav.kam')
                @endIf
                @permission(['manage_mail_groups','manage_query','manage_source_of_information','manage_customer_support_order','manage_complain','manage_feedback','manage_unique_head','manage_reaction','manage_inquiry','manage_inquiry_status'])
                @include('layouts.nav.cus')
                @endpermission
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->

                        @if (Session::has('message'))
                        <script type="text/javascript">
                            $(document ).ready(function() {
                                    // Toast Alert
                                    Command: toastr['success']("{{ Session::get('message') }}", "Success")
                                    toastr.options = {
                                      "closeButton": true,
                                      "debug": false,
                                      "positionClass": "toast-top-right",
                                      "onclick": null,
                                      "showDuration": "1000",
                                      "hideDuration": "1000",
                                      "timeOut": "5000",
                                      "extendedTimeOut": "1000",
                                      "showEasing": "swing",
                                      "hideEasing": "linear",
                                      "showMethod": "fadeIn",
                                      "hideMethod": "fadeOut"
                                  }
                              });
                          </script>
                          @endif

                          @yield('content')

                      </div>
                      <!-- END CONTENT BODY -->
                  </div>
                  <!-- END CONTENT -->
              </div>

              <div class="modal fade" id="inventory" tabindex="-1" role="basic" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Inventory</h4>
                        </div>
                        <div class="modal-body" id="inventory-text"> loading... </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark btn-outline" data-dismiss="modal">Ok</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <div class="page-footer">
                <div class="page-footer-inner"> <?php echo date('Y'); ?> &copy; iPost System By
                    <a target="_blank" href="https://sslwireless.com">SSL Wireless</a>
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
        </div>
        <!--[if lt IE 9]>
<script src="{{ secure_asset('assets/global/plugins/respond.min.js') }}"></script>
<script src="{{ secure_asset('assets/global/plugins/excanvas.min.js') }}"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="{{ secure_asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ secure_asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/pages/scripts/table-datatables-responsive.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/global/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ secure_asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
{{-- <script src="{{ secure_asset('assets/pages/scripts/dashboard.min.js') }}" type="text/javascript"></script> --}}
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ secure_asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ secure_asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>

<script src="{{ secure_asset('assets/pages/scripts/ui-modals.min.js') }}" type="text/javascript"></script>

<!-- Custom JS -->
<script type="text/javascript">
    var site_path = "{{ secure_url('/') }}"+"/";
</script>
<script src="{{ secure_asset('custom/js/highlight-nav.js') }}" type="text/javascript"></script>
<script src="{{ secure_asset('custom/js/location-list.js') }}" type="text/javascript"></script>

@if (Session::has('inventory'))
<script type="text/javascript">
    $(window).load(function(){
        $('#inventory').modal('show');
        $('#inventory-text').html("{{ Session::get('inventory') }}");
    });
</script>
@endif

<script type="text/javascript">
    $(window).load(function(){
        $('.focus_it').focus();
    });
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@yield('select2JS')

</body>
