@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Trang quản lý đặt bàn</h3>
                                <div>
                                    <button type="button" class="btn btn-warning mr-2" id="btnPendingRefund"
                                        data-toggle="modal" data-target="#pendingRefundModal">
                                        <i class="fas fa-money-bill-wave"></i> Đơn cần hoàn tiền
                                        @if (isset($pendingRefundCount) && $pendingRefundCount > 0)
                                            <span class="badge badge-danger">{{ $pendingRefundCount }}</span>
                                        @else
                                            <span class="badge badge-secondary">0</span>
                                        @endif
                                    </button>
                                    <a href="{{ route('admin.datBan.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tạo đơn mới
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div id="notify-alert" class="alert alert-dismissible fade show shadow-sm position-fixed"
                                    role="alert"
                                    style="
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 320px;
            max-width: 420px;
            border-radius: 10px;
            font-size: 14px;
            background-color: #e9f7ef;
            border: 1px solid #b7e4c7;
            color: #2d6a4f;
        ">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle mr-2" style="color:#40916c;font-size:18px;"></i>
                                        <div class="flex-grow-1">
                                            {{ session('success') }}
                                        </div>
                                        <button type="button" class="close ml-2" data-dismiss="alert"
                                            style="color:#2d6a4f">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                </div>

                                @if (str_contains(session('success'), 'hoàn tiền'))
                                    <script>
                                        (function() {
                                            function refreshPendingRefunds() {
                                                const modal = document.getElementById('pendingRefundModal');
                                                if (modal && (modal.classList.contains('show') || modal.style.display === 'block')) {
                                                    setTimeout(() => {
                                                        if (typeof loadPendingRefundsList === 'function') {
                                                            if (typeof isLoaded !== 'undefined') {
                                                                isLoaded = false;
                                                            }
                                                            loadPendingRefundsList();
                                                        }
                                                    }, 300);
                                                }
                                            }

                                            document.readyState === 'loading' ?
                                                document.addEventListener('DOMContentLoaded', refreshPendingRefunds) :
                                                refreshPendingRefunds();
                                        })();
                                    </script>
                                @endif
                            @endif


                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <form action="{{ route('admin.datBan.index') }}" method="GET" class="row g-3"
                                        id="filterFormDatBan">
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Ngày đặt</label>
                                            <input type="date" name="date" class="form-control"
                                                value="{{ request('date') }}"
                                                onchange="document.getElementById('filterFormDatBan').submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Ca</label>
                                            <select name="shift" class="form-control"
                                                onchange="document.getElementById('filterFormDatBan').submit()">
                                                <option value="">Tất cả ca</option>
                                                <option value="morning"
                                                    {{ request('shift') == 'morning' ? 'selected' : '' }}>Ca sáng (8-13h)
                                                </option>
                                                <option value="afternoon"
                                                    {{ request('shift') == 'afternoon' ? 'selected' : '' }}>Ca trưa (13-18h)
                                                </option>
                                                <option value="evening"
                                                    {{ request('shift') == 'evening' ? 'selected' : '' }}>Ca tối (18-23h)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Trạng thái</label>
                                            <select name="status" class="form-control"
                                                onchange="document.getElementById('filterFormDatBan').submit()">
                                                <option value="">Tất cả trạng thái</option>
                                                <option value="deposit_pending"
                                                    {{ request('status') == 'deposit_pending' ? 'selected' : '' }}>Chờ đặt
                                                    cọc</option>
                                                <option value="deposit_paid"
                                                    {{ request('status') == 'deposit_paid' ? 'selected' : '' }}>Đặt thành
                                                    công</option>
                                                <option value="serving"
                                                    {{ request('status') == 'serving' ? 'selected' : '' }}>Đang phục vụ
                                                </option>
                                                <option value="completed"
                                                    {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn tất
                                                </option>
                                                <option value="cancelled"
                                                    {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ request('phone') }}" placeholder="Nhập số điện thoại"
                                                onkeypress="if(event.key === 'Enter') { document.getElementById('filterFormDatBan').submit(); }">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Mã đơn</label>
                                            <div class="input-group">
                                                <input type="text" name="reservation_code" class="form-control"
                                                    value="{{ request('reservation_code') }}" placeholder="Nhập mã đơn"
                                                    onkeypress="if(event.key === 'Enter') { document.getElementById('filterFormDatBan').submit(); }">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">&nbsp;</label>
                                            <div>
                                                <a href="{{ route('admin.datBan.index') }}"
                                                    class="btn btn-secondary btn-block">
                                                    <i class="fas fa-redo"></i> Đặt lại
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Số điện thoại</th>
                                        <th>Ngày đặt</th>
                                        <th>Ca</th>
                                        <th>Bàn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tables as $index => $reservation)
                                        <tr>
                                            <td>{{ $tables->firstItem() + $index }}</td>
                                            <td>
                                                <strong
                                                    class="text-primary">{{ $reservation->reservation_code ?? '#' . $reservation->id }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $reservation->user->name }}</strong>
                                            </td>
                                            <td>
                                                {{ $reservation->user->phone ?? 'N/A' }}
                                                <br>
                                                @if ($reservation->phone_confirmed)
                                                    <span class="badge badge-success badge-sm">
                                                        <i class="fas fa-check-circle"></i> Đã xác nhận
                                                    </span>
                                                @else
                                                    <form
                                                        action="{{ route('admin.datBan.confirmPhone', $reservation->id) }}"
                                                        method="POST" style="display:inline; margin-top: 5px;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Xác nhận đã gọi điện">
                                                            <i class="fas fa-phone"></i> Xác nhận
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                @switch($reservation->shift)
                                                    @case('morning')
                                                        <span class="badge badge-info">Sáng<br><small>8-13h</small></span>
                                                    @break

                                                    @case('afternoon')
                                                        <span class="badge badge-warning">Trưa<br><small>13-18h</small></span>
                                                    @break

                                                    @case('evening')
                                                        <span class="badge badge-dark">Tối<br><small>18-23h</small></span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if ($reservation->tables->isEmpty())
                                                    <span class="badge badge-danger">Chưa có bàn</span>
                                                @else
                                                    @foreach ($reservation->tables as $table)
                                                        <span class="badge badge-success">{{ $table->name }}</span>
                                                    @endforeach
                                                    <br>
                                                    <small class="text-muted">({{ $reservation->tables->count() }}
                                                        bàn)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($reservation->status)
                                                    @case('pending')
                                                        <span class="badge badge-secondary">Chờ xác nhận</span>
                                                    @break

                                                    @case('deposit_pending')
                                                        <span class="badge badge-warning">Chờ đặt cọc</span>
                                                    @break

                                                    @case('deposit_paid')
                                                        <span class="badge badge-success">Đặt thành công</span>
                                                    @break

                                                    @case('serving')
                                                        <span class="badge badge-primary">Đang phục vụ</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge badge-info">Hoàn tất</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Đã hủy</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                    data-target="#detailModal{{ $reservation->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if ($reservation->status == 'deposit_paid')
                                                    <form action="{{ route('admin.datBan.updateStatus') }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id"
                                                            value="{{ $reservation->id }}">
                                                        <input type="hidden" name="status" value="serving">
                                                        <button type="submit" class="btn btn-primary btn-sm"
                                                            title="Bắt đầu phục vụ">
                                                            <i class="fas fa-concierge-bell"></i> Bắt đầu phục vụ
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($reservation->status == 'serving')
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#invoiceModal{{ $reservation->id }}">
                                                        <i class="fas fa-receipt"></i> Hoàn tất
                                                    </button>
                                                @endif

                                                @if ($reservation->status != 'cancelled' && $reservation->status != 'completed' && $reservation->status != 'serving')
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#cancelModal{{ $reservation->id }}">
                                                        <i class="fas fa-times"></i> Hủy
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Chưa có đơn đặt bàn nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        Hiển thị {{ $tables->firstItem() ?? 0 }} đến {{ $tables->lastItem() ?? 0 }}
                                        trong tổng số {{ $tables->total() }} kết quả
                                    </div>
                                    <div>
                                        {{ $tables->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($tables as $reservation)
            <div class="modal fade" id="detailModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-info-circle"></i> Chi tiết đơn đặt bàn {{ $reservation->reservation_code }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold">Thông tin khách hàng:</h6>
                                    <p>
                                        <strong>Tên:</strong> {{ $reservation->user->name }}<br>
                                        <strong>Email:</strong> {{ $reservation->user->email }}<br>
                                        <strong>Số điện thoại:</strong> {{ $reservation->user->phone ?? 'Chưa có' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold">Thông tin đặt bàn:</h6>
                                    <p>
                                        <strong>Ngày:</strong>
                                        {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}<br>
                                        <strong>Ca:</strong>
                                        @if ($reservation->shift == 'morning')
                                            Ca sáng (8-13h)
                                        @elseif($reservation->shift == 'afternoon')
                                            Ca trưa (13-18h)
                                        @else
                                            Ca tối (18-23h)
                                        @endif
                                        <br>
                                        <strong>Số người:</strong> {{ $reservation->num_people }} người<br>
                                        <strong>Ghi chú :</strong> {{ $reservation->depsection }}<br>
                                        <strong>Trạng thái:</strong>
                                        @switch($reservation->status)
                                            @case('pending')
                                                <span class="badge badge-secondary">Chờ xác nhận</span>
                                            @break

                                            @case('deposit_pending')
                                                <span class="badge badge-warning">Chờ đặt cọc</span>
                                            @break

                                            @case('deposit_paid')
                                                <span class="badge badge-success">Đặt thành công</span>
                                            @break

                                            @case('serving')
                                                <span class="badge badge-primary">Đang phục vụ</span>
                                            @break

                                            @case('completed')
                                                <span class="badge badge-info">Hoàn tất</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge badge-danger">Đã hủy</span>
                                            @break
                                        @endswitch
                                        <br>
                                        @if ($reservation->status == 'cancelled' && $reservation->cancellation_reason)
                                            @php
                                                $reasonParts = explode(
                                                    "\n\nSố tài khoản hoàn tiền: ",
                                                    $reservation->cancellation_reason,
                                                );
                                                $reason = $reasonParts[0];
                                                $accountNumber = isset($reasonParts[1]) ? trim($reasonParts[1]) : null;

                                                // Kiểm tra xem đơn có đủ điều kiện hoàn tiền không
                                                $isEligibleForRefund = false;
                                                if (
                                                    $reservation->deposit &&
                                                    $reservation->deposit > 0 &&
                                                    !$reservation->refunded_at
                                                ) {
                                                    $now = \Carbon\Carbon::now();
                                                    if (!$reservation->reservation_date->isPast()) {
                                                        // Lấy refund_days từ cấu hình chung (Settings), đảm bảo tối thiểu là 1
                                                        $refundDays = max(
                                                            1,
                                                            (int) \App\Models\Setting::getValue('refund_days', 1),
                                                        );
                                                        $daysUntilReservation = $now->diffInDays(
                                                            $reservation->reservation_date,
                                                            false,
                                                        );
                                                        $isEligibleForRefund = $daysUntilReservation >= $refundDays;
                                                    }
                                                }
                                            @endphp
                                            <strong>Lý do hủy đơn:</strong> {{ $reason }}<br>
                                            @if ($accountNumber && $isEligibleForRefund)
                                                <strong>Số tài khoản hoàn tiền:</strong>
                                                <span class="badge badge-info">{{ $accountNumber }}</span><br>
                                            @endif
                                        @endif
                                        @if ($reservation->refunded_at)
                                            <br>
                                            <strong>Trạng thái hoàn tiền:</strong>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Đã hoàn tiền
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <strong>Ngày hoàn:</strong>
                                                {{ \Carbon\Carbon::parse($reservation->refunded_at)->format('d/m/Y H:i') }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>



                            <hr>

                            <h6 class="font-weight-bold">Thông tin thanh toán:</h6>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    @php
                                        $tableDeposit = $reservation->getTableDeposit();
                                        $foodDeposit = $reservation->getFoodDeposit();
                                    @endphp
                                    <p class="mb-2">
                                        <strong>Tiền cọc bàn:</strong>
                                        <span class="text-primary">{{ number_format($tableDeposit, 0, ',', '.') }} VND</span>
                                    </p>
                                    @if ($foodDeposit > 0)
                                        <p class="mb-2">
                                            <strong>Tiền cọc món gọi trước:</strong>
                                            <span class="text-primary">{{ number_format($foodDeposit, 0, ',', '.') }}
                                                VND</span>
                                        </p>
                                    @endif
                                    <p class="mb-0">
                                        <strong>Tổng tiền cọc:</strong>
                                        <span
                                            class="text-success font-weight-bold">{{ number_format($reservation->deposit ?? 0, 0, ',', '.') }}
                                            VND</span>
                                    </p>
                                    @if ($reservation->status == 'cancelled' && $reservation->cancellation_reason)
                                        @php
                                            $reasonParts = explode(
                                                "\n\nSố tài khoản hoàn tiền: ",
                                                $reservation->cancellation_reason,
                                            );
                                            $accountNumber = isset($reasonParts[1]) ? trim($reasonParts[1]) : null;

                                            // Kiểm tra xem đơn có đủ điều kiện hoàn tiền không
                                            $isEligibleForRefund = false;
                                            if (
                                                $reservation->deposit &&
                                                $reservation->deposit > 0 &&
                                                !$reservation->refunded_at
                                            ) {
                                                $now = \Carbon\Carbon::now();
                                                if (!$reservation->reservation_date->isPast()) {
                                                    // Lấy refund_days từ cấu hình chung (Settings)
                                                    $refundDays = (int) \App\Models\Setting::getValue('refund_days', 1);
                                                    $daysUntilReservation = $now->diffInDays(
                                                        $reservation->reservation_date,
                                                        false,
                                                    );
                                                    $isEligibleForRefund = $daysUntilReservation >= $refundDays;
                                                }
                                            }
                                        @endphp
                                        @if ($accountNumber && $isEligibleForRefund)
                                            <hr>
                                            <p class="mb-2">
                                                <strong>Số tài khoản hoàn tiền:</strong>
                                                <span class="badge badge-info badge-lg">{{ $accountNumber }}</span>
                                            </p>
                                        @endif
                                    @endif
                                    @if ($reservation->refunded_at)
                                        <hr>
                                        <p class="mb-0">
                                            <strong>Trạng thái hoàn tiền:</strong>
                                            <span class="badge badge-success badge-lg">
                                                <i class="fas fa-check-circle"></i> Đã hoàn tiền
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <strong>Ngày hoàn:</strong>
                                                {{ \Carbon\Carbon::parse($reservation->refunded_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <h6 class="font-weight-bold">Bàn đã gán:</h6>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    @foreach ($reservation->tables as $table)
                                        <span class="badge badge-success badge-lg">{{ $table->name }}</span>
                                    @endforeach
                                    <span class="text-muted">({{ $reservation->tables->count() }} bàn)</span>
                                </div>
                                @if ($reservation->status != 'completed' && $reservation->status != 'cancelled')
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                        data-target="#editTablesModal{{ $reservation->id }}">
                                        <i class="fas fa-edit"></i> Chỉnh sửa bàn
                                    </button>
                                @endif
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
                                                <td class="text-right">
                                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        @php
                                            $subtotal = $reservation->reservationItems->sum(function ($m) {
                                                return $m->price * $m->quantity;
                                            });
                                            $vat = $subtotal * 0.08;
                                            $totalBeforeDiscount = $subtotal + $vat;
                                            $voucherDiscount = $reservation->voucher_discount ?? 0;
                                            $total = $totalBeforeDiscount - $voucherDiscount;
                                            if ($total < 0) {
                                                $total = 0;
                                            }
                                        @endphp
                                        <tr>
                                            <th colspan="3" class="text-right">Tạm tính:</th>
                                            <th class="text-right">
                                                {{ number_format($subtotal, 0, ',', '.') }}đ
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">VAT 8%:</th>
                                            <th class="text-right">
                                                {{ number_format($vat, 0, ',', '.') }}đ
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Tổng tiền:</th>
                                            <th class="text-right">
                                                {{ number_format($totalBeforeDiscount, 0, ',', '.') }}đ
                                            </th>
                                        </tr>
                                        @if ($reservation->voucher_id && $voucherDiscount > 0)
                                            <tr class="bg-success text-white">
                                                <th colspan="3" class="text-right">
                                                    <i class="fas fa-ticket-alt"></i> Giảm giá voucher
                                                    @if ($reservation->voucher)
                                                        ({{ $reservation->voucher->code }})
                                                    @endif
                                                    :
                                                </th>
                                                <th class="text-right">
                                                    -{{ number_format($voucherDiscount, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="3" class="text-right">Thành tiền:</th>
                                            <th class="text-right text-danger font-weight-bold">
                                                {{ number_format($total, 0, ',', '.') }}đ
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <p class="text-muted">Chưa đặt món.</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            @if ($reservation->reservation_code)
                                <a href="{{ route('invoice.pdf', $reservation->reservation_code) }}" class="btn btn-primary">
                                    In hóa đơn
                                </a>
                            @endif

                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Chỉnh sửa bàn -->
            @if ($reservation->status != 'completed' && $reservation->status != 'cancelled')
                <div class="modal fade" id="editTablesModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('admin.datBan.updateTables', $reservation->id) }}" method="POST"
                                id="editTablesForm{{ $reservation->id }}"
                                onsubmit="return validateTableSelection({{ $reservation->id }})">
                                @csrf
                                @method('PUT')
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit"></i> Chỉnh sửa bàn cho đơn
                                        #{{ $reservation->reservation_code }}
                                    </h5>
                                    <button type="button" class="close"
                                        onclick="closeEditTableModal({{ $reservation->id }})" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Số người: <strong>{{ $reservation->num_people }}</strong> |
                                        Ngày:
                                        <strong>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</strong>
                                        |
                                        Ca: <strong>
                                            @if ($reservation->shift == 'morning')
                                                Sáng (8-13h)
                                            @elseif($reservation->shift == 'afternoon')
                                                Trưa (13-18h)
                                            @else
                                                Tối (18-23h)
                                            @endif
                                        </strong>
                                    </div>

                                    <div id="errorMessage{{ $reservation->id }}" class="alert alert-danger"
                                        style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Vui lòng chọn ít nhất 1
                                            bàn!</strong>
                                    </div>

                                    @if (session('conflicting_tables') && session('open_edit_modal') == $reservation->id)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Cảnh báo:</strong> Các bàn sau đang được sử dụng bởi đơn khác:
                                            <ul class="mb-0 mt-2">
                                                @foreach (session('conflicting_tables') as $conflict)
                                                    <li>
                                                        <strong>Bàn {{ $conflict['table_name'] }}</strong> -
                                                        Đang phục vụ cho đơn {{ $conflict['conflicting_reservation_code'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="mb-0 mt-2"><strong>Vui lòng chọn bàn khác để tiếp tục!</strong></p>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="font-weight-bold">Chọn bàn:</label>
                                        <div class="border p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                            @php
                                                $currentTableIds = $reservation->tables->pluck('id')->toArray();
                                                $allTables = \App\Models\BanAn::all();
                                                $conflictingTableIds =
                                                    session('conflicting_tables') &&
                                                    session('open_edit_modal') == $reservation->id
                                                        ? collect(session('conflicting_tables'))
                                                            ->pluck('table_id')
                                                            ->toArray()
                                                        : [];
                                            @endphp

                                            @foreach ($allTables as $table)
                                                @php
                                                    // Check if table is busy in this shift/date (excluding current reservation)
                                                    // Kiểm tra các trạng thái: confirmed (đã xác nhận), deposit_paid (đã đặt cọc), serving (đang phục vụ)
                                                    $isBusy = $table
                                                        ->reservations()
                                                        ->where('reservation_date', $reservation->reservation_date)
                                                        ->where('shift', $reservation->shift)
                                                        ->whereIn('status', ['confirmed', 'deposit_paid', 'serving'])
                                                        ->where('reservations.id', '!=', $reservation->id)
                                                        ->exists();

                                                    // Kiểm tra bàn đang được phục vụ bởi đơn khác (bất kỳ ngày/ca nào)
                                                    // Vì đang phục vụ nghĩa là đang sử dụng bàn ngay bây giờ
                                                    $isBeingServed = $table
                                                        ->reservations()
                                                        ->where('status', 'serving')
                                                        ->where('reservations.id', '!=', $reservation->id)
                                                        ->exists();

                                                    // Check if this table is conflicting (being served by another reservation)
                                                    $isConflicting = in_array($table->id, $conflictingTableIds);
                                                @endphp

                                                <div
                                                    class="custom-control custom-checkbox mb-2 {{ $isConflicting ? 'border border-danger p-2 rounded bg-light' : '' }}">
                                                    <input type="checkbox"
                                                        class="custom-control-input table-checkbox-{{ $reservation->id }}"
                                                        id="table{{ $table->id }}_{{ $reservation->id }}"
                                                        name="table_ids[]" value="{{ $table->id }}"
                                                        {{ in_array($table->id, $currentTableIds) ? 'checked' : '' }}
                                                        {{ $isBusy || $isBeingServed || $isConflicting ? 'disabled' : '' }}>
                                                    <label
                                                        class="custom-control-label {{ $isConflicting || $isBeingServed ? 'text-danger font-weight-bold' : '' }}"
                                                        for="table{{ $table->id }}_{{ $reservation->id }}">
                                                        {{ $table->name }}
                                                        @if ($isConflicting || $isBeingServed)
                                                            <span class="badge badge-danger ml-2">Đang phục vụ</span>
                                                        @elseif($isBusy)
                                                            <span class="badge badge-danger badge-sm">Đang bận</span>
                                                        @elseif(in_array($table->id, $currentTableIds))
                                                            <span class="badge badge-success badge-sm">Đang chọn</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-sm">Rỗi</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb"></i> Bạn có thể chọn nhiều bàn. Bàn "Đang bận" không
                                            thể chọn.
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        onclick="closeEditTableModal({{ $reservation->id }})">Hủy</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($reservation->status == 'serving')
                <div class="modal fade" id="invoiceModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-invoice"></i> Hóa đơn thanh toán
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h6 class="font-weight-bold">Chi tiết món ăn:</h6>
                                @if ($reservation->reservationItems->count() > 0)
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Món ăn</th>
                                                <th width="100">Số lượng</th>
                                                <th width="120">Đơn giá</th>
                                                <th width="120">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reservation->reservationItems as $item)
                                                <tr>
                                                    <td>{{ $item->menu->name }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light">
                                            @php
                                                $subtotal = $reservation->reservationItems->sum(function ($item) {
                                                    return $item->price * $item->quantity;
                                                });
                                                $vat = $subtotal * 0.08;
                                                $totalBeforeDiscount = $subtotal + $vat;

                                                // Tính với voucher nếu có
                                                $voucherDiscount = $reservation->voucher_discount ?? 0;
                                                $totalAfterDiscount = max(0, $totalBeforeDiscount - $voucherDiscount);

                                                // Trừ cả cọc bàn và cọc đồ ăn ban đầu (nếu đã cọc)
                                                $tableDeposit = $reservation->getTableDeposit();
                                                $foodDeposit = $reservation->getFoodDeposit();
                                                $remainingAmount = $totalAfterDiscount - $tableDeposit - $foodDeposit;
                                            @endphp
                                            <tr>
                                                <th colspan="3" class="text-right">Tạm tính:</th>
                                                <th class="text-right">
                                                    {{ number_format($subtotal, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">VAT 8%:</th>
                                                <th class="text-right">
                                                    {{ number_format($vat, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">Tổng tiền:</th>
                                                <th class="text-right">
                                                    {{ number_format($totalBeforeDiscount, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            @if ($reservation->voucher && $voucherDiscount > 0)
                                                <tr id="voucherDiscountRow{{ $reservation->id }}">
                                                    <th colspan="3" class="text-right">
                                                        @php
                                                            $voucherDisplay = 'Voucher (';
                                                            if ($reservation->voucher->discount_type == 'percent') {
                                                                $voucherDisplay .=
                                                                    $reservation->voucher->discount_value . '%)';
                                                            } else {
                                                                $voucherDisplay .=
                                                                    number_format(
                                                                        $reservation->voucher->discount_value,
                                                                        0,
                                                                        ',',
                                                                        '.',
                                                                    ) . 'đ)';
                                                            }
                                                        @endphp
                                                        {{ $voucherDisplay }}:
                                                    </th>
                                                    <th class="text-right text-danger">
                                                        - {{ number_format($voucherDiscount, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                                <tr id="totalAfterDiscountRow{{ $reservation->id }}">
                                                    <th colspan="3" class="text-right">Tổng sau giảm giá:</th>
                                                    <th class="text-right">
                                                        {{ number_format($totalAfterDiscount, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th colspan="3" class="text-right">Tiền cọc bàn:</th>
                                                <th class="text-right text-success">
                                                    - {{ number_format($tableDeposit, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            @if ($foodDeposit > 0)
                                                <tr>
                                                    <th colspan="3" class="text-right">Tiền cọc đồ ăn:</th>
                                                    <th class="text-right text-success">
                                                        - {{ number_format($foodDeposit, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                            @endif
                                            @if ($remainingAmount > 0)
                                                <tr class="bg-warning">
                                                    <th colspan="3" class="text-right">Còn phải thanh toán:</th>
                                                    <th class="text-right">
                                                        <h5 class="mb-0" id="remainingAmount{{ $reservation->id }}">
                                                            {{ number_format($remainingAmount, 0, ',', '.') }}đ
                                                        </h5>
                                                    </th>
                                                </tr>
                                            @elseif($remainingAmount < 0)
                                                <tr class="bg-warning">
                                                    <th colspan="3" class="text-right">Hoàn lại cho khách:</th>
                                                    <th class="text-right">
                                                        <h5 class="mb-0">
                                                            {{ number_format(abs($remainingAmount), 0, ',', '.') }}đ</h5>
                                                    </th>
                                                </tr>
                                            @else
                                                <tr class="bg-warning">
                                                    <th colspan="3" class="text-right">Đã thanh toán đủ:</th>
                                                    <th class="text-right">
                                                        <h5 class="mb-0">0đ</h5>
                                                    </th>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                @else
                                    <p class="text-muted">Khách chưa đặt món</p>
                                @endif

                                <hr>

                                <!-- Phần Voucher -->
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white"
                                        @if ($reservation->voucher) style="display: none;" @endif>
                                        <h6 class="mb-0">
                                            <i class="fas fa-ticket-alt"></i> Voucher
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($reservation->voucher)
                                            <div class="alert alert-success">
                                                <strong>Voucher đã áp dụng:</strong> {{ $reservation->voucher->code }}<br>
                                                <strong>Giảm giá:</strong>
                                                @if ($reservation->voucher->discount_type == 'percent')
                                                    {{ $reservation->voucher->discount_value }}%
                                                @else
                                                    {{ number_format($reservation->voucher->discount_value, 0, ',', '.') }}đ
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger float-right"
                                                    onclick="removeVoucher({{ $reservation->id }})">
                                                    <i class="fas fa-times"></i> Hủy voucher
                                                </button>
                                            </div>
                                        @else
                                            @if ($reservation->reservationItems->count() > 0)
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="loadVouchers({{ $reservation->id }})">
                                                    <i class="fas fa-search"></i> Xem danh sách voucher
                                                </button>
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-info-circle"></i> Vui lòng thêm món ăn trước khi áp dụng
                                                    voucher.
                                                </div>
                                            @endif
                                        @endif

                                        <div id="voucherList{{ $reservation->id }}"
                                            style="display: none; margin-top: 15px;">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Đang tải...</span>
                                                </div>
                                                <p class="mt-2">Đang tải danh sách voucher...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                @if ($reservation->reservationItems->count() > 0)
                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Xác nhận hoàn tất
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-success" disabled title="Khách chưa đặt món nào">
                                        <i class="fas fa-check"></i> Xác nhận hoàn tất
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal Hủy Đơn -->
            @if ($reservation->status != 'cancelled' && $reservation->status != 'completed' && $reservation->status != 'serving')
                <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                <input type="hidden" name="status" value="cancelled">

                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle"></i> Xác nhận hủy đơn
                                        {{ $reservation->reservation_code ?? '#' . $reservation->id }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold">Thông tin khách hàng:</h6>
                                            <p>
                                                <strong>Tên:</strong> {{ $reservation->user->name }}<br>
                                                <strong>Email:</strong> {{ $reservation->user->email }}<br>
                                                <strong>Số điện thoại:</strong> {{ $reservation->user->phone ?? 'Chưa có' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold">Thông tin đặt bàn:</h6>
                                            <p>
                                                <strong>Ngày:</strong>
                                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}<br>
                                                <strong>Ca:</strong>
                                                @if ($reservation->shift == 'morning')
                                                    Ca sáng (8-13h)
                                                @elseif($reservation->shift == 'afternoon')
                                                    Ca trưa (13-18h)
                                                @else
                                                    Ca tối (18-23h)
                                                @endif
                                                <br>
                                                <strong>Số người:</strong> {{ $reservation->num_people }} người<br>
                                                <strong>Voucher:</strong>
                                                {{ $reservation->voucher->code ?? 'Không có voucher' }}<br>
                                                <strong>Ghi chú :</strong> {{ $reservation->depsection }}<br>
                                                <strong>Trạng thái:</strong>
                                                @switch($reservation->status)
                                                    @case('pending')
                                                        <span class="badge badge-secondary">Chờ xác nhận</span>
                                                    @break

                                                    @case('deposit_pending')
                                                        <span class="badge badge-warning">Chờ đặt cọc</span>
                                                    @break

                                                    @case('deposit_paid')
                                                        <span class="badge badge-success">Đặt thành công</span>
                                                    @break

                                                    @case('serving')
                                                        <span class="badge badge-primary">Đang phục vụ</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge badge-info">Hoàn tất</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Đã hủy</span>
                                                    @break
                                                @endswitch
                                                <br>
                                                @if ($reservation->status == 'cancelled' && $reservation->cancellation_reason)
                                                    <strong>Lý do hủy đơn:</strong>
                                                    {{ $reservation->cancellation_reason }}<br>
                                                @endif
                                            </p>
                                        </div>
                                    </div>



                                    <hr>

                                    <h6 class="font-weight-bold">Bàn đã gán:</h6>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            @foreach ($reservation->tables as $table)
                                                <span class="badge badge-success badge-lg">{{ $table->name }}</span>
                                            @endforeach
                                            <span class="text-muted">({{ $reservation->tables->count() }} bàn)</span>
                                        </div>
                                        @if ($reservation->status != 'completed' && $reservation->status != 'cancelled')
                                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                data-target="#editTablesModal{{ $reservation->id }}">
                                                <i class="fas fa-edit"></i> Chỉnh sửa bàn
                                            </button>
                                        @endif
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
                                                        <td class="text-right">
                                                            {{ number_format($item->price, 0, ',', '.') }}đ</td>
                                                        <td class="text-right">
                                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                @php
                                                    $subtotal = $reservation->reservationItems->sum(function ($m) {
                                                        return $m->price * $m->quantity;
                                                    });
                                                    $vat = $subtotal * 0.08;
                                                    $total = $subtotal + $vat;
                                                @endphp
                                                <tr>
                                                    <th colspan="3" class="text-right">Tạm tính:</th>
                                                    <th class="text-right">
                                                        {{ number_format($subtotal, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-right">VAT 8%:</th>
                                                    <th class="text-right">
                                                        {{ number_format($vat, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-right">Tổng:</th>
                                                    <th class="text-right text-danger">
                                                        {{ number_format($total, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @else
                                        <p class="text-muted">Chưa đặt món.</p>
                                    @endif

                                    <hr>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Lý do hủy <span class="text-danger">*</span></label>
                                        <textarea name="cancellation_reason" class="form-control" rows="4"
                                            placeholder="Vui lòng nhập lý do hủy đơn..." required></textarea>
                                    </div>

                                    @php
                                        // Kiểm tra xem đơn có thể được hoàn tiền không
                                        // Giống logic trong isEligibleForRefund() của DatBanController
                                        $canBeRefunded = false;
                                        
                                        // Phải có tiền cọc
                                        $hasDeposit = ($reservation->deposit && (float)$reservation->deposit > 0);
                                        
                                        if ($hasDeposit) {
                                            // Ngày đặt phải trong tương lai
                                            if (!$reservation->reservation_date->isPast()) {
                                                // Lấy refund_days từ cấu hình
                                                $refundDays = max(1, (int)\App\Models\Setting::getValue('refund_days', 1));
                                                
                                                // Tính số ngày còn lại đến ngày đặt
                                                $now = \Carbon\Carbon::now();
                                                $daysUntilReservation = (int)ceil($now->diffInDays($reservation->reservation_date, false));
                                                
                                                // Nếu hủy trước >= số ngày quy định thì được hoàn
                                                $canBeRefunded = $daysUntilReservation >= $refundDays;
                                            }
                                        }
                                    @endphp

                                    @if ($canBeRefunded)
                                        <div class="form-group">
                                            <label class="font-weight-bold">Ngân hàng <span
                                                    class="text-danger">*</span></label>
                                            <select name="refund_bank" class="form-control" required>
                                                <option value="">-- Chọn ngân hàng --</option>
                                                <option value="mbank" {{ old('refund_bank') == 'mbank' ? 'selected' : '' }}>
                                                    MBank</option>
                                                <option value="techcombank"
                                                    {{ old('refund_bank') == 'techcombank' ? 'selected' : '' }}>Techcombank
                                                </option>
                                                <option value="vietcombank"
                                                    {{ old('refund_bank') == 'vietcombank' ? 'selected' : '' }}>Vietcombank
                                                </option>
                                                <option value="bidv" {{ old('refund_bank') == 'bidv' ? 'selected' : '' }}>
                                                    BIDV</option>
                                                <option value="agribank"
                                                    {{ old('refund_bank') == 'agribank' ? 'selected' : '' }}>Agribank</option>
                                                <option value="vietinbank"
                                                    {{ old('refund_bank') == 'vietinbank' ? 'selected' : '' }}>VietinBank
                                                </option>
                                                <option value="acb" {{ old('refund_bank') == 'acb' ? 'selected' : '' }}>
                                                    ACB</option>
                                                <option value="vpbank"
                                                    {{ old('refund_bank') == 'vpbank' ? 'selected' : '' }}>VPBank</option>
                                                <option value="tpbank"
                                                    {{ old('refund_bank') == 'tpbank' ? 'selected' : '' }}>TPBank</option>
                                                <option value="shb" {{ old('refund_bank') == 'shb' ? 'selected' : '' }}>
                                                    SHB</option>
                                                <option value="hdbank"
                                                    {{ old('refund_bank') == 'hdbank' ? 'selected' : '' }}>HDBank</option>
                                                <option value="msb" {{ old('refund_bank') == 'msb' ? 'selected' : '' }}>
                                                    MSB</option>
                                                <option value="ocb" {{ old('refund_bank') == 'ocb' ? 'selected' : '' }}>
                                                    OCB</option>
                                                <option value="vib" {{ old('refund_bank') == 'vib' ? 'selected' : '' }}>
                                                    VIB</option>
                                                <option value="seabank"
                                                    {{ old('refund_bank') == 'seabank' ? 'selected' : '' }}>SeABank</option>
                                                <option value="eximbank"
                                                    {{ old('refund_bank') == 'eximbank' ? 'selected' : '' }}>Eximbank</option>
                                                <option value="scb" {{ old('refund_bank') == 'scb' ? 'selected' : '' }}>
                                                    SCB</option>
                                                <option value="vietabank"
                                                    {{ old('refund_bank') == 'vietabank' ? 'selected' : '' }}>VietABank
                                                </option>
                                                <option value="lienvietpostbank"
                                                    {{ old('refund_bank') == 'lienvietpostbank' ? 'selected' : '' }}>
                                                    LienVietPostBank</option>
                                                <option value="pvcombank"
                                                    {{ old('refund_bank') == 'pvcombank' ? 'selected' : '' }}>PVcomBank
                                                </option>
                                                <option value="publicbank"
                                                    {{ old('refund_bank') == 'publicbank' ? 'selected' : '' }}>PublicBank
                                                </option>
                                                <option value="saigonbank"
                                                    {{ old('refund_bank') == 'saigonbank' ? 'selected' : '' }}>SaigonBank
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Số tài khoản <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="refund_account_number" class="form-control"
                                                placeholder="Nhập số tài khoản" value="{{ old('refund_account_number') }}"
                                                maxlength="20" required>
                                            <small class="form-text text-muted">Vui lòng nhập số tài khoản của khách hàng để
                                                thực hiện hoàn tiền</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-ban"></i> Xác nhận hủy
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal Chỉnh sửa bàn -->
            @if ($reservation->status != 'completed' && $reservation->status != 'cancelled')
                <div class="modal fade" id="editTablesModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('admin.datBan.updateTables', $reservation->id) }}" method="POST"
                                id="editTablesForm{{ $reservation->id }}"
                                onsubmit="return validateTableSelection({{ $reservation->id }})">
                                @csrf
                                @method('PUT')
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit"></i> Chỉnh sửa bàn cho đơn
                                        #{{ $reservation->reservation_code }}
                                    </h5>
                                    <button type="button" class="close"
                                        onclick="closeEditTableModal({{ $reservation->id }})" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Số người: <strong>{{ $reservation->num_people }}</strong> |
                                        Ngày:
                                        <strong>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</strong>
                                        |
                                        Ca: <strong>
                                            @if ($reservation->shift == 'morning')
                                                Sáng (8-13h)
                                            @elseif($reservation->shift == 'afternoon')
                                                Trưa (13-18h)
                                            @else
                                                Tối (18-23h)
                                            @endif
                                        </strong>
                                    </div>

                                    <div id="errorMessage{{ $reservation->id }}" class="alert alert-danger"
                                        style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Vui lòng chọn ít nhất 1
                                            bàn!</strong>
                                    </div>

                                    @if (session('conflicting_tables') && session('open_edit_modal') == $reservation->id)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Cảnh báo:</strong> Các bàn sau đang được sử dụng bởi đơn khác:
                                            <ul class="mb-0 mt-2">
                                                @foreach (session('conflicting_tables') as $conflict)
                                                    <li>
                                                        <strong>Bàn {{ $conflict['table_name'] }}</strong> -
                                                        Đang phục vụ cho đơn {{ $conflict['conflicting_reservation_code'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="mb-0 mt-2"><strong>Vui lòng chọn bàn khác để tiếp tục!</strong></p>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="font-weight-bold">Chọn bàn:</label>
                                        <div class="border p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                            @php
                                                $currentTableIds = $reservation->tables->pluck('id')->toArray();
                                                $allTables = \App\Models\BanAn::all();
                                                $conflictingTableIds =
                                                    session('conflicting_tables') &&
                                                    session('open_edit_modal') == $reservation->id
                                                        ? collect(session('conflicting_tables'))
                                                            ->pluck('table_id')
                                                            ->toArray()
                                                        : [];
                                            @endphp

                                            @foreach ($allTables as $table)
                                                @php
                                                    // Check if table is busy in this shift/date (excluding current reservation)
                                                    // Kiểm tra các trạng thái: confirmed (đã xác nhận), deposit_paid (đã đặt cọc), serving (đang phục vụ)
                                                    $isBusy = $table
                                                        ->reservations()
                                                        ->where('reservation_date', $reservation->reservation_date)
                                                        ->where('shift', $reservation->shift)
                                                        ->whereIn('status', ['confirmed', 'deposit_paid', 'serving'])
                                                        ->where('reservations.id', '!=', $reservation->id)
                                                        ->exists();

                                                    // Kiểm tra bàn đang được phục vụ bởi đơn khác (bất kỳ ngày/ca nào)
                                                    // Vì đang phục vụ nghĩa là đang sử dụng bàn ngay bây giờ
                                                    $isBeingServed = $table
                                                        ->reservations()
                                                        ->where('status', 'serving')
                                                        ->where('reservations.id', '!=', $reservation->id)
                                                        ->exists();

                                                    // Check if this table is conflicting (being served by another reservation)
                                                    $isConflicting = in_array($table->id, $conflictingTableIds);
                                                @endphp

                                                <div
                                                    class="custom-control custom-checkbox mb-2 {{ $isConflicting || $isBeingServed ? 'border border-danger p-2 rounded bg-light' : '' }}">
                                                    <input type="checkbox"
                                                        class="custom-control-input table-checkbox-{{ $reservation->id }}"
                                                        id="table{{ $table->id }}_{{ $reservation->id }}"
                                                        name="table_ids[]" value="{{ $table->id }}"
                                                        {{ in_array($table->id, $currentTableIds) ? 'checked' : '' }}
                                                        {{ $isBusy || $isBeingServed || $isConflicting ? 'disabled' : '' }}>
                                                    <label
                                                        class="custom-control-label {{ $isConflicting || $isBeingServed ? 'text-danger font-weight-bold' : '' }}"
                                                        for="table{{ $table->id }}_{{ $reservation->id }}">
                                                        {{ $table->name }}
                                                        @if ($isConflicting || $isBeingServed)
                                                            <span class="badge badge-danger ml-2">Đang phục vụ</span>
                                                        @elseif ($isBusy)
                                                            <span class="badge badge-danger badge-sm">Đang bận</span>
                                                        @elseif(in_array($table->id, $currentTableIds))
                                                            <span class="badge badge-success badge-sm">Đang chọn</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-sm">Rỗi</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb"></i> Bạn có thể chọn nhiều bàn. Bàn "Đang bận" không
                                            thể chọn.
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        onclick="closeEditTableModal({{ $reservation->id }})">Hủy</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($reservation->status == 'serving')
                <div class="modal fade" id="invoiceModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-invoice"></i> Hóa đơn thanh toán
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h6 class="font-weight-bold">Chi tiết món ăn:</h6>
                                @if ($reservation->reservationItems->count() > 0)
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Món ăn</th>
                                                <th width="100">Số lượng</th>
                                                <th width="120">Đơn giá</th>
                                                <th width="120">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reservation->reservationItems as $item)
                                                <tr>
                                                    <td>{{ $item->menu->name }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light">
                                            @php
                                                $subtotal = $reservation->reservationItems->sum(function ($item) {
                                                    return $item->price * $item->quantity;
                                                });
                                                $vat = $subtotal * 0.08;
                                                $totalMenuPrice = $subtotal + $vat; // Tổng tiền đã có VAT
                                                $depositPaid = $reservation->deposit ?? 0;
                                                $remainingAmount = $totalMenuPrice - $depositPaid;
                                            @endphp
                                            <tr>
                                                <th colspan="3" class="text-right">Tạm tính:</th>
                                                <th class="text-right">
                                                    {{ number_format($subtotal, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">VAT 8%:</th>
                                                <th class="text-right">
                                                    {{ number_format($vat, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">Tổng tiền:</th>
                                                <th class="text-right">
                                                    {{ number_format($totalMenuPrice, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-right">Tiền đặt cọc đã trả:</th>
                                                <th class="text-right text-success">
                                                    - {{ number_format($depositPaid, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr class="bg-warning">
                                                <th colspan="3" class="text-right">Còn phải thanh toán:</th>
                                                <th class="text-right text-danger">
                                                    <h5 class="mb-0">{{ number_format($remainingAmount, 0, ',', '.') }}đ
                                                    </h5>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                @else
                                    <p class="text-muted">Khách chưa đặt món</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                <form action="{{ route('admin.datBan.updateStatus') }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Xác nhận hoàn tất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal Hủy Đơn -->
            @if ($reservation->status != 'cancelled' && $reservation->status != 'completed' && $reservation->status != 'serving')
                <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                <input type="hidden" name="status" value="cancelled">

                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle"></i> Xác nhận hủy đơn
                                        {{ $reservation->reservation_code ?? '#' . $reservation->id }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <p class="font-weight-bold">Bạn có chắc chắn muốn hủy đơn đặt bàn này?</p>
                                    <p class="text-muted">
                                        <strong>Khách hàng:</strong> {{ $reservation->user->name }}<br>
                                        <strong>Ngày:</strong>
                                        {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}<br>
                                        <strong>Ca:</strong>
                                        @if ($reservation->shift == 'morning')
                                            Sáng (6-10h)
                                        @elseif($reservation->shift == 'afternoon')
                                            Trưa (10-14h)
                                        @elseif($reservation->shift == 'evening')
                                            Chiều (14-18h)
                                        @else
                                            Tối (18-22h)
                                        @endif
                                    </p>

                                    <hr>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Lý do hủy <span class="text-danger">*</span></label>
                                        <textarea name="cancellation_reason" class="form-control" rows="4"
                                            placeholder="Vui lòng nhập lý do hủy đơn..." required></textarea>
                                    </div>

                                    @php
                                        // Kiểm tra xem đơn có thể được hoàn tiền không
                                        // Giống logic trong isEligibleForRefund() của DatBanController
                                        $canBeRefunded = false;
                                        
                                        // Phải có tiền cọc
                                        $hasDeposit = ($reservation->deposit && (float)$reservation->deposit > 0);
                                        
                                        if ($hasDeposit) {
                                            // Ngày đặt phải trong tương lai
                                            if (!$reservation->reservation_date->isPast()) {
                                                // Lấy refund_days từ cấu hình
                                                $refundDays = max(1, (int)\App\Models\Setting::getValue('refund_days', 1));
                                                
                                                // Tính số ngày còn lại đến ngày đặt
                                                $now = \Carbon\Carbon::now();
                                                $daysUntilReservation = (int)ceil($now->diffInDays($reservation->reservation_date, false));
                                                
                                                // Nếu hủy trước >= số ngày quy định thì được hoàn
                                                $canBeRefunded = $daysUntilReservation >= $refundDays;
                                            }
                                        }
                                    @endphp

                                    @if ($canBeRefunded)
                                        <div class="form-group">
                                            <label class="font-weight-bold">Ngân hàng <span
                                                    class="text-danger">*</span></label>
                                            <select name="refund_bank" class="form-control" required>
                                                <option value="">-- Chọn ngân hàng --</option>
                                                <option value="mbank" {{ old('refund_bank') == 'mbank' ? 'selected' : '' }}>
                                                    MBank</option>
                                                <option value="techcombank"
                                                    {{ old('refund_bank') == 'techcombank' ? 'selected' : '' }}>Techcombank
                                                </option>
                                                <option value="vietcombank"
                                                    {{ old('refund_bank') == 'vietcombank' ? 'selected' : '' }}>Vietcombank
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Số tài khoản <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="refund_account_number" class="form-control"
                                                placeholder="Nhập số tài khoản" value="{{ old('refund_account_number') }}"
                                                maxlength="20" required>
                                            <small class="form-text text-muted">Vui lòng nhập số tài khoản của khách hàng để
                                                thực hiện hoàn tiền (nếu đủ điều kiện)</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Đóng
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-ban"></i> Xác nhận hủy
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Modal Đơn cần hoàn tiền -->
        <div class="modal fade" id="pendingRefundModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-money-bill-wave"></i> Danh sách đơn cần hoàn tiền
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="pendingRefundList">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xác nhận hoàn tiền -->
        <div class="modal fade" id="confirmRefundModal" tabindex="-1" role="dialog"
            aria-labelledby="confirmRefundModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="confirmRefundModalLabel">
                            <i class="fas fa-exclamation-triangle"></i> Xác nhận hoàn tiền
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="closeConfirmRefundModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmRefundMessage"></p>
                        <hr>
                        <form id="confirmRefundForm" method="POST" enctype="multipart/form-data"
                            onsubmit="return validateRefundForm(event)">
                            @csrf
                            <div class="form-group">
                                <label for="refundBillImage" class="font-weight-bold">
                                    <i class="fas fa-file-upload"></i> Upload bill hoàn tiền <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control-file" id="refundBillImage"
                                    name="refund_bill_image" accept="image/*,application/pdf" required>
                                <small class="form-text text-muted">Vui lòng upload ảnh bill để xác nhận hoàn tiền (JPG, PNG,
                                    GIF, PDF)</small>
                                <div id="refundBillImagePreview" class="mt-2" style="display: none;">
                                    <img id="refundBillImagePreviewImg" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 300px; max-height: 300px; display: none;">
                                    <div id="refundBillPdfPreview" style="display: none;">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        <p class="mt-2"><strong id="refundBillFileName"></strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top: none; padding-top: 0; margin-top: 20px;">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                    onclick="closeConfirmRefundModal()">
                                    <i class="fas fa-times"></i> Hủy
                                </button>
                                <button type="submit" class="btn btn-success" id="confirmRefundSubmitBtn" disabled>
                                    <i class="fas fa-check"></i> Xác nhận
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Code độc lập để load danh sách đơn cần hoàn tiền
            // Đặt ở đây để chạy ngay cả khi có lỗi từ các file khác
            (function() {
                'use strict';

                var isLoaded = false;

                // Đợi DOM sẵn sàng
                function initPendingRefundModal() {
                    var modal = document.getElementById('pendingRefundModal');
                    if (!modal) {
                        console.error('Modal pendingRefundModal not found');
                        return;
                    }

                    // Thử nhiều cách để detect khi modal mở
                    // Cách 1: Bootstrap 4 event với jQuery (nếu có)
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                        try {
                            jQuery('#pendingRefundModal').on('show.bs.modal', function() {
                                console.log('Modal opening detected via jQuery');
                                isLoaded = false; // Reset flag để load lại
                                loadPendingRefundsList();
                            });
                            jQuery('#pendingRefundModal').on('hidden.bs.modal', function() {
                                isLoaded = false; // Reset flag khi đóng modal
                            });
                        } catch (e) {
                            console.error('jQuery event failed:', e);
                        }
                    }

                    // Cách 2: MutationObserver để detect khi modal hiển thị
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                var target = mutation.target;
                                if (target.id === 'pendingRefundModal' && target.classList.contains(
                                        'show') && !target.classList.contains('hide')) {
                                    if (!isLoaded) {
                                        console.log('Modal opening detected via MutationObserver');
                                        isLoaded = true;
                                        loadPendingRefundsList();
                                    }
                                } else if (target.id === 'pendingRefundModal' && !target.classList.contains(
                                        'show')) {
                                    isLoaded = false;
                                }
                            }
                        });
                    });

                    observer.observe(modal, {
                        attributes: true,
                        attributeFilter: ['class']
                    });

                    // Cách 3: Click event trên button mở modal
                    var openButton = document.querySelector('[data-target="#pendingRefundModal"]');
                    if (openButton) {
                        openButton.addEventListener('click', function() {
                            setTimeout(function() {
                                console.log('Modal opening detected via button click');
                                loadPendingRefundsList();
                            }, 300);
                        });
                    }
                }

                function loadPendingRefundsList() {
                    console.log('Loading pending refunds list...');
                    var listDiv = document.getElementById('pendingRefundList');
                    if (!listDiv) {
                        console.error('Element pendingRefundList not found');
                        return;
                    }

                    // Reset flag để force reload
                    isLoaded = false;

                    listDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

                    var url = '{{ route('admin.datBan.getPendingRefunds') }}';
                    console.log('Fetching URL:', url);

                    // Sử dụng fetch API thay vì jQuery để tránh conflict
                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                        .then(function(response) {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            console.log('Response data:', data);
                            renderPendingRefundsList(data, listDiv);
                        })
                        .catch(function(error) {
                            console.error('Error loading pending refunds:', error);
                            listDiv.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải danh sách: ' +
                                error.message + '<br><small>Vui lòng mở Console (F12) để xem chi tiết.</small></div>';
                        });
                }

                function renderPendingRefundsList(response, container) {
                    var html = '';

                    if (!response || !response.success) {
                        html = '<div class="alert alert-warning">Không thể tải dữ liệu. Vui lòng thử lại.</div>';
                    } else if (!response.data || response.data.length === 0) {
                        html =
                            '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Không có đơn nào cần hoàn tiền.</div>';
                    } else {
                        html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
                        html += '<thead><tr>';
                        html += '<th>STT</th>';
                        html += '<th>Mã đơn</th>';
                        html += '<th>Khách hàng</th>';
                        html += '<th>Số điện thoại</th>';
                        html += '<th>Ngày đặt</th>';
                        html += '<th>Ca</th>';
                        html += '<th>Tiền cọc</th>';
                        html += '<th>Tài Khoản</th>';
                        html += '<th>Thao tác</th>';
                        html += '</tr></thead><tbody>';

                        response.data.forEach(function(item, index) {
                            html += '<tr>';
                            html += '<td>' + (index + 1) + '</td>';
                            html += '<td><strong>' + (item.reservation_code || 'N/A') + '</strong></td>';
                            html += '<td>' + (item.user_name || 'N/A') + '</td>';
                            html += '<td>' + (item.user_phone || 'N/A') + '</td>';
                            html += '<td>' + (item.reservation_date || 'N/A') + '</td>';
                            html += '<td>';
                            if (item.shift === 'morning') {
                                html += '<span class="badge badge-info">Sáng</span>';
                            } else if (item.shift === 'afternoon') {
                                html += '<span class="badge badge-warning">Trưa</span>';
                            } else if (item.shift === 'evening') {
                                html += '<span class="badge badge-dark">Tối</span>';
                            } else {
                                html += '<span class="badge badge-secondary">' + (item.shift || 'N/A') + '</span>';
                            }
                            html += '</td>';
                            html += '<td><strong class="text-primary">' + formatNumber(item.deposit || 0) +
                                ' VND</strong></td>';
                            html += '<td>';
                            if (item.refund_account_number && item.refund_account_number !== 'Chưa có') {
                                html += '<span class="badge badge-info">' + item.refund_account_number + '</span>';
                            } else {
                                html += '<span class="text-muted">Chưa có</span>';
                            }
                            html += '</td>';
                            html += '<td>';
                            var reservationCode = item.reservation_code || '#' + item.id;
                            html +=
                                '<button type="button" class="btn btn-sm btn-success" onclick="showConfirmRefundModal(' +
                                item.id + ', \'' + reservationCode + '\')">';
                            html += '<i class="fas fa-check"></i> Đã hoàn tiền';
                            html += '</button>';
                            html += '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                    }

                    container.innerHTML = html;
                }

                function formatNumber(number) {
                    if (typeof number === 'undefined' || number === null) {
                        return '0';
                    }
                    return new Intl.NumberFormat('vi-VN').format(Math.round(number));
                }

                // Khởi tạo khi DOM sẵn sàng
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initPendingRefundModal);
                } else {
                    initPendingRefundModal();
                }
            })();

            // Hàm hiển thị modal xác nhận hoàn tiền
            function showConfirmRefundModal(reservationId, reservationCode) {
                var message = 'Bạn có chắc muốn đánh dấu đã hoàn tiền cho đơn <strong>' + reservationCode + '</strong>?';
                document.getElementById('confirmRefundMessage').innerHTML = message;
                document.getElementById('confirmRefundForm').action = '/admin/dat-ban/' + reservationId + '/process-refund';

                // Sử dụng jQuery nếu có, nếu không dùng vanilla JS
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#confirmRefundModal').modal('show');
                } else {
                    var modal = document.getElementById('confirmRefundModal');
                    if (modal) {
                        modal.style.display = 'block';
                        modal.classList.add('show');
                        document.body.classList.add('modal-open');
                        var backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.id = 'confirmRefundModalBackdrop';
                        document.body.appendChild(backdrop);
                    }
                }
            }

            // Hàm đóng modal xác nhận hoàn tiền
            function closeConfirmRefundModal() {
                // Reset form
                var form = document.getElementById('confirmRefundForm');
                if (form) {
                    form.reset();
                }
                var preview = document.getElementById('refundBillImagePreview');
                if (preview) {
                    preview.style.display = 'none';
                }
                var previewImg = document.getElementById('refundBillImagePreviewImg');
                if (previewImg) {
                    previewImg.style.display = 'none';
                }
                var pdfPreview = document.getElementById('refundBillPdfPreview');
                if (pdfPreview) {
                    pdfPreview.style.display = 'none';
                }
                var submitBtn = document.getElementById('confirmRefundSubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                // Sử dụng jQuery nếu có, nếu không dùng vanilla JS
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#confirmRefundModal').modal('hide');
                } else {
                    var modal = document.getElementById('confirmRefundModal');
                    if (modal) {
                        modal.style.display = 'none';
                        modal.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        var backdrop = document.getElementById('confirmRefundModalBackdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                    }
                }
            }

            // Validate form trước khi submit
            function validateRefundForm(event) {
                console.log('validateRefundForm called');

                var fileInput = document.getElementById('refundBillImage');
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    event.preventDefault();
                    alert('Vui lòng upload ảnh hoặc PDF bill hoàn tiền trước khi xác nhận!');
                    return false;
                }

                // Kiểm tra kích thước file (tối đa 5MB)
                var file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    event.preventDefault();
                    alert('Kích thước file không được vượt quá 5MB!');
                    return false;
                }

                // Kiểm tra định dạng file
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    event.preventDefault();
                    alert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF) hoặc PDF!');
                    return false;
                }

                // Kiểm tra form action
                var form = document.getElementById('confirmRefundForm');
                if (form) {
                    console.log('Form action:', form.action);
                    console.log('Form method:', form.method);
                    console.log('File selected:', file.name, file.size, file.type);
                }

                // Disable nút submit để tránh double submit
                var submitBtn = document.getElementById('confirmRefundSubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
                }

                // Form sẽ submit và redirect, không cần đóng modal ở đây
                // Trang sẽ được reload sau khi redirect
                console.log('Form validation passed, submitting...');
                return true;
            }

            // Preview ảnh/PDF khi chọn file và enable/disable nút submit
            document.addEventListener('DOMContentLoaded', function() {
                var fileInput = document.getElementById('refundBillImage');
                var preview = document.getElementById('refundBillImagePreview');
                var previewImg = document.getElementById('refundBillImagePreviewImg');
                var pdfPreview = document.getElementById('refundBillPdfPreview');
                var fileName = document.getElementById('refundBillFileName');
                var submitBtn = document.getElementById('confirmRefundSubmitBtn');

                if (fileInput && preview && previewImg && submitBtn) {
                    fileInput.addEventListener('change', function(e) {
                        var file = e.target.files[0];
                        if (file) {
                            // Kiểm tra kích thước
                            if (file.size > 5 * 1024 * 1024) {
                                alert('Kích thước file không được vượt quá 5MB!');
                                e.target.value = '';
                                preview.style.display = 'none';
                                previewImg.style.display = 'none';
                                if (pdfPreview) pdfPreview.style.display = 'none';
                                submitBtn.disabled = true;
                                return;
                            }

                            // Kiểm tra định dạng
                            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                                'application/pdf'
                            ];
                            if (!allowedTypes.includes(file.type)) {
                                alert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF) hoặc PDF!');
                                e.target.value = '';
                                preview.style.display = 'none';
                                previewImg.style.display = 'none';
                                if (pdfPreview) pdfPreview.style.display = 'none';
                                submitBtn.disabled = true;
                                return;
                            }

                            // Hiển thị preview
                            preview.style.display = 'block';

                            if (file.type === 'application/pdf') {
                                // Hiển thị preview cho PDF
                                previewImg.style.display = 'none';
                                if (pdfPreview) {
                                    pdfPreview.style.display = 'block';
                                    if (fileName) {
                                        fileName.textContent = file.name;
                                    }
                                }
                            } else {
                                // Hiển thị preview cho ảnh
                                if (pdfPreview) pdfPreview.style.display = 'none';
                                previewImg.style.display = 'block';
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    previewImg.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }

                            submitBtn.disabled = false;
                        } else {
                            preview.style.display = 'none';
                            previewImg.style.display = 'none';
                            if (pdfPreview) pdfPreview.style.display = 'none';
                            submitBtn.disabled = true;
                        }
                    });
                }

                // Đóng modal khi click vào backdrop
                var modal = document.getElementById('confirmRefundModal');
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeConfirmRefundModal();
                        }
                    });
                }
            });
        </script>
    @endsection

    @push('scripts')
        <script>
            // Hàm đóng modal chỉnh sửa bàn
            function closeEditTableModal(reservationId) {
                $('#editTablesModal' + reservationId).modal('hide');
                // Xóa backdrop nếu còn sót lại
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }

            // Tự động mở modal chỉnh sửa bàn nếu có bàn trùng
            @if (session('open_edit_modal'))
                $(document).ready(function() {
                    $('#editTablesModal{{ session('open_edit_modal') }}').modal('show');
                });
            @endif

            function validateTableSelection(reservationId) {
                // Đếm số checkbox được chọn
                var checkedCount = document.querySelectorAll('.table-checkbox-' + reservationId + ':checked').length;
                var errorMessage = document.getElementById('errorMessage' + reservationId);

                if (checkedCount === 0) {
                    // Hiển thị thông báo lỗi
                    errorMessage.style.display = 'block';

                    // Cuộn lên đầu modal để người dùng thấy thông báo
                    errorMessage.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    return false; // Ngăn form submit
                }

                // Ẩn thông báo lỗi nếu đã chọn bàn
                errorMessage.style.display = 'none';
                return true; // Cho phép form submit
            }

            // Voucher helper functions
            window.formatDate = function(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN');
            }

            window.getMaxUses = function(voucher) {
                if (!voucher || voucher.max_uses === null || voucher.max_uses === undefined) {
                    return 1;
                }
                const maxUses = parseInt(voucher.max_uses, 10);
                return isNaN(maxUses) || maxUses <= 0 ? 1 : maxUses;
            }

            window.getUsedCount = function(voucher) {
                if (!voucher || voucher.used_count === null || voucher.used_count === undefined) {
                    return 0;
                }
                const usedCount = parseInt(voucher.used_count, 10);
                return isNaN(usedCount) || usedCount < 0 ? 0 : usedCount;
            }

            window.getRemainingUses = function(voucher) {
                const maxUses = window.getMaxUses(voucher);
                const usedCount = window.getUsedCount(voucher);
                const remaining = maxUses - usedCount;
                return remaining < 0 ? 0 : remaining;
            }

            // Voucher functions - Đảm bảo hàm được định nghĩa trong scope global
            window.loadVouchers = function(reservationId) {
                const voucherListDiv = document.getElementById('voucherList' + reservationId);
                if (!voucherListDiv) {
                    console.error('Không tìm thấy element voucherList' + reservationId);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    return;
                }

                voucherListDiv.style.display = 'block';
                voucherListDiv.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang tải danh sách voucher...</p>
                    </div>
                `;

                const url = '{{ url('admin/dat-ban') }}/' + reservationId + '/applicable-vouchers';
                console.log('Loading vouchers from:', url);

                fetch(url)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Network response was not ok: ' + response
                                    .status);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Voucher data:', data);
                        if (data.success) {
                            displayVouchers(reservationId, data.data);
                        } else {
                            voucherListDiv.innerHTML =
                                '<div class="alert alert-danger">Không thể tải danh sách voucher: ' + (data.message ||
                                    'Lỗi không xác định') + '</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading vouchers:', error);
                        voucherListDiv.innerHTML = '<div class="alert alert-danger">' + error.message + '</div>';
                    });
            }

            function displayVouchers(reservationId, data) {
                const voucherListDiv = document.getElementById('voucherList' + reservationId);
                let html = '';

                if (data.can_apply && data.can_apply.length > 0) {
                    html += '<div class="list-group mb-3">';
                    data.can_apply.forEach(voucher => {
                        html += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="flex: 1;">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="text-primary">${voucher.code}</strong>
                                            ${voucher.end_date ? '<small class="text-muted ml-2">(Hết hạn: ' + window.formatDate(voucher.end_date) + ')</small>' : ''}
                                        </div>
                                        <div class="mt-2">
                                            <div class="mb-1">
                                                <strong>Giảm giá:</strong>
                                                ${voucher.discount_type === 'percent' ? voucher.discount_value + '%' : window.number_format(voucher.discount_value) + 'đ'}
                                                ${voucher.max_discount_value ? ' (Tối đa: ' + window.number_format(voucher.max_discount_value) + 'đ)' : ''}
                                            </div>
                                            <div class="text-muted small">
                                                <div><i class="fas fa-check-circle text-success"></i> <strong>Điều kiện:</strong></div>
                                                <ul class="mb-1 pl-3">
                                                    ${voucher.min_order_value ? '<li>Đơn hàng tối thiểu: ' + window.number_format(voucher.min_order_value) + 'đ</li>' : '<li>Không giới hạn giá trị đơn hàng tối thiểu</li>'}
                                                    <li>Còn lại ${window.getRemainingUses(voucher)} lần sử dụng</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <button class="btn btn-sm btn-primary" onclick="applyVoucher(${reservationId}, ${voucher.id})">
                                            Áp dụng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                if (data.cannot_apply && data.cannot_apply.length > 0) {
                    html += '<div class="list-group">';
                    data.cannot_apply.forEach(voucher => {
                        html += `
                            <div class="list-group-item">
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="text-secondary">${voucher.code}</strong>
                                        ${voucher.end_date ? '<small class="text-muted ml-2">(Hết hạn: ' + window.formatDate(voucher.end_date) + ')</small>' : ''}
                                    </div>
                                    <div class="mt-2">
                                        <div class="mb-1">
                                            <strong>Giảm giá:</strong>
                                            ${voucher.discount_type === 'percent' ? voucher.discount_value + '%' : window.number_format(voucher.discount_value) + 'đ'}
                                            ${voucher.max_discount_value ? ' (Tối đa: ' + window.number_format(voucher.max_discount_value) + 'đ)' : ''}
                                        </div>
                                        <div class="text-muted small">
                                            <div><i class="fas fa-times-circle text-danger"></i> <strong>Điều kiện:</strong></div>
                                            <ul class="mb-1 pl-3">
                                                ${voucher.min_order_value ? '<li>Đơn hàng tối thiểu: ' + window.number_format(voucher.min_order_value) + 'đ</li>' : '<li>Không giới hạn giá trị đơn hàng tối thiểu</li>'}
                                                <li>Còn lại ${window.getRemainingUses(voucher)} lần sử dụng</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                if ((!data.can_apply || data.can_apply.length === 0) && (!data.cannot_apply || data.cannot_apply.length ===
                        0)) {
                    html = '<div class="alert alert-info">Không có voucher nào</div>';
                }

                voucherListDiv.innerHTML = html;
            }

            window.applyVoucher = function(reservationId, voucherId) {
                // Tìm nút đang được click để disable
                const buttons = document.querySelectorAll(
                    `button[onclick*="applyVoucher(${reservationId}, ${voucherId})"]`);
                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang áp dụng...';
                });

                fetch('{{ url('admin/dat-ban') }}/' + reservationId + '/apply-voucher', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            voucher_id: voucherId
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Lỗi khi áp dụng voucher');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            updateInvoiceWithVoucher(reservationId, data.data);
                            updateVoucherSection(reservationId, data.data.voucher);
                        } else {
                            alert('Lỗi: ' + data.message);
                            buttons.forEach(btn => {
                                btn.disabled = false;
                                btn.innerHTML = 'Áp dụng';
                            });
                        }
                    })
                    .catch(error => {
                        alert('Có lỗi xảy ra khi áp dụng voucher');
                        buttons.forEach(btn => {
                            btn.disabled = false;
                            btn.innerHTML = 'Áp dụng';
                        });
                    });
            }

            function updateInvoiceWithVoucher(reservationId, data) {
                // Lấy các giá trị từ bảng hiện tại
                const tfoot = document.querySelector(`#invoiceModal${reservationId} tfoot`);
                const totalRow = Array.from(tfoot.querySelectorAll('tr')).find(row =>
                    row.textContent.includes('Tổng tiền')
                );
                const totalBeforeDiscountText = totalRow.querySelector('th:last-child').textContent;
                const totalBeforeDiscount = parseFloat(totalBeforeDiscountText.replace(/[^\d]/g, '')) || data.total_price;

                const discountAmount = data.discount_amount || 0;
                const finalAmount = data.final_amount || (totalBeforeDiscount - discountAmount);

                // Lấy giá trị cọc từ bảng
                const tableDepositRow = Array.from(tfoot.querySelectorAll('tr')).find(row =>
                    row.textContent.includes('Tiền cọc bàn')
                );
                const tableDepositText = tableDepositRow ? tableDepositRow.querySelector('th:last-child').textContent : '0';
                const tableDeposit = parseFloat(tableDepositText.replace(/[^\d]/g, '')) || 0;

                const foodDepositRow = Array.from(tfoot.querySelectorAll('tr')).find(row =>
                    row.textContent.includes('Tiền cọc đồ ăn')
                );
                const foodDepositText = foodDepositRow ? foodDepositRow.querySelector('th:last-child').textContent : '0';
                const foodDeposit = parseFloat(foodDepositText.replace(/[^\d]/g, '')) || 0;

                const totalAfterDiscount = finalAmount;
                const remainingAmount = totalAfterDiscount - tableDeposit - foodDeposit;

                // Kiểm tra xem đã có dòng voucher discount chưa
                let voucherRow = document.getElementById(`voucherDiscountRow${reservationId}`);
                let totalAfterDiscountRow = document.getElementById(`totalAfterDiscountRow${reservationId}`);

                if (!voucherRow) {
                    // Thêm dòng giảm giá sau dòng "Tổng tiền"
                    voucherRow = document.createElement('tr');
                    voucherRow.id = `voucherDiscountRow${reservationId}`;
                    totalRow.insertAdjacentElement('afterend', voucherRow);
                }
                // Format voucher display: "Voucher (10%)" hoặc "Voucher (100k)"
                const voucherDisplay = data.voucher.discount_type === 'percent' ?
                    `Voucher (${data.voucher.discount_value}%)` :
                    `Voucher (${window.number_format(data.voucher.discount_value)}đ)`;

                voucherRow.innerHTML = `
                    <th colspan="3" class="text-right">${voucherDisplay}:</th>
                    <th class="text-right text-danger">- ${window.number_format(discountAmount)}đ</th>
                `;

                if (!totalAfterDiscountRow) {
                    // Thêm dòng tổng sau giảm giá
                    totalAfterDiscountRow = document.createElement('tr');
                    totalAfterDiscountRow.id = `totalAfterDiscountRow${reservationId}`;
                    voucherRow.insertAdjacentElement('afterend', totalAfterDiscountRow);
                }
                totalAfterDiscountRow.innerHTML = `
                    <th colspan="3" class="text-right">Tổng sau giảm giá:</th>
                    <th class="text-right">${window.number_format(totalAfterDiscount)}đ</th>
                `;

                // Cập nhật số tiền còn lại
                updateRemainingAmount(reservationId, remainingAmount);
            }

            function updateRemainingAmount(reservationId, remainingAmount) {
                let remainingRow = document.querySelector(`#invoiceModal${reservationId} tfoot .bg-warning`);

                if (!remainingRow) {
                    // Tạo dòng mới nếu chưa có
                    const tfoot = document.querySelector(`#invoiceModal${reservationId} tfoot`);
                    remainingRow = document.createElement('tr');
                    remainingRow.className = 'bg-warning';
                    tfoot.appendChild(remainingRow);
                }

                if (remainingAmount > 0) {
                    remainingRow.innerHTML = `
                        <th colspan="3" class="text-right">Còn phải thanh toán:</th>
                        <th class="text-right">
                            <h5 class="mb-0" id="remainingAmount${reservationId}">${window.number_format(remainingAmount)}đ</h5>
                        </th>
                    `;
                } else if (remainingAmount < 0) {
                    remainingRow.innerHTML = `
                        <th colspan="3" class="text-right">Hoàn lại cho khách:</th>
                        <th class="text-right">
                            <h5 class="mb-0">${window.number_format(Math.abs(remainingAmount))}đ</h5>
                        </th>
                    `;
                } else {
                    remainingRow.innerHTML = `
                        <th colspan="3" class="text-right">Đã thanh toán đủ:</th>
                        <th class="text-right">
                            <h5 class="mb-0">0đ</h5>
                        </th>
                    `;
                }
            }

            function updateVoucherSection(reservationId, voucher) {
                // Ẩn header khi voucher đã được áp dụng
                const voucherHeader = document.querySelector(`#invoiceModal${reservationId} .card-header`);
                if (voucherHeader) {
                    voucherHeader.style.display = 'none';
                }

                // Cập nhật body
                const voucherBody = document.querySelector(`#invoiceModal${reservationId} .card-body`);
                const discountDisplay = voucher.discount_type === 'percent' ?
                    voucher.discount_value + '%' :
                    window.number_format(voucher.discount_value) + 'đ';
                voucherBody.innerHTML = `
                    <div class="alert alert-success">
                        <strong>Voucher đã áp dụng:</strong> ${voucher.code}<br>
                        <strong>Giảm giá:</strong> ${discountDisplay}
                        <button type="button" class="btn btn-sm btn-danger float-right" onclick="removeVoucher(${reservationId})">
                            <i class="fas fa-times"></i> Hủy voucher
                        </button>
                    </div>
                `;

                // Ẩn danh sách voucher
                const voucherListDiv = document.getElementById(`voucherList${reservationId}`);
                if (voucherListDiv) {
                    voucherListDiv.style.display = 'none';
                }
            }

            function removeVoucher(reservationId) {
                const btn = event.target.closest('button');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xóa...';
                }

                fetch('{{ url('admin/dat-ban') }}/' + reservationId + '/remove-voucher', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            removeVoucherFromInvoice(reservationId, data.data);
                            resetVoucherSection(reservationId);
                        } else {
                            alert('Lỗi: ' + data.message);
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-times"></i> Hủy voucher';
                            }
                        }
                    })
                    .catch(error => {
                        alert('Có lỗi xảy ra khi hủy voucher');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-times"></i> Hủy voucher';
                        }
                    });
            }

            function removeVoucherFromInvoice(reservationId, data) {
                const tfoot = document.querySelector(`#invoiceModal${reservationId} tfoot`);

                // Xóa dòng giảm giá và tổng sau giảm giá
                const voucherRow = document.getElementById(`voucherDiscountRow${reservationId}`);
                const totalAfterDiscountRow = document.getElementById(`totalAfterDiscountRow${reservationId}`);

                if (voucherRow) voucherRow.remove();
                if (totalAfterDiscountRow) totalAfterDiscountRow.remove();

                // Tính lại số tiền còn lại
                const totalRow = Array.from(tfoot.querySelectorAll('tr')).find(row =>
                    row.textContent.includes('Tổng tiền')
                );
                const totalBeforeDiscountText = totalRow.querySelector('th:last-child').textContent;
                const totalBeforeDiscount = parseFloat(totalBeforeDiscountText.replace(/[^\d]/g, '')) || data.total_price;

                const tableDepositRow = Array.from(tfoot.querySelectorAll('tr')).find(row =>
                    row.textContent.includes('Tiền cọc bàn')
                );
                const tableDepositText = tableDepositRow ? tableDepositRow.querySelector('th:last-child').textContent : '0';
                const tableDeposit = parseFloat(tableDepositText.replace(/[^\d]/g, '')) || 0;

                const foodDepositRow = Array.from(tfoot.querySelectorAll('tr')).find(row =>
                    row.textContent.includes('Tiền cọc đồ ăn')
                );
                const foodDepositText = foodDepositRow ? foodDepositRow.querySelector('th:last-child').textContent : '0';
                const foodDeposit = parseFloat(foodDepositText.replace(/[^\d]/g, '')) || 0;

                const remainingAmount = totalBeforeDiscount - tableDeposit - foodDeposit;
                updateRemainingAmount(reservationId, remainingAmount);
            }

            function resetVoucherSection(reservationId) {
                // Hiện lại header khi hủy voucher
                const voucherHeader = document.querySelector(`#invoiceModal${reservationId} .card-header`);
                if (voucherHeader) {
                    voucherHeader.style.display = '';
                }

                // Reset body
                const voucherBody = document.querySelector(`#invoiceModal${reservationId} .card-body`);
                voucherBody.innerHTML = `
                    <button type="button" class="btn btn-primary btn-sm" onclick="loadVouchers(${reservationId})">
                        <i class="fas fa-search"></i> Xem danh sách voucher
                    </button>
                    <div id="voucherList${reservationId}" style="display: none; margin-top: 15px;"></div>
                `;
            }

            window.number_format = function(number) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(number));
            }
            setTimeout(() => {
                const alert = document.getElementById('notify-alert');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 300);
                }
            }, 3000);
        </script>
    @endpush
