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


            <li class="nav-item start dashboard">
                <a href="{{ secure_url('home') }}">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
                    <!-- <span class="selected"></span> -->
                    <!-- <span class="arrow open"></span> -->
                </a>
            </li>
            <li class="heading">
                <h3 class="uppercase">Orders Management</h3>
            </li>

            <li class="nav-item start orders">
                <a href="{{ secure_url('order') }}">
                    <i class="fa fa-cube"></i>
                    <span class="title">Orders</span>
                </a>
            </li>

            <!--start accounts-->
<!--            <li class="heading">
                <h3 class="uppercase">Manage Accounts</h3>
            </li>-->
            {{--<li class="nav-item bank">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Bank</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-bank">
                        <a href="{{ secure_url('/bank') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-bank">
                        <a href="{{ secure_url('/bank/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item bank_accounts">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Bank Accounts</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-bank-accounts">
                        <a href="{{ secure_url('/bank-accounts') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-bank-accounts">
                        <a href="{{ secure_url('/bank-accounts/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item hub_bank_accounts">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Hub Bank Accounts</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-hub-bank-accounts">
                        <a href="{{ secure_url('/hub-bank-accounts') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-hub-bank-accounts">
                        <a href="{{ secure_url('/hub-bank-accounts/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>--}}
<!--            <li class="nav-item merchant_bank_accounts">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Merchant Bank Accounts</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-merchant-bank-accounts">
                        <a href="{{ secure_url('/merchant-bank-accounts') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-hub-bank-accounts">
                        <a href="{{ secure_url('/merchant-bank-accounts/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>-->
            {{--<li class="nav-item vault">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Vaults</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-vault">
                        <a href="{{ secure_url('/vault') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-vault">
                        <a href="{{ secure_url('/vault/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>--}}
            </li>
            <!--end account-->


            <li class="heading">
                <h3 class="uppercase">Manage Accounts & Bills</h3>
            </li>

            <li class="nav-item start accounts_bills">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-money"></i>
                    <span class="title">Accounts & Bills</span>
                    <span class="arrow"></span>
                    <span class="badge badge-info trip_nav" id="final_account">0</span>
                </a>

                <ul class="sub-menu">
                    <li class="nav-item receive_hub_payment">
                        <a href="{{ secure_url('accumulated-collected-cash-confirm') }}" class="nav-link ">
                            <span class="title">Receive Hub Payment</span>
                            <span class="badge badge-success trip_nav" id="receive_hub_payment">0</span>
                        </a>
                    </li>
                    <li class="nav-item merchant_checkout">
                        <a href="{{ secure_url('collected-cash-merchant') }}" class="nav-link ">
                            <span class="title">Merchant Checkout</span>
                            <span class="badge badge-success trip_nav" id="merchant_checkout">0</span>
                        </a>
                    </li>
                    <li class="nav-item confirm_checkout">
                        <a href="{{ secure_url('collected-cash-merchant-confirm') }}" class="nav-link ">
                            <span class="title">Manage Checkout</span>
                            <span class="badge badge-success trip_nav" id="manage_checkout">0</span>
                        </a>
                    </li>
                  {{--  saif develop--}}
               {{--     <li class="nav-item checkout_history">
                        <a href="{{ secure_url('collected-cash-merchant-final') }}" class="nav-link ">
                            <span class="title">Checkout History</span>
                        </a>
                    </li>--}}


                    {{--<li class="nav-item manage_vault">
                        <a href="{{ secure_url('manage-vault-accounts') }}" class="nav-link ">
                            <span class="title">Manage Vault</span>
                        </a>
                    </li>

                    <li class="nav-item manage_checkout">
                        <a href="{{ secure_url('manage-checkout-accounts') }}" class="nav-link ">
                            <span class="title">Manage Hub Checkout</span>
                        </a>
                    </li>
                    <li class="nav-item create_merchant_checkout">
                        <a href="{{ secure_url('create-merchant-checkout') }}" class="nav-link ">
                            <span class="title">Create Merchant Checkout</span>
                        </a>
                    </li>
                    <li class="nav-item manage_merchant_checkout">
                        <a href="{{ secure_url('manage-merchant-checkout') }}" class="nav-link ">
                            <span class="title">Manage Merchant Checkout</span>
                        </a>
                    </li>
                    <li class="nav-item create_merchant_bill">
                        <a href="{{ secure_url('create-merchant-bill') }}" class="nav-link ">
                            <span class="title">Create Merchant Bill</span>
                        </a>
                    </li>
                    <li class="nav-item manage_merchant_bill">
                        <a href="{{ secure_url('manage-merchant-bill') }}" class="nav-link ">
                            <span class="title">Manage Merchant Bill</span>
                        </a>
                    </li>--}}

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
                url: "{{secure_url('head-account-nav')}}",
                type: "get",
                data: {},
                success: function (data) {

                    $('#receive_hub_payment').html(data.receive_hub_payment);
                    $('#merchant_checkout').html(data.merchant_checkout);
                    $('#manage_checkout').html(data.manage_checkout);
                    $('#final_account').html(data.final_account);

                  console.log(data);
                },
                error: function (data) {
                    console.log("Error: ", data);
                }

            });
        }
    });
</script>