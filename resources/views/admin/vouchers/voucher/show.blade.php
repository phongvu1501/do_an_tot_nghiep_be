@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Chi tiết voucher: {{ $voucher->code }}</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th>Mã voucher</th>
                                        <td>{{ $voucher->code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Người dùng</th>
                                        <td>
                                            @if ($voucher->users && $voucher->users->count() > 0)
                                                <ul class="mb-0">
                                                    @foreach ($voucher->users as $user)
                                                        <li>{{ $user->name }} ({{ $user->email }})</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                Không gán
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tier</th>
                                        <td>{{ $voucher->tier ? $voucher->tier->name . ' - ' . $voucher->tier->discount_percent . '%' : 'Không gán' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Loại giảm</th>
                                        <td>{{ $voucher->discount_type === 'percent' ? 'Theo %' : 'Theo tiền' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Giá trị giảm</th>
                                        <td>
                                            @if ($voucher->discount_type === 'percent')
                                                {{ $voucher->discount_value }}%
                                            @else
                                                {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Đơn hàng tối thiểu</th>
                                        <td>{{ number_format($voucher->min_order_value ?? 0, 0, ',', '.') }}đ</td>
                                    </tr>
                                    <tr>
                                        <th>Giá trị áp dụng</th>
                                        <td>{{ number_format($voucher->order_value_allowed ?? 0, 0, ',', '.') }}đ</td>
                                    </tr>
                                    <tr>
                                        <th>Số lần sử dụng tối đa</th>
                                        <td>{{ $voucher->max_uses ?? 'Không giới hạn' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Trạng thái</th>
                                        <td>
                                            @if ($voucher->status === 'active')
                                                <span class="badge badge-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-secondary">Tạm dừng</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Ngày bắt đầu</th>
                                        <td>{{ \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ngày kết thúc</th>
                                        <td>{{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ngày tạo</th>
                                        <td>{{ \Carbon\Carbon::parse($voucher->created_at)->format('d/m/Y') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <a href="{{ route('admin.vouchers.voucher.index') }}" class="btn btn-secondary">Quay
                                    lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
