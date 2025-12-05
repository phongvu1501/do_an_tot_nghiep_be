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
                                <a href="{{ route('admin.datBan.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tạo đơn mới
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <form action="{{ route('admin.datBan.index') }}" method="GET" class="row g-3" id="filterFormDatBan">
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Ngày đặt</label>
                                            <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="document.getElementById('filterFormDatBan').submit()">
                                            <div class="mt-2">
                                                <a href="{{ route('admin.datBan.index') }}" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-redo"></i> Đặt lại
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Ca</label>
                                            <select name="shift" class="form-control"
                                                onchange="document.getElementById('filterFormDatBan').submit()">
                                                <option value="">Tất cả ca</option>
                                                <option value="morning"
                                                    {{ request('shift') == 'morning' ? 'selected' : '' }}>Ca sáng (8-13h)</option>
                                                <option value="afternoon"
                                                    {{ request('shift') == 'afternoon' ? 'selected' : '' }}>Ca trưa (13-18h)</option>
                                                <option value="evening"
                                                    {{ request('shift') == 'evening' ? 'selected' : '' }}>Ca tối (18-23h)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Trạng thái</label>
                                            <select name="status" class="form-control"
                                                onchange="document.getElementById('filterFormDatBan').submit()">
                                                <option value="">Tất cả trạng thái</option>
                                                <option value="pending"
                                                    {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận
                                                </option>
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
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Số điện thoại</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       name="phone"
                                                       class="form-control"
                                                       value="{{ request('phone') }}"
                                                       placeholder="Nhập số điện thoại"
                                                       onkeypress="if(event.key === 'Enter') { document.getElementById('filterFormDatBan').submit(); }">
                                                <div class="input-group-append" style="margin-left: 5px;">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
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
                                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST"
                                                        style="display:inline;">
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

                                                @if ($reservation->status != 'cancelled' && $reservation->status != 'completed')
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
                                    <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}<br>
                                    <strong>Ca:</strong>
                                    @if($reservation->shift == 'morning') Ca sáng (8-13h)
                                    @elseif($reservation->shift == 'afternoon') Ca trưa (13-18h)
                                    @else Ca tối (18-23h)
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
                                    @if($reservation->status == 'cancelled' && $reservation->cancellation_reason)
                                        <strong>Lý do hủy đơn:</strong> {{ $reservation->cancellation_reason }}<br>
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
                                @if($foodDeposit > 0)
                                <p class="mb-2">
                                    <strong>Tiền cọc món gọi trước:</strong>
                                    <span class="text-primary">{{ number_format($foodDeposit, 0, ',', '.') }} VND</span>
                                </p>
                                @endif
                                <p class="mb-0">
                                    <strong>Tổng tiền cọc:</strong>
                                    <span class="text-success font-weight-bold">{{ number_format($reservation->deposit ?? 0, 0, ',', '.') }} VND</span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Bàn đã gán:</h6>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                @foreach($reservation->tables as $table)
                                    <span class="badge badge-success badge-lg">{{ $table->name }}</span>
                                @endforeach
                                <span class="text-muted">({{ $reservation->tables->count() }} bàn)</span>
                            </div>
                            @if($reservation->status != 'completed' && $reservation->status != 'cancelled')
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editTablesModal{{ $reservation->id }}">
                                    <i class="fas fa-edit"></i> Chỉnh sửa bàn
                                </button>
                            @endif
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Món ăn đã đặt:</h6>
                        @if($reservation->reservationItems->count() > 0)
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
                                    @foreach($reservation->reservationItems as $item)
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
                                        $subtotal = $reservation->reservationItems->sum(function($m) {
                                            return $m->price * $m->quantity;
                                        });
                                        $vat = $subtotal * 0.1;
                                        $totalBeforeDiscount = $subtotal + $vat;
                                        $voucherDiscount = $reservation->voucher_discount ?? 0;
                                        $total = $totalBeforeDiscount - $voucherDiscount;
                                        if ($total < 0) $total = 0;
                                    @endphp
                                    <tr>
                                        <th colspan="3" class="text-right">Tạm tính:</th>
                                        <th class="text-right">
                                            {{ number_format($subtotal, 0, ',', '.') }}đ
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">VAT 10%:</th>
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
                                    @if($reservation->voucher_id && $voucherDiscount > 0)
                                    <tr class="bg-success text-white">
                                        <th colspan="3" class="text-right">
                                            <i class="fas fa-ticket-alt"></i> Giảm giá voucher
                                            @if($reservation->voucher)
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
                        <a href="{{ route('invoice.pdf', ['code' => $reservation->reservation_code]) }}" class="btn btn-primary">
                            In hóa đơn
                        </a>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chỉnh sửa bàn -->
        @if($reservation->status != 'completed' && $reservation->status != 'cancelled')
        <div class="modal fade" id="editTablesModal{{ $reservation->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.datBan.updateTables', $reservation->id) }}" method="POST" id="editTablesForm{{ $reservation->id }}" onsubmit="return validateTableSelection({{ $reservation->id }})">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="fas fa-edit"></i> Chỉnh sửa bàn cho đơn #{{ $reservation->reservation_code }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Số người: <strong>{{ $reservation->num_people }}</strong> |
                                Ngày: <strong>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</strong> |
                                Ca: <strong>
                                    @if($reservation->shift == 'morning') Sáng (8-13h)
                                    @elseif($reservation->shift == 'afternoon') Trưa (13-18h)
                                    @else Tối (18-23h)
                                    @endif
                                </strong>
                            </div>

                            <div id="errorMessage{{ $reservation->id }}" class="alert alert-danger" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Vui lòng chọn ít nhất 1 bàn!</strong>
                            </div>

                            @if(session('conflicting_tables') && session('open_edit_modal') == $reservation->id)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Cảnh báo:</strong> Các bàn sau đang được sử dụng bởi đơn khác:
                                    <ul class="mb-0 mt-2">
                                        @foreach(session('conflicting_tables') as $conflict)
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
                                        $conflictingTableIds = session('conflicting_tables') && session('open_edit_modal') == $reservation->id
                                            ? collect(session('conflicting_tables'))->pluck('table_id')->toArray()
                                            : [];
                                    @endphp

                                    @foreach($allTables as $table)
                                        @php
                                            // Check if table is busy in this shift/date (excluding current reservation)
                                            $isBusy = $table->reservations()
                                                ->where('reservation_date', $reservation->reservation_date)
                                                ->where('shift', $reservation->shift)
                                                ->whereIn('status', ['deposit_paid', 'serving'])
                                                ->where('reservations.id', '!=', $reservation->id)
                                                ->exists();

                                            // Check if this table is conflicting (being served by another reservation)
                                            $isConflicting = in_array($table->id, $conflictingTableIds);
                                        @endphp

                                        <div class="custom-control custom-checkbox mb-2 {{ $isConflicting ? 'border border-danger p-2 rounded bg-light' : '' }}">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input table-checkbox-{{ $reservation->id }}"
                                                id="table{{ $table->id }}_{{ $reservation->id }}"
                                                name="table_ids[]"
                                                value="{{ $table->id }}"
                                                {{ in_array($table->id, $currentTableIds) ? 'checked' : '' }}
                                                {{ ($isBusy || $isConflicting) ? 'disabled' : '' }}
                                            >
                                            <label class="custom-control-label {{ $isConflicting ? 'text-danger font-weight-bold' : '' }}" for="table{{ $table->id }}_{{ $reservation->id }}">
                                                {{ $table->name }}
                                                @if($isConflicting)
                                                    <span class="badge badge-danger ml-2">Đang phục vụ</span>
                                                @endif
                                                @if($isBusy)
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
                                    <i class="fas fa-lightbulb"></i> Bạn có thể chọn nhiều bàn. Bàn "Đang bận" không thể chọn.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if($reservation->status == 'serving')
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
                        @if($reservation->reservationItems->count() > 0)
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
                                    @foreach($reservation->reservationItems as $item)
                                        <tr>
                                            <td>{{ $item->menu->name }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                            <td class="text-right"><strong>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    @php
                                        $subtotal = $reservation->reservationItems->sum(function($item) {
                                            return $item->price * $item->quantity;
                                        });
                                        $vat = $subtotal * 0.1;
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
                                        <th colspan="3" class="text-right">VAT 10%:</th>
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
                                    @if($reservation->voucher && $voucherDiscount > 0)
                                    <tr id="voucherDiscountRow{{ $reservation->id }}">
                                        <th colspan="3" class="text-right">
                                            @php
                                                $voucherDisplay = 'Voucher (';
                                                if ($reservation->voucher->discount_type == 'percent') {
                                                    $voucherDisplay .= $reservation->voucher->discount_value . '%)';
                                                } else {
                                                    $voucherDisplay .= number_format($reservation->voucher->discount_value, 0, ',', '.') . 'đ)';
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
                                    @if($foodDeposit > 0)
                                    <tr>
                                        <th colspan="3" class="text-right">Tiền cọc đồ ăn:</th>
                                        <th class="text-right text-success">
                                            - {{ number_format($foodDeposit, 0, ',', '.') }}đ
                                        </th>
                                    </tr>
                                    @endif
                                    @if($remainingAmount > 0)
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
                                            <h5 class="mb-0">{{ number_format(abs($remainingAmount), 0, ',', '.') }}đ</h5>
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
                            <div class="card-header bg-primary text-white" @if($reservation->voucher) style="display: none;" @endif>
                                <h6 class="mb-0">
                                    <i class="fas fa-ticket-alt"></i> Voucher
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($reservation->voucher)
                                    <div class="alert alert-success">
                                        <strong>Voucher đã áp dụng:</strong> {{ $reservation->voucher->code }}<br>
                                        <strong>Giảm giá:</strong>
                                        @if($reservation->voucher->discount_type == 'percent')
                                            {{ $reservation->voucher->discount_value }}%
                                        @else
                                            {{ number_format($reservation->voucher->discount_value, 0, ',', '.') }}đ
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger float-right" onclick="removeVoucher({{ $reservation->id }})">
                                            <i class="fas fa-times"></i> Hủy voucher
                                        </button>
                                    </div>
                                @else
                                    <button type="button" class="btn btn-primary btn-sm" onclick="loadVouchers({{ $reservation->id }})">
                                        <i class="fas fa-search"></i> Xem danh sách voucher
                                    </button>
                                @endif

                                <div id="voucherList{{ $reservation->id }}" style="display: none; margin-top: 15px;">
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
                        @if($reservation->reservationItems->count() > 0)
                        <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
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
        @if($reservation->status != 'cancelled' && $reservation->status != 'completed')
        <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                        <input type="hidden" name="status" value="cancelled">

                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i> Xác nhận hủy đơn #{{ $reservation->id }}
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
                                        <strong>Voucher:</strong> {{ $reservation->voucher->code ?? 'Không có voucher' }}<br>
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
                                            <strong>Lý do hủy đơn:</strong> {{ $reservation->cancellation_reason }}<br>
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
                                            $vat = $subtotal * 0.1;
                                            $total = $subtotal + $vat;
                                        @endphp
                                        <tr>
                                            <th colspan="3" class="text-right">Tạm tính:</th>
                                            <th class="text-right">
                                                {{ number_format($subtotal, 0, ',', '.') }}đ
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">VAT 10%:</th>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Chỉnh sửa bàn -->
        @if($reservation->status != 'completed' && $reservation->status != 'cancelled')
        <div class="modal fade" id="editTablesModal{{ $reservation->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.datBan.updateTables', $reservation->id) }}" method="POST" id="editTablesForm{{ $reservation->id }}" onsubmit="return validateTableSelection({{ $reservation->id }})">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="fas fa-edit"></i> Chỉnh sửa bàn cho đơn #{{ $reservation->reservation_code }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Số người: <strong>{{ $reservation->num_people }}</strong> |
                                Ngày: <strong>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</strong> |
                                Ca: <strong>
                                    @if($reservation->shift == 'morning') Sáng (8-13h)
                                    @elseif($reservation->shift == 'afternoon') Trưa (13-18h)
                                    @else Tối (18-23h)
                                    @endif
                                </strong>
                            </div>

                            <div id="errorMessage{{ $reservation->id }}" class="alert alert-danger" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Vui lòng chọn ít nhất 1 bàn!</strong>
                            </div>

                            @if(session('conflicting_tables') && session('open_edit_modal') == $reservation->id)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Cảnh báo:</strong> Các bàn sau đang được sử dụng bởi đơn khác:
                                    <ul class="mb-0 mt-2">
                                        @foreach(session('conflicting_tables') as $conflict)
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
                                            $isBusy = $table
                                                ->reservations()
                                                ->where('reservation_date', $reservation->reservation_date)
                                                ->where('shift', $reservation->shift)
                                                ->whereIn('status', ['deposit_paid', 'serving'])
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
                                                {{ $isBusy || $isConflicting ? 'disabled' : '' }}>
                                            <label
                                                class="custom-control-label {{ $isConflicting ? 'text-danger font-weight-bold' : '' }}"
                                                for="table{{ $table->id }}_{{ $reservation->id }}">
                                                {{ $table->name }}
                                                @if ($isConflicting)
                                                    <span class="badge badge-danger ml-2">Đang phục vụ</span>
                                                @endif
                                                @if ($isBusy)
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
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
                                                $vat = $subtotal * 0.1;
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
                                                <th colspan="3" class="text-right">VAT 10%:</th>
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
            @if ($reservation->status != 'cancelled' && $reservation->status != 'completed')
                <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                <input type="hidden" name="status" value="cancelled">

                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle"></i> Xác nhận hủy đơn #{{ $reservation->id }}
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
    @endsection

    @push('scripts')
        <script>
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
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN');
            }

            function getMaxUses(voucher) {
                if (!voucher || voucher.max_uses === null || voucher.max_uses === undefined) {
                    return 1;
                }
                const maxUses = parseInt(voucher.max_uses, 10);
                return isNaN(maxUses) || maxUses <= 0 ? 1 : maxUses;
            }

            function getUsedCount(voucher) {
                if (!voucher || voucher.used_count === null || voucher.used_count === undefined) {
                    return 0;
                }
                const usedCount = parseInt(voucher.used_count, 10);
                return isNaN(usedCount) || usedCount < 0 ? 0 : usedCount;
            }

            function getRemainingUses(voucher) {
                const maxUses = getMaxUses(voucher);
                const usedCount = getUsedCount(voucher);
                const remaining = maxUses - usedCount;
                return remaining < 0 ? 0 : remaining;
            }

            // Voucher functions
            function loadVouchers(reservationId) {
                const voucherListDiv = document.getElementById('voucherList' + reservationId);
                voucherListDiv.style.display = 'block';
                voucherListDiv.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang tải danh sách voucher...</p>
                    </div>
                `;

                fetch('{{ url('admin/dat-ban') }}/' + reservationId + '/applicable-vouchers')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            displayVouchers(reservationId, data.data);
                        } else {
                            voucherListDiv.innerHTML = '<div class="alert alert-danger">Không thể tải danh sách voucher: ' + (data.message || 'Lỗi không xác định') + '</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading vouchers:', error);
                        voucherListDiv.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải voucher. Vui lòng thử lại.</div>';
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
                                            ${voucher.end_date ? '<small class="text-muted ml-2">(Hết hạn: ' + formatDate(voucher.end_date) + ')</small>' : ''}
                                        </div>
                                        <div class="mt-2">
                                            <div class="mb-1">
                                                <strong>Giảm giá:</strong>
                                                ${voucher.discount_type === 'percent' ? voucher.discount_value + '%' : number_format(voucher.discount_value) + 'đ'}
                                                ${voucher.max_discount_value ? ' (Tối đa: ' + number_format(voucher.max_discount_value) + 'đ)' : ''}
                                            </div>
                                            <div class="text-muted small">
                                                <div><i class="fas fa-check-circle text-success"></i> <strong>Điều kiện:</strong></div>
                                                <ul class="mb-1 pl-3">
                                                    ${voucher.min_order_value ? '<li>Đơn hàng tối thiểu: ' + number_format(voucher.min_order_value) + 'đ</li>' : '<li>Không giới hạn giá trị đơn hàng tối thiểu</li>'}
                                                    <li>Còn lại ${getRemainingUses(voucher)} lần sử dụng</li>
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
                                        ${voucher.end_date ? '<small class="text-muted ml-2">(Hết hạn: ' + formatDate(voucher.end_date) + ')</small>' : ''}
                                    </div>
                                    <div class="mt-2">
                                        <div class="mb-1">
                                            <strong>Giảm giá:</strong>
                                            ${voucher.discount_type === 'percent' ? voucher.discount_value + '%' : number_format(voucher.discount_value) + 'đ'}
                                            ${voucher.max_discount_value ? ' (Tối đa: ' + number_format(voucher.max_discount_value) + 'đ)' : ''}
                                        </div>
                                        <div class="text-muted small">
                                            <div><i class="fas fa-times-circle text-danger"></i> <strong>Điều kiện:</strong></div>
                                            <ul class="mb-1 pl-3">
                                                ${voucher.min_order_value ? '<li>Đơn hàng tối thiểu: ' + number_format(voucher.min_order_value) + 'đ</li>' : '<li>Không giới hạn giá trị đơn hàng tối thiểu</li>'}
                                                <li>Còn lại ${getRemainingUses(voucher)} lần sử dụng</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                if ((!data.can_apply || data.can_apply.length === 0) && (!data.cannot_apply || data.cannot_apply.length === 0)) {
                    html = '<div class="alert alert-info">Không có voucher nào</div>';
                }

                voucherListDiv.innerHTML = html;
            }

            function applyVoucher(reservationId, voucherId) {
                // Tìm nút đang được click để disable
                const buttons = document.querySelectorAll(`button[onclick*="applyVoucher(${reservationId}, ${voucherId})"]`);
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
                    body: JSON.stringify({ voucher_id: voucherId })
                })
                .then(response => response.json())
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
                const voucherDisplay = data.voucher.discount_type === 'percent'
                    ? `Voucher (${data.voucher.discount_value}%)`
                    : `Voucher (${number_format(data.voucher.discount_value)}đ)`;

                voucherRow.innerHTML = `
                    <th colspan="3" class="text-right">${voucherDisplay}:</th>
                    <th class="text-right text-danger">- ${number_format(discountAmount)}đ</th>
                `;

                if (!totalAfterDiscountRow) {
                    // Thêm dòng tổng sau giảm giá
                    totalAfterDiscountRow = document.createElement('tr');
                    totalAfterDiscountRow.id = `totalAfterDiscountRow${reservationId}`;
                    voucherRow.insertAdjacentElement('afterend', totalAfterDiscountRow);
                }
                totalAfterDiscountRow.innerHTML = `
                    <th colspan="3" class="text-right">Tổng sau giảm giá:</th>
                    <th class="text-right">${number_format(totalAfterDiscount)}đ</th>
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
                            <h5 class="mb-0" id="remainingAmount${reservationId}">${number_format(remainingAmount)}đ</h5>
                        </th>
                    `;
                } else if (remainingAmount < 0) {
                    remainingRow.innerHTML = `
                        <th colspan="3" class="text-right">Hoàn lại cho khách:</th>
                        <th class="text-right">
                            <h5 class="mb-0">${number_format(Math.abs(remainingAmount))}đ</h5>
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
                const discountDisplay = voucher.discount_type === 'percent'
                    ? voucher.discount_value + '%'
                    : number_format(voucher.discount_value) + 'đ';
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

            function number_format(number) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(number));
            }
        </script>
    @endpush
