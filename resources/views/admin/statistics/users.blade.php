@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Thống kê người dùng</h1>

                <form method="GET">
                    <div class="btn-group mb-2">
                        @foreach ([
            'today' => 'Hôm nay',
            'this_week' => 'Tuần này',
            'this_month' => 'Tháng này',
            'this_year' => 'Năm nay',
        ] as $k => $v)
                            <button type="submit" name="filter" value="{{ $k }}"
                                class="btn btn-sm {{ $filterType == $k ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $v }}
                            </button>
                        @endforeach
                    </div>

                    <div class="form-inline mb-2">
                        <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                            class="form-control form-control-sm mr-2">
                        <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}"
                            class="form-control form-control-sm mr-2">
                        <button type="submit" name="filter" value="custom" class="btn btn-sm btn-outline-secondary">
                            Áp dụng
                        </button>
                    </div>

                    <small class="text-muted">
                        {{ $filterLabel }} ({{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }})
                    </small>
                </form>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h3>{{ $totalUsers }}</h3>
                                <p>Tổng người dùng</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-success">
                            <div class="inner text-center">
                                <h3>{{ $newUsersThisMonth }}</h3>
                                <p>Người dùng mới</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-primary">
                            <div class="inner text-center">
                                <h3>{{ $usersWithReservation }}</h3>
                                <p>Đã từng đặt bàn</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>🏆 Top khách đặt bàn nhiều nhất</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>SĐT</th>
                                    <th>Số lần đặt</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topBookingUsers->where('role', '!=', 'admin') as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                                        <td class="fw-bold">{{ $u->reservations_count }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $u->id }}">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- <div class="card mt-4">
                    <div class="card-header">
                        <h5>💰 Top khách chi tiêu nhiều nhất</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>SĐT</th>
                                    <th>Tổng tiền</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topSpendingUsers->where('role', '!=', 'admin') as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                                        <td class="fw-bold text-danger">
                                            {{ number_format($u->total_spent ?? 0) }} đ
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $u->id }}">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> --}}


                <div class="card mt-4">
                    <div class="card-header">
                        <h5>📋 Danh sách người dùng</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Vai trò</th>
                                    <th>Ngày tạo</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $u)
                                    <tr>
                                        <td>{{ $u->id }}</td>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->phone ?? '—' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $u->role === 'admin' ? 'danger' : 'primary' }}">
                                                {{ $u->role }}
                                            </span>
                                        </td>
                                        <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
                                        <td >
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $u->id }}">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Không có người dùng mới trong khoảng thời gian này
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                </div>


                @foreach ($topBookingUsers->merge($topSpendingUsers)->merge($users)->unique('id') as $u)
                    <div class="modal fade" id="detailModal{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Chi tiết tài khoản: {{ $u->name }}
                                        <small class="text-muted">({{ $u->phone ?? 'Chưa cập nhật' }})</small>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    @if ($u->role === 'admin')
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>ID</th>
                                                <td>{{ $u->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Họ tên</th>
                                                <td>{{ $u->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $u->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>SĐT</th>
                                                <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Vai trò</th>
                                                <td>
                                                    <span class="badge badge-danger">ADMIN</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Ngày tạo</th>
                                                <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    @else
                                        @if ($u->reservations->count())
                                            @foreach ($u->reservations as $reservation)
                                                <div class="border rounded p-3 mb-3">
                                                    @php
                                                        $statusMap = [
                                                            'pending' => 'Chờ xác nhận',
                                                            'completed' => 'Hoàn thành',
                                                            'cancelled' => 'Đã huỷ',
                                                            'deposit_pending' => 'Chờ đặt cọc',
                                                        ];

                                                        $statusColorMap = [
                                                            'pending' => 'warning',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                            'deposit_pending' => 'info',
                                                        ];
                                                    @endphp


                                                    <div class="mb-2">
                                                        <div class="fw-bold fs-5">
                                                            🧾 Mã đơn: {{ $reservation->reservation_code }}
                                                        </div>

                                                        <div class="fw-semibold">
                                                            📌 Trạng thái:
                                                            <span
                                                                class="badge bg-{{ $statusColorMap[$reservation->status] ?? 'secondary' }}">
                                                                {{ $statusMap[$reservation->status] ?? 'Không xác định' }}
                                                            </span>
                                                        </div>
                                                    </div>


                                                    <h6 class="fw-bold">🪑 Bàn đã đặt:</h6>
                                                    @foreach ($reservation->tables as $table)
                                                        <span class="badge bg-success">{{ $table->name }}</span>
                                                    @endforeach

                                                    <h6 class="fw-bold mt-3">🍽️ Món ăn:</h6>
                                                    <table class="table table-sm table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Món</th>
                                                                <th>SL</th>
                                                                <th>Đơn giá</th>
                                                                <th>Thành tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $subtotal = 0; @endphp
                                                            @foreach ($reservation->reservationItems as $item)
                                                                @php
                                                                    $line = $item->price * $item->quantity;
                                                                    $subtotal += $line;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $item->menu->name ?? 'N/A' }}</td>
                                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                                    <td class="text-end">{{ number_format($item->price) }}
                                                                        đ</td>
                                                                    <td class="text-end">{{ number_format($line) }} đ</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            @php
                                                                $vat = $subtotal * 0.08;
                                                                $totalBefore = $subtotal + $vat;
                                                                $discount = $reservation->voucher_discount ?? 0;
                                                                $total = max($totalBefore - $discount, 0);
                                                            @endphp
                                                            <tr>
                                                                <th colspan="3" class="text-end">Tạm tính</th>
                                                                <th class="text-end">{{ number_format($subtotal) }} đ</th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="3" class="text-end">VAT 8%</th>
                                                                <th class="text-end">{{ number_format($vat) }} đ</th>
                                                            </tr>
                                                            @if ($reservation->voucher)
                                                                <tr class="bg-success text-white">
                                                                    <th colspan="3" class="text-end">
                                                                        Voucher ({{ $reservation->voucher->code }})
                                                                    </th>
                                                                    <th class="text-end">-{{ number_format($discount) }} đ
                                                                    </th>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <th colspan="3" class="text-end">Thành tiền</th>
                                                                <th class="text-end text-danger fw-bold">
                                                                    {{ number_format($total) }} đ
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">Người dùng chưa có đơn đặt bàn.</p>
                                        @endif
                                    @endif

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>📈 Biểu đồ người dùng theo ngày</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const labels = @json($chartLabels);

            new Chart(document.getElementById('userChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Người dùng đăng ký',
                            data: @json($newUsersData),
                            borderColor: 'blue',
                            backgroundColor: 'transparent',
                            pointRadius: 5,
                            tension: 0.3
                        },
                        {
                            label: 'Người dùng có đặt bàn',
                            data: @json($activeUsersData),
                            borderColor: 'green',
                            backgroundColor: 'transparent',
                            pointRadius: 5,
                            tension: 0.3
                        },
                        {
                            label: 'Người dùng không hoạt động',
                            data: @json($inactiveUsersData),
                            borderColor: 'red',
                            backgroundColor: 'transparent',
                            pointRadius: 5,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        </script>
    @endpush


@endsection
