@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Danh sách Voucher</h3>
                            </div>

                            <div class="card-body">

                                {{-- Thông báo --}}
                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show position-fixed"
                                        role="alert"
                                        style="
                                            top: 20px;
                                            right: 20px;
                                            z-index: 1050;
                                            min-width: 320px;
                                            max-width: 420px;
                                            border-radius: 10px;
                                            font-size: 14px;
                                            background-color: #e9f7ef;
                                            border: 1px solid #b7e4c7;
                                            color: #2d6a4f;">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div id="error-alert"
                                        class="alert alert-danger alert-dismissible fade show position-fixed" role="alert"
                                        style="
                                         top: 20px;
                                         right: 20px;
                                         z-index: 1050;
                                         font-size: 14px;
                                         padding: 10px 15px;
                                         border-radius: 8px;
                                         max-width: 300px;">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <a href="{{ route('admin.vouchers.voucher.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Thêm voucher mới
                                    </a>
                                </div>
                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <form action="{{ route('admin.vouchers.voucher.index') }}" method="GET"
                                            class="row align-items-end">

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Mã voucher</label>
                                                <input type="text" name="code" class="form-control"
                                                    value="{{ request('code') }}" placeholder="Nhập mã">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Trạng thái</label>
                                                <select name="status" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="active"
                                                        {{ request('status') == 'active' ? 'selected' : '' }}>
                                                        Hoạt động
                                                    </option>
                                                    <option value="inactive"
                                                        {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                                        Tạm dừng
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Loại giảm</label>
                                                <select name="discount_type" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="percent"
                                                        {{ request('discount_type') == 'percent' ? 'selected' : '' }}>
                                                        %
                                                    </option>
                                                    <option value="amount"
                                                        {{ request('discount_type') == 'amount' ? 'selected' : '' }}>
                                                        Tiền
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="font-weight-bold">Người dùng</label>
                                                <select name="user_id" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Từ ngày</label>
                                                <input type="date" name="start_date" class="form-control"
                                                    value="{{ request('start_date') }}">
                                            </div>

                                            <div class="col-md-1">
                                                <label class="font-weight-bold">Đến</label>
                                                <input type="date" name="end_date" class="form-control"
                                                    value="{{ request('end_date') }}">
                                            </div>

                                            <div class="col-md-12 mt-2 d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-sm mr-2">
                                                    <i class="fas fa-search"></i> Tìm kiếm
                                                </button>

                                                <a href="{{ route('admin.vouchers.voucher.index') }}"
                                                    class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-redo"></i> Đặt lại
                                                </a>
                                            </div>

                                        </form>
                                    </div>
                                </div>


                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã voucher</th>
                                            <th>Loại giảm giá</th>
                                            <th>Giá trị giảm</th>
                                            <th>Đối tượng</th>
                                            <th>Giới hạn sử dụng</th>
                                            <th>Đơn hàng tối thiểu</th>
                                            <th>Giá trị giảm tối đa</th>
                                            <th>Ngày bắt đầu</th>
                                            <th>Ngày kết thúc</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($vouchers as $voucher)
                                            <tr>
                                                <td>{{ ($vouchers->currentPage() - 1) * $vouchers->perPage() + $loop->iteration }}
                                                </td>
                                                <td><strong>{{ $voucher->code }}</strong></td>
                                                <td>
                                                    @if ($voucher->discount_type === 'percent')
                                                        <span class="badge badge-primary">Phần trăm</span>
                                                    @else
                                                        <span class="badge badge-secondary">Số tiền</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($voucher->discount_type === 'percent')
                                                        <strong
                                                            class="text-primary">{{ number_format($voucher->discount_value, 0) }}%</strong>
                                                    @else
                                                        <strong
                                                            class="text-success">{{ number_format($voucher->discount_value, 0) }}
                                                            ₫</strong>
                                                    @endif
                                                </td>

                                                <td>
                                                    @php
                                                        $assignedUsersCount = $voucher->users
                                                            ? $voucher->users->count()
                                                            : 0;
                                                        $isForAllUsers =
                                                            $assignedUsersCount > 0 &&
                                                            $assignedUsersCount == $totalUsers;
                                                    @endphp

                                                    @if ($isForAllUsers)
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-users"></i> Tất cả mọi người
                                                        </span>
                                                    @elseif ($assignedUsersCount > 0)
                                                        @foreach ($voucher->users as $user)
                                                            <span class="badge badge-primary">{{ $user->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge badge-secondary">Chưa gán</span>
                                                    @endif
                                                </td>
                                                <td>{{ $voucher->max_uses ?? 'Không giới hạn' }}</td>
                                                <td>{{ $voucher->min_order_value ? number_format($voucher->min_order_value, 0) . ' ₫' : 'Không yêu cầu' }}
                                                </td>
                                                <td>
                                                    @if ($voucher->discount_type === 'percent' && $voucher->order_value_allowed)
                                                        {{ number_format($voucher->order_value_allowed, 0) }} ₫
                                                    @elseif ($voucher->discount_type === 'percent')
                                                        <span class="text-muted">Không giới hạn</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('d/m/Y', strtotime($voucher->start_date)) }}</td>
                                                <td>{{ date('d/m/Y', strtotime($voucher->end_date)) }}</td>
                                                <td>
                                                    @if ($voucher->status === 'active')
                                                        <span class="badge badge-success">Hoạt động</span>
                                                    @else
                                                        <span class="badge badge-secondary">Tạm dừng</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.vouchers.voucher.show', $voucher->id) }}"
                                                        class="btn btn-info btn-sm">Chi tiết</a>
                                                    <a href="{{ route('admin.vouchers.voucher.edit', $voucher->id) }}"
                                                        class="btn btn-warning btn-sm">Chỉnh sửa</a>
                                                    <form action="{{ route('admin.vouchers.voucher.disable', $voucher->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')

                                                        @if ($voucher->is_active == 0)
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                disabled>
                                                                Tạm dừng
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Bạn có chắc chắn muốn tạm dừng voucher {{ $voucher->code }} không?');">
                                                                Tạm dừng
                                                            </button>
                                                        @endif
                                                    </form>


                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="14" class="text-center">Không có voucher nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $vouchers->links('vendor.pagination.bootstrap-4') }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endsection
