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
                            <div class="card-header"></div>

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
                                            font-size: 14px;
                                            padding: 10px 15px;
                                            border-radius: 8px;
                                            max-width: 300px;">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <a href="{{ route('admin.vouchers.voucher.create') }}"
                                    class="btn btn-success btn-sm mb-3 col-1">Thêm mới</a>

                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã voucher</th>
                                            <th>Loại giảm giá</th>
                                            <th>Giá trị giảm</th>
                                            <th>Thuộc Tier</th>
                                            <th>Áp dụng cho</th>
                                            <th>Giới hạn sử dụng</th>
                                            <th>Đơn hàng tối thiểu</th>
                                            <th>Đơn hàng tối đa áp dụng</th>
                                            <th>Ngày bắt đầu</th>
                                            <th>Ngày kết thúc</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($vouchers as $voucher)
                                            <tr>
                                                <td>{{ ($vouchers->currentPage() - 1) * $vouchers->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $voucher->code }}</td>
                                                <td>{{ $voucher->discount_type === 'percent' ? 'Phần trăm' : 'Số tiền' }}
                                                </td>
                                                <td>{{ number_format($voucher->discount_value) }}</td>
                                                <td>
                                                    @if ($voucher->tier)
                                                        <span class="badge badge-info">{{ $voucher->tier->name }}</span>
                                                    @else
                                                        <span class="text-muted">Không thuộc tier</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($voucher->user)
                                                        <span class="badge badge-primary">{{ $voucher->user->name }}</span>
                                                    @else
                                                        <span class="badge badge-success">Tất cả người dùng</span>
                                                    @endif
                                                </td>
                                                <td>{{ $voucher->max_uses ?? 'Không giới hạn' }}</td>
                                                <td>{{ number_format($voucher->min_order_value, 0) }}</td>
                                                <td>{{ $voucher->order_value_allowed ? number_format($voucher->order_value_allowed) . ' ₫' : 'Không giới hạn' }}
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
                                                <td>{{ $voucher->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.vouchers.voucher.show', $voucher->id) }}"
                                                        class="btn btn-info btn-sm">Chi tiết</a>
                                                    <a href="{{ route('admin.vouchers.voucher.edit', $voucher->id) }}"
                                                        class="btn btn-warning btn-sm">Sửa</a>
                                                    @if ($voucher->status === 'active')
                                                        <form
                                                            action="{{ route('admin.vouchers.voucher.disable', $voucher->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Bạn có chắc muốn dừng voucher {{ $voucher->code }} không?');">
                                                                Dừng
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge badge-secondary">Đã dừng</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="14" class="text-center">Không có voucher nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- PHÂN TRANG NẰM NGOÀI TABLE --}}
                                @if ($vouchers->hasPages())
                                    <div class="mt-3">
                                        {{ $vouchers->links() }}
                                    </div>
                                @endif

                                @if ($vouchers->hasPages())
                                    <div class="mt-3">
                                        {{ $vouchers->links() }}
                                    </div>
                                @endif
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
