@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <!-- Tiêu đề trang -->
        <section class="content-header px-4 mt-3 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-primary mb-0">Tài khoản quản trị viên</h2>
        </section>

        <!-- Nội dung chính -->
        <section class="content px-4 mt-3">
            <div class="row">
                <!-- Nội dung phải -->
                <div class="col-md-9">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-light border-0">
                            <h4 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Thông tin tài khoản
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- Cột thông tin -->
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Tên:</strong> {{ $user->name }}</li>
                                        <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                                        <li class="list-group-item"><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa có' }}</li>
                                        <li class="list-group-item"><strong>Chức vụ:</strong> {{ ucfirst($user->role) }}</li>
                                        <li class="list-group-item"><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y') }}</li>
                                    </ul>
                                </div>

                                <!-- Cột chỉnh sửa -->
                                <div class="col-md-6">
                                    <form action="{{ route('admin.profile.update') }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tên</label>
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ old('name', $user->name) }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email" class="form-control"
                                                   value="{{ $user->email }}" disabled>
                                       
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control"
                                                   value="{{ old('phone', $user->phone) }}">
                                        </div>

                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i> Lưu thay đổi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
