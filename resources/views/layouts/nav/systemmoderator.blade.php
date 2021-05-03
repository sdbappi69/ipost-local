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

            <!-- <li class="nav-item tasks">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-check"></i>
                    <span class="title">Tasks</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item verify-orders">
                        <a href="{{ secure_url('verify-order') }}" class="nav-link ">
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
                        <a href="{{ secure_url('order') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item order-draft">
                        <a href="{{ secure_url('order-draftv2') }}" class="nav-link ">
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
                        <a href="{{ secure_url('product-category/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item product-category-manage">
                        <a href="{{ secure_url('product-category') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item product-category-charge-approve">
                        <a href="{{ secure_url('product-category-charge-approval/v2') }}" class="nav-link ">
                            <span class="title">Approve Charges</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Merchants Management</h3>
            </li>

            <li class="nav-item start merchants merchant-manage">
                <a href="{{ secure_url('merchant') }}">
                    <i class="fa fa-child"></i>
                    <span class="title">Merchants</span>
                </a>
            </li>

            <li class="nav-item start stores store-manage">
                <a href="{{ secure_url('store') }}">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="title">Stores</span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Vehicles Management</h3>
            </li>

            <li class="nav-item start vehicles vehicle">
                <a href="{{ secure_url('vehicle') }}">
                    <i class="fa fa-truck"></i>
                    <span class="title">Vehicles</span>
                </a>
            </li>

<!--            <li class="nav-item drivers">
                <a href="{{ secure_url('driver') }}">
                    <i class="fa fa-hand-rock-o"></i>
                    <span class="title">Drivers</span>
                </a>
            </li>-->

            <li class="heading">
                <h3 class="uppercase">Hubs Management</h3>
            </li>

            <li class="nav-item hubs hub-manage">
                <a href="{{ secure_url('hub') }}">
                    <i class="fa fa-square"></i>
                    <span class="title">Hubs</span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Settings</h3>
            </li>

            <li class="nav-item picking-times picking-times-manage">
                <a href="{{ secure_url('picking-time') }}">
                    <i class="fa fa-clock-o"></i>
                    <span class="title">Picking Times</span>
                </a>
            </li>

            <li class="nav-item charge-model">
                <a href="{{ secure_url('charge-model') }}">
                    <i class="fa fa-balance-scale"></i>
                    <span class="title">Charge Models</span>
                </a>
            </li>

            {{-- <li class="nav-item start charges charge-manage">
                <a href="{{ secure_url('charge') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Charges</span>
                </a>
            </li> --}}
<!--            <li class="nav-item cod">
                <a href="{{ secure_url('charge/1') }}">
                    <i class="fa fa-hand-lizard-o"></i>
                    <span class="title">Cash on Delivery</span>
                </a>
            </li>-->

        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->