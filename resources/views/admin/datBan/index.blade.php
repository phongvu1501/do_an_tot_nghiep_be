@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Trang quản lý đặt bàn</h3>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <form action="{{ route('admin.datBan.index') }}" method="GET" class="row">
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Ngày đặt</label>
                                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold">Ca</label>
                                            <select name="shift" class="form-control">
                                                <option value="">Tất cả ca</option>
                                                <option value="morning" {{ request('shift') == 'morning' ? 'selected' : '' }}>Ca sáng</option>
                                                <option value="afternoon" {{ request('shift') == 'afternoon' ? 'selected' : '' }}>Ca trưa</option>
                                                <option value="evening" {{ request('shift') == 'evening' ? 'selected' : '' }}>Ca tối</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Trạng thái</label>
                                            <select name="status" class="form-control">
                                                <option value="">Tất cả trạng thái</option>
                                                <option value="waiting_payment" {{ request('status') == 'waiting_payment' ? 'selected' : '' }}>Chờ thanh toán</option>
                                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-filter"></i> Lọc
                                            </button>
                                            <a href="{{ route('admin.datBan.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo"></i> Reset
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Ca</th>
                                        <th>Số người</th>
                                        <th>Bàn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tables as $index => $reservation)
                                        <tr>
                                            <td>{{ $tables->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $reservation->user->name }}</strong><br>
                                                <small class="text-muted">{{ $reservation->user->email }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @switch($reservation->shift)
                                                    @case('morning')
                                                        <span class="badge badge-info">Ca sáng<br><small>6h-11h</small></span>
                                                        @break
                                                    @case('afternoon')
                                                        <span class="badge badge-warning">Ca trưa<br><small>11h-14h</small></span>
                                                        @break
                                                    @case('evening')
                                                        <span class="badge badge-primary">Ca tối<br><small>17h-22h</small></span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td><strong>{{ $reservation->num_people }}</strong> người</td>
                                            <td>
                                                @if ($reservation->tables->isEmpty())
                                                    <span class="badge badge-danger">Chưa có bàn</span>
                                                @else
                                                    @foreach($reservation->tables as $table)
                                                        <span class="badge badge-success">{{ $table->name }}</span>
                                                    @endforeach
                                                    <br>
                                                    <small class="text-muted">({{ $reservation->tables->count() }} bàn)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($reservation->status)
                                                    @case('waiting_payment')
                                                        <span class="badge badge-warning">Chờ thanh toán</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge badge-warning">Chờ thanh toán</span>
                                                        @break
                                                    @case('confirmed')
                                                        <span class="badge badge-success">Đã xác nhận</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge badge-info">Hoàn tất</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Đã hủy</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($reservation->status == 'confirmed')
                                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn btn-success btn-sm">Hoàn tất</button>
                                                    </form>
                                                @endif
                                                
                                                @if($reservation->status != 'cancelled' && $reservation->status != 'completed')
                                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hủy?')">Hủy</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Chưa có đơn đặt bàn nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $tables->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
