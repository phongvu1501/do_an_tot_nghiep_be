@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">
    <!-- Tiêu đề trang -->
    <section class="content-header px-4 mt-3">
        <h2 class="fw-bold mb-1">Danh sách tài khoản khách hàng</h2>
        <p class="text-muted small">Xem tất cả tài khoản người dùng trong hệ thống</p>
    </section>

    <!-- Nội dung -->
    <section class="content px-4 mt-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead style="background-color: #000000; color: white;">
                        <tr>
                            <th style="width:5%;">Stt</th>
                            <th style="width:20%;">Tên</th>
                            <th style="width:20%;">Email</th>
                            <th style="width:15%;">Số điện thoại</th>
                            <th style="width:15%;">Chức vụ</th>
                            <th style="width:25%;">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr style="background-color: #f8f9fa;">
                                <td>{{ $user->id }}</td>
                                <td class="text-start ps-3">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'Chưa có' }}</td>
                                <td>{{ ucfirst($user->role ?? 'user') }}</td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-exclamation-circle me-1"></i> Chưa có tài khoản khách hàng nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center text-muted small" style="background-color: #e9ecef;">
                Tổng khách hàng: {{ $users->count() }} | Cập nhật: {{ now()->format('H:i d/m/Y') }}
            </div>
        </div>
    </section>
</div>
@endsection
