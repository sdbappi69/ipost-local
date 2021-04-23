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
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item user-manage">
                        <a href="{{ URL::to('user') }}" class="nav-link ">
                            <span class="title">Manage</span>
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

            <li class="heading">
                <h3 class="uppercase">Settings</h3>
            </li>

            <li class="nav-item charges">
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
            </li>
            <li class="nav-item cod">
                <a href="{{ URL::to('charge/1') }}">
                    <i class="fa fa-hand-lizard-o"></i>
                    <span class="title">Cash on Delivery</span>
                </a>
            </li>
            <li class="nav-item settings">
                <a href="{{ URL::to('settings') }}">
                    <i class="icon-settings"></i>
                    <span class="title">Company</span>
                </a>
            </li>

        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->