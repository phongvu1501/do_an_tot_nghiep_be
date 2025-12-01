@extends('admin.layouts.main')


@section('noidung')
<div class="content-wrapper">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold text-primary mb-0">Thông tin khách hàng</h5>
                <a href="{{ route('admin.datBan.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
            <hr>

            <p><strong>Tên:</strong> {{ $reservation->user->name ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $reservation->user->phone ?? 'Chưa cập nhật' }}</p>
            <p><strong>Email:</strong> {{ $reservation->user->email ?? 'Chưa có email' }}</p>
            <p><strong>Ngày đặt:</strong> {{ $reservation->reservation_date }} {{ $reservation->reservation_time }}
                | <strong>Số người:</strong> {{ $reservation->num_people }}
                | <strong>Số bàn:</strong> {{ $reservation->table_id ?? 'Chưa chọn' }}</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-bold text-primary">Chi tiết đơn hàng</h5>
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>STT</th>
                        <th>Món</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->reservationItems as $index => $item)
                    <tr class="text-center">
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">{{ $item->menu->name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                        <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p><strong>Ghi chú:</strong> {{ $reservation->depsection ?? 'Không có' }}</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-primary mb-3">Thông tin hóa đơn</h5>
                    @php
                        $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
                        $tax = $subtotal * 0.1;
                        $total = $subtotal + $tax;
                    @endphp
                    <p><strong>Tạm tính:</strong> {{ number_format($subtotal, 0, ',', '.') }} VND</p>
                    <p><strong>Giảm giá:</strong> 0 VND</p>
                    <p><strong>Thuế 10%:</strong> {{ number_format($tax, 0, ',', '.') }} VND</p>
                    <hr>
                    <p class="fw-bold fs-5">Tổng: {{ number_format($total, 0, ',', '.') }} VND</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-primary mb-3">Thông tin thanh toán</h5>
                    @php
                        // Trừ cả cọc bàn và cọc đồ ăn ban đầu (nếu đã cọc)
                        $tableDeposit = $reservation->getTableDeposit();
                        $foodDeposit = $reservation->getFoodDeposit(); // Cọc đồ ăn ban đầu
                        $remainingAmount = $total - $tableDeposit - $foodDeposit;
                    @endphp
                    <p><strong>Tiền cọc bàn:</strong> {{ number_format($tableDeposit, 0, ',', '.') }} VND</p>
                    @if($foodDeposit > 0)
                    <p><strong>Tiền cọc đồ ăn:</strong> {{ number_format($foodDeposit, 0, ',', '.') }} VND</p>
                    @endif
                    <p><strong>Tiền cọc tổng:</strong> {{ number_format($reservation->deposit ?? 0, 0, ',', '.') }} VND</p>
                    @if($remainingAmount > 0)
                    <p><strong>Còn phải thanh toán:</strong> {{ number_format($remainingAmount, 0, ',', '.') }} VND</p>
                    @elseif($remainingAmount < 0)
                    <p><strong>Hoàn lại cho khách:</strong> {{ number_format(abs($remainingAmount), 0, ',', '.') }} VND</p>
                    @else
                    <p><strong>Đã thanh toán đủ:</strong> 0 VND</p>
                    @endif
                    <p><strong>Trạng thái:</strong>
                        <span class="badge {{ $reservation->status == 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($reservation->status ?? 'Đang xử lý') }}
                        </span>
                    </p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.datBan.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
