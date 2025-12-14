@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper p-4" style="background-color: #f8f9fa; min-height: 100vh;">



        <!-- Thông tin tài khoản -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <h3 class="fw-bold mb-3 text-primary"> Thông tin tài khoản</h3>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa có' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Vai trò:</strong> {{ ucfirst($user->role ?? 'user') }}</p>
                        <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Số điểm hiện tại:</strong> {{ $user->points ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn đặt bàn -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <h4 class="fw-bold mb-3 text-success"> Danh sách các lần đặt bàn</h4>

                @forelse($user->reservations as $reservation)
                    <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold mb-0 text-primary">Đơn đặt #{{ $reservation->id }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $reservation->reservation_date }}
                            </small>
                        </div>

                        <p class="mb-1"><strong>Số người:</strong> {{ $reservation->num_people }}</p>
                        <p class="mb-2"><strong>Khu vực:</strong> {{ $reservation->depsection ?? 'Không rõ' }}</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark mt-3"> Bàn đã đặt:</h6>
                                @if ($reservation->tables->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach ($reservation->tables as $table)
                                            <li>Bàn số {{ $table->id }}
                                                {{ $table->name ? '(' . $table->name . ')' : '' }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">Không có bàn được chọn.</p>
                                @endif
                            </div>


                          <h6 class="font-weight-bold">Món ăn đã đặt:</h6>

@if ($reservation->reservationItems->count() > 0)
    <div class="table-responsive mt-2">
        <table class="table table-bordered table-striped table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Món</th>
                    <th width="80">SL</th>
                    <th width="120">Đơn giá</th>
                    <th width="120">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($reservation->reservationItems as $index => $item)
                    @php
                        $subtotal = $item->price * $item->quantity;
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->menu->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                        <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Tạm tính:</td>
                    <td class="text-right font-weight-bold">{{ number_format($total, 0, ',', '.') }}đ</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">VAT 10%:</td>
                    <td class="text-right font-weight-bold">{{ number_format($total * 0.1, 0, ',', '.') }}đ</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Tổng:</td>
                    <td class="text-right font-weight-bold">{{ number_format($total * 1.1, 0, ',', '.') }}đ</td>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted">Người dùng chưa đặt món ăn nào.</p>
@endif

                            <h6 class="font-weight-bold">Món ăn đã đặt:</h6>

                            @if ($reservation->reservationItems->count() > 0)
                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Món</th>
                                                <th width="80">SL</th>
                                                <th width="120">Đơn giá</th>
                                                <th width="120">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach ($reservation->reservationItems as $index => $item)
                                                @php
                                                    $subtotal = $item->price * $item->quantity;
                                                    $total += $subtotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->menu->name ?? 'N/A' }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ
                                                    </td>
                                                    <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}đ
                                                    </td>
                                                </tr>
                                            @endforeach
                                        <tfoot class="bg-light">
                                            @php
                                                $subtotal = $reservation->reservationItems->sum(
                                                    fn($m) => $m->price * $m->quantity,
                                                );
                                                $vat = $subtotal * 0.08;
                                                $totalBeforeDiscount = $subtotal + $vat;
                                                $voucherDiscount = $reservation->voucher_discount ?? 0;
                                                $total = max($totalBeforeDiscount - $voucherDiscount, 0);
                                            @endphp
                                            <tr>
                                                <th colspan="4" class="text-end">Tạm tính:</th>
                                                <th class="text-end">{{ number_format($subtotal, 0, ',', '.') }}đ</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-end">VAT 8%:</th>
                                                <th class="text-end">{{ number_format($vat, 0, ',', '.') }}đ</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-end">Tổng tiền trước voucher:</th>
                                                <th class="text-end">
                                                    {{ number_format($totalBeforeDiscount, 0, ',', '.') }}đ</th>
                                            </tr>
                                            @if ($reservation->voucher_id && $voucherDiscount > 0)
                                                <tr class="bg-success text-white">
                                                    <th colspan="4" class="text-end">
                                                        <i class="fas fa-ticket-alt"></i> Giảm giá voucher
                                                        @if ($reservation->voucher)
                                                            ({{ $reservation->voucher->code }})
                                                        @endif
                                                        :
                                                    </th>
                                                    <th class="text-end">
                                                        -{{ number_format($voucherDiscount, 0, ',', '.') }}đ</th>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th colspan="4" class="text-end">Thành tiền:</th>
                                                <th class="text-end text-danger fw-bold">
                                                    {{ number_format($total, 0, ',', '.') }}đ</th>
                                            </tr>
                                        </tfoot>


                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Người dùng chưa đặt món ăn nào.</p>
                            @endif
>>>>>>> origin/develop-2





                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center mb-0">
                        Người dùng này chưa có đặt bàn nào.
                    </div>
                @endforelse
            </div>
        </div>
        <a href="{{ route('user.accounts') }}" class="btn btn-secondary mb-3 shadow-sm">
            ← Quay lại danh sách
        </a>
    </div>
@endsection
