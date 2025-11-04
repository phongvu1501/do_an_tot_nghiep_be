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
                                                        <span class="badge badge-danger badge-lg">
                                                            <i class="fas fa-user"></i> Bận
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $activeReservation->user->name }}
                                                            @if($activeReservation->status == 'pending')
                                                                <span class="badge badge-secondary badge-sm">Chờ xác nhận</span>
                                                            @elseif($activeReservation->status == 'deposit_pending')
                                                                <span class="badge badge-warning badge-sm">Chờ đặt cọc</span>
                                                            @elseif($activeReservation->status == 'deposit_paid')
                                                                <span class="badge badge-info badge-sm">Đã đặt cọc</span>
                                                            @elseif($activeReservation->status == 'serving')
                                                                <span class="badge badge-primary badge-sm">Đang phục vụ</span>
                                                            @endif
                                                        </small>
                                                    @else
                                                        <span class="badge badge-success badge-lg">
                                                            <i class="fas fa-check-circle"></i> Rỗi
                                                        </span>
                                                    @endif
                                                </td>

                                            <td>
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
