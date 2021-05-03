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
                </a>
            </li>
            <li class="heading">
                <h3 class="uppercase">Customer Services</h3>
            </li>
            @permission('manage_customer_support_order')
            <li class="nav-item start order_cs">
                <a href="{{ secure_url('order-cs') }}">
                    <i class="icon-list"></i>
                    <span class="title">Order List</span>
                </a>
            </li>
            @endpermission
            @permission('manage_complain')
            <li class="nav-item inbound">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-angle-double-right"></i>
                    <span class="title">Inbound</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @permission('manage_complain')
                    <li class="nav-item complain">
                        <a href="{{ secure_url('complain') }}" class="nav-link ">
                            <span class="title">Complain</span>
                        </a>
                    </li>
                    @endpermission
                    @permission('manage_inquiry')
                    <li class="nav-item inquiry">
                        <a href="{{ secure_url('inquiry') }}" class="nav-link ">
                            <span class="title">Inquiry</span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>
            @endpermission
            @permission('manage_feedback')
            <li class="nav-item outbound">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-comments"></i>
                    <span class="title">Outbound</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item get_feedback">
                        <a href="{{ secure_url('feedback') }}" class="nav-link ">
                            <span class="title">Get Feedback</span>
                        </a>
                    </li>
                    <li class="nav-item collected_feedback">
                        <a href="{{ secure_url('feedback?status_s=1') }}" class="nav-link ">
                            <span class="title">Collected Feedback</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endpermission
            @permission(['manage_query','manage_mail_groups','manage_reaction','manage_unique_head','manage_source_of_information'])
            <li class="nav-item setting">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-wrench"></i>
                    <span class="title">Setting</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @permission('manage_inquiry_status')
                    <li class="nav-item inquiry_status">
                        <a href="{{ secure_url('inquiry-status') }}" class="nav-link ">
                            <span class="title">Inquiry Status</span>
                        </a>
                    </li>
                    @endpermission
                    @permission('manage_mail_groups')
                    <li class="nav-item mail_group">
                        <a href="{{ secure_url('mail-groups') }}" class="nav-link ">
                            <span class="title">Mail Groups</span>
                        </a>
                    </li>
                    @endpermission
                    @permission('manage_query')
                    <li class="nav-item query">
                        <a href="{{ secure_url('query') }}" class="nav-link ">
                            <span class="title">Queries</span>
                        </a>
                    </li>
                    @endpermission
                    @permission('manage_source_of_information')
                    <li class="nav-item src_of_info">
                        <a href="{{ secure_url('source-of-info') }}" class="nav-link ">
                            <span class="title">Source Of Information</span>
                        </a>
                    </li>
                    @endpermission
                    @permission('manage_unique_head')
                    <li class="nav-item unique_head">
                        <a href="{{ secure_url('unique-head') }}" class="nav-link ">
                            <span class="title">Unique Head</span>
                        </a>
                    </li>
                    @endpermission
                    @permission('manage_reaction')
                    <li class="nav-item reaction">
                        <a href="{{ secure_url('reaction') }}" class="nav-link ">
                            <span class="title">Reaction</span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>
            @endpermission
        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->