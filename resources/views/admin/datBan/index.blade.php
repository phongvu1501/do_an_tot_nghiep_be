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
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã hóa đơn</th>
                                        <th>Họ và tên</th>
                                        <th>Ngày đặt</th>
                                        <th>Giờ đặt</th>
                                        <th>Món ăn</th>
                                        <th>Bàn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($tables) && count($tables) > 0)
                                        @foreach ($tables as $index => $reservation)
                                            <tr>
                                                <td>{{ $reservation->order_code }}</td>
                                                <td>{{ $reservation->user->name }}</td>
                                                <td>{{ $reservation->reservation_date }}</td>
                                                <td>{{ $reservation->reservation_time }}</td>

                                                <!-- Button to trigger Modal -->
                                                <td>
                                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#menuModal{{ $reservation->id }}">
                                                        Xem món
                                                    </button>
                                                </td>
                                                <!-- Button to trigger Modal for selecting table -->
                                                <td>
                                                    <button
                                                        class="btn btn-info btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#selectTableModal{{ $reservation->id }}"
                                                        @if ($reservation->status != 'pending') disabled @endif>
                                                        {{ $reservation->tables->isEmpty() ? 'Chưa chọn' : 'Bàn ' . $reservation->tables->first()->table_number }}
                                                    </button>
                                                </td>
                                                <td>
                                                    @switch($reservation->status)
                                                        @case('pending')
                                                            Chờ xác nhận
                                                            @break
                                                        @case('confirmed')
                                                            Đã xác nhận
                                                            @break
                                                        @case('serving')
                                                            Đang phục vụ
                                                            @break
                                                        @case('completed')
                                                            Hoàn tất
                                                            @break
                                                        @case('cancelled')
                                                            Hủy
                                                            @break
                                                        @case('suspended')
                                                            Tạm dừng
                                                            @break
                                                        @default
                                                            Không xác định
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.datBan.show', $reservation->id) }}" class="btn btn-info btn-sm">
                                                        <i class="nav-icon fas fa-eye"></i>
                                                    </a>
                                                    @switch($reservation->status)
                                                        @case('pending')
                                                            <!-- Hành động xác nhận và hủy -->
    @if ($reservation->tables->isEmpty())
        <button type="button" class="btn btn-success btn-sm" disabled>Chưa chọn bàn</button>
    @else
        <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
            <input type="hidden" name="status" value="confirmed">
            <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
        </form>
    @endif

                                                            @break
                                                        @case('confirmed')
                                                            <!-- Hành động bắt đầu phục vụ và hủy -->
                                                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                                <input type="hidden" name="status" value="serving">
                                                                <button type="submit" class="btn btn-warning btn-sm">Bắt đầu phục vụ</button>
                                                            </form>
                                                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                                                            </form>
                                                            @break
                                                        @case('serving')
                                                            <!-- Hành động hoàn tất hoặc tạm dừng -->
                                                            <!-- <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="btn btn-success btn-sm">Hoàn tất</button>
                                                            </form> -->
                                                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                                <input type="hidden" name="status" value="suspended">
                                                                <button type="submit" class="btn btn-secondary btn-sm">Tạm dừng</button>
                                                            </form>
                                                            <button class="button btn-warning"><i class="fas fa-money-check-alt"></i></button>
                                                            @break
                                                        @case('waiting_for_payment')
                                                            @break
                                                        @case('completed')
                                                            @break
                                                        @case('cancelled')
                                                            @break
                                                        @case('suspended')
                                                            <!-- Hành động tiếp tục hoặc hủy -->
                                                            <form action="{{ route('admin.datBan.updateStatus') }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                                <input type="hidden" name="status" value="serving">
                                                                <button type="submit" class="btn btn-success btn-sm">Tiếp tục</button>
                                                            </form>
                                                            <form action="" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                                                            </form>
                                                            @break
                                                        @default
                                                            <span class="text-muted">Không xác định</span>
                                                    @endswitch
                                                </td>
                                            </tr>

                                            <!-- Modal for selecting table -->
                                            <div class="modal fade" id="selectTableModal{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="selectTableModalLabel{{ $reservation->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="selectTableModalLabel{{ $reservation->id }}">Chọn bàn cho đặt chỗ</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.datBan.update', $reservation->id) }}" method="POST">
                                                            @method('PUT')
                                                            @csrf
                                                            <div class="modal-body">
                                                                <label for="table_id">Chọn bàn</label>
                                                                <select name="table_id" id="table_id" class="form-control" required>
                                                                    <option value="">Chọn bàn</option>
                                                                    @foreach ($availableTables as $table)
                                                                        <option value="{{ $table->id }}">
                                                                            Ngày: {{ \Carbon\Carbon::parse($table->available_date)->format('d/m/Y') }} -
                                                                            Bàn {{ $table->table_number }} -
                                                                            Sức chứa: {{ $table->capacity }} người -
                                                                            Từ {{ \Carbon\Carbon::parse($table->available_from)->format('H:i') }} đến {{ \Carbon\Carbon::parse($table->available_until)->format('H:i') }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Xác nhận bàn</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Modal for displaying menu details -->
                                            <div class="modal fade" id="menuModal{{ $reservation->id }}" tabindex="-1" role="dialog" aria-labelledby="menuModalLabel{{ $reservation->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="menuModalLabel{{ $reservation->id }}">Món ăn đã chọn</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Display the list of menus -->
                                                            @foreach ($reservation->menus as $menu)
                                                                <p>
                                                                    <strong>{{ $menu->name }}</strong><br>
                                                                    Số lượng: {{ $menu->pivot->quantity }}<br>
                                                                    Giá: {{ number_format($menu->price, 0, ',', '.') }} VND
                                                                </p>
                                                            @endforeach
                                                            <hr>
                                                            <p><strong>Tổng giá trị đơn hàng:</strong> {{ number_format($reservation->menus->sum(function($menu) {
                                                                return $menu->price * $menu->pivot->quantity;
                                                            }), 0, ',', '.') }} VND</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9">Không có đặt chỗ nào được ghi nhận.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <!-- Phân trang -->
                            <div class="mt-3">
                                {{ $tables->links('pagination::bootstrap-5') }}
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
                    $('#success-alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    </script>
@endsection
