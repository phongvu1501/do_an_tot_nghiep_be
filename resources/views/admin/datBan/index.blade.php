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
                                                <option value="waiting_for_payment" {{ request('status') == 'waiting_for_payment' ? 'selected' : '' }}>Chờ thanh toán</option>
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
                                                        <span class="badge badge-info">Sáng<br><small>6-10h</small></span>
                                                        @break
                                                    @case('afternoon')
                                                        <span class="badge badge-warning">Trưa<br><small>10-14h</small></span>
                                                        @break
                                                    @case('evening')
                                                        <span class="badge badge-success">Chiều<br><small>14-18h</small></span>
                                                        @break
                                                    @case('night')
                                                        <span class="badge badge-primary">Tối<br><small>18-22h</small></span>
                                                        @break
                                                @endswitch
                                            </td>
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
                                                    @case('waiting_for_payment')
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
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal{{ $reservation->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                @if($reservation->status == 'confirmed')
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#invoiceModal{{ $reservation->id }}">
                                                        <i class="fas fa-receipt"></i> Hoàn tất
                                                    </button>
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

    @foreach ($tables as $reservation)
        <div class="modal fade" id="detailModal{{ $reservation->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle"></i> Chi tiết đơn đặt bàn #{{ $reservation->id }}
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
                                    <strong>Tên:</strong> {{ $reservation->user->name }}<br>
                                    <strong>Email:</strong> {{ $reservation->user->email }}<br>
                                    <strong>Số điện thoại:</strong> {{ $reservation->user->phone ?? 'Chưa có' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Thông tin đặt bàn:</h6>
                                <p>
                                    <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}<br>
                                    <strong>Ca:</strong> 
                                    @if($reservation->shift == 'morning') Ca sáng (6-10h)
                                    @elseif($reservation->shift == 'afternoon') Ca trưa (10-14h)
                                    @elseif($reservation->shift == 'evening') Ca chiều (14-18h)
                                    @else Ca tối (18-22h)
                                    @endif
                                    <br>
                                    <strong>Số người:</strong> {{ $reservation->num_people }} người<br>
                                    <strong>Khu vực:</strong> {{ $reservation->depsection ?? 'Không chỉ định' }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Bàn đã gán:</h6>
                        <p>
                            @foreach($reservation->tables as $table)
                                <span class="badge badge-success badge-lg">{{ $table->name }}</span>
                            @endforeach
                            <span class="text-muted">({{ $reservation->tables->count() }} bàn)</span>
                        </p>

                        <hr>

                        <h6 class="font-weight-bold">Món ăn đã đặt:</h6>
                        @if($reservation->menus->count() > 0)
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Món</th>
                                        <th width="80">SL</th>
                                        <th width="120">Đơn giá</th>
                                        <th width="120">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservation->menus as $menu)
                                        <tr>
                                            <td>{{ $menu->name }}</td>
                                            <td class="text-center">{{ $menu->pivot->quantity }}</td>
                                            <td class="text-right">{{ number_format($menu->price, 0, ',', '.') }}đ</td>
                                            <td class="text-right">{{ number_format($menu->price * $menu->pivot->quantity, 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="3" class="text-right">Tổng:</th>
                                        <th class="text-right text-danger">
                                            {{ number_format($reservation->menus->sum(function($m) {
                                                return $m->price * $m->pivot->quantity;
                                            }), 0, ',', '.') }}đ
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

        @if($reservation->status == 'confirmed')
        <div class="modal fade" id="invoiceModal{{ $reservation->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice"></i> Hóa đơn thanh toán
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="font-weight-bold">Thông tin đặt bàn:</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="150"><strong>Khách hàng:</strong></td>
                                <td>{{ $reservation->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $reservation->user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ngày đặt:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ca:</strong></td>
                                <td>
                                    @if($reservation->shift == 'morning') Ca sáng (6h-11h)
                                    @elseif($reservation->shift == 'afternoon') Ca trưa (11h-14h)
                                    @else Ca tối (17h-22h)
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Số người:</strong></td>
                                <td>{{ $reservation->num_people }} người</td>
                            </tr>
                            <tr>
                                <td><strong>Bàn:</strong></td>
                                <td>
                                    @foreach($reservation->tables as $table)
                                        <span class="badge badge-success">{{ $table->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        </table>

                        <hr>

                        <h6 class="font-weight-bold">Chi tiết món ăn:</h6>
                        @if($reservation->menus->count() > 0)
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
                                    @foreach($reservation->menus as $menu)
                                        <tr>
                                            <td>{{ $menu->name }}</td>
                                            <td class="text-center">{{ $menu->pivot->quantity }}</td>
                                            <td class="text-right">{{ number_format($menu->price, 0, ',', '.') }}đ</td>
                                            <td class="text-right"><strong>{{ number_format($menu->price * $menu->pivot->quantity, 0, ',', '.') }}đ</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="3" class="text-right">Tổng tiền món ăn:</th>
                                        <th class="text-right text-danger">
                                            {{ number_format($reservation->menus->sum(function($menu) {
                                                return $menu->price * $menu->pivot->quantity;
                                            }), 0, ',', '.') }}đ
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-muted">Khách chưa đặt món trước.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                            <input type="hidden" name="status" value="completed">
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
@endsection
