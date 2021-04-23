<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-light " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>
            <!-- END SIDEBAR TOGGLER BUTTON -->
            <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
            <!-- <li class="sidebar-search-wrapper"> -->
            <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
            <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
            <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
            <!-- <form class="sidebar-search  " action="page_general_search_3.html" method="POST">
                <a href="javascript:;" class="remove">
                    <i class="icon-close"></i>
                </a>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <a href="javascript:;" class="btn submit">
                            <i class="icon-magnifier"></i>
                        </a>
                    </span>
                </div>
            </form> -->
            <!-- END RESPONSIVE QUICK SEARCH FORM -->
            <!-- </li> -->

            <li class="nav-item start dashboard">
                <a href="{{ URL::to('home') }}">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
                    <!-- <span class="selected"></span> -->
                    <!-- <span class="arrow open"></span> -->
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Orders Management</h3>
            </li>


            <li class="nav-item start consignments">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-sticky-note"></i>
                    <span class="title">Consignments</span>
                    <span class="arrow"></span>
                    <span class="badge badge-info" id="consignment_total_nav">0</span>
                </a>

                <ul class="sub-menu">
                    <li class="nav-item delivery_consignments">
                        <a href="{{ URL::to('consignments-delivery') }}" class="nav-link ">
                            <span class="title">Delivery</span>
                            <span class="badge badge-success" id="consignment_delivery_nav">0</span>
                        </a>
                    </li>
                    <li class="nav-item pick_up_consignments">
                        <a href="{{ URL::to('consignments-pick-up') }}" class="nav-link ">
                            <span class="title">Pick Up</span>
                            <span class="badge badge-success" id="consignment_pickup_nav">0</span>
                        </a>
                    </li>
                    <li class="nav-item return_consignments">
                        <a href="{{ URL::to('consignments-return') }}" class="nav-link ">
                            <span class="title">Return</span>
                            <span class="badge badge-success" id="consignment_return_nav">0</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item all_consignments">
                        <a href="{{ URL::to('consignments-all') }}" class="nav-link ">
                            <span class="title">Consignments</span>
                        </a>
                    </li> -->
                    <li class="nav-item consignmentsv2">
                        <a href="{{ URL::to('v2consignment') }}" class="nav-link ">
                            <span class="title">Consignments</span>
                            <span class="badge badge-success" id="consignment_nav">0</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item start pickup">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-arrow-circle-left"></i>
                    <span class="title">Inbound</span>
                    <span class="arrow"></span>
                    <span class="badge badge-info" id="inbound_nav">0</span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item receive-prodcut">
                        <a href="{{ URL::to('receive-prodcut') }}" class="nav-link ">
                            <span class="title">Product Receive & Verify</span>
                            <span class="badge badge-success" id="receive_prodcut">0</span>
                        </a>
                    </li>
<!--                    <li class="nav-item accept-picked">
                        <a href="{{ URL::to('accept-picked') }}" class="nav-link ">
                            <span class="title">Receive from rider</span>
                            <span class="badge badge-success" id="accept_picked_nav">0</span>
                        </a>
                    </li>-->
                    <!-- <li class="nav-item return-pickup">
                        <a href="{{ URL::to('return-pickup') }}" class="nav-link ">
                            <span class="title">Pickup failed or pertial</span>
                        </a>
                    </li> -->
                    <!-- <li class="nav-item receive-transferd">
                        <a href="{{ URL::to('receive-transferd') }}" class="nav-link ">
                            <span class="title">Receive transferd picking</span>
                        </a>
                    </li> -->
