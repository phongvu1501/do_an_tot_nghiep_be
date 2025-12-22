@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Thống kê Doanh thu</h1>

                <form action="{{ route('admin.thongkeStatistics') }}" method="GET" class="mt-3">
                    <div class="btn-group">
                        @foreach ([
            'today' => 'Hôm nay',
            'this_week' => 'Tuần này',
            'this_month' => 'Tháng này',
            'this_year' => 'Năm nay',
        ] as $k => $v)
                            <button type="submit" name="filter" value="{{ $k }}"
                                class="btn btn-sm {{ ($filterType ?? 'this_month') == $k ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $v }}
                            </button>
                        @endforeach
                    </div>

                    <div class="form-inline mt-2">
                        <label class="mr-2">Từ</label>
                        <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                            class="form-control form-control-sm mr-2">

                        <label class="mr-2">đến</label>
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
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ number_format($totalRevenue) }}₫</h3>
                                <p>Tổng doanh thu</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($totalDeposit) }}₫</h3>
                                <p>Tổng tiền cọc</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($totalVoucherDiscount) }}₫</h3>
                                <p>Voucher giảm</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($totalRevenueAfterDiscount) }}₫</h3>
                                <p>Doanh thu thực tế</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>📅 Doanh thu theo ngày</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Doanh thu</th>
                                    <th>Tiền cọc</th>
                                    <th>Voucher</th>
                                    <th>Thực tế</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dailyStatistics as $stat)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($stat->date)->format('d/m/Y') }}</td>
                                        <td>{{ number_format($stat->total_revenue) }}₫</td>
                                        <td>{{ number_format($stat->deposit) }}₫</td>
                                        <td>{{ number_format($stat->voucher_discount) }}₫</td>
                                        <td class="fw-bold text-danger">
                                            {{ number_format(max($stat->total_revenue - $stat->voucher_discount, 0)) }}₫
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3 d-flex justify-content-center">
                            {{ $dailyStatistics->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>💰 Top khách chi tiêu nhiều nhất</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>SĐT</th>
                                    <th>Tổng chi tiêu</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topSpendingUsers as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                                        <td class="fw-bold text-danger">
                                            {{ number_format($u->total_spent ?? 0) }}₫
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $u->id }}">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>📈 Biểu đồ doanh thu</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="120"></canvas>
                    </div>
                </div>

            </div>
        </section>
    </div>

    @foreach ($topSpendingUsers as $u)
        <div class="modal fade" id="detailModal{{ $u->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Chi tiết tài khoản: {{ $u->name }}
                            <small class="text-muted">({{ $u->phone ?? 'Chưa có SĐT' }})</small>
                        </h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        @forelse ($u->reservations as $r)
                            <div class="border rounded p-3 mb-4">

                                <div class="fw-bold mb-1">
                                    🧾 {{ $r->reservation_code }}

                                    @if ($r->status === 'completed')
                                        <span class="badge bg-success ms-2">Hoàn thành</span>
                                    @elseif ($r->status === 'cancelled')
                                        <span class="badge bg-danger ms-2">Đã huỷ</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Khác</span>
                                    @endif
                                </div>

                                <div class="mb-2">
                                    🪑 Bàn:
                                    @forelse ($r->tables as $table)
                                        <span class="badge bg-success">Bàn {{ $table->name }}</span>
                                    @empty
                                        <span class="text-muted">Không có</span>
                                    @endforelse
                                </div>

                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Món</th>
                                            <th width="70">SL</th>
                                            <th width="120">Đơn giá</th>
                                            <th width="140">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subtotal = 0; @endphp

                                        @forelse ($r->reservationItems as $item)
                                            @php
                                                $itemTotal = $item->price * $item->quantity;
                                                $subtotal += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $item->menu->name ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->price) }}₫</td>
                                                <td>{{ number_format($itemTotal) }}₫</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    Không có món ăn
                                                </td>
                                            </tr>
                                        @endforelse

                                        <tr>
                                            <td colspan="3"><strong>Tạm tính</strong></td>
                                            <td><strong>{{ number_format($subtotal) }}₫</strong></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">VAT 8%</td>
                                            <td>{{ number_format($r->vat_amount ?? 0) }}₫</td>
                                        </tr>

                                        @if ($r->voucher_discount > 0)
                                            <tr class="table-success">
                                                <td colspan="3">
                                                    Voucher ({{ $r->voucher->code ?? '' }})
                                                </td>
                                                <td>-{{ number_format($r->voucher_discount) }}₫</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td colspan="3"><strong>Thành tiền</strong></td>
                                            <td class="fw-bold text-danger">
                                                {{ number_format($r->status === 'completed' ? $r->total_amount : 0) }}₫
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                Không có đơn trong khoảng thời gian này
                            </div>
                        @endforelse

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach




    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                            label: 'Doanh thu',
                            data: @json($chartRevenue),
                            borderColor: 'blue',
                            fill: false
                        },
                        {
                            label: 'Thực tế',
                            data: @json($chartRevenueAfterDiscount),
                            borderColor: 'green',
                            fill: false
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
