@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid mt-3">

            ```
            <!-- ================= Thống kê nhanh ================= -->
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box bg-info p-3 rounded shadow-sm">
                        <div class="inner text-center">
                            <h3 class="fs-2 fw-bold">{{ $totalUsers }}</h3>
                            <p>Tổng User</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box bg-success p-3 rounded shadow-sm">
                        <div class="inner text-center">
                            <h3 class="fs-2 fw-bold">{{ $newUsersThisMonth }}</h3>
                            <p>User mới tháng này</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box bg-primary p-3 rounded shadow-sm">
                        <div class="inner text-center">
                            <h3 class="fs-2 fw-bold">{{ $usersWithReservation }}</h3>
                            <p>User từng đặt bàn</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= Top khách đặt bàn ================= -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">🏆 Top khách đặt bàn nhiều nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên</th>
                                    <th>SĐT</th>
                                    <th>Số lần đặt</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topBookingUsers as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                                        <td class="fw-bold">{{ $u->reservations_count }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $u->id }}">
                                                <i class="fas fa-eye">Xem Chi Tiết</i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= Top khách tiêu tiền ================= -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">💰 Top khách tiêu tiền nhiều nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên</th>
                                    <th>SĐT</th>
                                    <th>Tổng tiền đã tiêu</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topSpendingUsers as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                                        <td class="fw-bold text-danger">{{ number_format($u->total_spent) }} đ</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $u->id }}">
                                                <i class="fas fa-eye">Xem Chi Tiết</i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= Modal chi tiết user ================= -->
            @foreach ($topBookingUsers as $u)
                <div class="modal fade" id="detailModal{{ $u->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Chi tiết khách hàng: {{ $u->name }}
                                    <small class="text-muted">({{ $u->phone ?? 'Chưa cập nhật' }})</small>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @if ($u->reservations->count() > 0)
                                    @foreach ($u->reservations as $reservation)
                                        <div class="mb-3 p-3 border rounded">
                                            <h6 class="fw-bold">Bàn đã gán:</h6>
                                            <div class="mb-2">
                                                @foreach ($reservation->tables as $table)
                                                    <span class="badge bg-success">{{ $table->name }}</span>
                                                @endforeach
                                                <span class="text-muted">({{ $reservation->tables->count() }} bàn)</span>
                                            </div>

                                            <h6 class="fw-bold">Món ăn đã đặt:</h6>
                                            @if ($reservation->reservationItems->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-2">
                                                        <thead>
                                                            <tr>
                                                                <th>Món</th>
                                                                <th>SL</th>
                                                                <th>Đơn giá</th>
                                                                <th>Thành tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($reservation->reservationItems as $item)
                                                                <tr>
                                                                    <td>{{ $item->menu->name ?? 'N/A' }}</td>
                                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                                    <td class="text-end">{{ number_format($item->price) }}
                                                                        đ</td>
                                                                    <td class="text-end">
                                                                        {{ number_format($item->price * $item->quantity) }}
                                                                        đ</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            @php
                                                                $subtotal = $reservation->reservationItems->sum(
                                                                    fn($i) => $i->price * $i->quantity,
                                                                );
                                                                $vat = $subtotal * 0.1;
                                                                $total = $subtotal + $vat;
                                                            @endphp
                                                            <tr>
                                                                <th colspan="3" class="text-end">Tạm tính:</th>
                                                                <th class="text-end">{{ number_format($subtotal) }} đ</th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="3" class="text-end">VAT 10%:</th>
                                                                <th class="text-end">{{ number_format($vat) }} đ</th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="3" class="text-end">Tổng:</th>
                                                                <th class="text-end text-danger">
                                                                    {{ number_format($total) }} đ</th>
                                                            </tr>
                                                        </tfoot>

                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Chưa đặt món.</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">User chưa có đơn hàng nào.</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
        ```

    </div>
@endsection