<!--                    <li class="nav-item receive-picked">
                        <a href="{{ URL::to('receive-picked') }}" class="nav-link ">
                            <span class="title">Verify Product</span>
                            <span class="badge badge-success" id="receive_picked_nav">0</span>
                        </a>
                    </li>-->
                    <!-- <li class="nav-item return-delivery">
                        <a href="{{ URL::to('return-delivery') }}" class="nav-link ">
                            <span class="title">Failed or pertial Delivery</span>
                        </a>
                    </li> -->
                </ul>
            </li>

            <li class="nav-item start delivery">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-arrow-circle-right"></i>
                    <span class="title">Outbound</span>
                    <span class="arrow"></span>
                    <span class="badge badge-info" id="outbound_nav">0</span>
                </a>
                <ul class="sub-menu">
                    <!-- <li class="nav-item queued-picked">
                        <a href="{{ URL::to('queued-picked') }}" class="nav-link ">
                            <span class="title">Picked to transfer</span>
                        </a>
                    </li> -->
                    <li class="nav-item delivery-from-office">
                        <a href="{{ URL::to('office-delivery-list') }}" class="nav-link ">
                            <span class="title">Delivery From Office</span>
                            <span class="badge badge-success" id="office_delivery_nav">0</span>
                        </a>
                    </li>
                    <!--                    <li class="nav-item queued-shipping">
                                            <a href="{{ URL::to('queued-shipping') }}" class="nav-link ">
                                                <span class="title">Orders to transfer</span>
                                            </a>
                                        </li>-->
                    <li class="nav-item accept-suborder">
                        <a href="{{ URL::to('accept-suborder') }}" class="nav-link ">
                            <span class="title">Receive transferd Orders</span>
                            <span class="badge badge-success" id="accept_suborder_nav">0</span>
                        </a>
                    </li>
                    <li class="nav-item transfer">
                        <a href="{{ URL::to('transfer') }}" class="nav-link ">
                            <span class="title">Change delivery address</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- <li class="nav-item tasks">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-check"></i>
                    <span class="title">Tasks</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item assign-pickup">
                        <a href="{{ URL::to('assign-pickup') }}" class="nav-link ">
                            <span class="title">Assign Picker</span>
                        </a>
                    </li>
                    <li class="nav-item accept-picked">
                        <a href="{{ URL::to('accept-picked') }}" class="nav-link ">
                            <span class="title">Receive Picked</span>
                        </a>
                    </li>
                    <li class="nav-item return-pickup">
                        <a href="{{ URL::to('return-pickup') }}" class="nav-link ">
                            <span class="title">Return Pickup</span>
                        </a>
                    </li>
                    <li class="nav-item receive-picked">
                        <a href="{{ URL::to('receive-picked') }}" class="nav-link ">
                            <span class="title">Verify Picked</span>
                        </a>
                    </li>
                    <li class="nav-item receive-transferd">
                        <a href="{{ URL::to('receive-transferd') }}" class="nav-link ">
                            <span class="title">Receive Transferred</span>
                        </a>
                    </li>
                <li class="nav-item transfer-product">
                    <a href="{{ URL::to('transfer-product') }}" class="nav-link ">
                        <span class="title">Transfer Product</span>
                    </a>
                </li> -->
            <!-- <li class="nav-item transfer-suborder">
                <a href="{{ URL::to('transfer-suborder') }}" class="nav-link ">
                    <span class="title">Transfer Sub-Order</span>
                </a>
            </li> -->
            <!-- <li class="nav-item queued-shipping">
                <a href="{{ URL::to('queued-shipping') }}" class="nav-link ">
                    <span class="title">Queued Sub-Orders</span>
                </a>
            </li>
            <li class="nav-item accept-suborder">
                <a href="{{ URL::to('accept-suborder') }}" class="nav-link ">
                    <span class="title">Receive Sub-Order</span>
                </a>
            </li>
            <li class="nav-item assign-delivery">
                <a href="{{ URL::to('assign-delivery') }}" class="nav-link ">
                    <span class="title">Assign Delivery</span>
                </a>
            </li>
            <li class="nav-item return-delivery">
                <a href="{{ URL::to('return-delivery') }}" class="nav-link ">
                    <span class="title">Return Delivery</span>
                </a>
            </li> -->
            <!-- <li class="nav-item assign-delivery">
                <a href="{{ URL::to('assign-delivery') }}" class="nav-link ">
                    <span class="title">Assign Delivery</span>
                </a>
            </li> -->
            <!-- </ul>
        </li> -->

            <li class="nav-item start orders">
                <a href="{{ URL::to('hub-order') }}">
                    <i class="fa fa-cube"></i>
                    <span class="title">Orders</span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Warehouse Management</h3>
            </li>

            <li class="nav-item shelfs">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-object-group"></i>
                    <span class="title">Shelfs</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item warehouse-add">
                        <a href="{{ URL::to('shelf/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item warehouse-manage">
                        <a href="{{ URL::to('shelf') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item racks">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-sitemap"></i>
                    <span class="title">Racks</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item rack-add">
                        <a href="{{ URL::to('rack/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item rack-manage">
                        <a href="{{ URL::to('rack') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item start trips">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-truck"></i>
                    <span class="title">Trips</span>
                    <span class="arrow"></span>
                    <span class="badge badge-info trip_nav" id="trip_nav">0</span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item trip-create">
                        <a href="{{ URL::to('trip/create') }}" class="nav-link ">
                            <span class="title">Add</span>                            
                            <span class="badge badge-success trip_nav" id="trip_nav">0</span>
                        </a>
                    </li>
                    <li class="nav-item trip-manage">
                        <a href="{{ URL::to('trip') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item start accounts_bills">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-money"></i>
                    <span class="title">Accounts & Bills</span>
                    <span class="arrow"></span>
                    <span class="badge badge-info" id="account_bill">0</span>
                </a>

                <ul class="sub-menu">
                    <li class="nav-item cash_collection">
                        <a href="{{ URL::to('collected-cash-amount') }}" class="nav-link ">
                            <span class="title">Cash Collection</span>
                            <span class="badge badge-success" id="cash_collection">0</span>

                        </a>
                    </li>
                   <li class="nav-item cash_transfer">
                        <a href="{{ URL::to('accumulated-collected-cash') }}" class="nav-link ">
                            <span class="title">Cash Transfer</span>
                            <span class="badge badge-success" id="cash_transfer">0</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item cash_collection">
                        <a href="{{ URL::to('cash-collection') }}" class="nav-link ">
                            <span class="title">Cash Collection</span>
                        </a>
                    </li>
                    <li class="nav-item transfer_to_vault">
                        <a href="{{ URL::to('transfer-to-vault') }}" class="nav-link ">
                            <span class="title">Transfer To Vault</span>
                        </a>
                    </li>
                    <li class="nav-item vault_list">
                        <a href="{{ URL::to('vault-list') }}" class="nav-link ">
                            <span class="title">Vault Approvals</span>
                        </a>
                    </li>
                    <li class="nav-item manage_vault">
                        <a href="{{ URL::to('manage-vault') }}" class="nav-link ">
                            <span class="title">Manage Vault</span>
                        </a>
                    </li>
                    <li class="nav-item transfer_to_bank">
                        <a href="{{ URL::to('transfer-to-bank') }}" class="nav-link ">
                            <span class="title">Transfer To Bank</span>
                        </a>
                    </li>
                    <li class="nav-item bank_list">
                        <a href="{{ URL::to('bank-list') }}" class="nav-link ">
                            <span class="title">Bank Transfer Approvals</span>
                        </a>
                    </li>
                    <li class="nav-item bank_canceled">
                        <a href="{{ URL::to('bank-canceled') }}" class="nav-link ">
                            <span class="title">Bank Transfer Canceled</span>
                        </a>
                    </li>
                    <li class="nav-item manage_checkout">
                        <a href="{{ URL::to('manage-checkout') }}" class="nav-link ">
                            <span class="title">Manage Hub Checkout</span>
                        </a>
                    </li>--}}
                    <!-- <li class="nav-item create_merchant_checkout">
                        <a href="{{ URL::to('create-merchant-checkout') }}" class="nav-link ">
                            <span class="title">Create Merchant Checkout</span>
                        </a>
                    </li>
                    <li class="nav-item manage_merchant_checkout">
                        <a href="{{ URL::to('manage-merchant-checkout') }}" class="nav-link ">
                            <span class="title">Manage Merchant Checkout</span>
                        </a>
                    </li> -->

                </ul>
            </li>


        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->

