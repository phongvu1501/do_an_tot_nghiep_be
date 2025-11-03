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
                                <thead>
                                    <tr>
                                        <th>Mã voucher</th>
                                        <th>Loại giảm</th>
                                        <th>Giá trị giảm</th>
                                        <th>Giá trị đơn hàng tối thiểu</th>
                                        <th>Giá trị đơn hàng áp dụng</th>
                                        <th>Số lần sử dụng tối đa</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>{{ $voucher->code }}</td>
                                        <td>
                                            @if ($voucher->discount_type === 'percent')
                                                Giảm theo %
                                            @else
                                                Giảm theo tiền
                                            @endif
                                        </td>
                                        <td>
                                            @if ($voucher->discount_type === 'percent')
                                                {{ $voucher->discount_value }}%
                                            @else
                                                {{ number_format($voucher->discount_value, 0, ',', '.') }}đ
                                            @endif
                                        </td>
                                        <td>{{ number_format($voucher->min_order_value ?? 0, 0, ',', '.') }}đ</td>
                                        <td>{{ number_format($voucher->order_value_allowed ?? 0, 0, ',', '.') }}đ</td>
                                        <td>{{ $voucher->max_uses ?? 'Không giới hạn' }}</td>
                                        <td>
                                            @switch($voucher->status)
                                                @case('active')
                                                    <span class="badge badge-success">Hoạt động</span>
                                                    @break
                                                @case('inactive')
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-light">{{ $voucher->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($voucher->start_date instanceof \Carbon\Carbon)
                                                {{ $voucher->start_date->format('d/m/Y') }}
                                            @elseif(!empty($voucher->start_date))
                                                {{ \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') }}
                                            @else
                                                <em>Không có</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($voucher->end_date instanceof \Carbon\Carbon)
                                                {{ $voucher->end_date->format('d/m/Y') }}
                                            @elseif(!empty($voucher->end_date))
                                                {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}
                                            @else
                                                <em>Không có</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($voucher->created_at instanceof \Carbon\Carbon)
                                                {{ $voucher->created_at->format('d/m/Y') }}
                                            @elseif(!empty($voucher->created_at))
                                                {{ \Carbon\Carbon::parse($voucher->created_at)->format('d/m/Y') }}
                                            @else
                                                <em>Không có</em>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th>Mã voucher</th>
                                        <th>Loại giảm</th>
                                        <th>Giá trị giảm</th>
                                        <th>Giá trị đơn hàng tối thiểu</th>
                                        <th>Giá trị đơn hàng áp dụng</th>
                                        <th>Số lần sử dụng tối đa</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 ml-3">
                                <a href="{{ route('admin.voucher.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
