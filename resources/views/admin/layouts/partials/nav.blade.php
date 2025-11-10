<div class="wrapper">


    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="index3.html" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Contact</a>
            </li>
        </ul>



        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Navbar Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                    role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        </ul>
   <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center text-dark fw-semibold" href="#" id="userDropdown"
        role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle fa-lg me-2 text-primary"></i>
        {{ Auth::user()->name ?? 'Admin' }}
    </a>

    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 py-2" 
        aria-labelledby="userDropdown" style="min-width: 180px;">
        
        <li class="px-3 py-1 text-muted small">
            Xin chào, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong>
        </li>
        <li><hr class="dropdown-divider"></li>

        <li>
            <a href="{{ route('admin.profile') }}" class="dropdown-item">
                <i class="fas fa-user-cog me-2 text-primary"></i> Hồ sơ cá nhân
            </a>
        </li>

        <li>
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" 
                        class="dropdown-item text-danger fw-semibold d-flex align-items-center">
                    <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                </button>
            </form>
        </li>
    </ul>
</li>


    </nav>