<script>
    $(document).ready(function () {
        hubManagerNav();

        setInterval(function () {
            hubManagerNav();
        }, 1000 * 10 * 1);

        function hubManagerNav() {
            $.ajax({
                url: "{{url('hub-manager-nav')}}",
                type: "get",
                data: {},
                success: function (data) {
                   console.log(data.consignment_nav);
                    $("#cash_collection").html(data.cash_collection);
                    $("#cash_transfer").html(data.cash_transfer);
                    $("#account_bill").html(data.account_bill);

                    $("#consignment_delivery_nav").html(data.delivery_nav);
                    $("#consignment_pickup_nav").html(data.pickup_nav);
                    $("#consignment_return_nav").html(data.return_nav);
                    $("#consignment_nav").html(data.consignment_nav);
                    $("#consignment_widget").html(data.consignment_nav);
                    $("#consignment_total_nav").html(data.consignment_total_nav);

                    $("#receive_prodcut").html(data.receive_prodcut_nav);
                    $("#receive_prodcut_widget").html(data.receive_prodcut_nav);
                    $("#accept_picked_nav").html(data.picked_nav);
                    $("#receive_picked_nav").html(data.received_nav);
                    $("#inbound_nav").html(data.inbound_nav);

                    $("#office_delivery_nav").html(data.office_delivery_nav);
                    $("#accept_suborder_nav").html(data.accept_suborder_nav);
                    $("#outbound_nav").html(data.outbound_nav);

                    $(".trip_nav").html(data.trip_nav);
                    $("#trip_widget").html(data.trip_nav);
                },
                error: function (data) {
                    console.log("Error: ", data);
                }

            });
        }
    });
</script>
