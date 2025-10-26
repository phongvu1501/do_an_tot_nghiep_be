@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ $title }}</h3>
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
                            
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Ca</th>
                                        <th>Số người</th>
                                        <th>Bàn đã gán</th>
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
                                                    @default
                                                        {{ $reservation->shift }}
                                                @endswitch
                                            </td>
                                            <td><strong>{{ $reservation->num_people }}</strong> người</td>
                                            
                                            <!-- Bàn đã được tự động gán -->
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
                                                    @case('pending')
                                                        <span class="badge badge-warning">Chờ xác nhận</span>
                                                        @break
                                                    @case('confirmed')
                                                        <span class="badge badge-success">Đã xác nhận</span>
                                                        @break
                                                    @case('serving')
                                                        <span class="badge badge-primary">Đang phục vụ</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge badge-secondary">Hoàn tất</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Đã hủy</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-light">{{ $reservation->status }}</span>
                                                @endswitch
                                            </td>
                                            
                                            <td>
                                                @if($reservation->status == 'pending')
                                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="btn btn-success btn-sm" 
                                                            onclick="return confirm('Xác nhận đơn đặt bàn này?')">
                                                            Xác nhận
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Hủy đơn đặt bàn này?')">
                                                            Hủy
                                                        </button>
                                                    </form>
                                                @elseif($reservation->status == 'confirmed')
                                                    <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                        <input type="hidden" name="status" value="serving">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            Phục vụ
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
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

                            <!-- Phân trang -->
                            <div class="mt-3">
                                {{ $tables->links() }}
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
