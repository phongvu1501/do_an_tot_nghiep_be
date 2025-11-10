@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">
    <!-- Tiêu đề trang -->
    <section class="content-header px-4 mt-3">
        <h2 class="fw-bold mb-2">Danh sách tài khoản quản trị viên</h2>
        <p class="text-muted small">Xem tất cả tài khoản admin trong hệ thống</p>
    </section>

    <!-- Nội dung -->
    <section class="content px-4 mt-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
              <table class="table table-hover table-bordered align-middle mb-0 text-center">
    <thead style="background-color: #000000; color: white;">
        <tr>
            <th scope="col" style="width:5%;">ID</th>
            <th scope="col" style="width:20%;">Tên</th>
            <th scope="col" style="width:20%;">Email</th>
            <th scope="col" style="width:15%;">Số điện thoại</th>
            <th style="width:10%;">Chức vụ</th>
            <th scope="col" style="width:15%;">Ngày tạo</th>
            <th scope="col" style="width:15%;">Chi tiết</th> <!-- Cột mới -->
        </tr>
    </thead>
    <tbody>
        @forelse ($admins as $admin)
            <tr style="background-color: #f8f9fa;">
                <td>{{ $admin->id }}</td>
                <td class="text-start ps-3">{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->phone ?? 'Chưa có' }}</td>
                <td>{{ ucfirst($admin->role ?? 'admin') }}</td>
                <td>{{ $admin->created_at->format('d/m/Y') }}</td>
                <td>
                    <!-- Nút mở modal chi tiết -->
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#adminModal{{ $admin->id }}">
                        Xem chi tiết
                    </button>
                </td>
            </tr>

            <!-- Modal chi tiết admin -->
            <div class="modal fade" id="adminModal{{ $admin->id }}" tabindex="-1" aria-labelledby="adminModalLabel{{ $admin->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="adminModalLabel{{ $admin->id }}">Chi tiết quản trị viên</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body text-start">
                            <p><strong>ID:</strong> {{ $admin->id }}</p>
                            <p><strong>Tên:</strong> {{ $admin->name }}</p>
                            <p><strong>Email:</strong> {{ $admin->email }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $admin->phone ?? 'Chưa có' }}</p>
                            <p><strong>Chức vụ:</strong> {{ ucfirst($admin->role ?? 'admin') }}</p>
                            <p><strong>Ngày tạo:</strong> {{ $admin->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Cập nhật gần nhất:</strong> {{ $admin->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <tr>
                <td colspan="7" class="text-muted py-3">Chưa có quản trị viên nào</td>
            </tr>
        @endforelse
    </tbody>
</table>

            </div>
            <div class="card-footer text-center text-muted small" style="background-color: #e9ecef;">
                Tổng: {{ $admins->count() }} quản trị viên | Cập nhật: {{ now()->format('H:i d/m/Y') }}
            </div>
        </div>
    </section>
</div>
@endsection
