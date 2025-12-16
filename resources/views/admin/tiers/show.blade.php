@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Chi tiết Tier: {{ $tier->name }}</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên Tier</th>
                                        <th>Điểm cần đổi</th>
                                        <th>% Giảm giá</th>
                                        <th>Giá trị giảm tối đa</th>
                                        <th>Đơn hàng tối thiểu</th>
                                        {{-- <th>Giá trị đơn hàng áp dụng</th> --}}
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Ngày cập nhật</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>{{ $tier->name }}</td>
                                        <td>{{ $tier->points_required }}</td>
                                        <td>{{ $tier->discount_percent }}%</td>
                                        <td>{{ number_format($tier->max_discount_value ?? 0, 0, ',', '.') }}đ</td>
                                        <td>{{ number_format($tier->min_order_value ?? 0, 0, ',', '.') }}đ</td>
                                        <td>{{ number_format($tier->order_value_allowed, 0) }}</td>

                                        {{-- <td>
                                            {{ number_format($tier->order_value_allowed ?? 0, 0, ',', '.') }}đ
                                        </td> --}}

                                        <td>
                                            @if ($tier->is_active)
                                                <span class="badge badge-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-secondary">Tạm dừng</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tier->created_at instanceof \Carbon\Carbon)
                                                {{ $tier->created_at->format('d/m/Y') }}
                                            @elseif(!empty($tier->created_at))
                                                {{ \Carbon\Carbon::parse($tier->created_at)->format('d/m/Y') }}
                                            @else
                                                <em>Không có</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tier->updated_at instanceof \Carbon\Carbon)
                                                {{ $tier->updated_at->format('d/m/Y') }}
                                            @elseif(!empty($tier->updated_at))
                                                {{ \Carbon\Carbon::parse($tier->updated_at)->format('d/m/Y') }}
                                            @else
                                                <em>Không có</em>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th>Tên Tier</th>
                                        <th>Điểm cần đổi</th>
                                        <th>% Giảm giá</th>
                                        <th>Giá trị giảm tối đa</th>
                                        <th>Đơn hàng tối thiểu</th>
                                        {{-- <th>Giá trị đơn hàng áp dụng</th> --}}
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Ngày cập nhật</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 ml-3">
                                <a href="{{ route('admin.tiers.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
