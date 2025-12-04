@extends('admin.layouts.main')

@section('noidung')
    <style>
        /* Giới hạn chiều rộng ô và ẩn chữ thừa bằng dấu ... */
        .text-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 320px;
            /* chỉnh theo cần — 320px cho desktop */
            vertical-align: middle;
        }

        /* Với màn hình nhỏ, giảm max-width */
        @media (max-width: 767px) {
            .text-ellipsis {
                max-width: 160px;
            }
        }
    </style>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Thống kê Menu</h1>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('admin.menuStatistics') }}" method="GET" id="filterForm">
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
                                <input type="date" name="from"
                                    value="{{ request('from', optional($from)->format('Y-m-d')) }}"
                                    class="form-control form-control-sm mr-2" id="customFrom">
                                <label class="mr-2">đến</label>
                                <input type="date" name="to"
                                    value="{{ request('to', optional($to)->format('Y-m-d')) }}"
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
                                    ({{ optional($from)->format('d/m/Y') ?? '-' }} -
                                    {{ optional($to)->format('d/m/Y') ?? '-' }})
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- KPI cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalMenus ?? 0 }}</h3>
                                <p>Tổng số món</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h4 class="mb-1">Món bán chạy nhất</h4>
                                @if ($bestSelling)
                                    <p class="mb-0"><strong>{{ $bestSelling['name'] }}</strong></p>
                                    <p class="mb-0">Số lượng: {{ $bestSelling['total_sold'] }}</p>
                                @else
                                    <p>Chưa có dữ liệu</p>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-fastforward"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 col-12">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4 class="mb-1">Món và combo được chọn nhiều nhất</h4>
                                @if ($topCategory)
                                    <p class="mb-0"><strong>{{ $topCategory['name'] }}</strong></p>
                                    <p class="mb-0">Số lượng: {{ $topCategory['total_qty'] }}</p>
                                @else
                                    <p>Chưa có dữ liệu</p>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-people"></i>
                            </div>
                        </div>
                    </div>
                </div>

                

                <!-- (Optional) Full table of revenue (paginated) -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Danh sách món - Doanh thu chi tiết</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Món</th>
                                                <th>Doanh thu (VND)</th>
                                                <th>Số lượng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($revenuePerItem ?? [] as $i => $row)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $row['name'] }}</td>
                                                    <td>{{ number_format($row['revenue'] ?? 0, 0, ',', '.') }}</td>
                                                    <td>{{ $row['total_qty'] ?? 0 }}</td>
                                                </tr>
                                            @endforeach
                                            @if (empty($revenuePerItem))
                                                <tr>
                                                    <td colspan="4" class="text-center">Chưa có dữ liệu</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end rows -->
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Build chart data from PHP $revenuePerItem
        (function() {
            const revenueRows = @json($revenuePerItem ?? []);
            const labels = revenueRows.map(r => r.name);
            const revenueData = revenueRows.map(r => parseFloat(r.revenue ?? 0));
            const qtyData = revenueRows.map(r => parseInt(r.total_qty ?? 0));

            // Revenue chart (bar)
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Doanh thu (VND)',
                                data: revenueData,
                                yAxisID: 'y',
                                borderWidth: 1,
                                // colors are inherited from Chart.js defaults (do not explicitly set to respect guidelines)
                            },
                            {
                                label: 'Số lượng',
                                data: qtyData,
                                yAxisID: 'y1',
                                type: 'line',
                                tension: 0.2,
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Doanh thu (VND)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value);
                                    }
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Số lượng'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: false
                            }
                        }
                    }
                });
            }
        })();

        // Sync filter buttons with date pickers (same UX as reservation UI)
        (function() {
            function formatDate(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }
            const customFrom = document.getElementById('customFrom');
            const customTo = document.getElementById('customTo');
            const customFilter = document.getElementById('customFilter');

            document.querySelectorAll('button[name="filter"]').forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.value;
                    if (filter !== 'custom') {
                        const today = new Date();
                        let fromDate, toDate;
                        switch (filter) {
                            case 'today':
                                fromDate = new Date(today);
                                toDate = new Date(today);
                                break;
                            case 'this_week':
                                const day = today.getDay();
                                const diff = today.getDate() - day + (day === 0 ? -6 : 1);
                                fromDate = new Date(today.getFullYear(), today.getMonth(), diff);
                                toDate = new Date(fromDate);
                                toDate.setDate(fromDate.getDate() + 6);
                                break;
                            case 'this_month':
                                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                                break;
                            case 'this_year':
                                fromDate = new Date(today.getFullYear(), 0, 1);
                                toDate = new Date(today.getFullYear(), 11, 31);
                                break;
                        }
                        if (fromDate && toDate && customFrom && customTo) {
                            customFrom.value = formatDate(fromDate);
                            customTo.value = formatDate(toDate);
                            customFilter.value = 'custom';
                        }
                    }
                });
            });

            // ensure date pickers initialize to server values
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    @if (isset($from))
                        document.getElementById('customFrom').value = '{{ $from->format('Y-m-d') }}';
                    @endif
                    @if (isset($to))
                        document.getElementById('customTo').value = '{{ $to->format('Y-m-d') }}';
                    @endif
                } catch (e) {}
            });
        })();
    </script>
@endpush
