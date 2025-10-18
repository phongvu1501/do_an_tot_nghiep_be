<!DOCTYPE html>
<html lang="en">

@include('admin.layouts.partials.head')

<body class="hold-transition sidebar-mini layout-fixed">

    @include('admin.layouts.partials.nav')

    @include('admin.layouts.partials.aside')

    @yield('noidung')

    @include('admin.layouts.partials.footer')

</body>
<!-- Bootstrap JS & jQuery (nếu chưa có) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</html>
