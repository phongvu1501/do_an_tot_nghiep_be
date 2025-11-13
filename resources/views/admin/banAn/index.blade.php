@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $banAn }}</h3>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            
                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <form action="{{ route('admin.banAn.index') }}" method="GET" class="row" id="filterForm">
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Ngày</label>
                                            <input type="date" name="date" class="form-control" value="{{ $filterDate }}" onchange="document.getElementById('filterForm').submit()">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Ca</label>
                                            <select name="shift" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                                <option value="morning" {{ $filterShift == 'morning' ? 'selected' : '' }}>Ca sáng (6-10h)</option>
                                                <option value="afternoon" {{ $filterShift == 'afternoon' ? 'selected' : '' }}>Ca trưa (10-14h)</option>
                                                <option value="evening" {{ $filterShift == 'evening' ? 'selected' : '' }}>Ca chiều (14-18h)</option>
                                                <option value="night" {{ $filterShift == 'night' ? 'selected' : '' }}>Ca tối (18-22h)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end justify-content-end">
                                            <a href="{{ route('admin.banAn.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus"></i> Thêm bàn
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <table class="table table-bordered table-hover">
                                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bàn</th>
                                        <th>Tình trạng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tables as $index => $table)
                                        <tr>
                                            <td>{{ $tables->firstItem() + $index }}</td>
                                            <td><strong>{{ $table->name }}</strong></td>

                                            <!-- Tình trạng bàn theo ca -->
                                            <td>
                                                    @php
                                                        // Bàn BẬN khi có reservation với status khác cancelled và completed
                                                        $activeReservation = $table->reservations()
                                                            ->where('reservation_date', $filterDate)
                                                            ->where('shift', $filterShift)
                                                            ->whereNotIn('status', ['cancelled', 'completed'])
                                                            ->with('user')
                                                            ->first();
                                                    @endphp
                                                    
                                                    @if($activeReservation)
                                                        @if($activeReservation->status == 'pending')
                                                            <span class="badge badge-secondary badge-lg">
                                                                <i class="fas fa-clock"></i> Chờ xác nhận
                                                            </span>
                                                        @elseif($activeReservation->status == 'deposit_pending')
                                                            <span class="badge badge-warning badge-lg">
                                                                <i class="fas fa-credit-card"></i> Chờ đặt cọc
                                                            </span>
                                                        @elseif($activeReservation->status == 'deposit_paid')
                                                            <span class="badge badge-info badge-lg">
                                                                <i class="fas fa-check-circle"></i> Đã đặt cọc
                                                            </span>
                                                        @elseif($activeReservation->status == 'serving')
                                                            <span class="badge badge-primary badge-lg">
                                                                <i class="fas fa-concierge-bell"></i> Đang phục vụ
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger badge-lg">
                                                                <i class="fas fa-user"></i> Bận
                                                            </span>
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">{{ $activeReservation->user->name }}</small>
                                                    @else
                                                        <span class="badge badge-success badge-lg">
                                                            <i class="fas fa-check-circle"></i> Rỗi
                                                        </span>
                                                    @endif
                                                </td>

                                            <td>
                                                @if($activeReservation)
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reservationDetailModal{{ $table->id }}">
                                                        <i class="fas fa-eye"></i> Chi tiết
                                                    </button>
                                                    
                                                    @if($activeReservation->status == 'deposit_paid')
                                                        <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            <input type="hidden" name="reservation_id" value="{{ $activeReservation->id }}">
                                                            <input type="hidden" name="status" value="serving">
                                                            <input type="hidden" name="redirect_to" value="banAn">
                                                            <input type="hidden" name="filter_date" value="{{ $filterDate }}">
                                                            <input type="hidden" name="filter_shift" value="{{ $filterShift }}">
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-concierge-bell"></i> Phục vụ
                                                            </button>
                                                        </form>
                                                    @elseif($activeReservation->status == 'serving')
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#invoiceModal{{ $table->id }}">
                                                            <i class="fas fa-receipt"></i> Hoàn tất
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                                
                                                <!-- Comment lại chức năng sửa/xóa bàn
                                                <a href="{{ route('admin.banAn.edit', $table->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                
                                                <form action="{{ route('admin.banAn.destroy', $table->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Bạn có chắc muốn xóa bàn này?')">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                                -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Chưa có bàn ăn nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
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

    <!-- Modals Chi tiết đơn đặt bàn -->
    @foreach ($tables as $table)
        @php
            $activeReservation = $table->reservations()
                ->where('reservation_date', $filterDate)
                ->where('shift', $filterShift)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->with(['user', 'reservationItems.menu'])
                ->first();
        @endphp

        @if($activeReservation)
        <div class="modal fade" id="reservationDetailModal{{ $table->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle"></i> Chi tiết đơn đặt bàn - {{ $table->name }}
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
                                    <strong>Tên:</strong> {{ $activeReservation->user->name }}<br>
                                    <strong>Email:</strong> {{ $activeReservation->user->email }}<br>
                                    <strong>SĐT:</strong> {{ $activeReservation->user->phone ?? 'Chưa có' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Thông tin đặt bàn:</h6>
                                <p>
                                    <strong>Mã đơn:</strong> {{ $activeReservation->reservation_code ?? '#'.$activeReservation->id }}<br>
                                    <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($activeReservation->reservation_date)->format('d/m/Y') }}<br>
                                    <strong>Ca:</strong>
                                    @if($activeReservation->shift == 'morning') Ca sáng (6-10h)
                                    @elseif($activeReservation->shift == 'afternoon') Ca trưa (10-14h)
                                    @elseif($activeReservation->shift == 'evening') Ca chiều (14-18h)
                                    @else Ca tối (18-22h)
                                    @endif
                                    <br>
                                    <strong>Số người:</strong> {{ $activeReservation->num_people }} người<br>
                                    <strong>Ghi chú:</strong> {{ $activeReservation->depsection ?? '-' }}<br>
                                    <strong>Trạng thái:</strong>
                                    @if($activeReservation->status == 'pending')
                                        <span class="badge badge-secondary">Chờ xác nhận</span>
                                    @elseif($activeReservation->status == 'deposit_pending')
                                        <span class="badge badge-warning">Chờ đặt cọc</span>
                                    @elseif($activeReservation->status == 'deposit_paid')
                                        <span class="badge badge-info">Đã đặt cọc</span>
                                    @elseif($activeReservation->status == 'serving')
                                        <span class="badge badge-primary">Đang phục vụ</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Bàn đã gán:</h6>
                        <div class="mb-3">
                            @foreach($activeReservation->tables as $t)
                                <span class="badge badge-success badge-lg">{{ $t->name }}</span>
                            @endforeach
                            <span class="text-muted">({{ $activeReservation->tables->count() }} bàn)</span>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Món ăn đã đặt:</h6>
                        @if($activeReservation->reservationItems->count() > 0)
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Món</th>
                                        <th width="80">SL</th>
                                        <th width="120">Đơn giá</th>
                                        <th width="120">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeReservation->reservationItems as $item)
                                        <tr>
                                            <td>{{ $item->menu->name }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                            <td class="text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    @php
                                        $totalMenuPrice = $activeReservation->reservationItems->sum(function($item) {
                                            return $item->price * $item->quantity;
                                        });
                                        $depositPaid = $activeReservation->deposit ?? 0;
                                    @endphp
                                    <tr>
                                        <th colspan="3" class="text-right">Tổng tiền món ăn:</th>
                                        <th class="text-right">{{ number_format($totalMenuPrice, 0, ',', '.') }}đ</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Tiền cọc đã trả:</th>
                                        <th class="text-right text-success">- {{ number_format($depositPaid, 0, ',', '.') }}đ</th>
                                    </tr>
                                    <tr class="bg-warning">
                                        <th colspan="3" class="text-right">Còn phải thu:</th>
                                        <th class="text-right text-danger">
                                            <strong>{{ number_format($totalMenuPrice - $depositPaid, 0, ',', '.') }}đ</strong>
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

        <!-- Modal Hóa đơn thanh toán -->
        @php
            $servingReservation = $table->reservations()
                ->where('reservation_date', $filterDate)
                ->where('shift', $filterShift)
                ->where('status', 'serving')
                ->with(['user', 'reservationItems.menu'])
                ->first();
        @endphp

        @if($servingReservation)
        <div class="modal fade" id="invoiceModal{{ $table->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice"></i> Hóa đơn thanh toán - {{ $table->name }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="font-weight-bold">Chi tiết món ăn:</h6>
                        @if($servingReservation->reservationItems->count() > 0)
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
                                    @foreach($servingReservation->reservationItems as $item)
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
                                        $totalMenuPrice = $servingReservation->reservationItems->sum(function($item) {
                                            return $item->price * $item->quantity;
                                        });
                                        $depositPaid = $servingReservation->deposit ?? 0;
                                        $remainingAmount = $totalMenuPrice - $depositPaid;
                                    @endphp
                                    <tr>
                                        <th colspan="3" class="text-right">Tổng tiền món ăn:</th>
                                        <th class="text-right">
                                            {{ number_format($totalMenuPrice, 0, ',', '.') }}đ
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Tiền cọc đã trả:</th>
                                        <th class="text-right text-success">
                                            - {{ number_format($depositPaid, 0, ',', '.') }}đ
                                        </th>
                                    </tr>
                                    <tr class="bg-warning">
                                        <th colspan="3" class="text-right">Còn phải thanh toán:</th>
                                        <th class="text-right text-danger">
                                            <h5 class="mb-0">{{ number_format($remainingAmount, 0, ',', '.') }}đ</h5>
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
                        <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="reservation_id" value="{{ $servingReservation->id }}">
                            <input type="hidden" name="status" value="completed">
                            <input type="hidden" name="redirect_to" value="banAn">
                            <input type="hidden" name="filter_date" value="{{ $filterDate }}">
                            <input type="hidden" name="filter_shift" value="{{ $filterShift }}">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Xác nhận hoàn tất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <script>
        $(document).ready(function() {
            if ($('#success-alert').length) {
                setTimeout(function() {
                    $('#success-alert').fadeOut('slow');
                }, 3000);
            }
        });
    </script>
@endsection
