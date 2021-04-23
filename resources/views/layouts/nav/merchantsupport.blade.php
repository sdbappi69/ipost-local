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
                <a href="{{ URL::to('home') }}">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
                    <!-- <span class="selected"></span> -->
                    <!-- <span class="arrow open"></span> -->
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">Order Management</h3>
            </li>

            <li class="nav-item merchant-orders">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cube"></i>
                    <span class="title">Orders</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item merchant-order-add">
                        <a href="{{ URL::to('merchant-orderv2/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item merchant-order-bulk">
                        <a href="{{ URL::to('merchant-order-bulk') }}" class="nav-link ">
                            <span class="title">Bulk Upload</span>
                        </a>
                    </li>
                    <li class="nav-item merchant-order-manage">
                        <a href="{{ URL::to('merchant-order') }}" class="nav-link ">
                            <span class="title">Manage</span>
                        </a>
                    </li>
                    <li class="nav-item merchant-order-draft">
                        <a href="{{ URL::to('merchant-order-draftv2') }}" class="nav-link ">
                            <span class="title">Draft</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="heading">
                <h3 class="uppercase">Store Management</h3>
            </li>

            <li class="nav-item stores">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="title">Stores</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item store-manage">
                        <a href="{{ URL::to('store') }}" class="nav-link ">
                            <span class="title">Store</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item warehouses">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">Warehouses</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item warehouse-add">
                        <a href="{{ URL::to('warehouse/create') }}" class="nav-link ">
                            <span class="title">Add</span>
                        </a>
                    </li>
                    <li class="nav-item warehouse-manage">
                        <a href="{{ URL::to('warehouse') }}" class="nav-link ">
                            <span class="title">Manage</span>
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