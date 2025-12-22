@extends('admin.layouts.main')

@section('noidung')
    <style>
        .detail-row {
            background: #fafafa;
        }

        .detail-box {
            padding: 15px;
            border-left: 3px solid #007bff;
            background: #ffffff;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .detail-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 15px;
        }
    </style>

    <div class="content-wrapper">

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header"></div>

                            <div class="card-body">

                                {{-- Alert thành công --}}
                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show position-fixed"
                                        role="alert"
                                        style="
                                            top: 20px;
                                            right: 20px;
                                            z-index: 1050;
                                            background-color: #d4edda;
                                            color: #155724;
                                            border-color: #c3e6cb;
                                            font-size: 14px;
                                            padding: 10px 15px;
                                            border-radius: 8px;
                                            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                            max-width: 300px;">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                            style="outline: none;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="card mb-3 bg-light">
                                    <div class="card-body p-3">
                                        <form method="GET" class="row align-items-end">

                                            <div class="col-md-2 col-sm-6 mb-2 mr-2">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Người dùng</label>
                                                <select name="user_id" class="form-control form-control-sm">
                                                    <option value="">-- Tất cả --</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 mb-2 mr-2">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Đánh giá</label>
                                                <select name="rating" class="form-control form-control-sm">
                                                    <option value="">Tất cả</option>
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}"
                                                            {{ request('rating') == $i ? 'selected' : '' }}>
                                                            {{ $i }} ⭐
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-sm-6 mb-2 mr-2">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Từ ngày</label>
                                                <input type="date" name="from_date" class="form-control form-control-sm"
                                                    value="{{ request('from_date') }}">
                                            </div>

                                            <div class="col-md-2 col-sm-6 mb-2 mr-2">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px;">Đến ngày</label>
                                                <input type="date" name="to_date" class="form-control form-control-sm"
                                                    value="{{ request('to_date') }}">
                                            </div>

                                            <div class="col-md-3 col-sm-12 mb-2">
                                                <label class="font-weight-bold mb-1" style="font-size: 13px; visibility: hidden;">Actions</label>
                                                <div class="d-flex" style="gap: 8px;">
                                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                        <i class="fas fa-search"></i> Tìm kiếm
                                                </button>
                                                <a href="{{ route('admin.comments.index') }}"
                                                        class="btn btn-secondary btn-sm flex-fill">
                                                        <i class="fas fa-redo"></i> Đặt lại
                                                </a>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Người dùng</th>
                                            <th>Mã đặt bàn</th>
                                            <th>Đánh giá</th>
                                            <th>Bình luận</th>
                                            <th>Ngày bình luận</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($comments as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>

                                                <td>{{ $item->user->name ?? 'Không rõ' }}</td>

                                                <td>
                                                    {{ $item->reservation->reservation_code ?? 'Mã: #' . $item->reservation_id }}
                                                </td>

                                                <td><strong>{{ $item->rating }}/5</strong></td>

                                                <td>{{ $item->comment }}</td>

                                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>

                                                <td class="text-nowrap">
                                                    {{-- Nút xem chi tiết --}}
                                                    <button class="btn btn-info btn-sm"
                                                        onclick="toggleDetail({{ $item->id }})">
                                                        Chi tiết
                                                    </button>
                                                </td>
                                            </tr>

                                            {{-- Hàng chi tiết --}}
                                            <tr id="detail-{{ $item->id }}" class="detail-row" style="display:none;">
                                                <td colspan="7">
                                                    <div class="detail-box">
                                                        <div class="detail-title">📌 Thông tin đánh giá:</div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                        <p><strong>Người dùng:</strong> {{ $item->user->name }}</p>
                                                                <p><strong>Số điện thoại:</strong> {{ $item->user->phone ?? 'Không có' }}</p>
                                                                <p><strong>Email:</strong> {{ $item->user->email ?? 'Không có' }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Điểm đánh giá:</strong> <span class="badge badge-warning">{{ $item->rating }}/5 ⭐</span></p>
                                                                <p><strong>Bình luận:</strong> {{ $item->comment ?: 'Không có bình luận' }}</p>
                                                                <p><strong>Ngày đánh giá:</strong> {{ $item->created_at->format('d/m/Y H:i') }}</p>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="detail-title mt-3">📋 Thông tin đơn đặt bàn:</div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <p><strong>Mã đặt bàn:</strong> 
                                                                    <span class="badge badge-info">{{ $item->reservation->reservation_code ?? 'N/A' }}</span>
                                                                </p>
                                                                <p><strong>Ngày đặt:</strong> {{ $item->reservation->reservation_date->format('d/m/Y') ?? 'N/A' }}</p>
                                                                <p><strong>Ca:</strong> 
                                                                    @php
                                                                        $shiftTexts = [
                                                                            'morning' => 'Ca sáng (8-13h)',
                                                                            'afternoon' => 'Ca trưa (13-18h)',
                                                                            'evening' => 'Ca tối (18-23h)'
                                                                        ];
                                                                        $shift = $item->reservation->shift ?? '';
                                                                        $shiftText = $shiftTexts[$shift] ?? ucfirst($shift);
                                                                    @endphp
                                                                    {{ $shiftText }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Thời gian tạo đơn:</strong> {{ $item->reservation->created_at->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                                                @if($item->reservation->tables && $item->reservation->tables->count() > 0)
                                                                    <p><strong>Bàn đã gán:</strong> 
                                                                        @foreach($item->reservation->tables as $table)
                                                                            <span class="badge badge-success mr-1">{{ $table->name }}</span>
                                                                        @endforeach
                                                                        <span class="text-muted ml-2">({{ $item->reservation->tables->count() }} bàn)</span>
                                                                    </p>
                                                                @endif
                                                                <p><strong>Số người:</strong> {{ $item->reservation->num_people ?? 'N/A' }} người</p>
                                                                @if($item->reservation->depsection)
                                                                    <p><strong>Ghi chú:</strong> {{ $item->reservation->depsection }}</p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @php
                                                            // Lấy giá trị deposit từ reservation - giống như trong modal chi tiết datBan/index.blade.php
                                                            $reservation = $item->reservation;
                                                            
                                                            // Lấy tổng tiền cọc - đảm bảo lấy đúng giá trị
                                                            $totalDeposit = 0;
                                                            if ($reservation) {
                                                                // Lấy deposit trực tiếp, xử lý các trường hợp null, 0, string, number
                                                                $depositRaw = $reservation->deposit;
                                                                if ($depositRaw !== null && $depositRaw !== '') {
                                                                    $totalDeposit = is_numeric($depositRaw) ? (float)$depositRaw : 0;
                                                                }
                                                                
                                                                // Tính tiền cọc bàn và món - giống như dòng 448-449 trong datBan/index.blade.php
                                                                try {
                                                                    $tableDeposit = $reservation->getTableDeposit();
                                                                    $foodDeposit = $reservation->getFoodDeposit();
                                                                } catch (\Exception $e) {
                                                                    // Nếu không tính được, dùng tổng tiền cọc
                                                                    $tableDeposit = 0;
                                                                    $foodDeposit = $totalDeposit;
                                                                }
                                                            } else {
                                                                $tableDeposit = 0;
                                                                $foodDeposit = 0;
                                                            }
                                                        @endphp

                                                        <div class="mb-3 p-3 bg-light rounded">
                                                            <strong class="mb-2 d-block">Thông tin thanh toán:</strong>
                                                            @if($tableDeposit > 0)
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <strong>Tiền cọc bàn:</strong>
                                                                    <span class="text-info">{{ number_format($tableDeposit, 0, ',', '.') }} VND</span>
                                                                </div>
                                                            @endif
                                                            @if($foodDeposit > 0)
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <strong>Tiền cọc món gọi trước:</strong>
                                                                    <span class="text-info">{{ number_format($foodDeposit, 0, ',', '.') }} VND</span>
                                                                </div>
                                                            @endif
                                                            <div class="d-flex justify-content-between">
                                                                <strong>Tổng tiền cọc:</strong>
                                                                <span class="text-success"><strong>{{ number_format($totalDeposit, 0, ',', '.') }} VND</strong></span>
                                                            </div>
                                                        </div>

                                                        @if($item->reservation->reservationItems && $item->reservation->reservationItems->count() > 0)
                                                            @php
                                                                $subtotal = $item->reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
                                                                $vat = $subtotal * 0.08;
                                                                $totalBeforeDiscount = $subtotal + $vat;
                                                                $voucherDiscount = $item->reservation->voucher_discount ?? 0;
                                                                $finalAmount = $totalBeforeDiscount - $voucherDiscount;
                                                                if ($finalAmount < 0) $finalAmount = 0;
                                                            @endphp
                                                            
                                                            <div class="mb-3">
                                                                <strong>Món ăn đã đặt:</strong>
                                                                <table class="table table-sm table-bordered mt-2">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Món</th>
                                                                            <th>SL</th>
                                                                            <th>Đơn giá</th>
                                                                            <th>Thành tiền</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($item->reservation->reservationItems as $menuItem)
                                                                            <tr>
                                                                                <td>{{ $menuItem->menu->name ?? 'N/A' }}</td>
                                                                                <td>{{ $menuItem->quantity }}</td>
                                                                                <td>{{ number_format($menuItem->price, 0, ',', '.') }}₫</td>
                                                                                <td>{{ number_format($menuItem->price * $menuItem->quantity, 0, ',', '.') }}₫</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                
                                                                <div class="mt-3 p-3 bg-light rounded">
                                                                    <div class="d-flex justify-content-between mb-2">
                                                                        <strong>Tạm tính:</strong>
                                                                        <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between mb-2">
                                                                        <strong>VAT 8%:</strong>
                                                                        <span>{{ number_format($vat, 0, ',', '.') }}₫</span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between mb-2">
                                                                        <strong>Tổng tiền:</strong>
                                                                        <span>{{ number_format($totalBeforeDiscount, 0, ',', '.') }}₫</span>
                                                                    </div>
                                                                    @if($item->reservation->voucher_id && $voucherDiscount > 0)
                                                                        <div class="d-flex justify-content-between mb-2 text-success">
                                                                            <strong>
                                                                                Giảm giá voucher 
                                                                                @if($item->reservation->voucher)
                                                                                    ({{ $item->reservation->voucher->code }})
                                                                                @endif
                                                                                :
                                                                            </strong>
                                                                            <span>-{{ number_format($voucherDiscount, 0, ',', '.') }}₫</span>
                                                                        </div>
                                                                    @endif
                                                                    <hr>
                                                                    <div class="d-flex justify-content-between">
                                                                        <strong class="text-primary" style="font-size: 1.1em;">Thành tiền:</strong>
                                                                        <strong class="text-primary" style="font-size: 1.1em;">{{ number_format($finalAmount, 0, ',', '.') }}₫</strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không có bình luận nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{ $comments->links() }}

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function toggleDetail(id) {
            const row = document.getElementById("detail-" + id);
            row.style.display = row.style.display === "none" ? "" : "none";
        }

        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endsection
