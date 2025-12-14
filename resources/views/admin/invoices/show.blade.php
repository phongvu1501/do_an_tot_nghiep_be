<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn {{ $reservation->reservation_code }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #f8f8f8;
            padding: 20px;
            color: #2b2b2b;
        }

        .invoice-card {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        h3, h4 { margin: 0 0 10px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table th {
            background: #f1f1f1;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .print-buttons { margin-top: 20px; text-align: center; }
        @media print { .print-buttons { display: none; } body { background: #fff; } }
    </style>

</head>
<body>

<div class="invoice-card">
    <h3>HÓA ĐƠN ĐẶT BÀN</h3>
    <p>Mã đơn: <strong>{{ $reservation->reservation_code }}</strong></p>
</div>

{{-- THÔNG TIN KHÁCH HÀNG --}}
<div class="invoice-card">
    <h4>Thông tin khách hàng</h4>
    <p>
        <strong>Tên:</strong> {{ $reservation->user->name }}<br>
        <strong>Email:</strong> {{ $reservation->user->email }}<br>
        <strong>Số điện thoại:</strong> {{ $reservation->user->phone ?? 'Chưa có' }}
    </p>
</div>

{{-- THÔNG TIN ĐẶT BÀN --}}
<div class="invoice-card">
    <h4>Thông tin đặt bàn</h4>

    <p>
        <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}<br>

        <strong>Ca:</strong>
        @if($reservation->shift == 'morning') Ca sáng (8–13h)
        @elseif($reservation->shift == 'afternoon') Ca trưa (13–18h)
        @else Ca tối (18–23h)
        @endif
        <br>

        <strong>Số người:</strong> {{ $reservation->num_people }}<br>
        <strong>Ghi chú:</strong> {{ $reservation->depsection ?: 'Không có' }}<br>

        <strong>Trạng thái:</strong>
        @switch($reservation->status)
            @case('pending') Chờ xác nhận @break
            @case('deposit_pending') Chờ đặt cọc @break
            @case('deposit_paid') Đặt thành công @break
            @case('serving') Đang phục vụ @break
            @case('completed') Hoàn tất @break
            @case('cancelled') Đã hủy @break
        @endswitch
        <br>

        @if($reservation->cancellation_reason)
            <strong>Lý do hủy:</strong> {{ $reservation->cancellation_reason }}
        @endif
    </p>
</div>

{{-- THÔNG TIN THANH TOÁN --}}
<div class="invoice-card">
    <h4>Thông tin thanh toán</h4>

    @php
        $tableDeposit = $reservation->getTableDeposit();
        $foodDeposit  = $reservation->getFoodDeposit();
    @endphp

    <p><strong>Tiền cọc bàn:</strong> {{ number_format($tableDeposit, 0, ',', '.') }} VND</p>

    @if($foodDeposit > 0)
        <p><strong>Tiền cọc món gọi trước:</strong> {{ number_format($foodDeposit, 0, ',', '.') }} VND</p>
    @endif

    <p><strong>Tổng tiền cọc:</strong>
        <span style="color: green; font-weight: 600;">
            {{ number_format($reservation->deposit ?? 0, 0, ',', '.') }} VND
        </span>
    </p>
</div>

{{-- BÀN ĐÃ GÁN --}}
<div class="invoice-card">
    <h4>Bàn đã gán ({{ $reservation->tables->count() }} bàn)</h4>

    @foreach($reservation->tables as $table)
        <span style="background:#28a745; color:white; padding:4px 8px; border-radius:4px; margin-right:6px;">
            {{ $table->name }}
        </span>
    @endforeach
</div>

{{-- MÓN ĂN --}}
<div class="invoice-card">
    <h4>Món ăn đã đặt</h4>

    @if($reservation->reservationItems->count())
    <table>
        <thead>
            <tr>
                <th>Món</th>
                <th width="70">SL</th>
                <th width="120">Đơn giá</th>
                <th width="120">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservation->reservationItems as $item)
                <tr>
                    <td>{{ $item->menu->name }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                    <td style="text-align:right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            @php
                $subtotal = $reservation->reservationItems->sum(fn($m) => $m->price * $m->quantity);
                $vat = $subtotal * 0.08;
                $beforeDiscount = $subtotal + $vat;
                $voucherDiscount = $reservation->voucher_discount ?? 0;
                $total = max(0, $beforeDiscount - $voucherDiscount);
            @endphp

            <tr><th colspan="3" style="text-align:right">Tạm tính:</th>
                <th style="text-align:right">{{ number_format($subtotal,0,',','.') }}đ</th></tr>

            <tr><th colspan="3" style="text-align:right">VAT 8%:</th>
                <th style="text-align:right">{{ number_format($vat,0,',','.') }}đ</th></tr>

            <tr><th colspan="3" style="text-align:right">Tổng tiền:</th>
                <th style="text-align:right">{{ number_format($beforeDiscount,0,',','.') }}đ</th></tr>

            @if($voucherDiscount > 0)
                <tr style="background:#d1ffd1">
                    <th colspan="3" style="text-align:right">Giảm giá voucher:</th>
                    <th style="text-align:right">-{{ number_format($voucherDiscount,0,',','.') }}đ</th>
                </tr>
            @endif

            <tr>
                <th colspan="3" style="text-align:right">Thành tiền:</th>
                <th style="text-align:right; color:red; font-weight:700;">
                    {{ number_format($total,0,',','.') }}đ
                </th>
            </tr>

        </tfoot>
    </table>
    @else
        <p class="text-muted">Chưa đặt món.</p>
    @endif
</div>

</body>
</html>
