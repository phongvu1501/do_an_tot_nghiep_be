@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">

        <!-- HEADER -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Thống kê Voucher</h1>
                    </div>
                </div>

                <!-- FILTER -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('admin.voucherStatistics') }}" method="GET" id="filterForm">
                            <div class="btn-group" role="group">
                                <button type="submit" name="filter" value="today"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Hôm nay
                                </button>
                                <button type="submit" name="filter" value="this_week"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tuần này
                                </button>
                                <button type="submit" name="filter" value="this_month"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tháng này
                                </button>
                                <button type="submit" name="filter" value="this_year"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_year' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Năm nay
                                </button>
                            </div>

                            <div class="form-inline mt-2">
                                <label class="mr-2">Tùy chọn:</label>
                                <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                                    class="form-control form-control-sm mr-2" id="customFrom">
                                <label class="mr-2">đến</label>
                                <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}"
                                    class="form-control form-control-sm mr-2" id="customTo">

                                <input type="hidden" name="filter" value="custom" id="customFilter">

                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-search"></i> Áp dụng
                                </button>
                            </div>

                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>{{ $filterLabel ?? 'Tháng này' }}</strong>
                                    ({{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }})
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <section class="content">
            <div class="container-fluid">

                <!-- BOXES -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalVouchersUsed }}</h3>
                                <p>Voucher đã sử dụng</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pricetags"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ count($activeVouchers) }}</h3>
                                <p>Voucher đang hoạt động</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalVoucherCreated ?? 0 }}</h3>
                                <p>Tổng voucher được tạo</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-plus-round"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $expiredVoucher ?? 0 }}</h3>
                                <p>Voucher hết hạn</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-close-round"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LIST OF ACTIVE VOUCHERS -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Voucher đang hoạt động</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Mã</th>
                                            <th>Giảm</th>
                                            <th>Từ ngày</th>
                                            <th>Đến ngày</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activeVouchers as $v)
                                            <tr>
                                                <td>{{ $v->code }}</td>
                                                <td>{{ $v->discount }}%</td>
                                                <td>{{ \Carbon\Carbon::parse($v->start_date)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($v->end_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    @php
                                                        $today = \Carbon\Carbon::today();
                                                        $isActive =
                                                            $v->status === 'active' &&
                                                            $today->greaterThanOrEqualTo($v->start_date) &&
                                                            $today->lessThanOrEqualTo($v->end_date);
                                                    @endphp

                                                    @if ($isActive)
                                                        <span class="badge badge-success">Đang hoạt động</span>
                                                    @elseif ($v->status === 'inactive')
                                                        <span class="badge badge-warning">Tạm dừng</span>
                                                    @elseif ($today->lt($v->start_date))
                                                        <span class="badge badge-secondary">Chưa bắt đầu</span>
                                                    @elseif ($today->gt($v->end_date))
                                                        <span class="badge badge-danger">Đã hết hạn</span>
                                                    @else
                                                        <span class="badge badge-dark">Không xác định</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    Không có voucher nào trong khoảng thời gian này
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHART -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Biểu đồ sử dụng voucher theo ngày</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="voucherChart" style="height: 100;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const chartLabels = @json($chartLabels);
            const chartUsed = @json($chartUsed);
            const chartCreated = @json($chartCreated);
            const chartExpired = @json($chartExpired);

            const ctx = document.getElementById('voucherChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                            label: 'Voucher được tạo',
                            data: chartCreated,
                            borderWidth: 2,
                            borderColor: 'blue',
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Voucher hết hạn',
                            data: chartExpired,
                            borderWidth: 2,
                            borderColor: 'red',
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Voucher được sử dụng',
                            data: chartUsed,
                            borderWidth: 2,
                            borderColor: 'green',
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        </script>
    @endpush
@endsection
