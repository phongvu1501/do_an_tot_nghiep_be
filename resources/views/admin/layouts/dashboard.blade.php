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
                <div class="row">

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
                                <h3>{{ $totalRevenue }}</h3>
                                <p>Doanh số</p>
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

@endsection
