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
                            
                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ route('admin.banAn.create') }}" class="btn btn-success btn-sm">Thêm mới bàn</a>
                            </div>
                            
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bàn</th>
                                        <th>Số lượng người</th>
                                        <th>Trạng thái bàn</th>
                                        <th>Lịch đặt</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tables as $index => $table)
                                        <tr>
                                            <td>{{ $tables->firstItem() + $index }}</td>
                                            <td><strong>{{ $table->name }}</strong></td>
                                            <td>{{ $table->limit_number }} người</td>

                                            <!-- Trạng thái bàn -->
                                            <td>
                                                @if($table->status == 'active')
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                @endif
                                            </td>

                                            <!-- Lịch đặt bàn -->
                                            <td>
                                                @php
                                                    $busySchedules = $table->reservations()
                                                        ->whereIn('status', ['waiting_payment', 'pending', 'confirmed'])
                                                        ->orderBy('reservation_date')
                                                        ->orderByRaw("FIELD(shift, 'morning', 'afternoon', 'evening')")
                                                        ->get();
                                                @endphp
                                                
                                                @if($busySchedules->count() > 0)
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#scheduleModal{{ $table->id }}">
                                                        <i class="fas fa-calendar-alt"></i> Bận ({{ $busySchedules->count() }})
                                                    </button>
                                                @else
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Rỗi</span>
                                                @endif
                                            </td>

                                            <td>{{ $table->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.banAn.edit', $table->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                                
                                                @if($table->status == 'active')
                                                    <form action="{{ route('admin.banAn.disable', $table->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-secondary btn-sm" 
                                                            onclick="return confirm('Bạn có chắc muốn tạm dừng bàn này?')">
                                                            Tạm dừng
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Chưa có bàn ăn nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $tables->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals - Đặt BÊN NGOÀI table -->
    @if (isset($tables) && count($tables) > 0)
        @foreach ($tables as $table)
            @php
                $busySchedules = $table->reservations()
                    ->whereIn('status', ['waiting_payment', 'pending', 'confirmed'])
                    ->orderBy('reservation_date')
                    ->orderByRaw("FIELD(shift, 'morning', 'afternoon', 'evening')")
                    ->get();
            @endphp
            
            @if($busySchedules->count() > 0)
            <div class="modal fade" id="scheduleModal{{ $table->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h5 class="modal-title">
                                <i class="fas fa-calendar-alt"></i> Lịch đặt - {{ $table->name }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Ca</th>
                                        <th>Khách hàng</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($busySchedules as $schedule)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($schedule->reservation_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @switch($schedule->shift)
                                                    @case('morning')
                                                        <span class="badge badge-info">Sáng (6-11h)</span>
                                                        @break
                                                    @case('afternoon')
                                                        <span class="badge badge-warning">Trưa (11-14h)</span>
                                                        @break
                                                    @case('evening')
                                                        <span class="badge badge-primary">Tối (17-22h)</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $schedule->user->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($schedule->status == 'waiting_payment' || $schedule->status == 'pending')
                                                    <span class="badge badge-warning">Chờ thanh toán</span>
                                                @elseif($schedule->status == 'confirmed')
                                                    <span class="badge badge-success">Đã xác nhận</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

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
