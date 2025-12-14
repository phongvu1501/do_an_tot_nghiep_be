@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">

    <!-- HEADER -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Thống kê Doanh thu</h1>
                </div>
            </div>

            <!-- FILTER -->
            <div class="row mb-3">
                <div class="col-12">
                    <form action="{{ route('admin.thongkeStatistics') }}" method="GET" id="filterForm">
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
                            <h3>{{ number_format($totalRevenue, 0, ',', '.') }}₫</h3>
                            <p>Tổng doanh thu</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-cash"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($totalDeposit, 0, ',', '.') }}₫</h3>
                            <p>Tổng tiền đặt cọc</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-android-clipboard"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($totalVoucherDiscount, 0, ',', '.') }}₫</h3>
                            <p>Tổng giảm giá voucher</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pricetags"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($totalRevenueAfterDiscount, 0, ',', '.') }}₫</h3>
                            <p>Doanh thu thực tế</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ number_format($avgRevenuePerReservation, 0, ',', '.') }}₫</h3>
                            <p>Doanh thu trung bình mỗi đơn</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DAILY REVENUE TABLE -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Doanh thu hàng ngày</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Tổng doanh thu</th>
                                        <th>Tiền đặt cọc</th>
                                        <th>Voucher giảm</th>
                                        <th>Doanh thu thực tế</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dailyStatistics as $stat)
                                    <tr>
                                        <td>{{ $stat['date_display'] }}</td>
                                        <td>{{ number_format($stat['total_revenue'], 0, ',', '.') }}₫</td>
                                        <td>{{ number_format($stat['deposit'], 0, ',', '.') }}₫</td>
                                        <td>{{ number_format($stat['voucher_discount'], 0, ',', '.') }}₫</td>
                                        <td>{{ number_format($stat['revenue_after_discount'], 0, ',', '.') }}₫</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Không có dữ liệu trong khoảng thời gian này
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
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Biểu đồ doanh thu theo ngày</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" style="height: 100px;"></canvas>
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
    const chartRevenue = @json($chartRevenue);
    const chartRevenueAfterDiscount = @json($chartRevenueAfterDiscount);

    const ctx = document.getElementById('revenueChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                    label: 'Tổng doanh thu',
                    data: chartRevenue,
                    borderWidth: 2,
                    borderColor: 'blue',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Doanh thu thực tế',
                    data: chartRevenueAfterDiscount,
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