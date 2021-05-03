<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-light " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item start dashboard">
                <a href="{{ secure_url('home') }}">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
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
                </a>
                <ul class="sub-menu">
                    <li class="nav-item pick_up_consignments">
                        <a href="{{ secure_url('consignments-pick-up') }}" class="nav-link ">
                            <span class="title">Pick Up</span>
                        </a>
                    </li>
                    <li class="nav-item return_consignments">
                        <a href="{{ secure_url('consignments-return') }}" class="nav-link ">
                            <span class="title">Return</span>
                        </a>
                    </li>
                    <li class="nav-item consignmentsv2">
                        <a href="{{ secure_url('v2consignment') }}" class="nav-link ">
                            <span class="title">Consignments</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item start pickup">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-arrow-circle-left"></i>
                    <span class="title">Inbound</span>
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

            <li class="nav-item start orders">
                <a href="{{ secure_url('hub-order') }}">
                    <i class="fa fa-cube"></i>
                    <span class="title">Orders</span>
                </a>
            </li>

            <li class="nav-item start reconciliation">
                <a href="{{ secure_url('reconciliation') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Reconciliation</span>
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

        </ul>
    </div>
</div>
