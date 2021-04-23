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

            <!-- <li class="nav-item tasks">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-check"></i>
                    <span class="title">Tasks</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item verify-orders">
                        <a href="{{ URL::to('verify-order') }}" class="nav-link ">
                            <span class="title">Verify Orders</span>
                        </a>
                    </li>
                </ul>
            </li> -->

            <li class="nav-item orders">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cube"></i>
                    <span class="title">Orders</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item manage-orders">
                        <a href="{{ URL::to('order') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item order-draft">
                        <a href="{{ URL::to('order-draftv2') }}" class="nav-link ">
                            <span class="title">Draft</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Product Category Management</h3>
            </li>

            <li class="nav-item product-category">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-square"></i>
                    <span class="title">Product Categories</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item product-category-add">
                        <a href="{{ URL::to('product-category/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item product-category-manage">
                        <a href="{{ URL::to('product-category') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item product-category-charge-approve">
                        <a href="{{ URL::to('product-category-charge-approval/v2') }}" class="nav-link ">
                            <span class="title">Approve Charges</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Merchants Management</h3>
            </li>

            <li class="nav-item merchants">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-child"></i>
                    <span class="title">Merchants</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item merchant-add">
                        <a href="{{ URL::to('merchant/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item merchant-manage">
                        <a href="{{ URL::to('merchant') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item stores">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="title">Stores</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item store-add">
                        <a href="{{ URL::to('store/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item store-manage">
                        <a href="{{ URL::to('store') }}" class="nav-link ">
                            <span class="title">Store</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Vehicles Management</h3>
            </li>

            <li class="nav-item vehicles">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-truck"></i>
                    <span class="title">Vehicles</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item vehicle-type">
                        <a href="{{ URL::to('vehicle-type') }}" class="nav-link ">
                            <span class="title">Types</span>
                        </a>
                    </li>
                    <li class="nav-item vehicle">
                        <a href="{{ URL::to('vehicle') }}" class="nav-link ">
                            <span class="title">Vehicles</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item drivers">
                <a href="{{ URL::to('driver') }}">
                    <i class="fa fa-hand-rock-o"></i>
                    <span class="title">Drivers</span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Hubs Management</h3>
            </li>

            <li class="nav-item hubs">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-square"></i>
                    <span class="title">Hubs</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item hub-add">
                        <a href="{{ URL::to('hub/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item hub-manage">
                        <a href="{{ URL::to('hub') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item trip-map">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-truck"></i>
                    <span class="title">Trip Map</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item trip-map-add">
                        <a href="{{ URL::to('trip-map/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item trip-map-manage">
                        <a href="{{ URL::to('trip-map') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="heading">
                <h3 class="uppercase">Users Management</h3>
            </li>

            <li class="nav-item users">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users"></i>
                    <span class="title">Users</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item user-add">
                        <a href="{{ URL::to('user/create') }}" class="nav-link ">
                            <span class="title">Add User</span>
                        </a>
                    </li>
                    <li class="nav-item user-manage">
                        <a href="{{ URL::to('user') }}" class="nav-link ">
                            <span class="title">Manage User</span>
                        </a>
                    </li>
                    <li class="nav-item rider-manage">
                        <a href="{{ URL::to('rider') }}" class="nav-link ">
                            <span class="title">Manage New Rider</span>
                        </a>
                    </li>
                    <li class="nav-item profile-update-request">
                        <a href="{{ URL::to('rider-profile-update-request') }}" class="nav-link ">
                            <span class="title">Rider Info Update Request</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item roles">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-user-secret"></i>
                    <span class="title">Roles</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item role-add">
                        <a href="{{ URL::to('role/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item role-manage">
                        <a href="{{ URL::to('role') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item permissions">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-mouse-pointer"></i>
                    <span class="title">Permission</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item permission-add">
                        <a href="{{ URL::to('permission/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item permission-manage">
                        <a href="{{ URL::to('permission') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Locations Management</h3>
            </li>

            <li class="nav-item locations">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Location</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item country">
                        <a href="{{ URL::to('country') }}" class="nav-link ">
                            <span class="title">Countries</span>
                        </a>
                    </li>
                    <li class="nav-item state">
                        <a href="{{ URL::to('state') }}" class="nav-link ">
                            <span class="title">States</span>
                        </a>
                    </li>
                    <li class="nav-item city">
                        <a href="{{ URL::to('city') }}" class="nav-link ">
                            <span class="title">Cities</span>
                        </a>
                    </li>
                    <li class="nav-item zone">
                        <a href="{{ URL::to('zone') }}" class="nav-link ">
                            <span class="title">Zones</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- <li class="nav-item charges">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-money"></i>
                    <span class="title">Charges</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item charge-add">
                        <a href="{{ URL::to('charge/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item charge-manage">
                        <a href="{{ URL::to('charge') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            
            <li class="heading">
                <h3 class="uppercase">Charges</h3>
            </li>

            <li class="nav-item cod">
                <a href="{{ URL::to('charge/1') }}">
                    <i class="fa fa-hand-lizard-o"></i>
                    <span class="title">Cash on Delivery</span>
                </a>
            </li>

            <li class="nav-item discounts">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-magic"></i>
                    <span class="title">Discount</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item discount-add">
                        <a href="{{ URL::to('discount/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item discount-manage">
                        <a href="{{ URL::to('discount') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Settings</h3>
            </li>

            <li class="nav-item picking-times">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-clock-o"></i>
                    <span class="title">Picking Times</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item picking-times-add">
                        <a href="{{ URL::to('picking-time/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item picking-times-manage">
                        <a href="{{ URL::to('picking-time') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item charge-model">
                <a href="{{ URL::to('charge-model') }}">
                    <i class="fa fa-balance-scale"></i>
                    <span class="title">Charge Models</span>
                </a>
            </li>

            <li class="nav-item settings">
                <a href="{{ URL::to('settings') }}">
                    <i class="icon-settings"></i>
                    <span class="title">Company</span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Manage Accounts</h3>
            </li>
            <li class="nav-item bank">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Bank</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-bank">
                        <a href="{{ URL::to('/bank') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-bank">
                        <a href="{{ URL::to('/bank/create') }}" class="nav-link ">
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
                        <a href="{{ URL::to('/bank-accounts') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-bank-accounts">
                        <a href="{{ URL::to('/bank-accounts/create') }}" class="nav-link ">
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
                        <a href="{{ URL::to('/hub-bank-accounts') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-hub-bank-accounts">
                        <a href="{{ URL::to('/hub-bank-accounts/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item merchant_bank_accounts">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Merchant Bank Accounts</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-merchant-bank-accounts">
                        <a href="{{ URL::to('/merchant-bank-accounts') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-hub-bank-accounts">
                        <a href="{{ URL::to('/merchant-bank-accounts/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item vault">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-globe"></i>
                    <span class="title">Vaults</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item all-vault">
                        <a href="{{ URL::to('/vault') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item add-vault">
                        <a href="{{ URL::to('/vault/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->
