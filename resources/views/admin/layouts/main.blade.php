<!DOCTYPE html>
<html lang="en">

@include('admin.layouts.partials.head')

<body class="hold-transition sidebar-mini layout-fixed">

    @include('admin.layouts.partials.nav')

    @include('admin.layouts.partials.aside')

    @yield('noidung')

    @include('admin.layouts.partials.footer')

</body>

</html>
