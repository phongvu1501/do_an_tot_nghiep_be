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
                                                max-width: 300px;
                                            ">
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                                style="outline: none;">
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
                                                <th>Giới hạn sử dụng</th>
                                                <th>Đơn hàng tối thiểu</th>
                                                <th>Giá trị đơn hàng áp dụng</th>
                                                <th>Ngày bắt đầu</th>
                                                <th>Ngày kết thúc</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if (isset($vouchers) && count($vouchers) > 0)
                                                @foreach ($vouchers as $index => $voucher)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $voucher->code }}</td>
                                                        <td>
                                                            {{ $voucher->discount_type === 'percent' ? 'Theo phần trăm' : 'Theo số tiền' }}
                                                        </td>
                                                        <td>{{ number_format($voucher->discount_value, 0) }}</td>
                                                        <td>{{ $voucher->max_uses }}</td>
                                                        <td>{{ number_format($voucher->min_order_value, 0) }}</td>
                                                        <td>
                                                            {{ $voucher->order_value_allowed ? number_format($voucher->order_value_allowed, 0) . ' ₫' : 'Không giới hạn' }}
                                                        </td>
                                                        <td>{{ $voucher->start_date ? date('d/m/Y', strtotime($voucher->start_date)) : '' }}</td>
                                                        <td>{{ $voucher->end_date ? date('d/m/Y', strtotime($voucher->end_date)) : '' }}</td>
                                                        <td>
                                                            @switch($voucher->status)
                                                                @case('active')
                                                                    <span class="badge badge-success">Hoạt động</span>
                                                                    @break
                                                                @case('inactive')
                                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                                    @break
                                                                @default
                                                                    {{ $voucher->status }}
                                                            @endswitch
                                                        </td>
                                                        <td>{{ $voucher->created_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.vouchers.voucher.show', $voucher->id) }}"
                                                                class="btn btn-info btn-sm">Chi tiết</a>
                                                            <a href="{{ route('admin.vouchers.voucher.edit', $voucher->id) }}"
                                                                class="btn btn-warning btn-sm">Chỉnh sửa</a>

                                                            @if ($voucher->status === 'active')
                                                                <form action="{{ route('admin.vouchers.voucher.disable', $voucher->id) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Bạn có chắc chắn muốn DỪNG HOẠT ĐỘNG voucher {{ $voucher->code }} này không?');">
                                                                        Dừng hoạt động
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="badge badge-secondary">Đã dừng</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="12" class="text-center">Không có voucher nào.</td>
                                                </tr>
                                            @endif
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th>STT</th>
                                                <th>Mã voucher</th>
                                                <th>Loại giảm giá</th>
                                                <th>Giá trị giảm</th>
                                                <th>Giới hạn sử dụng</th>
                                                <th>Đơn hàng tối thiểu</th>
                                                <th>Giá trị đơn hàng áp dụng</th>
                                                <th>Ngày bắt đầu</th>
                                                <th>Ngày kết thúc</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </tfoot>
                                    </table>
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