@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Thống kê đặt bàn</h1>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('admin.reservationStatistics') }}" method="GET" id="filterForm">
                            <div class="btn-group" role="group">
                                <button type="submit" name="filter" value="today" class="btn btn-sm {{ ($filterType ?? 'this_month') == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Hôm nay
                                </button>
                                <button type="submit" name="filter" value="this_week" class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tuần này
                                </button>
                                <button type="submit" name="filter" value="this_month" class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tháng này
                                </button>
                                <button type="submit" name="filter" value="this_year" class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_year' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Năm nay
                                </button>
                            </div>
                            
                            <div class="form-inline mt-2">
                                <label class="mr-2">Tùy chọn:</label>
                                <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}" class="form-control form-control-sm mr-2" id="customFrom">
                                <label class="mr-2">đến</label>
                                <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}" class="form-control form-control-sm mr-2" id="customTo">
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

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalReservationsInPeriod ?? 0 }}</h3>
                                <p>Tổng đơn đặt bàn</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-calendar"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $totalCompleted }}</h3>
                                <p>Đơn hoàn thành</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark-circled"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $totalCancelled }}</h3>
                                <p>Đơn đã hủy</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-close-circled"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalGuests ?? 0 }}</h3>
                                <p>Tổng số lượng khách đã phục vụ</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-stalker"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $cancellationRate }}%</h3>
                                <p>Tỷ lệ hủy</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $avgNumPeople }}</h3>
                                <p>Số khách trung bình/đơn</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">   
                                <h3 class="card-title">Thống kê theo ca ({{ $filterLabel ?? 'Tháng này' }})</h3>
                                <small class="text-muted">
                                    Tổng: {{ ($morningCountPeriod ?? 0) + ($afternoonCountPeriod ?? 0) + ($eveningCountPeriod ?? 0) }} đơn
                                    | Tổng số khách: {{ ($morningPeoplePeriod ?? 0) + ($afternoonPeoplePeriod ?? 0) + ($eveningPeoplePeriod ?? 0) }} người
                                </small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info elevation-1">
                                                <i class="fas fa-sun"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca sáng<br><small>8-13h</small></span>
                                                <span class="info-box-number">{{ $morningCountPeriod ?? 0 }} đơn</span>
                                                <span class="info-box-text"><small>{{ $morningPeoplePeriod ?? 0 }} người</small></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success elevation-1">
                                                <i class="fas fa-cloud-sun"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca trưa<br><small>13-18h</small></span>
                                                <span class="info-box-number">{{ $afternoonCountPeriod ?? 0 }} đơn</span>
                                                <span class="info-box-text"><small>{{ $afternoonPeoplePeriod ?? 0 }} người</small></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-dark elevation-1">
                                                <i class="fas fa-moon"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca tối<br><small>18-23h</small></span>
                                                <span class="info-box-number">{{ $eveningCountPeriod ?? 0 }} đơn</span>
                                                <span class="info-box-text"><small>{{ $eveningPeoplePeriod ?? 0 }} người</small></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Thống kê đơn theo ngày ({{ $filterLabel ?? 'Tháng này' }})</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="reservationChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Thống kê số khách đã phục vụ theo ngày ({{ $filterLabel ?? 'Tháng này' }})</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="guestsChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Thống kê tổng quan theo ca (Tất cả thời gian)</h3>
                                <small class="text-muted">
                                    Tổng: {{ ($morningCount ?? 0) + ($afternoonCount ?? 0) + ($eveningCount ?? 0) }} đơn
                                    | Tổng số khách: {{ ($morningPeople ?? 0) + ($afternoonPeople ?? 0) + ($eveningPeople ?? 0) }} người
                                </small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info elevation-1">
                                                <i class="fas fa-sun"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca sáng<br><small>8-13h</small></span>
                                                <span class="info-box-number">{{ $morningCount ?? 0 }} đơn</span>
                                                <span class="info-box-text"><small>{{ $morningPeople ?? 0 }} người</small></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success elevation-1">
                                                <i class="fas fa-cloud-sun"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca trưa<br><small>13-18h</small></span>
                                                <span class="info-box-number">{{ $afternoonCount ?? 0 }} đơn</span>
                                                <span class="info-box-text"><small>{{ $afternoonPeople ?? 0 }} người</small></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-dark elevation-1">
                                                <i class="fas fa-moon"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca tối<br><small>18-23h</small></span>
                                                <span class="info-box-number">{{ $eveningCount ?? 0 }} đơn</span>
                                                <span class="info-box-text"><small>{{ $eveningPeople ?? 0 }} người</small></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        @if(isset($chartLabels) && isset($chartData))
        const ctx = document.getElementById('reservationChart');
        if (ctx) {
            const reservationChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Tổng đơn',
                        data: @json($chartData),
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Hoàn thành',
                        data: @json($chartCompleted),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Đã hủy',
                        data: @json($chartCancelled),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Thống kê đơn đặt bàn theo ngày'
                    }
                }
            }
        });
        }
        @endif
        
        @if(isset($chartLabels) && isset($chartGuestsMorning) && isset($chartGuestsAfternoon) && isset($chartGuestsEvening))
        const guestsCtx = document.getElementById('guestsChart');
        if (guestsCtx) {
            const guestsChart = new Chart(guestsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Ca sáng (8-13h)',
                            data: @json($chartGuestsMorning),
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 1
                        },
                        {
                            label: 'Ca trưa (13-18h)',
                            data: @json($chartGuestsAfternoon),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 1
                        },
                        {
                            label: 'Ca tối (18-23h)',
                            data: @json($chartGuestsEvening),
                            backgroundColor: 'rgba(153, 102, 255, 0.6)',
                            borderColor: 'rgb(153, 102, 255)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Số lượng khách'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Ngày'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Thống kê số khách đã phục vụ theo ca và ngày'
                        }
                    }
                }
            });
        }
        @endif
        
        document.getElementById('customFrom').addEventListener('change', function() {
            document.getElementById('customFilter').value = 'custom';
        });
        document.getElementById('customTo').addEventListener('change', function() {
            document.getElementById('customFilter').value = 'custom';
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const customFrom = document.getElementById('customFrom');
            const customTo = document.getElementById('customTo');
            
            const serverFrom = '{{ $from->format("Y-m-d") }}';
            const serverTo = '{{ $to->format("Y-m-d") }}';
            
            if (customFrom && customTo) {
                customFrom.value = serverFrom;
                customTo.value = serverTo;
            }
        });
        
        document.querySelectorAll('button[name="filter"]').forEach(button => {
            button.addEventListener('click', function(e) {
                const filterType = this.value;
                if (filterType !== 'custom') {
                    const customFrom = document.getElementById('customFrom');
                    const customTo = document.getElementById('customTo');
                    const serverFrom = '{{ $from->format("Y-m-d") }}';
                    const serverTo = '{{ $to->format("Y-m-d") }}';
                    
                    const today = new Date();
                    let fromDate, toDate;
                    
                    switch(filterType) {
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
                    
                    if (fromDate && toDate) {
                        const formatDate = (date) => {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        };
                        
                        customFrom.value = formatDate(fromDate);
                        customTo.value = formatDate(toDate);
                    }
                }
            });
        });
        
        document.querySelectorAll('button[name="filter"]').forEach(button => {
            button.addEventListener('click', function(e) {
                const filterType = this.value;
                if (filterType !== 'custom') {
                    updateDatePicker(filterType);
                }
            });
        });
    </script>
    @endpush
@endsection

