


<h5>Chi tiết đơn hàng của {{ $user->name }}</h5>

@if($user->reservations->count() > 0)
    @foreach($user->reservations as $reservation)
        <div class="mb-3 p-3 border rounded">
            <h6 class="font-weight-bold">Bàn đã gán:</h6>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    @foreach ($reservation->tables as $table)
                        <span class="badge badge-success">{{ $table->name }}</span>
                    @endforeach
                    <span class="text-muted">({{ $reservation->tables->count() }} bàn)</span>
                </div>
            </div>

            <hr>

            <h6 class="font-weight-bold">Món ăn đã đặt:</h6>
            @if ($reservation->reservationItems->count() > 0)
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Món</th>
                            <th width="80">SL</th>
                            <th width="120">Đơn giá</th>
                            <th width="120">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservation->reservationItems as $item)
                            <tr>
                                <td>{{ $item->menu->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td class="text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        @php
                            $subtotal = $reservation->reservationItems->sum(fn($i) => $i->price * $i->quantity);
                            $vat = $subtotal * 0.1;
                            $total = $subtotal + $vat;
                        @endphp
                        <tr>
                            <th colspan="3" class="text-right">Tạm tính:</th>
                            <th class="text-right">{{ number_format($subtotal, 0, ',', '.') }}đ</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-right">VAT 10%:</th>
                            <th class="text-right">{{ number_format($vat, 0, ',', '.') }}đ</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-right">Tổng:</th>
                            <th class="text-right text-danger">{{ number_format($total, 0, ',', '.') }}đ</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p class="text-muted">Chưa đặt món.</p>
            @endif
            <small class="text-muted">Trạng thái: {{ ucfirst($reservation->status) }}</small>
        </div>
    @endforeach
@else
    <p class="text-muted">User chưa có đơn hàng nào.</p>
@endif
