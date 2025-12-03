@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">

        <!-- HEADER -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $dashboard }}</h1>
                    </div>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="form-inline">
                        <div class="form-group mb-2">
                            <label>Từ: </label>
                            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                                class="form-control ml-2">
                        </div>

                        <div class="form-group mb-2 ml-3">
                            <label>Đến: </label>
                            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                                class="form-control ml-2">
                        </div>

                        <button type="submit" class="btn btn-primary mb-2 ml-3">Filter</button>
                    </form>

                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <section class="content">
            <div class="container-fluid">
                <ul class="nav nav-tabs" id="statisticsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">
                            <i class="fas fa-chart-line"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="reservation-tab" data-toggle="tab" href="#reservation" role="tab" aria-controls="reservation" aria-selected="false">
                            <i class="fas fa-calendar-check"></i> Thống kê đặt bàn
                        </a>
                    </li>
                </ul>

                <!-- TABS CONTENT -->
                <div class="tab-content" id="statisticsTabsContent">
                    <!-- TAB: TỔNG QUAN -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="row mt-3">
                            <!-- TOTAL RESERVATIONS -->
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $totalReservations }}</h3>
                                        <p>Tổng đơn đặt</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>

                                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#reservationsModal">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>

                            <!-- TOTAL CANCELLED -->
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $totalCancelled }}</h3>
                                        <p>Tổng đơn hủy</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>

                                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#cancelledModal">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>

                            <!-- NEW USERS -->
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $newUsers }}</h3>
                                        <p>Tài khoản mới</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-person-add"></i>
                                    </div>

                                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#newUsersModal">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>

                            <!-- TOTAL REVENUE -->
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ number_format($totalRevenue) }}</h3>
                                        <p>Doanh số (VNĐ)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-pie-graph"></i>
                                    </div>

                                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#revenueModal">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: THỐNG KÊ ĐẶT BÀN -->
                    <div class="tab-pane fade" id="reservation" role="tabpanel" aria-labelledby="reservation-tab">
                        <!-- ============================== -->
                        <!--  THỐNG KÊ ĐẶT BÀN CHI TIẾT     -->
                        <!-- ============================== -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="mb-3">Thống kê đặt bàn</h3>
                    </div>
                </div>

                <div class="row">
                    <!-- Đơn đặt bàn hôm nay -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalToday ?? 0 }}</h3>
                                <p>Đơn đặt bàn hôm nay</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-calendar"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn đặt bàn trong tháng -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalThisMonth ?? 0 }}</h3>
                                <p>Đơn đặt bàn trong tháng</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn hoàn thành (tất cả) -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $totalCompletedAll ?? 0 }}</h3>
                                <p>Đơn hoàn thành (tất cả)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark-circled"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn đã hủy (tất cả) -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $totalCancelledAll ?? 0 }}</h3>
                                <p>Đơn đã hủy (tất cả)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-close-circled"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalPending ?? 0 }}</h3>
                                <p>Đơn chờ xác nhận</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $cancellationRate ?? 0 }}%</h3>
                                <p>Tỷ lệ hủy</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $avgNumPeople ?? 0 }}</h3>
                                <p>Số khách trung bình/đơn</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-stalker"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Thống kê theo ca ({{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }})</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info elevation-1">
                                                <i class="fas fa-sun"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca sáng</span>
                                                <span class="info-box-number">{{ $morningCountPeriod ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success elevation-1">
                                                <i class="fas fa-cloud-sun"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca trưa</span>
                                                <span class="info-box-number">{{ $afternoonCountPeriod ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning elevation-1">
                                                <i class="fas fa-moon"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ca tối</span>
                                                <span class="info-box-number">{{ $eveningCountPeriod ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Thống kê đơn đặt bàn theo ngày</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="reservationChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng thống kê chi tiết theo ngày -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Chi tiết thống kê theo ngày</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Tổng đơn</th>
                                            <th>Hoàn thành</th>
                                            <th>Đã hủy</th>
                                            <th>Chờ xác nhận</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($dailyStatistics))
                                            @foreach ($dailyStatistics as $stat)
                                                <tr>
                                                    <td>{{ $stat['date_display'] }}</td>
                                                    <td><span class="badge badge-info">{{ $stat['count'] }}</span></td>
                                                    <td><span class="badge badge-success">{{ $stat['completed'] }}</span></td>
                                                    <td><span class="badge badge-danger">{{ $stat['cancelled'] }}</span></td>
                                                    <td><span class="badge badge-warning">{{ $stat['pending'] }}</span></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                    <!-- END TAB: THỐNG KÊ ĐẶT BÀN -->
                </div>
                <!-- END TABS CONTENT -->

            </div>
        </section>
    </div>

    <!-- ============================== -->
    <!--  MODAL: TỔNG ĐƠN ĐẶT          -->
    <!-- ============================== -->
    <div class="modal fade" id="reservationsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Danh sách đơn đặt</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    @if (isset($reservationsList) && $reservationsList->count())
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Khách hàng</th>
                                    <th>Số người</th>
                                    <th>Ngày giờ đặt</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($reservationsList as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>{{ $item->user->name ?? 'Không có' }}</td>

                                        <td>{{ $item->num_people }}</td>

                                        <td>{{ \Carbon\Carbon::parse($item->reservation_date)->format('d/m/Y') }}
                                            {{ \Carbon\Carbon::parse($item->reservation_time)->format('H:i') }}
                                        </td>

                                        <td>{{ $item->depsection ?? '—' }}</td>
                                        @php
                                            $statusColors = [
                                                'cancelled' => 'badge badge-danger',
                                                'pending' => 'badge badge-warning',
                                                'deposit_pending' => 'badge badge-warning',
                                                'deposit_paid' => 'badge badge-info',
                                                'confirmed' => 'badge badge-primary',
                                                'serving' => 'badge badge-secondary',
                                                'completed' => 'badge badge-success',
                                                'waiting_for_payment' => 'badge badge-warning',
                                                'suspended' => 'badge badge-dark',
                                            ];

                                            $statusLabels = [
                                                'cancelled' => 'Đã hủy',
                                                'pending' => 'Chờ xác nhận',
                                                'deposit_pending' => 'Chờ đặt cọc',
                                                'deposit_paid' => 'Đã đặt cọc',
                                                'confirmed' => 'Đã xác nhận',
                                                'serving' => 'Đang phục vụ',
                                                'completed' => 'Hoàn tất',
                                                'waiting_for_payment' => 'Chờ thanh toán',
                                                'suspended' => 'Tạm dừng',
                                            ];
                                        @endphp

                                        <td>
                                            <span class="{{ $statusColors[$item->status] ?? 'badge badge-light' }}">
                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                            </span>
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Không có đơn đặt nào.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>



    <!-- ============================== -->
    <!--  MODAL: ĐƠN HỦY                -->
    <!-- ============================== -->
    <div class="modal fade" id="cancelledModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Danh sách đơn hủy</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    @if (isset($cancelledList) && $cancelledList->count())
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Khách hàng</th>
                                    <th>Số người</th>
                                    <th>Ngày giờ đặt</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($cancelledList as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->user->name ?? 'Không có' }}</td>
                                        <td>{{ $item->num_people }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($item->reservation_date)->format('d/m/Y') }}
                                            {{ \Carbon\Carbon::parse($item->reservation_time)->format('H:i') }}
                                        </td>
                                        <td>{{ $item->depsection ?? '—' }}</td>
                                        @php
                                            $statusColors = [
                                                'cancelled' => 'badge badge-danger',
                                            ];

                                            $statusLabels = [
                                                'cancelled' => 'Đã hủy',
                                            ];
                                        @endphp

                                        <td>
                                            <span class="{{ $statusColors[$item->status] ?? 'badge badge-light' }}">
                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                            </span>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Không có đơn hủy nào.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!--  MODAL: USER MỚI               -->
    <!-- ============================== -->
    <div class="modal fade" id="newUsersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Danh sách tài khoản mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    @if (isset($listUsers) && $listUsers->count())
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Tên</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listUsers as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->name ?? '—' }}</td>
                                        <td>{{ $user->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Không có tài khoản mới.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!--  MODAL: DOANH THU              -->
    <!-- ============================== -->
    <div class="modal fade" id="revenueModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Chi tiết doanh thu</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    @if (isset($revenueList) && $revenueList->count())
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày thanh toán</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($revenueList as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->customer_name ?? '—' }}</td>
                            <td>{{ number_format($item->total_price) }} đ</td>
                            <td>{{ $item->updated_at }}</td>
                        </tr>
                        @endforeach --}}
                            </tbody>
                        </table>
                    @else
                        <p>Không có dữ liệu doanh thu.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ thống kê đơn đặt bàn theo ngày
        @if(isset($reservationChartLabels) && isset($reservationChartData) && isset($reservationChartCompleted) && isset($reservationChartCancelled) && isset($reservationChartPending))
        const ctx = document.getElementById('reservationChart');
        if (ctx) {
            const reservationChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($reservationChartLabels),
                    datasets: [
                        {
                            label: 'Tổng đơn',
                            data: @json($reservationChartData),
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Hoàn thành',
                            data: @json($reservationChartCompleted),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Đã hủy',
                            data: @json($reservationChartCancelled),
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Chờ xác nhận',
                            data: @json($reservationChartPending),
                            borderColor: 'rgb(255, 206, 86)',
                            backgroundColor: 'rgba(255, 206, 86, 0.1)',
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
    </script>
    @endpush

@endsection