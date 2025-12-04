@extends('admin.layouts.main')

@section('noidung')
<div class="text-center mt-5">
    <h3 class="text-danger">🚫 Bạn không có quyền truy cập trang này!</h3>
    <a href="{{ route('login') }}" class="btn btn-primary mt-3">Quay lại đăng nhập</a>
</div>
@endsection
