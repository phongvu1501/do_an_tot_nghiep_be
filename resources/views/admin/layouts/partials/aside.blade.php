<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            {{-- <div class="image">
                <img src="{{ asset('./assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('admin.dashboard') }}" class="d-block">Admin</a>
                <a href="{{ route('admin.profile') }}" class="d-block">Admin</a>
            </div> --}}
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            {{-- <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div> --}}
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Thống kê</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- <a href="{{ route('admin.dashboard') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tổng quan</p>
                            </a> --}}
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.reservationStatistics') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thống kê đặt bàn</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.voucherStatistics') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thống kê vouchers</p>
                                <a href="{{ route('admin.menuStatistics') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Thống kê Menu</p>
                                </a>
                                <a href="{{ route('admin.thongkeStatistics') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Thống kê doanh thu</p>
                                </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.datBan.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Quản lý đặt bàn</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.depositRequiredDate.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Quản lý cọc</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-utensils"></i>
                        <p>Thực đơn</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/menu-categories') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh mục món ăn</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/menus') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Món ăn</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.banAn.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            Quản lý bàn ăn
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.tiers.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>
                            Quản lý tiers
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.vouchers.voucher.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>
                            Quản lý vouchers
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.comments.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-comment-dots"></i>
                        <p>Quản lý bình luận</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Quản lý tài khoản</p>
                        <i class="right fas fa-angle-left"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.accounts') }}" class="nav-link">
                                <i class="far fa-user nav-icon"></i>
                                <p>Tài khoản quản trị viên</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.accounts') }}" class="nav-link">
                                <i class="far fa-user nav-icon"></i>
                                <p>Tài khoản khách hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.profile') }}" class="nav-link">
                                <i class="far fa-user nav-icon"></i>
                                <p>Thông tin tài khoản</p>
                            </a>
                        </li>
                    </ul>

                </li>
            </ul>

        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
