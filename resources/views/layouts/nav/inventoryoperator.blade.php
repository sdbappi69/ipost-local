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

                <li class="nav-item start pickup">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-arrow-circle-left"></i>
                        <span class="title">In</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item accept-picked">
                            <a href="{{ secure_url('accept-picked') }}" class="nav-link ">
                                <span class="title">Receive from rider</span>
                            </a>
                        </li>
                        <li class="nav-item receive-picked">
                            <a href="{{ secure_url('receive-picked') }}" class="nav-link ">
                                <span class="title">Verify Product</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item start delivery">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-arrow-circle-right"></i>
                        <span class="title">Out</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item queued-shipping">
                            <a href="{{ secure_url('queued-shipping') }}" class="nav-link ">
                                <span class="title">Orders to transfer</span>
                            </a>
                        </li>
                        <li class="nav-item accept-suborder">
                            <a href="{{ secure_url('accept-suborder') }}" class="nav-link ">
                                <span class="title">Receive transferd Orders</span>
                            </a>
                        </li>
                    </ul>
                </li>

            <li class="nav-item start orders">
                <a href="{{ secure_url('hub-order') }}">
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
                    <a href="{{ secure_url('shelf/create') }}" class="nav-link ">
                        <span class="title">Add</span>
                    </a>
                </li>
                <li class="nav-item warehouse-manage">
                    <a href="{{ secure_url('shelf') }}" class="nav-link ">
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
                <a href="{{ secure_url('rack/create') }}" class="nav-link ">
                    <span class="title">Add</span>
                </a>
            </li>
            <li class="nav-item rack-manage">
                <a href="{{ secure_url('rack') }}" class="nav-link ">
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
        </a>
        <ul class="sub-menu">
            <li class="nav-item trip-create">
                <a href="{{ secure_url('trip/create') }}" class="nav-link ">
                    <span class="title">Add</span>
                </a>
            </li>
            <li class="nav-item trip-manage">
                <a href="{{ secure_url('trip') }}" class="nav-link ">
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
