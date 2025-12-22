@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title ?? 'Danh sách Tier' }}</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"></div>

                            <div class="card-body">
                                {{-- Hiển thị thông báo thành công --}}
                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show position-fixed"
                                        role="alert"
                                        style="
                                        top: 20px;
                                        right: 20px;
                                        z-index: 1050;
                                        background-color: #d4edda;
                                        color: #155724;
                                        border-color: #c3e6cb;
                                        font-size: 14px;
                                        padding: 10px 15px;
                                        border-radius: 8px;
                                        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                        max-width: 300px;">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                            style="outline: none;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <a href="{{ route('admin.tiers.create') }}" class="btn btn-success btn-sm mb-3 col-1">Thêm
                                    mới</a>
                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <form action="{{ route('admin.tiers.index') }}" method="GET" class="row g-3">

                                            <div class="col-md-3">
                                                <label class="font-weight-bold">Tên Tier</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ request('name') }}" placeholder="Nhập tên tier">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Điểm </label>
                                                <input type="number" name="points_required" class="form-control"
                                                    value="{{ request('points_required') }}" placeholder="VD: 10">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">% Giảm </label>
                                                <input type="number" name="discount_percent" class="form-control"
                                                    value="{{ request('discount_percent') }}" placeholder="VD: 20">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Trạng thái</label>
                                                <select name="is_active" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="1"
                                                        {{ request('is_active') === '1' ? 'selected' : '' }}>
                                                        Hoạt động
                                                    </option>
                                                    <option value="0"
                                                        {{ request('is_active') === '0' ? 'selected' : '' }}>
                                                        Tạm dừng
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Từ ngày</label>
                                                <input type="date" name="created_from" class="form-control"
                                                    value="{{ request('created_from') }}">
                                            </div>

                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-search">Tìm kiếm</i>
                                                </button>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <a href="{{ route('admin.tiers.index') }}"
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
                                            <th>Tên Tier</th>
                                            <th>Điểm cần đổi</th>
                                            <th>% Giảm giá</th>
                                            <th>Giá trị giảm tối đa</th>
                                            <th>Đơn hàng tối thiểu</th>
                                            {{-- <th>Giá trị đơn hàng áp dụng</th> --}}
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if (isset($tiers) && count($tiers) > 0)
                                            @foreach ($tiers as $index => $tier)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $tier->name }}</td>
                                                    <td>{{ $tier->points_required }}</td>
                                                    <td>{{ $tier->discount_percent }}%</td>
                                                    <td>{{ number_format($tier->max_discount_value, 0) }}</td>
                                                    <td>{{ number_format($tier->min_order_value, 0) }}</td>
                                                    {{-- <td>{{ number_format($tier->order_value_allowed, 0) }}</td> --}}
                                                    <td>
                                                        @if ($tier->is_active)
                                                            <span class="badge badge-success">Hoạt động</span>
                                                        @else
                                                            <span class="badge badge-secondary">Tạm dừng</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $tier->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.tiers.show', $tier->id) }}"
                                                            class="btn btn-info btn-sm">Chi tiết</a>
                                                        <a href="{{ route('admin.tiers.edit', $tier->id) }}"
                                                            class="btn btn-warning btn-sm">Chỉnh sửa</a>
                                                        <form action="{{ route('admin.tiers.disable', $tier->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT')

                                                            @if ($tier->is_active == 0)
                                                                <button type="button" class="btn btn-secondary btn-sm"
                                                                    disabled>
                                                                    Tạm dừng
                                                                </button>
                                                            @else
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn tạm dừng tier {{ $tier->name }} không?');">
                                                                    Tạm dừng
                                                                </button>
                                                            @endif
                                                        </form>


                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9" class="text-center">Không có tier nào.</td>
                                            </tr>
                                        @endif
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên Tier</th>
                                            <th>Điểm cần đổi</th>
                                            <th>% Giảm giá</th>
                                            <th>Giá trị giảm tối đa</th>
                                            <th>Đơn hàng tối thiểu</th>
                                            {{-- <th>Giá trị đơn hàng áp dụng</th> --}}
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="mt-3">
                                    {{ $tiers->links('vendor.pagination.bootstrap-4') }}
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
